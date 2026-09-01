<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseStoreRequest;
use App\Http\Requests\PurchaseReceiveRequest;
use App\Services\PurchaseService; 

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;

        $this->middleware('permission:purchase.view')
            ->only(['index', 'show']);

        $this->middleware('permission:purchase.create')
            ->only(['create', 'store']);

        $this->middleware('permission:purchase.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:purchase.delete')
            ->only(['destroy']);

        $this->middleware('permission:purchase.approve')
            ->only(['approve']);

        $this->middleware('permission:purchase.print')
            ->only(['print', 'multiPrint']);

        $this->middleware('permission:purchase.receive')
            ->only(['receive', 'storeReceive']);

        $this->middleware('permission:purchase.short-close')
            ->only(['shortClose']);
    }

    public function index()
    {
        $purchases = $this->purchaseService->getPurchases();

        return view(
            'Admin.Purchase.index',
            compact('purchases')
        );
    }

    public function create()
    {
        $data = $this->purchaseService->getCreateData();

        return view(
            'Admin.Purchase.create',
            $data
        );
    }

    public function show($id)
    {
        $purchase = $this->purchaseService->getPurchase($id);

        return view(
            'Admin.Purchase.show',
            compact('purchase')
        );
    }

   public function receive($id)
    {
          
    
            $purchase = $this->purchaseService
                ->getReceiveData($id);
    
            return view(
                'Admin.Purchase.receive',
                compact('purchase')
            ); 
         
    }

    public function store(PurchaseStoreRequest $request)
    {
        try {

            $this->purchaseService->createPurchase(
                $request->validated()
            );

            return redirect()
                ->route('Purchase')
                ->with(
                    'success',
                    'Purchase Order Created Successfully'
                );

        } catch (Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

   public function storeReceive(
    PurchaseReceiveRequest $request,
    $id
        ) {
            $this->purchaseService->storeReceive(
                $request->validated(),
                $id
            );
        
            return redirect()
                ->route('Purchase')
                ->with(
                    'success',
                    'Purchase received successfully.'
                );
        }

    public function shortClose($id)
    {
        try {

            $this->purchaseService->shortClose($id);

            return redirect()
                ->route('Purchase')
                ->with(
                    'success',
                    'PO short closed successfully.'
                );

        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function print($id)
    {
        $purchase = $this->purchaseService
            ->getPurchase($id);

        return view(
            'Admin.Purchase.print',
            compact('purchase')
        );
    }

    public function multiPrint($ids)
    {
        $ids = explode(',', $ids);

        $purchases = $this->purchaseService
            ->getPurchasesForPrint($ids);

        return view(
            'Admin.Purchase.multi-print',
            compact('purchases')
        );
    }
}