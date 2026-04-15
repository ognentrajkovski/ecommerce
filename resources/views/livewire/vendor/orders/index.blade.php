<?php

use App\Domain\OrderManagement\Actions\UpdateOrderStatusAction;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function getVendorProperty()
    {
        return Auth::user()->vendor;
    }

    public function getOrdersProperty()
    {
        $vendor = $this->vendor;
        if (!$vendor) {
            return collect();
        }

        return Order::query()
            ->whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['user', 'items' => function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)->with('product');
            }])
            ->latest()
            ->get();
    }
    
    public function getAvailableStatusesProperty(): array
    {
        return OrderStatus::cases();
    }

    public function updateStatus(string $orderId, string $newStatusValue, UpdateOrderStatusAction $action): void
    {
        $order = Order::find($orderId);
        $newStatus = OrderStatus::tryFrom($newStatusValue);
        
        if (!$order || !$newStatus) {
            return;
        }

        try {
            $action->execute($order, $newStatus);
            $this->dispatch('notify', message: "Order status updated to {$newStatus->value}");
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage());
        }
    }
};
?>
<div class="mx-auto max-w-6xl space-y-8">
    <h1 class="text-3xl font-bold">Manage Orders</h1>
    
    <div class="overflow-hidden rounded-lg border bg-white">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Order ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Buyer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Items (Your Products)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y line-relaxed">
                @forelse($this->orders as $order)
                    <tr wire:key="vendor-order-{{ $order->id }}">
                        <td class="px-4 py-4 font-mono text-sm align-top leading-tight">
                            <span class="block font-medium">{{ substr($order->id, 0, 8) }}</span>
                            <span class="block mt-1 text-xs text-gray-500">{{ $order->created_at->format('M j, Y') }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm align-top">{{ $order->user->name }}</td>
                        <td class="px-4 py-4 text-sm align-top">
                            <ul class="list-disc pl-4 text-gray-700">
                                @foreach($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->product->name ?? 'Deleted Product' }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-4 text-sm align-top">
                            <select 
                                wire:change="updateStatus('{{ $order->id }}', $event.target.value)"
                                class="rounded-md border-gray-300 px-3 py-1.5 text-sm bg-gray-50 focus:ring-black focus:border-black shadow-sm w-36"
                            >
                                @foreach($this->availableStatuses as $status)
                                    <option 
                                        value="{{ $status->value }}" 
                                        @selected($order->status === $status)
                                        @disabled(!$order->status->canTransitionTo($status) && $order->status !== $status)
                                    >
                                        {{ ucfirst($status->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No orders containing your products have been placed yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
