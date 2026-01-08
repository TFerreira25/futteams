<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">⚽ FutTeams</h1>
                    <nav class="flex gap-4">
                        <Link href="/" class="text-gray-600 hover:text-gray-900">Dashboard</Link>
                        <Link href="/players" class="text-gray-600 hover:text-gray-900">Jogadores</Link>
                        <Link href="/positions" class="text-gray-600 hover:text-gray-900">Posições</Link>
                        <Link href="/games" class="text-gray-600 hover:text-gray-900">Jogos</Link>
                        <Link href="/rankings" class="text-gray-600 hover:text-gray-900">Rankings</Link>
                    </nav>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
