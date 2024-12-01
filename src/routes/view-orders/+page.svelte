<script lang="ts">
  interface OrderItem {
    id: number;
    name: string;
    size: string;
    sizePrice: number;
    quantity: number;
    totalPrice: number;
  }

  interface SalesReport {
    date: string;
    orders: OrderItem[];
    totalSales: number;
  }

  // Example orders for testing
  let orders: OrderItem[] = [
    { id: 1, name: 'Salted Caramel', size: 'Medium', sizePrice: 39, quantity: 2, totalPrice: 78 },
    { id: 2, name: 'Spanish Latte', size: 'Large', sizePrice: 49, quantity: 1, totalPrice: 49 },
  ];

  // Array to store completed orders for the Sales Report
  let salesReport: SalesReport[] = [];

  // Function to calculate the total sales of the completed orders
  function calculateTotalSales(orders: OrderItem[]): number {
    return orders.reduce((total, order) => total + order.totalPrice, 0);
  }

  function editOrder(order: OrderItem) {
    alert(`Edit order: ${order.name}`);
  }

  function deleteOrder(order: OrderItem) {
    orders = orders.filter(o => o !== order);
  }

  function completeOrder(order: OrderItem) {
    // Move the order to salesReport
    salesReport = [...salesReport, {
      date: new Date().toLocaleDateString(),  // Current date
      orders: [order],
      totalSales: order.totalPrice
    }];
    
    // Remove the order from the current orders list
    orders = orders.filter(o => o !== order);
  }

  function submitSalesReport() {
    if (salesReport.length === 0) {
      alert('No completed orders to submit.');
      return;
    }

    // Log or submit sales report
    const report = {
      date: new Date().toLocaleDateString(),
      totalSales: calculateTotalSales(salesReport.flatMap(report => report.orders)),
    };

    console.log('Sales Report Submitted:', report);
    alert('Sales report submitted successfully.');
  }
</script>

<!-- svelte-ignore css_unused_selector -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  :global(body) {
    font-family: 'Poppins', sans-serif;
    background-color: #f5f3ef; /* Light beige */
    color: #4a4238; /* Deep coffee brown */
  }

  button {
    transition: transform 0.2s;
  }

  button:hover {
    transform: scale(1.05);
  }

  .bg-primary {
    background-color: #f8e8d4; /* Light beige */
  }

  .text-primary {
    color: #4a4238; /* Deep coffee brown */
  }

  .sidebar {
    background-color: #d9c6b0; /* Medium beige */
    color: #4a4238;
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
    background-color: #a88b6f; /* Darker muted brown */
  }

  .card {
    background-color: #ffffff; /* White for cards */
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
  }

  .card-header {
    color: #4a4238;
    font-weight: bold;
    margin-bottom: 1rem;
  }

  .form-group {
    margin-bottom: 1rem;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background-color: #d9c6b0; /* Medium beige */
    color: #4a4238;
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

  .button-primary {
    background-color: #b89f87;
    color: #4a4238;
  }

  .button-danger {
    background-color: #f8e8d4;
    color: #ffffff;
    border-radius: 0.5rem;
  }

  .button-danger:hover {
    background-color: #a88b6f; /* Darker muted brown */
  }
</style>

<main class="flex h-screen bg-primary">
  <aside class="w-1/5 sidebar p-4 shadow-lg flex flex-col justify-between rounded-lg">
    <div>
      <button
        class="w-full bg-primary py-2 rounded-lg hover:bg-gray-300"
        on:click={() => (window.location.href = '/dashboard/cashier-dashboard')}>
         Make Another Order
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <section class="flex-1 flex flex-col p-4">
    <h2 class="text-2xl font-bold text-primary mb-4">View Orders</h2>
    <div class="card">
      {#if orders.length === 0}
        <p class="text-secondary">No orders available.</p>
      {:else}
        <table>
          <thead>
            <tr class="text-secondary">
              <th>Name</th>
              <th>Size</th>
              <th>Quantity</th>
              <th>Total Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {#each orders as order}
              <tr class="border-b border-gray-300">
                <td>{order.name}</td>
                <td>{order.size}</td>
                <td>{order.quantity}</td>
                <td>₱{order.totalPrice.toFixed(2)}</td>
                <td class="space-x-2">
                  <button
                    class="button-primary px-2 py-1 rounded-lg text-white"
                    on:click={() => editOrder(order)}>
                    Edit
                  </button>
                  <button
                    class="bg-red-600 px-2 py-1 rounded-lg text-white"
                    on:click={() => deleteOrder(order)}>
                    Delete
                  </button>
                  <button
                    class="bg-green-600 px-2 py-1 rounded-lg text-white"
                    on:click={() => completeOrder(order)}>
                    Complete
                  </button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      {/if}
    </div>

    <!-- Sales Report Section -->
    <section class="mt-8">
      <h2 class="text-2xl font-bold text-primary mb-4">Sales Report</h2>
      <div class="card">
        {#if salesReport.length === 0}
          <p class="text-secondary">No completed orders yet.</p>
        {:else}
          <table>
            <thead>
              <tr class="text-secondary">
                <th>Date</th>
                <th>Name</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Total Price</th>
              </tr>
            </thead>
            <tbody>
              {#each salesReport as saleReport}
                {#each saleReport.orders as sale}
                  <tr class="border-b border-gray-300">
                    <td>{saleReport.date}</td>
                    <td>{sale.name}</td>
                    <td>{sale.size}</td>
                    <td>{sale.quantity}</td>
                    <td>₱{sale.totalPrice.toFixed(2)}</td>
                  </tr>
                {/each}
              {/each}
            </tbody>
          </table>
        {/if}
      </div>
    </section>

    <!-- Total Sales & Submit -->
    <section class="mt-4">
      <div class="card">
        <p>Total Sales Today: ₱{calculateTotalSales(salesReport.flatMap(report => report.orders)).toFixed(2)}</p>
        <button
          class="w-full button-primary py-2 rounded-lg "
          on:click={submitSalesReport}>
          Submit Sales Report
        </button>
      </div>
    </section>
  </section>
</main>
