<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $cashier1 = User::create([
            'name' => 'Cashier One',
            'email' => 'cashier1@example.com',
            'password' => Hash::make('password'),
        ]);

        $cashier2 = User::create([
            'name' => 'Cashier Two',
            'email' => 'cashier2@example.com',
            'password' => Hash::make('password'),
        ]);

        $store1 = Store::create([
            'owner_id' => $owner->id,
            'name' => 'ISP Maju Jaya',
        ]);

        $store2 = Store::create([
            'owner_id' => $owner->id,
            'name' => 'ISP Berkah',
        ]);

        $store1->users()->attach($owner->id, ['role' => 'owner']);
        $store1->users()->attach($cashier1->id, ['role' => 'cashier']);

        $store2->users()->attach($owner->id, ['role' => 'owner']);
        $store2->users()->attach($cashier2->id, ['role' => 'cashier']);

        // Create customers for store1
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567890', 'address' => 'Jl. Merdeka No. 10, Jakarta', 'speed_package' => '10 Mbps', 'monthly_fee' => 250000, 'join_date' => '2024-01-15', 'due_date' => 5],
            ['name' => 'Siti Aminah', 'phone' => '081234567891', 'address' => 'Jl. Sudirman No. 25, Jakarta', 'speed_package' => '20 Mbps', 'monthly_fee' => 350000, 'join_date' => '2024-02-10', 'due_date' => 10],
            ['name' => 'Ahmad Yani', 'phone' => '081234567892', 'address' => 'Jl. Gatot Subroto No. 5, Jakarta', 'speed_package' => '30 Mbps', 'monthly_fee' => 450000, 'join_date' => '2024-01-20', 'due_date' => 15],
            ['name' => 'Dewi Lestari', 'phone' => '081234567893', 'address' => 'Jl. Thamrin No. 88, Jakarta', 'speed_package' => '50 Mbps', 'monthly_fee' => 600000, 'join_date' => '2024-03-05', 'due_date' => 20],
            ['name' => 'Rudi Hartono', 'phone' => '081234567894', 'address' => 'Jl. Kuningan No. 12, Jakarta', 'speed_package' => '10 Mbps', 'monthly_fee' => 250000, 'join_date' => '2024-02-28', 'due_date' => 25],
            ['name' => 'Rina Wijaya', 'phone' => '081234567895', 'address' => 'Jl. Rasuna Said No. 7, Jakarta', 'speed_package' => '20 Mbps', 'monthly_fee' => 350000, 'join_date' => '2024-01-10', 'due_date' => 1],
            ['name' => 'Agus Setiawan', 'phone' => '081234567896', 'address' => 'Jl. HR Rasuna No. 33, Jakarta', 'speed_package' => '100 Mbps', 'monthly_fee' => 800000, 'join_date' => '2024-03-01', 'due_date' => 5],
            ['name' => 'Maya Sari', 'phone' => '081234567897', 'address' => 'Jl. Casablanca No. 18, Jakarta', 'speed_package' => '30 Mbps', 'monthly_fee' => 450000, 'join_date' => '2024-02-15', 'due_date' => 10],
        ];

        foreach ($customers as $customerData) {
            Customer::create(array_merge($customerData, ['store_id' => $store1->id]));
        }

        for ($i = 0; $i < 10; $i++) {
            Transaction::create([
                'store_id' => $store1->id,
                'user_id' => $cashier1->id,
                'type' => 'income',
                'amount' => rand(10000, 500000),
                'category' => 'Payment',
                'note' => 'Sample income transaction',
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            Transaction::create([
                'store_id' => $store1->id,
                'user_id' => $owner->id,
                'type' => 'expense',
                'amount' => rand(50000, 300000),
                'category' => ['Supplies', 'Rent', 'Utilities', 'Salary'][rand(0, 3)],
                'note' => 'Sample expense transaction',
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);
        }

        for ($i = 0; $i < 8; $i++) {
            Transaction::create([
                'store_id' => $store2->id,
                'user_id' => $cashier2->id,
                'type' => 'income',
                'amount' => rand(15000, 400000),
                'category' => 'Payment',
                'note' => 'Sample income transaction',
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
