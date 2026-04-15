<?php

use App\Domain\OrderManagement\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function getOrdersProperty()
    {
        return Order::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }
};
?>
<div class="mx-auto max-w-5xl space-y-8">
    <h1 class="text-3xl font-bold">My Orders</h1>
    <div class="overflow-hidden rounded-lg border bg-white">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Order ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($this->orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-mono text-sm">{{ substr($order->id, 0, 8) }}</td>
                        <td class="px-4 py-3 text-sm">{{ $order->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-sm">${{ number_format((float) $order->total_price, 2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-block rounded-full bg-gray-100 px-2 py-1 text-xs font-medium uppercase tracking-wide text-gray-600">
                                {{ $order->status->value }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('buyer.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">You haven't placed any orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
