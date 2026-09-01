<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\InventoryLog;
use App\Models\VendorLedger;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BusinessException;

class PurchaseService
{
    protected ActivityLogService $activityLogService;
    protected StockService $stockService;

    public function __construct(
        ActivityLogService $activityLogService,
        StockService $stockService
    ) {
        $this->activityLogService = $activityLogService;
        $this->stockService = $stockService;
    }


    /*
    |--------------------------------------------------------------------------
    | GET PURCHASES
    |--------------------------------------------------------------------------
    */

    public function getPurchases()
    {
        return Purchase::with('vendor')
            ->orderBy('id', 'desc')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | GET CREATE PAGE DATA
    |--------------------------------------------------------------------------
    */

    public function getCreateData()
    {
        $vendors = Vendor::all();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        $year = date('Y');

        $lastPurchase = Purchase::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $newNumber =
            $lastPurchase && $lastPurchase->invoice_no
                ? ((int) substr($lastPurchase->invoice_no, -4)) + 1
                : 1;

        $invoice_no =
            'PO' .
            $year .
            '-' .
            str_pad(
                $newNumber,
                4,
                '0',
                STR_PAD_LEFT
            );

        return compact(
            'vendors',
            'products',
            'invoice_no'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE PURCHASE
    |--------------------------------------------------------------------------
    */

    public function getPurchase($id)
    {
        return Purchase::with(
            'vendor',
            'items.product'
        )->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PURCHASE ORDER
    |--------------------------------------------------------------------------
    */

    public function createPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {

            $productIds = [];

            /*
            |--------------------------------------------------------------
            | Validate Products
            |--------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                if (in_array($item['product_id'], $productIds)) {
                    throw new BusinessException(
                        'Duplicate products are not allowed in same PO.'
                    );
                }

                $productIds[] = $item['product_id'];

                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new BusinessException(
                        'Selected product does not exist.'
                    );
                }

                if ($product->status !== 'active') {
                    throw new BusinessException(
                        'Product "' .
                        $product->name .
                        '" is inactive and cannot be added to a Purchase Order.'
                    );
                }

                if (
                    !isset($item['qty']) ||
                    $item['qty'] <= 0
                ) {
                    throw new BusinessException(
                        'Product "' .
                        $product->name .
                        '" must have quantity greater than 0.'
                    );
                }

                if (
                    !isset($item['price']) ||
                    $item['price'] < 0
                ) {
                    throw new BusinessException(
                        'Invalid price for product "' .
                        $product->name .
                        '".'
                    );
                }
            }


            /*
            |--------------------------------------------------------------
            | Create Purchase
            |--------------------------------------------------------------
            */

            $purchase = Purchase::create([

                'vendor_id' =>
                    $data['vendor_id'],

                'invoice_no' =>
                    $data['invoice_no'],

                'purchase_date' =>
                    $data['purchase_date'],

                'total_amount' =>
                    0,

                'status' =>
                    'pending',
            ]);


            $grandTotal = 0;


            /*
            |--------------------------------------------------------------
            | Create Purchase Items
            |--------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                $total =
                    $item['qty'] *
                    $item['price'];

                PurchaseItem::create([

                    'purchase_id' =>
                        $purchase->id,

                    'product_id' =>
                        $item['product_id'],

                    'qty' =>
                        $item['qty'],

                    'price' =>
                        $item['price'],

                    'total' =>
                        $total,
                ]);

                $grandTotal += $total;
            }


            /*
            |--------------------------------------------------------------
            | Update Grand Total
            |--------------------------------------------------------------
            */

            $purchase->update([
                'total_amount' => $grandTotal
            ]);


            /*
            |--------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------
            */

            $this->activityLogService->log(

                action: 'created',

                module: 'Purchase',

                description:
                    'Purchase Order created',

                model: $purchase,

                newValues:
                    $purchase
                        ->load('items')
                        ->toArray()
            );


            return $purchase;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | GET RECEIVE DATA
    |--------------------------------------------------------------------------
    */

    public function getReceiveData($id)
    {
        $purchase = Purchase::with(
            'items.product',
            'vendor'
        )->findOrFail($id);


        /*
        |--------------------------------------------------------------
        | Check PO Status
        |--------------------------------------------------------------
        */

        if (
            in_array(
                $purchase->status,
                [
                    'received',
                    'short_closed'
                ]
            )
        ) {
            throw new BusinessException(
                'This Purchase Order cannot be received.'
            );
        }


        /*
        |--------------------------------------------------------------
        | Calculate Pending Quantity
        |--------------------------------------------------------------
        */

        foreach ($purchase->items as $item) {

            $alreadyReceived =
                PurchaseReceiveItem::whereHas(
                    'receive',
                    function ($query) use ($id) {

                        $query->where(
                            'purchase_id',
                            $id
                        );
                    }
                )
                ->where(
                    'product_id',
                    $item->product_id
                )
                ->sum('received_qty');


            $item->already_received =
                $alreadyReceived;

            $item->remaining_qty =
                max(
                    0,
                    $item->qty - $alreadyReceived
                );

            $item->receive_now = 0;
        }


        return $purchase;
    }


    /*
    |--------------------------------------------------------------------------
    | STORE RECEIVE
    |--------------------------------------------------------------------------
    */

    public function storeReceive(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {

            /*
            |--------------------------------------------------------------
            | Get Purchase
            |--------------------------------------------------------------
            */

            $purchase = Purchase::with('vendor')
                ->findOrFail($id);

            $vendor = $purchase->vendor;


            /*
            |--------------------------------------------------------------
            | Check PO Status
            |--------------------------------------------------------------
            */

             if ($purchase->status === 'short_closed') {
                throw new BusinessException(
                    'PO is already short closed.'
                );
            }
            /*
            |--------------------------------------------------------------
            | Check Receive Quantity
            |--------------------------------------------------------------
            */

            $hasReceiveQty = false;

            foreach ($data['items'] as $item) {

                if (
                    (float) $item['received_qty'] > 0
                ) {
                    $hasReceiveQty = true;
                    break;
                }
            }

            if (!$hasReceiveQty) {
                throw new BusinessException(
                    'Please enter at least one receive quantity.'
                );
            }


            /*
            |--------------------------------------------------------------
            | Calculate Receive Amount 
            |--------------------------------------------------------------
            */

            $totalReceiveAmount = 0;

            foreach ($data['items'] as $item) {

                $qty =
                    (float) $item['received_qty'];

                $price =
                    (float) $item['price'];

                if ($qty > 0) {

                    $totalReceiveAmount +=
                        $qty * $price;
                }
            }


            /*
            |--------------------------------------------------------------
            | Credit Limit Check
            |--------------------------------------------------------------
            */

            $currentOutstanding =
                $vendor->getOutstandingAmount();

            $availableCredit =
                $vendor->credit_limit -
                $currentOutstanding;


            if (
                (
                    $currentOutstanding +
                    $totalReceiveAmount
                )
                >
                $vendor->credit_limit
            ) {
                throw new BusinessException(
                    'Credit limit exceeded. Available Credit: ₹' .
                    number_format(
                        $availableCredit,
                        2
                    )
                );
            }


            /*
            |--------------------------------------------------------------
            | Create Purchase Receive
            |--------------------------------------------------------------
            */

            $receive = PurchaseReceive::create([

                'purchase_id' =>
                    $purchase->id,

                'receive_date' =>
                    now(),

                'status' =>
                    'completed',
            ]);


            /*
            |--------------------------------------------------------------
            | Vendor Running Balance
            |--------------------------------------------------------------
            */

            $vendorRunningBalance =
                $currentOutstanding;


            /*
            |--------------------------------------------------------------
            | Process Items
            |--------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                $productId =
                    $item['product_id'];

                $orderedQty =
                    (float) $item['ordered_qty'];

                $receiveQty =
                    (float) $item['received_qty'];

                $price =
                    (float) $item['price'];


                /*
                |----------------------------------------------------------
                | Skip Zero Quantity
                |----------------------------------------------------------
                */

                if ($receiveQty <= 0) {
                    continue;
                }


                /*
                |----------------------------------------------------------
                | Product
                |----------------------------------------------------------
                */

                $product =
                    Product::find($productId);

                if (!$product) {
                    throw new BusinessException(
                        'Invalid product selected.'
                    );
                }


                /*
                |----------------------------------------------------------
                | Price
                |----------------------------------------------------------
                */

                if ($price <= 0) {
                    throw new BusinessException(
                        'Price must be greater than zero.'
                    );
                }


                /*
                |----------------------------------------------------------
                | Already Received
                |----------------------------------------------------------
                */

                $alreadyReceived =
                    PurchaseReceiveItem::whereHas(
                        'receive',
                        function ($query) use ($purchase) {

                            $query->where(
                                'purchase_id',
                                $purchase->id
                            );
                        }
                    )
                    ->where(
                        'product_id',
                        $productId
                    )
                    ->sum('received_qty');


                /*
                |----------------------------------------------------------
                | Remaining Quantity
                |----------------------------------------------------------
                */

                $remainingQty =
                    $orderedQty -
                    $alreadyReceived;


                if ($receiveQty > $remainingQty) {

                    throw new BusinessException(
                        "Receive Qty ({$receiveQty}) cannot exceed Pending Qty ({$remainingQty})"
                    );
                }


                /*
                |----------------------------------------------------------
                | Short Quantity
                |----------------------------------------------------------
                */

                $totalReceived =
                    $alreadyReceived +
                    $receiveQty;

                $shortQty =
                    max(
                        0,
                        $orderedQty -
                        $totalReceived
                    );


                /*
                |----------------------------------------------------------
                | INCREASE STOCK
                |
                | StockService handles:
                | - Product stock update
                | - StockIn
                | - StockLedger
                |----------------------------------------------------------
                */

               $this->stockService->increaseStock(

                    $productId,
                
                    $receiveQty,
                
                    [
                        'type' => 'purchase',
                
                        'reference_type' =>
                            'PURCHASE_RECEIVE',
                
                        'reference_id' =>
                            $purchase->id,
                
                        'po_no' =>
                            $purchase->invoice_no,
                
                        'user_id' =>
                            auth()->id(),
                    ]
                );
                /*
                |----------------------------------------------------------
                | Inventory Log
                |----------------------------------------------------------
                */

                InventoryLog::create([

                    'purchase_id' =>
                        $purchase->id,

                    'product_id' =>
                        $productId,

                    'action_type' =>
                        'receive',

                    'qty' =>
                        $receiveQty,

                    'remarks' =>
                        'Stock received',

                    'created_by' =>
                        auth()->id(),
                ]);


                /*
                |----------------------------------------------------------
                | Purchase Receive Item
                |----------------------------------------------------------
                */

                PurchaseReceiveItem::create([

                    'purchase_receive_id' =>
                        $receive->id,

                    'product_id' =>
                        $productId,

                    'ordered_qty' =>
                        $orderedQty,

                    'received_qty' =>
                        $receiveQty,

                    'short_qty' =>
                        $shortQty,

                    'price' =>
                        $price,
                ]);


                /*
                |----------------------------------------------------------
                | Vendor Ledger
                |----------------------------------------------------------
                */

                $receiveAmount =
                    $receiveQty *
                    $price;

                $vendorRunningBalance +=
                    $receiveAmount;


                VendorLedger::create([

                    'vendor_id' =>
                        $vendor->id,

                    'entry_type' =>
                        'CREDIT',

                    'amount' =>
                        $receiveAmount,

                    'reference_type' =>
                        'PURCHASE_RECEIVE',

                    'reference_id' =>
                        $purchase->id,

                    'balance_after' =>
                        $vendorRunningBalance,

                    'note' =>
                        'Stock received for PO ' .
                        $purchase->invoice_no,

                    'created_by' =>
                        auth()->id(),
                ]);
            }


            /*
            |--------------------------------------------------------------
            | Calculate PO Status
            |--------------------------------------------------------------
            */

            $totalOrdered =
                PurchaseItem::where(
                    'purchase_id',
                    $purchase->id
                )->sum('qty');


            $totalReceived =
                PurchaseReceiveItem::whereHas(
                    'receive',
                    function ($query) use ($purchase) {

                        $query->where(
                            'purchase_id',
                            $purchase->id
                        );
                    }
                )
                ->sum('received_qty');


            /*
            |--------------------------------------------------------------
            | Update PO Status
            |--------------------------------------------------------------
            */

            $purchase->update([

                'status' =>
                    (
                        $totalReceived >=
                        $totalOrdered
                    )
                    ? 'received'
                    : 'partial'
            ]);


            /*
            |--------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------
            */

            $this->activityLogService->log(

                action: 'received',

                module: 'Purchase',

                description:
                    'Purchase Order received',

                model: $purchase,

                newValues:
                    $purchase
                        ->load('items')
                        ->toArray()
            );


            return $purchase;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | SHORT CLOSE
    |--------------------------------------------------------------------------
    */

    public function shortClose($id)
    {
        return DB::transaction(function () use ($id) {

            /*
            |--------------------------------------------------------------
            | Get Purchase
            |--------------------------------------------------------------
            */

            $purchase =
                Purchase::findOrFail($id);


            /*
            |--------------------------------------------------------------
            | Total Ordered
            |--------------------------------------------------------------
            */

            $totalOrdered =
                PurchaseItem::where(
                    'purchase_id',
                    $purchase->id
                )->sum('qty');


            /*
            |--------------------------------------------------------------
            | Total Received
            |--------------------------------------------------------------
            */

            $totalReceived =
                PurchaseReceiveItem::whereHas(
                    'receive',
                    function ($query) use ($purchase) {

                        $query->where(
                            'purchase_id',
                            $purchase->id
                        );
                    }
                )
                ->sum('received_qty');


            /*
            |--------------------------------------------------------------
            | Fully Received Check
            |--------------------------------------------------------------
            */

            if (
                $totalReceived >=
                $totalOrdered
            ) {
                throw new BusinessException(
                    'Fully received PO cannot be short closed.'
                );
            }


            /*
            |--------------------------------------------------------------
            | Already Short Closed
            |--------------------------------------------------------------
            */

            if (
                $purchase->status ===
                'short_closed'
            ) {
                throw new BusinessException(
                    'PO is already short closed.'
                );
            }


            $oldStatus =
                $purchase->status;


            /*
            |--------------------------------------------------------------
            | Get Purchase Items
            |--------------------------------------------------------------
            */

            $items =
                PurchaseItem::where(
                    'purchase_id',
                    $purchase->id
                )->get();


            /*
            |--------------------------------------------------------------
            | Process Remaining Quantity
            |--------------------------------------------------------------
            */

            foreach ($items as $item) {

                $receivedQty =
                    PurchaseReceiveItem::whereHas(
                        'receive',
                        function ($query) use ($purchase) {

                            $query->where(
                                'purchase_id',
                                $purchase->id
                            );
                        }
                    )
                    ->where(
                        'product_id',
                        $item->product_id
                    )
                    ->sum('received_qty');


                $remainingQty =
                    $item->qty -
                    $receivedQty;


                if ($remainingQty > 0) {

                    InventoryLog::create([

                        'purchase_id' =>
                            $purchase->id,

                        'product_id' =>
                            $item->product_id,

                        'action_type' =>
                            'short_close',

                        'qty' =>
                            $remainingQty,

                        'amount' =>
                            $item->price *
                            $remainingQty,

                        'status_from' =>
                            $oldStatus,

                        'status_to' =>
                            'short_closed',

                        'remarks' =>
                            'Remaining qty cancelled on short close',

                        'created_by' =>
                            auth()->id(),
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------
            | Update PO
            |--------------------------------------------------------------
            */

            $purchase->update([

                'status' =>
                    'short_closed'
            ]);


            /*
            |--------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------
            */

            $this->activityLogService->log(

                action: 'short_closed',

                module: 'Purchase',

                description:
                    'Purchase Order short closed',

                model: $purchase,

                newValues:
                    $purchase->toArray()
            );


            return $purchase;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | PURCHASES FOR PRINT
    |--------------------------------------------------------------------------
    */

    public function getPurchasesForPrint(array $ids)
    {
        return Purchase::with(
            'vendor',
            'items.product'
        )
        ->whereIn('id', $ids)
        ->get();
    }
}