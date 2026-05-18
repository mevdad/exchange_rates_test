@assets
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
@endassets

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Exchange Rate — USD / {{ $this->record?->code }}
        </x-slot>

        {{-- Period selector --}}
        <div class="mb-6 flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Period:</label>
            <select
                wire:model.live="period"
                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
            >
                <option value="3">Last 3 days</option>
                <option value="5">Last 5 days</option>
                <option value="7">Last 7 days</option>
                <option value="10">Last 10 days</option>
                <option value="14">Last 14 days</option>
                <option value="0">All time</option>
            </select>
        </div>

        @php $chartData = $this->getChartData(); @endphp

        {{-- Chart — wire:key forces full DOM replacement on period change --}}
        <div wire:key="chart-{{ $period }}">
            @if (empty($chartData))
                <div class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 py-12 dark:border-gray-600">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No data available for this period.</p>
                </div>
            @else
                <div x-data="currencyChart">
                    <script type="application/json" x-ref="chartData">
                        {!! json_encode(['series' => $chartData['series'], 'categories' => $chartData['categories']]) !!}
                    </script>
                    <div x-ref="chart"></div>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>

@script
<script>
    // Registered once. Data is read from <script type="application/json"> on each init()
    // so it always reflects the current period, not the first render.
    Alpine.data('currencyChart', function () {
        return {
            chart: null,

            init() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                const { series, categories } = JSON.parse(this.$refs.chartData.textContent);

                this.$nextTick(() => {
                    this.chart = new ApexCharts(this.$refs.chart, {
                        chart: {
                            type: 'line',
                            height: 280,
                            toolbar: { show: true },
                            animations: { enabled: true },
                        },
                        series: series,
                        xaxis: {
                            categories: categories,
                            labels: { style: { fontSize: '12px', colors: '#ffffff' } },
                        },
                        yaxis: {
                            labels: { style: { fontSize: '12px', colors: '#ffffff' } },
                        },
                        stroke: { curve: 'smooth', width: 2 },
                        markers: { size: 4 },
                        tooltip: { x: { show: true } },
                        grid: { borderColor: '#e2e8f0' },
                        colors: ['#3b82f6'],
                    });

                    this.chart.render();
                });
            },

            destroy() {
                this.chart?.destroy();
            },
        };
    });
</script>
@endscript
