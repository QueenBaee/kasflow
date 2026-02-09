<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignCashierRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Models\Store;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request): JsonResponse
    {
        $stores = $request->user()->stores()->with('owner')->get();
        
        return response()->json(['data' => $stores]);
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $store = Store::create([
            'owner_id' => $request->user()->id,
            'name' => $request->name,
        ]);

        $store->users()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json(['data' => $store], 201);
    }

    public function show(Store $store): JsonResponse
    {
        $this->authorize('view', $store);
        
        return response()->json(['data' => $store->load('owner', 'cashiers')]);
    }

    public function update(StoreStoreRequest $request, Store $store): JsonResponse
    {
        $this->authorize('update', $store);
        
        $store->update($request->validated());
        
        return response()->json(['data' => $store]);
    }

    public function destroy(Store $store): JsonResponse
    {
        $this->authorize('delete', $store);
        
        $store->delete();
        
        return response()->json(['message' => 'Store deleted successfully']);
    }

    public function assignCashier(AssignCashierRequest $request, Store $store)
    {
        $this->authorize('manageCashiers', $store);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'User not found with that email');
        }
        
        if ($store->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User already assigned to this store');
        }
        
        $store->users()->attach($user->id, ['role' => 'cashier']);
        
        return back()->with('success', 'Cashier assigned successfully');
    }

    public function removeCashier(Request $request, Store $store, int $userId)
    {
        $this->authorize('manageCashiers', $store);
        
        $store->users()->wherePivot('role', 'cashier')->detach($userId);
        
        return back()->with('success', 'Cashier removed successfully');
    }
}
