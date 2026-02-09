<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Store $store): bool
    {
        return $user->hasStoreAccess($store->id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function delete(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function manageCashiers(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function viewReports(User $user, Store $store): bool
    {
        return $user->isStoreOwner($store->id);
    }

    public function createExpense(User $user, Store $store): bool
    {
        return $user->isStoreOwner($store->id);
    }
}
