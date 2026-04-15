<?php

namespace App\Domain\OrderManagement\Models;

use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected static string $factory = OrderFactory::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'payment_method',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
