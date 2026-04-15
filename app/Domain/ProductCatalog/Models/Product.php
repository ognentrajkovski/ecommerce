<?php

namespace App\Domain\ProductCatalog\Models;

use App\Domain\Cart\Models\CartItem;
use App\Domain\OrderManagement\Models\OrderItem;
use App\Domain\ProductCatalog\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected static string $factory = ProductFactory::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'image_url',
        'slug',
        'price',
        'stock',
        'is_active',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor(Builder $query, Vendor|string $vendor): Builder
    {
        $vendorId = $vendor instanceof Vendor ? $vendor->getKey() : $vendor;

        return $query->where('vendor_id', $vendorId);
    }
}
