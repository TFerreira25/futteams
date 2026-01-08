<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <h1 class="mb-6 text-3xl font-bold text-gray-900">➕ Novo Jogo</h1>

            <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-6 shadow">
                <!-- Data -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Data do Jogo</label>
                    <input
                        v-model="form.date"
                        type="datetime-local"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p v-if="errors.date" class="mt-1 text-sm text-red-500">{{ errors.date }}</p>
                </div>

                <!-- Número de Jogadores -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Número de Jogadores</label>
                    <input
                        v-model.number="form.total_players"
                        type="number"
                        min="4"
                        step="2"
                        placeholder="8, 10, 12..."
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p class="mt-1 text-sm text-gray-500">Deve ser um número par e mínimo 4</p>
                    <p v-if="errors.total_players" class="mt-1 text-sm text-red-500">{{ errors.total_players }}</p>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Criando...' : 'Criar Jogo' }}
                    </button>
                    <Link
                        href="/games"
                        class="flex-1 rounded-lg bg-gray-300 px-4 py-2 text-center font-bold text-gray-900 transition hover:bg-gray-400"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    date: new Date().toISOString().slice(0, 16),
    total_players: 10,
});

const errors = ref({});

const submit = () => {
    form.post('/games', {
        onError: (err) => (errors.value = err),
    });
};
</script>
