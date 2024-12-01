<script>
    let message = "Welcome to Sugar Cafe ♨️";
</script>

<main class="h-screen flex flex-col items-center justify-center bg-gradient-to-br from-amber-100 via-amber-200 to-stone-200">
    <div class="bg-white bg-opacity-95 rounded-lg p-10 shadow-xl text-center max-w-md transition-transform transform hover:scale-105 duration-300">
        <h1 class="text-5xl font-bold text-stone-800 mb-6">{message}</h1>
        <p class="text-lg text-stone-600 mb-8">Where every sip is a treat! 🍵</p>
        
        <div class="flex flex-col space-y-4">
    <a href="/login" class="bg-brown-500 text-black font-semibold py-3 px-6 rounded-full hover:bg-brown-600 transition duration-300 shadow-lg">
        OPEN
    </a>
</div>

</main>

<style>
    .bg-gradient-to-br {
        background: linear-gradient(to bottom right, #f3e5d7, #e0c6a8);
    }

    .rounded-lg {
        border-radius: 1rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .shadow-xl {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

   .bg-brown-500 {
    background-color: #d7c0aa;
}

.bg-brown-500:hover {
    background-color: #c9a689;
}


</style>
