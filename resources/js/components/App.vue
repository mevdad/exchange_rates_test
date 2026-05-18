<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <!-- Container -->
    <div class="max-w-xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Exchange Rates</h1>
        <p class="text-slate-600">Monitor currency exchange rates and trends</p>
      </div>

      <!-- Loading Indicator -->
      <div v-if="isLoading" class="flex justify-center items-center p-8 loader">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
        <p class="text-red-800 font-medium">Error: {{ errorMessage }}</p>
        <p class="text-red-700 text-sm mt-1">Please try again or contact support</p>
      </div>

      <div>
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            <!-- Currency Select -->
            <div>
              <label for="currency" class="block text-sm font-medium text-slate-700 mb-2">
                Currency
              </label>
              <select
                id="currency"
                v-model="selectedCurrency"
                @change="onCurrencyChange"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option v-for="item in currencies" :key="item.code" :value="item.code">
                  {{ item.code }} - {{ item.name }}
                </option>
              </select>
            </div>

            <!-- Period Select -->
            <div>
              <label for="period" class="block text-sm font-medium text-slate-700 mb-2">
                Period
              </label>
              <select
                id="period"
                v-model="selectedPeriod"
                @change="onPeriodChange"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="3">Last 3 days</option>
                <option value="5">Last 5 days</option>
                <option value="7">Last 7 days</option>
                <option value="10">Last 10 days</option>
                <option value="14">Last 14 days</option>
                <option value="0">All time</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">
              {{ selectedCurrency }} / USD
            </h2>
            <p class="text-slate-600 text-sm mt-1">Exchange rate over the last {{ selectedPeriod }} days</p>
          </div>

          <!-- Chart Placeholder -->
          <div class="bg-slate-50 rounded-lg border-2 border-dashed border-slate-300 flex items-center justify-center">
            <apexchart class="w-full" type="line" :key="key" :options="options" :series="series"></apexchart>
          </div>
        </div>

        <!-- Info Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Current Rate Card -->
          <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-blue-500">
            <h3 class="text-slate-600 font-semibold mb-2">Current Rate</h3>
            <p class="text-3xl font-bold text-slate-900">{{ statistics.current.toFixed(3) }}</p>
            <p class="text-slate-500 text-sm mt-2">1 USD = {{ statistics.current.toFixed(3) }} {{ selectedCurrency }}</p>
          </div>

          <!-- Change Card -->
          <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-green-500">
            <h3 class="text-slate-600 font-semibold mb-2">Change ({{ selectedPeriod == 0 ? 'all time' : selectedPeriod + 'd' }})</h3>
            <p class="text-3xl font-bold text-green-600">{{ statistics.change.toFixed(3) }}</p>
            <p class="text-slate-500 text-sm mt-2">{{ statistics.change_percent }}% from {{ selectedPeriod == 0 ? 'all time' : selectedPeriod + ' days ago' }}</p>
          </div>

          <!-- High/Low Card -->
          <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-purple-500">
            <h3 class="text-slate-600 font-semibold mb-2">High / Low</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-slate-500 text-xs uppercase mb-1">High</p>
                <p class="text-xl font-bold text-slate-900">{{ statistics.max.toFixed(3) }}</p>
              </div>
              <div>
                <p class="text-slate-500 text-xs uppercase mb-1">Low</p>
                <p class="text-xl font-bold text-slate-900">{{ statistics.min.toFixed(3) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExchangeRatesApp',
  data() {
    return {
      key: new Date().getTime(),
      selectedCurrency: 'EUR',
      currencies: [],
      selectedPeriod: '3',
      isLoading: false,
      errorMessage: null,
      options: {
        chart: {
          width: '100%',
          toolbar: {
            show: true
          }
        },
        xaxis: {
          categories: []
        }
      },
      series: [{
        name: 'series-1',
        data: []
      }],
      statistics: {
        change: 0,
        change_percent: 0,
        current: 0,
        min: 0,
        max: 0
      }
    };
  },
  methods: {
    async fetchDataMount() {
      this.isLoading = true;
      this.errorMessage = null;

      let cres = await fetch('/api/currencies');
      let cdata = await cres.json();
      console.log(cdata);
      if(!cdata.success) {
        this.errorMessage = cdata.message || 'Failed to fetch currencies';
        this.isLoading = false;
        return;
      }
      this.currencies = cdata.data.filter(c => c.code !== 'USD');
      
      this.options.xaxis.categories = this.currencies[0].chart_data.categories;
      this.series = this.currencies[0].chart_data.series;
      this.statistics = this.currencies[0].chart_data.statistics;
      this.key = new Date().getTime();
      this.isLoading = false;
    },
    async fetchData() {
      this.isLoading = true;
      this.errorMessage = null;
      let chartRes = await fetch('/api/rates/chart?to=' + this.selectedCurrency + '&from=USD&days=' + this.selectedPeriod);
      let chartData = await chartRes.json();
      console.log(chartData);
      if(!chartData.success) {
        this.errorMessage = chartData.message || 'Failed to fetch chart data';
        this.isLoading = false;
        return;
      }
      this.options.xaxis.categories = chartData.data.categories;
      this.series = chartData.data.series;
      this.statistics = chartData.data.statistics;
      this.key = new Date().getTime();
      this.isLoading = false;
    },
    onCurrencyChange() {
      this.fetchData();
    },
    onPeriodChange() {
      this.fetchData();
    }
  },
  async mounted() {
    await this.fetchDataMount();
  },
};
</script>

<style scoped>
/* Add any additional component-specific styles here */
.loader{
  position: fixed;
  top: 0;
  right: 0;
}
</style>