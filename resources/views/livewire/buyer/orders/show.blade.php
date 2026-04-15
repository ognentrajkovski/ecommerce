<?php

use App\Domain\OrderManagement\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Order $order;
    
    public function mount(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $this->order = $order->load(['items.product', 'items.vendor']);
    }
};
?>
<div class="mx-auto max-w-4xl space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold">Order #{{ substr($order->id, 0, 8) }}</h1>
        <a href="{{ route('buyer.orders.index') }}" class="text-sm font-medium text-gray-600 hover:underline">Back to Orders</a>
    </div>

    <div class="rounded-lg border bg-white shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap gap-8 border-b pb-4">
            <div>
                <p class="text-sm text-gray-500">Order Date</p>
                <p class="font-medium">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium capitalize">{{ $order->status->value }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total</p>
                <p class="font-medium">${{ number_format((float) $order->total_price, 2) }}</p>
            </div>
        </div>

        <h2 class="text-lg font-medium pt-2">Order Items</h2>
        <ul class="divide-y divide-gray-100">
            @foreach($order->items as $item)
                <li class="py-4 flex justify-between items-center sm:items-start flex-col sm:flex-row gap-4">
                    <div class="flex items-center gap-4">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="Product" class="h-16 w-16 object-cover rounded border">
                        @else
                            <div class="h-16 w-16 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-400">No Img</div>
                        @endif
                        <div>
                            <p class="font-medium">{{ $item->product->name ?? 'Deleted Product' }}</p>
                            <p class="text-sm text-gray-600">Sold by: {{ $item->vendor->name ?? 'Unknown Vendor' }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }} × ${{ number_format((float) $item->unit_price, 2) }}</p>
                        </div>
                    </div>
                    <p class="font-bold text-gray-900">${{ number_format((float) $item->total_price, 2) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</div>
