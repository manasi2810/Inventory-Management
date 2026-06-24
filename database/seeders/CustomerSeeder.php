<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
            {
                $customers = [
                    [
                        'name' => 'Rahul Sharma',
                        'company_name' => 'Sharma Traders',
                        'mobile' => '9876543210',
                        'alternate_mobile' => '9123456780',
                        'email' => 'rahul@sharmatraders.com',
                        'billing_address' => 'Shop No 12, Market Road',
                        'shipping_address' => 'Warehouse, MIDC Area',
                        'city' => 'Mumbai',
                        'state' => 'Maharashtra',
                        'pincode' => '400001',
                        'country' => 'India',
                        'gst_number' => '27ABCDE1234F1Z5',
                        'pan_number' => 'ABCDE1234F',
                        'credit_limit' => 50000,
                        'opening_balance' => 0,
                        'customer_type' => 'Business',
                        'status' => 1, //  
                        'notes' => 'Regular wholesale customer'
                    ],
                    [
                        'name' => 'Priya Mehta',
                        'company_name' => 'Mehta Pharma Distributors',
                        'mobile' => '9988776655',
                        'alternate_mobile' => null,
                        'email' => 'priya@mehtapharma.com',
                        'billing_address' => 'Plot 45, Industrial Area',
                        'shipping_address' => 'Same as billing',
                        'city' => 'Pune',
                        'state' => 'Maharashtra',
                        'pincode' => '411001',
                        'country' => 'India',
                        'gst_number' => '27PQRSX5678L1Z2',
                        'pan_number' => 'PQRSX5678L',
                        'credit_limit' => 100000,
                        'opening_balance' => 5000,
                        'customer_type' => 'Business',
                        'status' => 1, //  
                        'notes' => 'High volume pharma buyer'
                    ],
                    [
                        'name' => 'Amit Patel',
                        'company_name' => 'Patel Packaging',
                        'mobile' => '9090909090',
                        'alternate_mobile' => '8080808080',
                        'email' => 'amit@patelpackaging.com',
                        'billing_address' => 'GIDC Industrial Estate',
                        'shipping_address' => 'GIDC Warehouse Unit 5',
                        'city' => 'Ahmedabad',
                        'state' => 'Gujarat',
                        'pincode' => '380001',
                        'country' => 'India',
                        'gst_number' => '24PATEL1234G1Z9',
                        'pan_number' => 'PATEL1234G',
                        'credit_limit' => 75000,
                        'opening_balance' => 2000,
                        'customer_type' => 'Business',
                        'status' => 1,  
                        'notes' => 'Packaging materials distributor'
                    ],
                    [
                        'name' => 'Neha Joshi',
                        'company_name' => null,
                        'mobile' => '8765432109',
                        'alternate_mobile' => null,
                        'email' => 'neha.joshi@example.com',
                        'billing_address' => 'Andheri West',
                        'shipping_address' => 'Andheri West',
                        'city' => 'Mumbai',
                        'state' => 'Maharashtra',
                        'pincode' => '400058',
                        'country' => 'India',
                        'gst_number' => null,
                        'pan_number' => null,
                        'credit_limit' => 10000,
                        'opening_balance' => 0,
                        'customer_type' => 'individual',
                        'status' => 1,  
                        'notes' => 'Small retail customer'
                    ],
                ];

            foreach ($customers as $customer) {

            $customer['customer_code'] =
                'CUST-' . now()->format('YmdHisv') . '-' . random_int(100, 999);

            $customer['status'] = 1;

            Customer::create($customer);
        }
            }
}