<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <h1 class="mb-6 text-3xl font-bold text-gray-900">✏️ Editar Jogador</h1>

            <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-6 shadow">
                <!-- Nome -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Nome</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Nome do jogador"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-500">{{ errors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="email@example.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
                </div>

                <!-- Posição -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Posição</label>
                    <select
                        v-model="form.position_id"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="">Selecione uma posição</option>
                        <option v-for="position in positions" :key="position.id" :value="position.id">
                            {{ position.code }} - {{ position.name }}
                        </option>
                    </select>
                    <p v-if="errors.position_id" class="mt-1 text-sm text-red-500">{{ errors.position_id }}</p>
                </div>

                <!-- Ativo -->
                <div class="flex items-center">
                    <input v-model="form.active" type="checkbox" class="h-4 w-4 rounded text-indigo-600 focus:ring-2 focus:ring-indigo-500" />
                    <label class="ml-2 block text-sm text-gray-700">Jogador Ativo</label>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Atualizando...' : 'Atualizar' }}
                    </button>
                    <Link
                        href="/players"
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

const props = defineProps({
    player: Object,
    positions: Array,
});

const form = useForm({
    name: props.player?.name || '',
    email: props.player?.email || '',
    position_id: props.player?.position_id || '',
    active: props.player?.active !== false,
});

const errors = ref({});

const submit = () => {
    form.put(`/players/${props.player.id}`, {
        onError: (err) => (errors.value = err),
    });
};
</script>
