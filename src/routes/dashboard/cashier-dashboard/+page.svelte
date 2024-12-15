<script lang="ts">
  interface MenuItem {
    id: number;
    name: string;
    price: number;
    img: string;
    category: string;
  }

  interface OrderItem extends MenuItem {
    quantity: number;
    size: string;
    sizePrice: number;
  }

  const menuItems: MenuItem[] = [
  { id: 1, name: 'Salted Caramel', price: 39, img: '/icedcoffee/saltedcaramel.png', category: 'Coffee' },
  { id: 1, name: 'Spanish Latte', price: 39, img: '/icedcoffee/spanishlatte.png', category: 'Coffee' },
  { id: 1, name: 'Hazelnut', price: 39, img: '/icedcoffee/hazelnut.png', category: 'Coffee' },
  { id: 1, name: 'French Vanilla', price: 39, img: '/icedcoffee/frenchvanilla.png', category: 'Coffee' },
  { id: 1, name: 'Iced Americano', price: 39, img: '/icedcoffee/icedamericano.png', category: 'Coffee' },
  { id: 1, name: 'Caramel Macchiato', price: 49, img: '/icedcoffee/caramelmacchaito.png', category: 'Coffee' },
  { id: 1, name: 'Cappucino CheeseCake', price: 49, img: '/icedcoffee/capuccinocheesecake.png', category: 'Coffee' },
  { id: 1, name: 'Coffee Jelly', price: 49, img: '/images/espresso.png', category: 'Coffee' },
  { id: 1, name: 'Dirty Matcha', price: 49, img: '/images/espresso.png', category: 'Coffee' },
  { id: 1, name: 'Matcha Latte', price: 49, img: '/images/espresso.png', category: 'Coffee' },
  { id: 2, name: 'Salted Caramel', price: 49, img: '/hotcoffee/saltedcaramel.png', category: 'Hot Coffee' },
  { id: 2, name: 'Cappucino', price: 49, img: '/hotcoffee/capuccino.png', category: 'Hot Coffee' },
  { id: 2, name: 'Caramel Macchiato', price: 49, img: '/hotcoffee/caramelmacchiato.png', category: 'Hot Coffee' },
  { id: 2, name: 'French Vanilla', price: 49, img: '/hotcoffee/frenchvanilla.png', category: 'Hot Coffee' },
  { id: 2, name: 'Hazelnut', price: 49, img: '/hotcoffee/hazelnut.png', category: 'Hot Coffee' },
  { id: 2, name: 'Hot Choco', price: 49, img: '/hotcoffee/hotchoco.png', category: 'Hot Coffee' },
  { id: 2, name: 'Matcha Latte', price: 49, img: '/hotcoffee/matchalatte.png', category: 'Hot Coffee' },
  { id: 2, name: 'Roasted Coffee', price: 49, img: '/hotcoffee/roastedcoffee.png', category: 'Hot Coffee' },
  { id: 3, name: 'Chocolate', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Red Velvet', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Matcha', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Okinawa', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Wintermelon', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Cheesecake', price: 49, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Oreo Cheesecake', price: 49, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Red Velvet Cheesecake', price: 49, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Oreo Match', price: 49, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Choco Cheesecake', price: 49, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Mango Milk Tea', price: 39, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Mango Cheesecake', price: 59, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 3, name: 'Mango Graham Cheesecake', price: 69, img: '/images/milk-tea.png', category: 'Milk Tea' },
  { id: 4, name: 'Apple Green', price: 39, img: '/images/yogurt.png', category: 'Yogurt' },
  { id: 4, name: 'Lychee', price: 39, img: '/images/yogurt.png', category: 'Yogurt' },
  { id: 4, name: 'Blue Lemonaide', price: 39, img: '/images/yogurt.png', category: 'Yogurt' },
  { id: 4, name: 'Four Season', price: 39, img: '/images/yogurt.png', category: 'Yogurt' },
  { id: 4, name: 'Blue Berry', price: 39, img: '/images/yogurt.png', category: 'Yogurt' },
  { id: 5, name: 'Passion Fruit', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Four Season', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Kiwi', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Lychee', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Strawberry', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Greem Apple', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Blue Berry', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 5, name: 'Orange', price: 39, img: '/images/yogurt.png', category: 'Fruit Tea' },
  { id: 6, name: 'Siomai Rice', price: 40, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Spam Silog', price: 65, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Hungarian Silog', price: 75, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Tonkatsu Rice', price: 65, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Long Silog', price: 65, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Pancit Canton w/ Rice', price: 45, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Crispy Chicken Sandwich', price: 65, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Cheese Burger w/ Egg', price: 55, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Cheese Burger', price: 35, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Buy1Take1 Burger', price: 65, img: '/images/sandwich.png', category: 'Food' },
  { id: 6, name: 'Pancit Canton', price: 30, img: '/images/sandwich.png', category: 'Food' }
];

  let selectedCategory = "Coffee";
  let orders: OrderItem[] = [];
  let totalCost = 0;
  let customerPayment = 0;
  let change = 0;


  function getFilteredMenuItems() {
    return menuItems.filter(item => item.category === selectedCategory);
  }

  function addOrder(item: MenuItem, size: string) {
    const sizePrice = size === "Large" ? item.price + 10 : item.price;

    const existingOrder = orders.find(order => order.id === item.id && order.size === size);

    if (existingOrder) {
      existingOrder.quantity += 1;
      orders = [...orders];
    } else {
      orders = [...orders, { ...item, quantity: 1, size, sizePrice }];
    }
    updateTotalCost();
  }

  function incrementQuantity(id: number, size: string) {
    orders = orders.map(order => 
      order.id === id && order.size === size ? { ...order, quantity: order.quantity + 1 } : order
    );
    updateTotalCost();
  }

  function decrementQuantity(id: number, size: string) {
    orders = orders.map(order => 
      order.id === id && order.size === size && order.quantity > 1 ? { ...order, quantity: order.quantity - 1 } : order
    ).filter(order => order.quantity > 0);
    updateTotalCost();
  }

  function removeOrder(order: OrderItem) {
    orders = orders.filter(o => o !== order);
    updateTotalCost();
  }

  function updateTotalCost() {
    totalCost = orders.reduce((sum, order) => sum + (order.sizePrice * order.quantity), 0);
    calculateChange();
  }

  function calculateChange() {
    change = customerPayment >= totalCost ? customerPayment - totalCost : 0;
  }

  

</script>

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

  .bg-secondary {
    background-color: #d9c6b0; /* Light beige */
  }
  .button-primary {
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

  button:hover {
    background-color: #a88b6f; /* Darker muted brown */
  }
  .btn-danger {
    background-color: #a83232;
    color: #ffffff;
  }

  .btn-danger:hover {
    background-color: #8b2828;
  }

  h3 {
    font-size: 1.25rem;
  }

  p {
    font-size: 0.875rem;
  }

  button {
    transition: transform 0.2s;
  }

  button:hover {
    transform: scale(1.05);
  }
</style>

<main class="flex h-screen bg-primary text primary">
  <!-- Sidebar -->
  <aside class="w-1/5 sidebar p-4 shadow-lg flex flex-col justify-between rounded-lg">
    <div>
      <select
        class="w-full p-2 mb-4  rounded-lg focus:outline-none"
        bind:value={selectedCategory}>
        <option value="Coffee">Iced Coffee</option>
        <option value="Hot Coffee">Hot Coffee</option>
        <option value="Milk Tea">Milk Tea</option>
        <option value="Fruit Tea">Fruit Tea</option>
        <option value="Yogurt">Fruit Yogurt</option>
        <option value="Food">Food</option>
      </select>
      <button
      class="w-full mb-2  py-2 rounded-lg "
      on:click={() => window.location.href = '/view-orders'}>
      View Orders
    </button>    
      <button
        class="w-full  py-2 rounded-lg "
        on:click={() =>  window.location.href = '/login'}>
        Logout
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <section class="flex-1 flex flex-col p-4">
    <div class="flex-1 overflow-y-auto grid grid-cols-2 gap-4">
      {#each getFilteredMenuItems() as item}
        <div class="sidebar p-4 rounded-lg shadow hover:shadow-md transition">
          <img src={item.img} alt={item.name} class="h-50 w-60 mx-auto mb-2 rounded " />
          <h3 class="text-lg font-semibold">{item.name}</h3>
          <p class="text-primary">₱{item.price.toFixed(2)}</p>
          {#if item.category !== 'Food'}
            <div class="text-sm text-primary">
              Medium: ₱{item.price.toFixed(2)}, Large: ₱{(item.price + 10).toFixed(2)}
            </div>
            <div class="mt-2">
              <button
                class="w-full  text-white py-1 rounded-lg  mb-2"
                on:click={() => addOrder(item, 'Medium')}>
                Medium
              </button>
              <button
                class="w-full  text-white py-1 rounded-lg "
                on:click={() => addOrder(item, 'Large')}>
                Large
              </button>
            </div>
          {:else}
            <button
              class="w-full  text-white py-2 rounded-lg  mt-2"
              on:click={() => addOrder(item, 'Default')}>
              Add to Order
            </button>
          {/if}
        </div>
      {/each}
    </div>

    <!-- Order Summary -->
    <div class="mt-4 p-4 bg-secondary rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
      {#if orders.length === 0}
        <p class="text-primary">No items added to the order.</p>
      {:else}
        <ul>
          {#each orders as order}
            <li class="flex justify-between items-center mb-2">
              <span>{order.name} ({order.size}) x {order.quantity}</span>
              <div>
                <button class="bg-green-600 px-2 py-1 rounded-lg text-white mr-2" on:click={() => incrementQuantity(order.id, order.size)}>+</button>
                <button class="bg-yellow-600 px-2 py-1 rounded-lg text-white mr-2" on:click={() => decrementQuantity(order.id, order.size)}>-</button>
                <button class="btn-danger px-2 py-1 rounded-lg text-white " on:click={() => removeOrder(order)}>Remove</button>
              </div>
            </li>
          {/each}
        </ul>
        <p class="mt-4">Total: ₱{totalCost.toFixed(2)}</p>
        <div class="mt-4">
          <label for="payment" class="block text-sm">Customer Payment:</label>
          <input
            id="payment"
            type="number"
            class="w-full p-2 bg-primary rounded-lg"
            bind:value={customerPayment}
            on:input={calculateChange}
            placeholder="Enter payment amount"
          />
          <p class="mt-2">Change: ₱{change.toFixed(2)}</p>
        </div>
        <button
          class="w-full mt-4 button-primary py-2 rounded-lg button:hover"
          on:click={placeOrder}>
          Place Order
        </button>
      {/if}
    </div>
  </section>
</main>

