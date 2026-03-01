<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;

class CustomerPaymentStatusController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $query = $store->customers()->with('transactions');

        // Filter by status
        if ($request->has('status')) {
            $status = strtolower($request->status);
            
            if ($status === 'lunas') {
                $query->lunas();
            } elseif ($status === 'belum_bayar') {
                $query->belumBayar();
            } elseif ($status === 'jatuh_tempo') {
                $query->jatuhTempo();
            }
        }

        $customers = $query->get()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'speed_package' => $customer->speed_package,
                'monthly_fee' => $customer->monthly_fee,
                'join_date' => $customer->join_date,
                'due_date' => $customer->due_date,
                'last_payment_date' => $customer->last_payment_date,
                'payment_status' => $customer->payment_status,
            ];
        });

        return response()->json($customers);
    }
}
