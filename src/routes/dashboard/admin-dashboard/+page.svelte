<script lang="ts">
  interface SalesReport {
    date: string; // Format: YYYY-MM-DD
    totalSales: number;
  }

  let salesReports: SalesReport[] = [
    { date: '2024-11-19', totalSales: 2000.0 },
    { date: '2024-11-18', totalSales: 1500.5 },
    { date: '2024-11-15', totalSales: 1800.75 },
    { date: '2024-11-10', totalSales: 2200.0 },
    { date: '2024-10-25', totalSales: 3000.0 },
  ];

  let weekStartDate: string = '';
  let weekEndDate: string = '';
  let selectedMonth: string = '';

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

  $: filteredSales = salesReports.filter((report) => {
    if (weekStartDate && weekEndDate) {
      const start = new Date(weekStartDate);
      const end = new Date(weekEndDate);
      const reportDate = new Date(report.date);
      return reportDate >= start && reportDate <= end;
    } else if (selectedMonth) {
      const [year, month] = selectedMonth.split('-').map(Number);
      const reportDate = new Date(report.date);
      return (
        reportDate.getFullYear() === year &&
        reportDate.getMonth() + 1 === month
      );
    }
    return true;
  });

  $: totalFilteredSales = filteredSales.reduce(
    (total, report) => total + report.totalSales,
    0
  );

  $: averageDailySales = filteredSales.length > 0
    ? totalFilteredSales / filteredSales.length
    : 0;

  $: highestSalesDay = filteredSales.reduce(
    (max, report) => (report.totalSales > max.totalSales ? report : max),
    { date: '', totalSales: 0 }
  );

  $: lowestSalesDay = filteredSales.reduce(
    (min, report) => (report.totalSales < min.totalSales ? report : min),
    { date: '', totalSales: Infinity }
  );

  $: salesTrend = filteredSales.length > 1
    ? (filteredSales[filteredSales.length - 1].totalSales - filteredSales[0].totalSales) /
      (filteredSales.length - 1)
    : 0;

  $: maxSales = Math.max(...filteredSales.map(report => report.totalSales));
  $: minSales = Math.min(...filteredSales.map(report => report.totalSales));

  $: sortedSales = [...filteredSales].sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());

  function getBarHeight(sales: number, maxHeight: number): number {
    return (sales / maxSales) * maxHeight;
  }

  function getYAxisLabels(maxSales: number): number[] {
    const step = maxSales / 4;
    return [0, step, step * 2, step * 3, maxSales];
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
        <label class="text-secondary">Select Week</label>
        <div class="flex space-x-2">
          <input type="date" bind:value={weekStartDate} />
          <input type="date" bind:value={weekEndDate} />
        </div>
      </div>
      <div class="form-group">
        <!-- svelte-ignore a11y_label_has_associated_control -->
        <label class="text-secondary">Select Month</label>
        <input type="month" bind:value={selectedMonth} />
      </div>
    </div>

    <div class="card">
      <h3 class="card-header">Sales Analysis</h3>
      <p class="text-lg text-secondary">Total Sales: ₱{totalFilteredSales.toFixed(2)}</p>
      <p class="text-secondary">Average Daily Sales: ₱{averageDailySales.toFixed(2)}</p>
      <p class="text-secondary">Highest Sales Day: {formatDate(highestSalesDay.date)} (₱{highestSalesDay.totalSales.toFixed(2)})</p>
      <p class="text-secondary">Lowest Sales Day: {formatDate(lowestSalesDay.date)} (₱{lowestSalesDay.totalSales.toFixed(2)})</p>
      <p class="text-secondary">Sales Trend: {salesTrend > 0 ? 'Increasing' : salesTrend < 0 ? 'Decreasing' : 'Stable'} (₱{Math.abs(salesTrend).toFixed(2)}/day)</p>
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
            {@const barHeight = getBarHeight(report.totalSales, 240)}
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
              ₱{report.totalSales.toFixed(0)}
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
            </tr>
          </thead>
          <tbody>
            {#each filteredSales as report}
              <tr>
                <td>{formatDate(report.date)}</td>
                <td>₱{report.totalSales.toFixed(2)}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      {/if}
    </div>
  </section>
</main>

