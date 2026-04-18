<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [

            [
                'name' => 'Raj Traders',
                'company_name' => 'Raj Traders Pvt Ltd',
                'mobile' => '9876543210',
                'alternate_mobile' => '9123456780',
                'email' => 'raj@rajtraders.com',
                'billing_address' => 'Market Yard, Shivaji Nagar',
                'shipping_address' => 'MIDC Industrial Area',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411037',
                'country' => 'India',
                'gst_number' => '27AABCR1234Z1Z5',
                'pan_number' => 'AABCR1234Z',
                'customer_type' => 'business',
                'status' => 1,
                'notes' => 'Bulk packaging supplier',
            ],

            [
                'name' => 'Sneha Enterprises',
                'company_name' => 'Sneha Enterprises',
                'mobile' => '9988776655',
                'alternate_mobile' => null,
                'email' => 'sneha@gmail.com',
                'billing_address' => 'MG Road',
                'shipping_address' => 'MG Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'country' => 'India',
                'gst_number' => '27ABCDE1234F1Z2',
                'pan_number' => 'ABCDE1234F',
                'customer_type' => 'business',
                'status' => 1,
                'notes' => 'Retail distributor',
            ],

            [
                'name' => 'Amit Sharma',
                'company_name' => null,
                'mobile' => '9876501234',
                'alternate_mobile' => null,
                'email' => 'amit@gmail.com',
                'billing_address' => 'Sai Residency Flat 203',
                'shipping_address' => 'Sai Residency',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411014',
                'country' => 'India',
                'gst_number' => null,
                'pan_number' => null,
                'customer_type' => 'individual',
                'status' => 1,
                'notes' => 'Individual buyer',
            ],

            [
                'name' => 'Global Packaging Solutions',
                'company_name' => 'Global Packaging LLP',
                'mobile' => '9090909090',
                'alternate_mobile' => null,
                'email' => 'contact@globalpack.com',
                'billing_address' => 'Industrial Estate Plot 18',
                'shipping_address' => 'Warehouse Block B',
                'city' => 'Nashik',
                'state' => 'Maharashtra',
                'pincode' => '422010',
                'country' => 'India',
                'gst_number' => '27AAFCG5678H1Z9',
                'pan_number' => 'AAFCG5678H',
                'customer_type' => 'business',
                'status' => 1,
                'notes' => 'Wholesale packaging buyer',
            ],

            [
                'name' => 'Metro Distributors',
                'company_name' => 'Metro Distributors Pvt Ltd',
                'mobile' => '8888888888',
                'alternate_mobile' => '8777777777',
                'email' => 'metro@distributors.com',
                'billing_address' => 'Industrial Area Phase 2',
                'shipping_address' => 'Warehouse Zone C',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411019',
                'country' => 'India',
                'gst_number' => '27AAGCM7890K1Z6',
                'pan_number' => 'AAGCM7890K',
                'customer_type' => 'business',
                'status' => 1,
                'notes' => 'High volume distributor',
            ],

            [
                'name' => 'Star Retail Shop',
                'company_name' => 'Star Retail',
                'mobile' => '7777777777',
                'alternate_mobile' => null,
                'email' => 'star@retail.com',
                'billing_address' => 'Main Bazaar Road',
                'shipping_address' => 'Main Bazaar Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400012',
                'country' => 'India',
                'gst_number' => null,
                'pan_number' => null,
                'customer_type' => 'business',
                'status' => 1,
                'notes' => 'Retail shop customer',
            ],

        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}