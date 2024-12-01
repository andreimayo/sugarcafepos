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
  }

  .sidebar button {
    background-color: #f8e8d4;
    color: #ffffff;
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    width: 100%;
  }

  .sidebar button:hover {
    background-color: #a88b6f; /* Darker muted brown on hover */
  }

  .card {
    background-color: #ffffff; /* White for cards */
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
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
</style>

<main class="flex h-screen bg-primary">
  <aside class="w-1/5 sidebar p-4 shadow-lg flex flex-col justify-between rounded-lg">
    <div>
      <button class="btn-primary" on:click={() => (window.location.href = '/dashboard/admin-dashboard')}>
        Sales Report
      </button>
      <button class="btn-secondary mt-2" on:click={() => (window.location.href = '/manage-inventory')}>
        Manage Inventory
      </button>
      <button class="btn-danger mt-2" on:click={() => (window.location.href = '/login')}>
        Logout
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <section class="flex-1 flex flex-col p-4">
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
      <p class="mt-4 text-lg text-secondary">Total Sales: ₱{totalFilteredSales.toFixed(2)}</p>
    </div>

    <div class="card mt-8">
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
                <td>{report.date}</td>
                <td>₱{report.totalSales.toFixed(2)}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      {/if}
    </div>
  </section>
</main>
