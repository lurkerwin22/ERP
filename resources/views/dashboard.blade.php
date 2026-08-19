<x-layout>
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard & Overview</h1>
                <p class="text-sm text-gray-500 mt-1">Real-time aggregate performance indicators across your business.</p>
            </div>
            <div class="text-xs font-semibold px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200 w-fit">
                Last updated: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- 1. Top Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Sales -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completed Sales</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalSalesCount) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Today: <span class="font-bold text-gray-700">{{ $todayStats->count }}</span></p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                    🛒
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($totalRevenue, 2) }} TND</p>
                    <p class="text-xs text-gray-400 mt-1">This Month: <span class="font-bold text-emerald-700">{{ number_format($monthStats->revenue, 2) }} TND</span></p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                    💵
                </div>
            </div>

            <!-- Customers -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Active Customers</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalCustomers) }}</p>
                    <a href="{{ route('customers.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline mt-1 inline-block">View directory &rarr;</a>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                    👥
                </div>
            </div>

            <!-- Product catalog -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Catalog Product</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalProduct)}}</p>
                    <p class="text-xs text-gray-400 mt-1">Total Units: <span class="font-bold text-gray-700">{{ number_format($stockStats->total_units ?? 0) }}</span></p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                    📦
                </div>
            </div>
        </div>

        <!-- 🤖 ======================================================== -->
        <!-- NEW: AI PROACTIVE INSIGHTS ALERT BOX                        -->
        <!-- ======================================================== -->
        @if(!empty($aiAlerts) && ($aiAlerts['low_stock_count'] > 0 || $aiAlerts['high_debt_customers'] > 0))
            <div class="bg-gradient-to-r from-amber-50 via-amber-50/80 to-indigo-50/50 border border-amber-200/80 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-sm">
                            🤖
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-amber-950">Points d'attention détectés par l'IA</h3>
                            <p class="text-xs text-amber-700">Analyse automatique en temps réel de votre activité ERP</p>
                        </div>
                    </div>
                    <a href="{{ route('ai.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-white px-3 py-1.5 rounded-lg border border-indigo-100 shadow-2xs hover:shadow-xs transition">
                        <span>Consulter l'assistant</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-amber-200/50">
                    @if($aiAlerts['low_stock_count'] > 0)
                        <div class="flex items-center space-x-2 text-xs text-amber-900 bg-white/70 p-2.5 rounded-lg border border-amber-100">
                            <span class="text-rose-500 font-bold">⚠️</span>
                            <span><strong>{{ $aiAlerts['low_stock_count'] }} produit(s)</strong> risquent une rupture de stock imminente.</span>
                        </div>
                    @endif

                    @if($aiAlerts['high_debt_customers'] > 0)
                        <div class="flex items-center space-x-2 text-xs text-amber-900 bg-white/70 p-2.5 rounded-lg border border-amber-100">
                            <span class="text-amber-600 font-bold">💳</span>
                            <span><strong>{{ $aiAlerts['high_debt_customers'] }} client(s)</strong> ont des solde(s) débiteur(s) à relancer.</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <!-- ======================================================== -->

        <!-- 2. Stock Health Overview Bar -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Inventory Health Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                    <span class="text-xs font-bold text-emerald-800 uppercase">✓ Normal Stock</span>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stockStats->normal_stock ?? 0 }} Product</p>
                </div>
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-200">
                    <span class="text-xs font-bold text-amber-800 uppercase">⚠ Low Stock Warning</span>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ $stockStats->low_stock ?? 0 }} Product</p>
                </div>
                <div class="p-4 rounded-lg bg-rose-50 border border-rose-200">
                    <span class="text-xs font-bold text-rose-800 uppercase">✕ Out of Stock</span>
                    <p class="text-2xl font-black text-rose-700 mt-1">{{ $stockStats->out_of_stock ?? 0 }} Product</p>
                </div>
            </div>
        </div>

        <!-- 3. Sales Trend (7-Day Breakdown) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Revenue (Last 7 Days)</h2>
                    <p class="text-xs text-gray-500">Daily sales breakdown over the past week</p>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-2 h-40 items-end pt-4 border-b pb-2">
                @php $maxRevenue = max($last7Days->pluck('revenue')->max(), 1); @endphp
                @foreach($last7Days as $day)
                    @php $heightPercent = max(round(($day['revenue'] / $maxRevenue) * 100), 4); @endphp
                    <div class="flex flex-col items-center h-full justify-end group">
                        <span class="text-[10px] font-bold text-gray-600 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ number_format($day['revenue'], 0) }}
                        </span>
                        <div class="w-full max-w-[36px] bg-indigo-500 hover:bg-indigo-600 rounded-t transition-all" style="height: {{ $heightPercent }}%;"></div>
                        <span class="text-xs font-bold text-gray-700 mt-2">{{ $day['day'] }}</span>
                        <span class="text-[10px] text-gray-400">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Tables Section (Recent Sales & Top Product) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Recent Sales Table -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-gray-900">Recent Transactions</h3>
                    <a href="{{ route('sales.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase">
                                <th class="py-2">Reference</th>
                                <th class="py-2">Customer</th>
                                <th class="py-2 text-right">Total</th>
                                <th class="py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="py-3 font-semibold text-gray-900">#{{ $sale->id }}</td>
                                    <!-- ✅ FIXED: Checks customer_name snapshot first -->
                                    <td class="py-3 text-gray-600">
                                        {{ $sale->customer_name ?? $sale->customer?->name ?? 'Walk-in Customer' }}
                                        @if(is_null($sale->customer_id) && $sale->customer_name && $sale->customer_name !== 'Walk-in Customer')
                                            <span class="text-[10px] text-gray-400 block">(Deleted)</span>
                                        @endif
                                    </td>
                                    <td class="py-3 font-bold text-emerald-600 text-right">{{ number_format($sale->total, 2) }} TND</td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-900">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-xs text-gray-400">No recorded sales yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Selling Product -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-4">Top 5 Best-Selling Product</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase">
                                <th class="py-2">Product Name</th>
                                <th class="py-2 text-center">Units Sold</th>
                                <th class="py-2 text-right">Revenue Generated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($topProduct as $item)
                                <tr>
                                    <!-- ✅ FIXED: Reads snapshot product name directly -->
                                    <td class="py-3 font-medium text-gray-900">{{ $item->product_name }}</td>
                                    <td class="py-3 text-center font-bold text-indigo-600">{{ $item->total_sold }}</td>
                                    <td class="py-3 font-semibold text-gray-900 text-right">{{ number_format($item->total_revenue, 2) }} TND</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-xs text-gray-400">No items sold yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- 5. Low Stock / Critical Inventory Alert Table -->
        @if($criticalStockProduct->isNotEmpty())
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base font-bold text-rose-900 flex items-center gap-2">
                            <span>⚠</span> Inventory Action Required
                        </h3>
                        <p class="text-xs text-rose-700">Product that have hit or dropped below their designated alert threshold.</p>
                    </div>
                    <a href="{{ route('stock.index') }}" class="px-3 py-1.5 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 transition">
                        Manage Inventory &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto bg-white rounded-lg border border-rose-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-100/50 text-[11px] font-bold text-rose-900 uppercase">
                                <th class="py-2 px-4">Product</th>
                                <th class="py-2 px-4 text-center">Current Stock</th>
                                <th class="py-2 px-4 text-center">Alert Threshold</th>
                                <th class="py-2 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100 text-sm">
                            @foreach($criticalStockProduct as $product)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-900">{{ $product->name ?? $product->name }}</td>
                                    <!-- ✅ FIXED: Changed quantity to stock -->
                                    <td class="py-3 px-4 text-center font-bold {{ $product->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                        {{ $product->stock }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-500">{{ $product->alert_threshold }}</td>
                                    <td class="py-3 px-4 text-right">
                                        @if($product->stock <= 0)
                                            <span class="px-2 py-0.5 text-xs font-extrabold bg-rose-100 text-rose-700 rounded">Out of Stock</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-extrabold bg-amber-100 text-amber-700 rounded">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layout>