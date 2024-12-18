<script lang="ts">
  import { onMount } from 'svelte';

  interface SalesReport {
    date: string;
    total_sales: number;
    order_count: number;
  }

  interface TopProduct {
    id: number;
    name: string;
    total_quantity: number;
    total_sales: number;
  }

  let salesReports: SalesReport[] = [];
  let topProducts: TopProduct[] = [];
  let totalRevenue: number = 0;

  let weekStartDate: string = '';
  let weekEndDate: string = '';
  let selectedMonth: string = '';

  onMount(async () => {
    await fetchInitialData();
  });

  async function fetchInitialData() {
    const today = new Date();
    const oneWeekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    weekStartDate = oneWeekAgo.toISOString().split('T')[0];
    weekEndDate = today.toISOString().split('T')[0];
    await fetchSalesData();
    await fetchTopProducts();
    await fetchTotalRevenue();
  }

  async function fetchSalesData() {
    try {
      const response = await fetch(`/api/dashboard,php?action=sales_data&start_date=${weekStartDate}&end_date=${weekEndDate}`);
      if (!response.ok) throw new Error('Failed to fetch sales data');
      salesReports = await response.json();
    } catch (error) {
      console.error('Error fetching sales data:', error);
      alert('Failed to fetch sales data. Please try again.');
    }
  }

  async function fetchTopProducts() {
    try {
      const response = await fetch('/api/dashboard.php?action=top_products&limit=5');
      if (!response.ok) throw new Error('Failed to fetch top products');
      topProducts = await response.json();
    } catch (error) {
      console.error('Error fetching top products:', error);
      alert('Failed to fetch top products. Please try again.');
    }
  }

  async function fetchTotalRevenue() {
    try {
      const response = await fetch(`/api/dashboard.php?action=total_revenue&start_date=${weekStartDate}&end_date=${weekEndDate}`);
      if (!response.ok) throw new Error('Failed to fetch total revenue');
      const data = await response.json();
      totalRevenue = data.total_revenue;
    } catch (error) {
      console.error('Error fetching total revenue:', error);
      alert('Failed to fetch total revenue. Please try again.');
    }
  }

  function logout() {
    window.location.href = '/login';
  }

  function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  function formatShortDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      month: 'short',
      day: 'numeric'
    });
  }

  $: filteredSales = salesReports;

  $: totalFilteredSales = filteredSales.reduce(
    (total, report) => total + report.total_sales,
    0
  );

  $: averageDailySales = filteredSales.length > 0
    ? totalFilteredSales / filteredSales.length
    : 0;

  $: highestSalesDay = filteredSales.reduce(
    (max, report) => (report.total_sales > max.total_sales ? report : max),
    { date: '', total_sales: 0 }
  );

  $: lowestSalesDay = filteredSales.reduce(
    (min, report) => (report.total_sales < min.total_sales ? report : min),
    { date: '', total_sales: Infinity }
  );

  $: salesTrend = filteredSales.length > 1
    ? (filteredSales[filteredSales.length - 1].total_sales - filteredSales[0].total_sales) /
      (filteredSales.length - 1)
    : 0;

  $: maxSales = Math.max(...filteredSales.map(report => report.total_sales));
  $: minSales = Math.min(...filteredSales.map(report => report.total_sales));

  $: sortedSales = [...filteredSales].sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());

  function getBarHeight(sales: number, maxHeight: number): number {
    return (sales / maxSales) * maxHeight;
  }

  function getYAxisLabels(maxSales: number): number[] {
    const step = maxSales / 4;
    return [0, step, step * 2, step * 3, maxSales];
  }

  async function handleDateChange() {
    await fetchSalesData();
    await fetchTotalRevenue();
  }
