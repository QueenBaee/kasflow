<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedStores(): HasMany
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function hasStoreAccess(int $storeId, ?string $role = null): bool
    {
        $query = $this->stores()->where('store_id', $storeId);
        
        if ($role) {
            $query->wherePivot('role', $role);
        }
        
        return $query->exists();
    }

    public function isStoreOwner(int $storeId): bool
    {
        return $this->hasStoreAccess($storeId, 'owner');
    }

    public function isStoreCashier(int $storeId): bool
    {
        return $this->hasStoreAccess($storeId, 'cashier');
    }
    
    public function getStoreRole(int $storeId): ?string
    {
        $pivot = $this->stores()->where('store_id', $storeId)->first()?->pivot;
        return $pivot?->role;
    }
    
    public function isOwner(): bool
    {
        return $this->stores()->wherePivot('role', 'owner')->exists();
    }
    
    public function isCashier(): bool
    {
        return $this->stores()->wherePivot('role', 'cashier')->exists() 
            && !$this->isOwner();
    }
}
