<?php

declare(strict_types=1);
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Login
        </h1>

        <?php if(!empty($error)): ?>
        <div 
            id="error-message" 
            class="bg-red-100 text-red-700 p-3 rounded mb-4 transition-opacity duration-500 ease-out opacity-100"
            >
                <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-5">

        <?= $csrf->hiddenInput() ?>

            <!-- Username -->
            <div>
                <label
                    for="username"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500"
                >
            </div>

            <!-- Password -->
            <div>
                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500"
                >
            </div>

            <!-- Login button -->
            <button
                type="submit"
                class="w-full py-3 px-4 bg-blue-600 text-white font-semibold
                       rounded-lg hover:bg-blue-700
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       transition"
            >
                Login
            </button>

        </form>

    </div>

</div>
