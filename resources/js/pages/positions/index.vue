<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">📍 Posições</h1>
            <Link href="/positions/create" class="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-700"> + Nova Posição </Link>
        </div>

        <!-- Tabela de Posições -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <table class="w-full">
                <thead class="border-b bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Código</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nome</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Máx. por Equipa</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Ordem</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="position in positions" :key="position.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-800">
                                {{ position.code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ position.name }}</div>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ position.max_per_team }}</td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ position.sort_order }}</td>
                        <td class="space-x-2 px-6 py-4 text-right">
                            <Link :href="`/positions/${position.id}/edit`" class="font-semibold text-indigo-600 hover:text-indigo-900"> Editar </Link>
                            <button @click="deletePosition(position.id)" class="font-semibold text-red-600 hover:text-red-900">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!positions || positions.length === 0" class="py-12 text-center text-gray-500">Nenhuma posição encontrada</div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    positions: Array,
});

const deletePosition = (id) => {
    if (confirm('Tem certeza que deseja eliminar esta posição?')) {
        router.delete(`/positions/${id}`);
    }
};
</script>