</script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  :global(body) {
    font-family: 'Poppins', sans-serif;
    background-color: #f5f3ef;
    color: #4a4238;
  }

  button {
    transition: transform 0.2s ease, background-color 0.3s ease;
  }

  button:hover {
    transform: scale(1.05);
  }

  .form-group {
    margin-bottom: 1rem;
  }

  .bg-primary {
    background-color: #f8e8d4; /* Light beige */
  }

  .text-primary {
    color: #4a4238; /* Deep coffee brown */
  }

  .text-secondary {
    color: #705c4e; /* Soft brown */
  }

  .sidebar {
    background-color: #d9c6b0; /* Medium beige for sidebar */
    color: #4a4238; /* Deep coffee text */
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 100vh;
    padding-right: 8px; /* Add padding for scrollbar */
  }

  .sidebar button {
    background-color: #f8e8d4;
    color: #ffffff;
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    width: 100%;
    margin-bottom: 0.5rem;
  }

  .sidebar button:hover {
    background-color: #a88b6f; /* Darker muted brown on hover */
  }

  .card {
    background-color: #ffffff; /* White for cards */
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 1rem;
  }

  .card-header {
    color: #4a4238; /* Deep coffee brown */
    font-weight: bold;
    margin-bottom: 1rem;
  }

  input {
    background-color: #f8f5f2; /* Light cream */
    border: 1px solid #d9c6b0; /* Medium beige */
    padding: 0.5rem;
    border-radius: 0.5rem;
    color: #4a4238; /* Deep coffee text */
  }

  input:focus {
    border-color: #b89f87; /* Muted brown */
    outline: none;
    box-shadow: 0 0 5px rgba(184, 159, 135, 0.5);
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background-color: #d9c6b0; /* Medium beige */
    color: #4a4238; /* Deep coffee brown */
    padding: 0.5rem;
  }

  td {
    border-bottom: 1px solid #f0e6db; /* Soft beige */
    padding: 0.5rem;
    color: #4a4238;
  }

  td:hover {
    background-color: #f7ece3; /* Subtle beige hover effect */
  }

  .chart-container {
    height: 350px;
    width: 100%;
    background-color: #f8f5f2;
    border-radius: 0.5rem;
    padding: 30px 20px 30px 60px;
    position: relative;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .chart-bar {
    fill: #a88b6f;
  }

  .chart-text {
    font-size: 12px;
    fill: #4a4238;
  }

  .chart-axis {
    stroke: #d9c6b0;
  }

  .legend {
    display: flex;
    justify-content: center;
    margin-top: 10px;
  }

  .legend-item {
    display: flex;
    align-items: center;
    margin-right: 20px;
  }

  .legend-color {
    width: 20px;
    height: 20px;
    margin-right: 5px;
    background-color: #a88b6f;
  }
</style>

<main class="flex h-screen bg-primary overflow-hidden">
  <aside class="w-1/5 sidebar p-4 shadow-lg flex flex-col justify-between rounded-lg overflow-y-auto">
    <div>
      <button on:click={() => (window.location.href = '/dashboard/admin-dashboard')}>
        Sales Report
      </button>
      <button on:click={() => (window.location.href = '/manage-inventory')}>
        Manage Inventory
      </button>
      <button on:click={logout}>
        Logout
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <section class="flex-1 flex flex-col p-4 overflow-y-auto overflow-x-hidden">
    <h2 class="text-2xl font-bold text-primary mb-4">Sales Report</h2>

    <div class="card">
      <h3 class="card-header">Filter Sales</h3>
      <div class="form-group">
        <!-- svelte-ignore a11y_label_has_associated_control -->
        <label class="text-secondary">Select Date Range</label>
        <div class="flex space-x-2">
          <input type="date" bind:value={weekStartDate} on:change={handleDateChange} />
          <input type="date" bind:value={weekEndDate} on:change={handleDateChange} />
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="card-header">Sales Analysis</h3>
      <p class="text-lg text-secondary">Total Revenue: ₱{totalRevenue.toFixed(2)}</p>
      <p class="text-secondary">Average Daily Sales: ₱{averageDailySales.toFixed(2)}</p>
      <p class="text-secondary">Highest Sales Day: {formatDate(highestSalesDay.date)} (₱{highestSalesDay.total_sales.toFixed(2)})</p>
      <p class="text-secondary">Lowest Sales Day: {formatDate(lowestSalesDay.date)} (₱{lowestSalesDay.total_sales.toFixed(2)})</p>
      <p class="text-secondary">Sales Trend: {salesTrend > 0 ? 'Increasing' : salesTrend < 0 ? 'Decreasing' : 'Stable'} (₱{Math.abs(salesTrend).toFixed(2)}/day)</p>
    </div>

    <div class="card">
      <h3 class="card-header">Top Selling Products</h3>
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Total Quantity</th>
            <th>Total Sales</th>
          </tr>
        </thead>
        <tbody>
          {#each topProducts as product}
            <tr>
              <td>{product.name}</td>
              <td>{product.total_quantity}</td>
              <td>₱{product.total_sales.toFixed(2)}</td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3 class="card-header">Sales Chart</h3>
      <div class="chart-container">
        <svg width="100%" height="100%" style="min-width: 600px;">
          <!-- Y-axis -->
          <line x1="40" y1="10" x2="40" y2="250" class="chart-axis" />
          {#each getYAxisLabels(maxSales) as label, i}
            <text x="35" y={250 - i * 60} text-anchor="end" class="chart-text">₱{label.toFixed(0)}</text>
          {/each}

          <!-- X-axis -->
          <line x1="40" y1="250" x2="100%" y2="250" class="chart-axis" />

          <!-- Bars and labels -->
          {#each sortedSales as report, i}
            {@const barHeight = getBarHeight(report.total_sales, 240)}
            {@const barWidth = (100 / sortedSales.length) - 2}
            {@const barX = 40 + (i * (100 / sortedSales.length)) + '%'}
            <rect 
              x={barX} 
              y={250 - barHeight} 
              width={`${barWidth}%`} 
              height={barHeight} 
              class="chart-bar" 
            />
            <text 
              x={`calc(${barX} + ${barWidth/2}%)`} 
              y="285" 
              text-anchor="middle" 
              class="chart-text"
              transform={`rotate(-30, calc(${barX} + ${barWidth/2}%), 285)`}
            >
              {formatShortDate(report.date)}
            </text>
            <text 
              x={`calc(${barX} + ${barWidth/2}%)`} 
              y={235 - barHeight} 
              text-anchor="middle" 
              class="chart-text"
            >
              ₱{report.total_sales.toFixed(0)}
            </text>
          {/each}
        </svg>
      </div>
      <div class="legend">
        <div class="legend-item">
          <div class="legend-color"></div>
          <span>Daily Sales</span>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="card-header">Sales Records</h3>
      {#if filteredSales.length === 0}
        <p class="text-secondary">No sales records found for the selected period.</p>
      {:else}
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Total Sales</th>
              <th>Order Count</th>
            </tr>
          </thead>
          <tbody>
            {#each filteredSales as report}
              <tr>
                <td>{formatDate(report.date)}</td>
                <td>₱{report.total_sales.toFixed(2)}</td>
                <td>{report.order_count}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      {/if}
    </div>
  </section>
</main>

