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
        // Bersihkan data lama
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('store_users')->truncate();
        \DB::table('transactions')->truncate();
        \DB::table('customers')->truncate();
        \DB::table('stores')->truncate();
        \DB::table('users')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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

        $store1->users()->attach($owner->id, ['role' => 'owner']);
        $store1->users()->attach($cashier1->id, ['role' => 'cashier']);
        $store1->users()->attach($cashier2->id, ['role' => 'cashier']);

        $feeMap = [3 => 100000, 5 => 110000, 8 => 150000, 10 => 160000, 20 => 180000];

        $customers = [
            // store1 - buring
            ['name' => 'ADIT',         'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'AGUNG',        'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BAKSOTOTOK',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUENDANG1',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUANA',        'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUFARIDA22',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUHABIBA',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUHORIDA',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUJANNAH',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUJUNI1',      'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUKHOIROH',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUKHUSNUL',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUMARDIYAH1',  'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUNORIS1',     'mbps' => 10, 'address' => 'buring',    'store' => $store1],
            ['name' => 'BUSOFI',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUSULIMAH1',   'mbps' => 8,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUULFIA',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'BUYUSRIKA',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'DIKA1',        'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'ESBUAH',       'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'FADHANELFI',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'FATHUR',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'FEBRINA',      'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'FELIS',        'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'GARASI',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'GARASI2',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MAKTINI',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASANGGER',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASHAMDAN',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASHERI',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASKHOIRON',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASMUNIR',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASROHMAN',    'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASSAIFUDIN',  'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASSAIFULR',   'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKANIS',     'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKAYU',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKINDRI',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKISA',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKITA',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKKIKI',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKKIKI2',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKLELY',     'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKLULUK',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKNOVI',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MBAKYUNI',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MULIACELL',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MULYONO',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'NABILA',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKALAL',      'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKDAURI',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKDEFRI',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'MASEKO',       'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKFAJAR',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKGATOT',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKHAMID',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKHADI',      'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKHARIONO',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKIWAN',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKJUMAIN',    'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKMAT',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKNANANG',    'mbps' => 8,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKROHIM',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKRUSBANI',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKSAIFULLAH', 'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKSAMSUL',    'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKSISWANTO',  'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKSOLIKIN',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKSUPAAT',    'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKTAKIM',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKTO',        'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKWAWAN1',    'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKWAWAN10',   'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKYANTO',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKYASIN',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'RISOLEH',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'ROFA',         'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SAMDONI',      'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SERVER',       'mbps' => 8,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SERVER1',      'mbps' => 8,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SHERLY',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SOFIAVIA',     'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SOFILESTARI',  'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SUGIHARTONO',  'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SUMAIYAH',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'TOKO',         'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'VIOLA',        'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'WAHYUDI',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKDEDIK',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'RAMANDA',      'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'ANDIK',        'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'DONI',         'mbps' => 3,  'address' => 'buring',    'store' => $store1],
            ['name' => 'AZWARZAHIH',   'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'JORGI',        'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'REVI',         'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'SULTANALTAF',  'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'YULIATI1',     'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            ['name' => 'PAKNUR',       'mbps' => 5,  'address' => 'buring',    'store' => $store1],
            // store1 - jambearjo
            ['name' => 'JAKOP',          'mbps' => 20, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MASADI',         'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MBAKLIA',        'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'PAKHUDA1',       'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'PAKWILDAN',      'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'ANDRE',          'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MENDOL',         'mbps' => 5,  'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'FARIS',          'mbps' => 20, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MBAKLAILA',      'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MBAKTANTI',      'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'BUYANTI',        'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'PAKMAT',         'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'HARIS',          'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'PABRIKTRIPLEK',  'mbps' => 20, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'UDINMASJID',     'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
            ['name' => 'MASBAGUS',       'mbps' => 10, 'address' => 'jambearjo', 'store' => $store1],
        ];

        foreach ($customers as $c) {
            Customer::create([
                'store_id'      => $c['store']->id,
                'name'          => $c['name'],
                'address'       => $c['address'],
                'speed_package' => $c['mbps'].' Mbps',
                'monthly_fee'   => $feeMap[$c['mbps']],
                'due_date'      => 25,
                'join_date'     => '2024-01-01',
            ]);
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
                'store_id' => $store1->id,
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
