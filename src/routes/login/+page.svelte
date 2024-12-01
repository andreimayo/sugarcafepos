<script>
  // @ts-nocheck

  // Navigate to admin or cashier login pages
  const navigateToAdminLogin = () => {
    window.location.href = '/login/admin-login';
  };

  const navigateToCashierLogin = () => {
    window.location.href = '/login/cashier-login';
  };

  // Navigate to the routes page
  const goBack = () => {
    window.location.href = '/';
  };
</script>

<style>
  /* Container styling */
  .account-selection {
    background: linear-gradient(to bottom right, #f3e5d7, #e0c6a8);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    color: #4a4a4a;
    position: relative;
    padding: 20px;
  }

  /* Back button styling */
  .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: #f2d5c9;
    color: #6d4c41;
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background-color 0.3s, transform 0.2s;
  }

  .back-button:hover {
    background-color: #e5bfa7;
    transform: scale(1.05);
  }

  /* Card styling */
  .account-card {
    background-color: #ffffff;
    border: 2px solid #d7c0aa;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
    border-radius: 16px;
    padding: 40px 20px;
    text-align: center;
    width: 280px;
    margin: 10px;
    transition: transform 0.3s ease-in-out;
  }

  .account-card:hover {
    transform: scale(1.1);
    border-color: #c9a689;
  }

  /* Icon styling */
  .icon {
    font-size: 3rem;
    color: #795548;
    margin-bottom: 15px;
  }

  /* Headings */
  .account-type {
    font-size: 1.5rem;
    font-weight: bold;
    color: #6d4c41;
    margin-bottom: 15px;
  }

  /* Button styling */
  .select-button {
    background-color: #d7c0aa;
    color: #4a4a4a;
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
  }

  .select-button:hover {
    background-color: #c9a689;
    transform: scale(1.05);
  }
</style>

<div class="account-selection">
  <!-- Back Button -->
  <button class="back-button" on:click={goBack}>
    <!-- Arrow Icon (SVG) -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"></path>
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 5l-7 7 7 7"></path>
    </svg>
    Back
  </button>

  <!-- Admin Card -->
  <div class="account-card">
    <!-- Head Icon for Admin -->
    <div class="icon">👤</div>
    <div class="account-type">Admin</div>
    <button class="select-button" on:click={navigateToAdminLogin}>Login</button>
  </div>

  <!-- Cashier Card -->
  <div class="account-card">
    <!-- Head Icon for Cashier -->
    <div class="icon">👤</div>
    <div class="account-type">Cashier</div>
    <button class="select-button" on:click={navigateToCashierLogin}>Login</button>
  </div>
</div>
