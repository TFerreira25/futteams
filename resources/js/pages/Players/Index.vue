<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">👥 Jogadores ({{ filteredPlayers.length }})</h1>
            <Link href="/players/create" class="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-700"> + Novo Jogador </Link>
        </div>

        <!-- Filtros -->
        <div class="mb-6 rounded-lg bg-white p-4 shadow">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Pesquisar jogador..."
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                />
                <select
                    v-model="selectedPosition"
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option value="">Todas as Posições</option>
                    <option value="GK">Guarda-redes</option>
                    <option value="DEF">Defesa</option>
                    <option value="MID">Médio</option>
                    <option value="FWD">Avançado</option>
                </select>
                <select
                    v-model="selectedStatus"
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option value="">Todos os Status</option>
                    <option :value="true">Ativos</option>
                    <option :value="false">Inativos</option>
                </select>
                <select
                    v-model.number="perPage"
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option :value="10">10 por página</option>
                    <option :value="25">25 por página</option>
                    <option :value="50">50 por página</option>
                    <option :value="100">100 por página</option>
                </select>
            </div>
        </div>

        <!-- Tabela de Jogadores -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <table class="w-full">
                <thead class="border-b bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nome</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Posição</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Jogos</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Golos</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Assist.</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Golos/Jogo</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="player in paginatedPlayers" :key="player.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ player.name }}</div>
                            <div class="text-sm text-gray-500">{{ player.email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                {{ player.position_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ player.stats.total_games }}</td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ player.stats.total_goals }}</td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ player.stats.total_assists }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-indigo-600">{{ player.stats.goals_per_game.toFixed(2) }}</span>
                        </td>
                        <td class="space-x-2 px-6 py-4 text-right">
                            <Link :href="`/players/${player.id}/edit`" class="font-semibold text-indigo-600 hover:text-indigo-900"> Editar </Link>
                            <button @click="deletePlayer(player.id)" class="font-semibold text-red-600 hover:text-red-900">Deletar</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="filteredPlayers.length === 0" class="py-8 text-center text-gray-500">Nenhum jogador encontrado</div>
        </div>

        <!-- Paginação -->
        <div v-if="filteredPlayers.length > perPage" class="mt-6 flex items-center justify-between rounded-lg bg-white p-4 shadow">
            <div class="text-sm text-gray-600">
                Mostrando <span class="font-semibold">{{ startIndex + 1 }}</span> a <span class="font-semibold">{{ endIndex }}</span> de
                <span class="font-semibold">{{ filteredPlayers.length }}</span> jogadores
            </div>
            <div class="flex gap-2">
                <button
                    @click="currentPage = 1"
                    :disabled="currentPage === 1"
                    class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    « Primeira
                </button>
                <button
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    ‹ Anterior
                </button>
                <span class="flex items-center px-3">
                    Página <input v-model.number="currentPage" type="number" :max="totalPages" class="mr-1 ml-1 w-12 rounded border px-2 py-1" /> de
                    {{ totalPages }}
                </span>
                <button
                    @click="currentPage++"
                    :disabled="currentPage === totalPages"
                    class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    Próxima ›
                </button>
                <button
                    @click="currentPage = totalPages"
                    :disabled="currentPage === totalPages"
                    class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    Última »
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    players: Array,
    meta: Object,
});

const searchQuery = ref('');
const selectedPosition = ref('');
const selectedStatus = ref('');
const perPage = ref(25);
const currentPage = ref(1);

const filteredPlayers = computed(() => {
    return props.players.filter((player) => {
        const matchesSearch = player.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesPosition = !selectedPosition.value || player.position_code === selectedPosition.value;
        const matchesStatus = selectedStatus.value === '' || player.active === (selectedStatus.value === 'true');
        return matchesSearch && matchesPosition && matchesStatus;
    });
});

const totalPages = computed(() => Math.ceil(filteredPlayers.value.length / perPage.value));

const startIndex = computed(() => (currentPage.value - 1) * perPage.value);
const endIndex = computed(() => Math.min(currentPage.value * perPage.value, filteredPlayers.value.length));

const paginatedPlayers = computed(() => {
    const start = startIndex.value;
    const end = endIndex.value;
    return filteredPlayers.value.slice(start, end);
});

const deletePlayer = (id) => {
    if (confirm('Tem certeza que deseja eliminar este jogador?')) {
        router.delete(`/players/${id}`);
    }
};
</script>
