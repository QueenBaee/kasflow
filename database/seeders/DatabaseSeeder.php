<?php

namespace Database\Seeders;

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
            'name' => 'Warung Maju Jaya',
        ]);

        $store2 = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Warung Berkah',
        ]);

        $store1->users()->attach($owner->id, ['role' => 'owner']);
        $store1->users()->attach($cashier1->id, ['role' => 'cashier']);

        $store2->users()->attach($owner->id, ['role' => 'owner']);
        $store2->users()->attach($cashier2->id, ['role' => 'cashier']);

        for ($i = 0; $i < 10; $i++) {
            Transaction::create([
                'store_id' => $store1->id,
                'user_id' => $cashier1->id,
                'type' => 'income',
                'amount' => rand(10000, 500000),
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
                'note' => 'Sample income transaction',
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
