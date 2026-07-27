<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Shree Packaging Solutions',
                'contact' => '9876543210',
                'address' => 'MIDC Industrial Area, Mumbai',
                'gst_number' => '27ABCDE1234F1Z5',
                'pan_number' => 'ABCDE1234F',
                'email' => 'info@shreepackaging.com',
                'company_name' => 'Shree Packaging Pvt Ltd',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'status' => 'active',
                'vendor_code' => 'VND-00001',
                'credit_limit' => 100000,
                'payment_days' => 30,
                'bank_name' => 'HDFC Bank',
                'bank_account_no' => '123456789012',
                'ifsc_code' => 'HDFC0001234',
                'remarks' => 'Regular packaging supplier',
                'opening_balance' => 5000,
                'opening_balance_type' => 'CR',
            ],
            [
                'name' => 'Alpha Industrial Suppliers',
                'contact' => '9123456780',
                'address' => 'GIDC Estate, Ahmedabad',
                'gst_number' => '24PQRSX5678L1Z2',
                'pan_number' => 'PQRSX5678L',
                'email' => 'sales@alphasuppliers.com',
                'company_name' => 'Alpha Industries',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'status' => 'active',
                'vendor_code' => 'VND-00002',
                'credit_limit' => 150000,
                'payment_days' => 60,
                'bank_name' => 'ICICI Bank',
                'bank_account_no' => '987654321098',
                'ifsc_code' => 'ICIC0005678',
                'remarks' => 'Raw material supplier',
                'opening_balance' => 0,
                'opening_balance_type' => 'CR',
            ],
            [
                'name' => 'Royal Plastics',
                'contact' => '9988776655',
                'address' => 'Industrial Zone, Pune',
                'gst_number' => '27ROYAL1234A1Z9',
                'pan_number' => 'ROYAL1234A',
                'email' => 'contact@royalplastics.com',
                'company_name' => 'Royal Plastics Industries',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'status' => 'active',
                'vendor_code' => 'VND-00003',
                'credit_limit' => 200000,
                'payment_days' => 45,
                'bank_name' => 'SBI Bank',
                'bank_account_no' => '112233445566',
                'ifsc_code' => 'SBIN0001111',
                'remarks' => 'Plastic raw material vendor',
                'opening_balance' => 10000,
                'opening_balance_type' => 'DR',
            ],
            [
                'name' => 'Sunrise Trading Co.',
                'contact' => '9090909090',
                'address' => 'Sector 18, Noida',
                'gst_number' => '09SUNRISE5678K1Z3',
                'pan_number' => 'SUNRISE5678K',
                'email' => 'info@sunrisetrading.com',
                'company_name' => 'Sunrise Trading Company',
                'city' => 'Noida',
                'state' => 'Uttar Pradesh',
                'status' => 'active',
                'vendor_code' => 'VND-00004',
                'credit_limit' => 120000,
                'payment_days' => 30,
                'bank_name' => 'Axis Bank',
                'bank_account_no' => '667788990011',
                'ifsc_code' => 'UTIB0002222',
                'remarks' => 'General supplier',
                'opening_balance' => 0,
                'opening_balance_type' => 'CR',
            ],
            [
                'name' => 'Bright Industrial Goods',
                'contact' => '8765432109',
                'address' => 'MIDC Area, Nagpur',
                'gst_number' => '27BRIGHT9999L1Z6',
                'pan_number' => 'BRIGHT9999L',
                'email' => 'sales@brightindustries.com',
                'company_name' => 'Bright Industries',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'status' => 'active',
                'vendor_code' => 'VND-00005',
                'credit_limit' => 180000,
                'payment_days' => 60,
                'bank_name' => 'Bank of Baroda',
                'bank_account_no' => '445566778899',
                'ifsc_code' => 'BARB0INDUSTRY',
                'remarks' => 'Industrial goods supplier',
                'opening_balance' => 25000,
                'opening_balance_type' => 'DR',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}