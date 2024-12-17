<script lang="ts">
  import { onMount } from 'svelte';

  interface InventoryItem {
    id: number;
    name: string;
    description: string;
    price: number;
    stock_quantity: number;
    category: string;
  }

  let inventory: InventoryItem[] = [];
  let newItem: InventoryItem = { id: 0, name: '', description: '', price: 0, stock_quantity: 0, category: '' };
  let editingMode = false;
  let currentItem: InventoryItem = { ...newItem };

  onMount(async () => {
    await fetchInventory();
  });

  async function fetchInventory() {
    try {
      const response = await fetch('/api/inventory/manage');
      if (!response.ok) throw new Error('Failed to fetch inventory');
      inventory = await response.json();
    } catch (error) {
      console.error('Error fetching inventory:', error);
      alert('Failed to fetch inventory. Please try again.');
    }
  }

  async function addItem() {
    if (currentItem.name && currentItem.stock_quantity > 0 && currentItem.price > 0) {
      try {
        const response = await fetch('/api/invetory/manage', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(currentItem)
        });
        if (!response.ok) throw new Error('Failed to add item');
        await fetchInventory();
        resetForm();
      } catch (error) {
        console.error('Error adding item:', error);
        alert('Failed to add item. Please try again.');
      }
    } else {
      alert('All fields are required.');
    }
  }

  async function editItem(item: InventoryItem) {
    editingMode = true;
    currentItem = { ...item };
  }

  async function saveItem() {
    try {
      const response = await fetch(`/api/inventory/manage`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(currentItem)
      });
      if (!response.ok) throw new Error('Failed to update item');
      await fetchInventory();
      resetForm();
    } catch (error) {
      console.error('Error updating item:', error);
      alert('Failed to update item. Please try again.');
    }
  }

  async function deleteItem(item: InventoryItem) {
    if (confirm(`Are you sure you want to delete ${item.name}?`)) {
      try {
        const response = await fetch(`/api/invetory/manage`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: item.id })
        });
        if (!response.ok) throw new Error('Failed to delete item');
        await fetchInventory();
      } catch (error) {
        console.error('Error deleting item:', error);
        alert('Failed to delete item. Please try again.');
      }
    }
  }

  function resetForm() {
    editingMode = false;
    currentItem = { id: 0, name: '', description: '', price: 0, stock_quantity: 0, category: '' };
  }
</script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  :global(body) {
    font-family: 'Poppins', sans-serif;
    background-color: #f8e8d4;
    color: #4a4238;
  }

  button {
    transition: transform 0.2s ease, background-color 0.3s ease;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  button:hover {
    transform: scale(1.05);
  }

  .btn-primary {
    background-color: #b89f87;
    color: #ffffff;
  }

  .btn-primary:hover {
    background-color: #b89f87;
  }

  .btn-secondary {
    background-color: #b89f87;
    color: #ffffff;
  }

  .btn-secondary:hover {
    background-color: #a88b6f;
  }

  .btn-danger {
    background-color: #a83232;
    color: #ffffff;
  }

  .btn-danger:hover {
    background-color: #8b2828;
  }

  .form-group {
    margin-bottom: 1rem;
  }

  input {
    background-color: #f8f5f2;
    border: 1px solid #d9c6b0;
    padding: 0.5rem;
    border-radius: 0.5rem;
    color: #4a4238;
  }

  input:focus {
    border-color: #b89f87;
    outline: none;
    box-shadow: 0 0 5px rgba(184, 159, 135, 0.5);
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background-color: #d9c6b0;
    color: #4a4238;
    padding: 0.5rem;
  }

  td {
    border-bottom: 1px solid #f0e6db;
    padding: 0.5rem;
    color: #4a4238;
  }

  td:hover {
    background-color: #f7ece3;
  }

  .card {
    background-color: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
  }

  .sidebar {
    background-color: #d9c6b0;
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
    background-color: #a88b6f;
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

  <section class="flex-1 flex flex-col p-4">
    <h2 class="text-2xl font-bold text-primary mb-4">Manage Inventory</h2>

    <div class="card">
      <h3 class="text-lg font-bold mb-2">{editingMode ? 'Edit Item' : 'Add Item'}</h3>
      <div class="form-group">
        <label for="itemName">Item Name</label>
        <input id="itemName" type="text" bind:value={currentItem.name} placeholder="Enter item name" />
      </div>
      <div class="form-group">
        <label for="itemDescription">Description</label>
        <input id="itemDescription" type="text" bind:value={currentItem.description} placeholder="Enter description" />
      </div>
      <div class="form-group">
        <label for="itemQuantity">Quantity</label>
        <input id="itemQuantity" type="number" bind:value={currentItem.stock_quantity} placeholder="Enter quantity" />
      </div>
      <div class="form-group">
        <label for="itemPrice">Price</label>
        <input id="itemPrice" type="number" bind:value={currentItem.price} placeholder="Enter price" />
      </div>
      <div class="form-group">
        <label for="itemCategory">Category</label>
        <input id="itemCategory" type="text" bind:value={currentItem.category} placeholder="Enter category" />
      </div>
      <button class={editingMode ? 'btn-secondary' : 'btn-primary'} on:click={editingMode ? saveItem : addItem}>
        {editingMode ? 'Save Changes' : 'Add Item'}
      </button>
    </div>

    <div class="card mt-4">
      {#if inventory.length === 0}
        <p>No inventory items available.</p>
      {:else}
        <table>
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Category</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {#each inventory as item}
              <tr>
                <td>{item.name}</td>
                <td>{item.description}</td>
                <td>{item.stock_quantity}</td>
                <td>₱{item.price.toFixed(2)}</td>
                <td>{item.category}</td>
                <td>
                  <button class="btn-secondary px-2 py-1" on:click={() => editItem(item)}>Edit</button>
                  <button class="btn-danger px-2 py-1" on:click={() => deleteItem(item)}>Delete</button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      {/if}
    </div>
  </section>
</main>

