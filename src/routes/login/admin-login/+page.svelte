<script lang="ts">
    import { onMount } from 'svelte';

    let password = '';
    let error = '';
    let showChangePassword = false;
    let currentPassword = '';
    let newPassword = '';
    let confirmPassword = '';

    // Update API URL to use the correct endpoint
    const apiUrl = 'http://localhost/sugarcafepos/api/auth/login';

    async function login(event: { preventDefault: () => void; }) {
        event.preventDefault();
        error = '';

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: 'login',
                    username: 'admin',
                    password: password,
                    role: 'admin'
                }),
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server response was not JSON');
            }

            const data = await response.json();

            if (data.success) {
                localStorage.setItem('user', JSON.stringify(data.data));
                window.location.href = '/dashboard/admin-dashboard';
            } else {
                error = data.message || 'Login failed. Please try again.';
            }
        } catch (err) {
            console.error('Login error:', err);
            error = 'An error occurred. Please try again later.';
        }
    }

    async function changePassword(event: { preventDefault: () => void; }) {
        event.preventDefault();
        error = '';

        if (newPassword !== confirmPassword) {
            error = 'New passwords do not match.';
            return;
        }

        try {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            
            if (!user.id) {
                throw new Error('User not logged in');
            }

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: 'change_password',
                    userId: user.id,
                    currentPassword: currentPassword,
                    newPassword: newPassword,
                }),
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server response was not JSON');
            }

            const data = await response.json();

            if (data.success) {
                alert('Password changed successfully.');
                showChangePassword = false;
                currentPassword = '';
                newPassword = '';
                confirmPassword = '';
            } else {
                error = data.message || 'Failed to change password. Please try again.';
            }
        } catch (err) {
            console.error('Change password error:', err);
            error = 'An error occurred. Please try again later.';
        }
    }

    const goBack = () => {
        window.location.href = '/login';
    };
</script>

<main class="h-screen flex items-center justify-center bg-gradient-to-br from-cream-100 via-cream-200 to-cream-300">
    <div class="relative w-full max-w-sm bg-white bg-opacity-95 p-8 rounded-lg shadow-lg transition-transform transform hover:scale-105 duration-300">
        <!-- svelte-ignore a11y_consider_explicit_label -->
        <button
            class="absolute top-4 left-4 bg-transparent text-brown-600 border-none px-3 py-1 rounded-lg hover:bg-brown-200 transition duration-300"
            on:click={goBack}
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5l-7 7 7 7"></path>
            </svg>
        </button>

        <div class="flex justify-center mb-6">
            <img src="/images/profile.png" alt="Profile Icon" class="w-24 h-24 rounded-full border-4 border-brown-200" />
        </div>
        
        <h1 class="text-3xl font-bold text-center text-brown-700 mb-6">Admin Login</h1>
        
        {#if !showChangePassword}
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
            <button
                class="w-full mt-4 bg-brown-200 text-brown-700 py-2 rounded-lg font-semibold hover:bg-brown-300 transition duration-300 shadow-md"
                on:click={() => showChangePassword = true}
            >
                Change Password
            </button>
        {:else}
            <form on:submit|preventDefault={changePassword}>
                {#if error}
                    <p class="text-red-500 text-sm mb-4">{error}</p>
                {/if}
                <div class="mb-4">
                    <label class="block text-brown-600 text-sm font-bold mb-2" for="currentPassword">Current Password</label>
                    <input
                        class="w-full px-3 py-2 text-brown-800 bg-brown-100 border border-brown-300 rounded-lg focus:outline-none focus:border-brown-500"
                        type="password"
                        id="currentPassword"
                        bind:value={currentPassword}
                        placeholder="Enter current password"
                    />
                </div>
                <div class="mb-4">
                    <label class="block text-brown-600 text-sm font-bold mb-2" for="newPassword">New Password</label>
                    <input
                        class="w-full px-3 py-2 text-brown-800 bg-brown-100 border border-brown-300 rounded-lg focus:outline-none focus:border-brown-500"
                        type="password"
                        id="newPassword"
                        bind:value={newPassword}
                        placeholder="Enter new password"
                    />
                </div>
                <div class="mb-4">
                    <label class="block text-brown-600 text-sm font-bold mb-2" for="confirmPassword">Confirm New Password</label>
                    <input
                        class="w-full px-3 py-2 text-brown-800 bg-brown-100 border border-brown-300 rounded-lg focus:outline-none focus:border-brown-500"
                        type="password"
                        id="confirmPassword"
                        bind:value={confirmPassword}
                        placeholder="Confirm new password"
                    />
                </div>
                <button
                    class="w-full bg-brown-600 text-white py-2 rounded-lg font-semibold hover:bg-brown-700 transition duration-300 shadow-md"
                    type="submit"
                >
                    Change Password
                </button>
            </form>
            <button
                class="w-full mt-4 bg-brown-200 text-brown-700 py-2 rounded-lg font-semibold hover:bg-brown-300 transition duration-300 shadow-md"
                on:click={() => showChangePassword = false}
            >
                Back to Login
            </button>
        {/if}
    </div>
</main>

<style>
    .bg-gradient-to-br {
        background: linear-gradient(to bottom right, #f5e8dc, #f3dfd1, #ebd4bf);
    }

    .rounded-lg {
        border-radius: 1rem;
    }

    .shadow-lg {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    }

    .bg-brown-600 {
        background-color: #8d6e63;
    }

    .bg-brown-100 {
        background-color: #f3e5dc;
    }

    .border-brown-300 {
        border-color: #d2bba5;
    }

    .text-brown-600 {
        color: #8d6e63;
    }

    .text-brown-700 {
        color: #70574e;
    }

    .hover\:bg-brown-700:hover {
        background-color: #70574e;
    }

    .hover\:bg-brown-200:hover {
        background-color: #f5d5c9;
    }

    .bg-brown-200 {
        background-color: #f5d5c9;
    }

    .hover\:bg-brown-300:hover {
        background-color: #e8c5b5;
    }
</style>