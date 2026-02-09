<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_store(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/stores', [
                'name' => 'Test Warung',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'name', 'owner_id']]);

        $this->assertDatabaseHas('stores', [
            'name' => 'Test Warung',
            'owner_id' => $user->id,
        ]);
    }

    public function test_owner_can_assign_cashier(): void
    {
        $owner = User::factory()->create();
        $cashier = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/stores/{$store->id}/cashiers", [
                'user_id' => $cashier->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('store_users', [
            'store_id' => $store->id,
            'user_id' => $cashier->id,
            'role' => 'cashier',
        ]);
    }

    public function test_non_owner_cannot_assign_cashier(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $cashier = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->postJson("/api/stores/{$store->id}/cashiers", [
                'user_id' => $cashier->id,
            ]);

        $response->assertStatus(403);
    }
}
