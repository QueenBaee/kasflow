<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_record_income(): void
    {
        $owner = User::factory()->create();
        $cashier = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($cashier->id, ['role' => 'cashier']);

        $response = $this->actingAs($cashier, 'sanctum')
            ->postJson("/api/stores/{$store->id}/transactions/income", [
                'amount' => 50000,
                'note' => 'Test income',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'store_id' => $store->id,
            'user_id' => $cashier->id,
            'type' => 'income',
            'amount' => 50000,
        ]);
    }

    public function test_owner_can_record_expense(): void
    {
        $owner = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/stores/{$store->id}/transactions/expense", [
                'amount' => 100000,
                'category' => 'Supplies',
                'note' => 'Test expense',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'store_id' => $store->id,
            'type' => 'expense',
            'amount' => 100000,
            'category' => 'Supplies',
        ]);
    }

    public function test_cashier_cannot_record_expense(): void
    {
        $owner = User::factory()->create();
        $cashier = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($cashier->id, ['role' => 'cashier']);

        $response = $this->actingAs($cashier, 'sanctum')
            ->postJson("/api/stores/{$store->id}/transactions/expense", [
                'amount' => 100000,
                'category' => 'Supplies',
            ]);

        $response->assertStatus(403);
    }

    public function test_amount_must_be_positive(): void
    {
        $owner = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/stores/{$store->id}/transactions/income", [
                'amount' => -50000,
            ]);

        $response->assertStatus(422);
    }
}
