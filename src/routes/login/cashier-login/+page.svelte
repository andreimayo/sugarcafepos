<script>
    let password = '';
    let error = '';

    const login = () => {
        if (password === '') {
            error = 'Password is required!';
            return;
        }

        if (password === 'sugarcafe') {
            error = ''; // Clear error on successful login
            window.location.href = '/dashboard/cashier-dashboard'; // Redirect to cashier dashboard
        } else {
            error = 'Incorrect password. Please try again.';
        }
    };

    const goBack = () => {
        window.location.href = '/login'; // Navigate back to login route
    };
</script>

<main class="h-screen flex items-center justify-center bg-gradient-to-br from-cream-100 via-cream-200 to-cream-300">
    <div class="relative w-full max-w-sm bg-white bg-opacity-95 p-8 rounded-lg shadow-lg transition-transform transform hover:scale-105 duration-300">
        <!-- Back Button -->
        <button
            class="absolute top-4 left-4 bg-transparent text-brown-600 px-3 py-1 rounded-lg hover:bg-brown-200 transition duration-300"
            on:click={goBack}
        >
            <!-- Arrow Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5l-7 7 7 7"></path>
            </svg>
        </button>

        <!-- Profile Icon -->
        <div class="flex justify-center mb-6">
            <img src="/images/profile.png" alt="Profile Icon" class="w-24 h-24 rounded-full border-4 border-brown-200" />
        </div>
        
        <h1 class="text-3xl font-bold text-center text-brown-700 mb-6">Cashier</h1>
        
        <form on:submit|preventDefault={login}>
            {#if error}
                <p class="text-red-500 text-sm mb-4">{error}</p>
            {/if}
            <div class="mb-4">
                <label class="block text-brown-600 text-sm font-bold mb-2" for="password">Password</label>
                <input
                    class="w-full px-3 py-2 text-brown-800 bg-brown-100 border border-brown-300 rounded-lg focus:outline-none focus:border-brown-500"
                    type="password"
                    id="password"
                    bind:value={password}
                    placeholder="Enter password"
                />
            </div>
            <button
                class="w-full bg-brown-600 text-white py-2 rounded-lg font-semibold hover:bg-brown-700 transition duration-300 shadow-md"
                type="submit"
            >
                Login
            </button>
        </form>
    </div>
</main>

<style>
    /* Gradient Background */
    .bg-gradient-to-br {
        background: linear-gradient(to bottom right, #f5e8dc, #f3dfd1, #ebd4bf);
    }

    /* Rounded Corners */
    .rounded-lg {
        border-radius: 1rem;
    }

    /* Shadow Effect */
    .shadow-lg {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    }

    /* Brown and Cream Palette */
    .bg-brown-600 {
        background-color: #8d6e63; /* Medium brown */
    }

    .bg-brown-100 {
        background-color: #f3e5dc; /* Light cream */
    }

    .border-brown-300 {
        border-color: #d2bba5; /* Soft brown */
    }

    .text-brown-600 {
        color: #8d6e63;
    }

    .text-brown-700 {
        color: #70574e;
    }

    /* Hover States */
    .hover\:bg-brown-700:hover {
        background-color: #70574e;
    }

    .hover\:bg-brown-200:hover {
        background-color: #f5d5c9;
    }
</style>
