<template>
    <AppLayout>
        <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="mb-8">
                    <h1 class="mb-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-4xl font-bold text-transparent">
                        👥 Selecionar Jogadores
                    </h1>
                    <p class="text-lg text-gray-600">Escolha {{ game.total_players }} jogadores para a próxima partida</p>
                </div>

                <div class="mb-8 grid grid-cols-1 gap-4 lg:grid-cols-4">
                    <div
                        class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-5 shadow-md transition hover:shadow-lg"
                    >
                        <p class="flex items-center gap-2 text-sm font-semibold text-blue-600">📊 Disponíveis</p>
                        <p class="mt-3 text-3xl font-bold text-blue-700">{{ totalAvailable }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-green-100 p-5 shadow-md transition hover:shadow-lg"
                    >
                        <p class="flex items-center gap-2 text-sm font-semibold text-green-600">✓ Selecionados</p>
                        <p class="mt-3 text-3xl font-bold text-green-700">{{ selected.length }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 shadow-md transition hover:shadow-lg"
                    >
                        <p class="flex items-center gap-2 text-sm font-semibold text-indigo-600">🎯 Necessários</p>
                        <p class="mt-3 text-3xl font-bold text-indigo-700">{{ game.total_players }}</p>
                    </div>
                    <div
                        :class="[
                            'rounded-xl border p-5 shadow-md transition hover:shadow-lg',
                            selected.length === game.total_players
                                ? 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100'
                                : 'border-amber-200 bg-gradient-to-br from-amber-50 to-amber-100',
                        ]"
                    >
                        <p
                            :class="[
                                'flex items-center gap-2 text-sm font-semibold',
                                selected.length === game.total_players ? 'text-emerald-600' : 'text-amber-600',
                            ]"
                        >
                            {{ selected.length === game.total_players ? '✅ Pronto!' : '⏳ Status' }}
                        </p>
                        <p :class="['mt-3 text-3xl font-bold', selected.length === game.total_players ? 'text-emerald-700' : 'text-amber-700']">
                            {{ selected.length === game.total_players ? '✓' : game.total_players - selected.length }}
                        </p>
                    </div>
                </div>

                <!-- Filtros por Posição -->
                <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-bold text-gray-900">🔍 Buscar Jogador</label>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Digite o nome do jogador..."
                            class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-500 transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none"
                        />
                    </div>
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-bold text-gray-900">🎪 Filtrar por Posição</label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="pos in positions"
                                :key="pos.code"
                                @click="filterPosition = pos.code"
                                :class="[
                                    'rounded-lg px-4 py-2 text-sm font-semibold transition duration-200',
                                    filterPosition === pos.code
                                        ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md'
                                        : 'border border-gray-200 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-indigo-600',
                                ]"
                            >
                                {{ pos.code }} <span class="ml-1 text-xs opacity-75">({{ pos.available }})</span>
                            </button>
                            <button
                                @click="filterPosition = ''"
                                :class="[
                                    'rounded-lg px-4 py-2 text-sm font-semibold transition duration-200',
                                    !filterPosition
                                        ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md'
                                        : 'border border-gray-200 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-indigo-600',
                                ]"
                            >
                                🔄 Todos
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t pt-4">
                        <p class="text-sm text-gray-700">
                            <span class="font-bold text-indigo-600">{{ filteredPlayers.length }}</span> jogadores disponíveis
                        </p>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700">Jogadores por página:</label>
                            <select
                                v-model.number="perPage"
                                class="rounded-lg border-2 border-gray-300 px-3 py-2 font-semibold text-gray-900 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            >
                                <option :value="10">10</option>
                                <option :value="12">12</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Lista de Jogadores -->
                <div class="rounded-xl border border-gray-100 bg-white shadow-lg">
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="player in availablePlayers"
                            :key="player.id"
                            @click="togglePlayer(player.id)"
                            :class="[
                                'group cursor-pointer rounded-xl border-2 transition-all duration-200',
                                isSelected(player.id)
                                    ? 'border-indigo-500 bg-indigo-50 shadow-md'
                                    : 'border-gray-200 bg-white hover:-translate-y-1 hover:border-indigo-400 hover:shadow-lg',
                            ]"
                        >
                            <div class="p-5">
                                <div class="mb-4 flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-lg leading-tight font-bold text-gray-900">{{ player.name }}</p>
                                        <p class="mt-1 text-sm font-semibold text-indigo-600">{{ player.position_code }}</p>
                                        <p class="mt-2 flex items-center gap-1 text-xs text-gray-500">📈 {{ player.stats.total_games }} jogos</p>
                                    </div>
                                    <div class="text-2xl font-bold text-indigo-600 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                        {{ isSelected(player.id) ? '✓' : '+' }}
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="rounded-lg border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-3 text-center">
                                        <p class="text-xs font-semibold text-gray-600">Golos</p>
                                        <p class="mt-1 text-lg font-bold text-blue-700">{{ player.stats.total_goals || 0 }}</p>
                                    </div>
                                    <div class="rounded-lg border border-green-200 bg-gradient-to-br from-green-50 to-green-100 p-3 text-center">
                                        <p class="text-xs font-semibold text-gray-600">Assist</p>
                                        <p class="mt-1 text-lg font-bold text-green-700">{{ player.stats.total_assists || 0 }}</p>
                                    </div>
                                    <div class="rounded-lg border border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 p-3 text-center">
                                        <p class="text-xs font-semibold text-gray-600">G/J</p>
                                        <p class="mt-1 text-lg font-bold text-purple-700">
                                            {{
                                                player.stats.total_games > 0
                                                    ? (player.stats.total_goals / player.stats.total_games).toFixed(2)
                                                    : '0.00'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginação -->
                    <div
                        v-if="filteredPlayers.length > perPage"
                        class="flex flex-col items-center justify-between gap-4 rounded-b-xl border-t bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 md:flex-row"
                    >
                        <div class="text-sm font-semibold text-gray-700">
                            Mostrando <span class="font-bold text-indigo-600">{{ startIndex + 1 }}</span> a
                            <span class="font-bold text-indigo-600">{{ endIndex }}</span> de
                            <span class="font-bold text-indigo-600">{{ filteredPlayers.length }}</span> jogadores
                        </div>
                        <div class="flex flex-wrap justify-center gap-2">
                            <button
                                @click="currentPage = 1"
                                :disabled="currentPage === 1"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                « Primeira
                            </button>
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                ‹ Anterior
                            </button>
                            <div class="flex items-center rounded-lg border-2 border-gray-300 bg-white px-3 font-semibold text-gray-900">
                                <input
                                    v-model.number="currentPage"
                                    type="number"
                                    :max="totalPages"
                                    class="w-12 bg-transparent text-center focus:outline-none"
                                />
                                <span class="text-sm text-gray-600">/{{ totalPages }}</span>
                            </div>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Próxima ›
                            </button>
                            <button
                                @click="currentPage = totalPages"
                                :disabled="currentPage === totalPages"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Última »
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="mt-8 flex gap-4">
                    <button
                        @click="generateTeams"
                        :disabled="selected.length < 4 || selected.length > game.total_players"
                        class="flex-1 rounded-xl bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-lg font-bold text-white shadow-md transition duration-200 hover:from-green-700 hover:to-green-800 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        ⚙️ Gerar Equipas ({{ selected.length }}/{{ game.total_players }} máx)
                    </button>
                    <Link
                        href="/games"
                        class="flex-1 rounded-xl bg-gradient-to-r from-gray-400 to-gray-500 px-6 py-4 text-center text-lg font-bold text-white shadow-md transition duration-200 hover:from-gray-500 hover:to-gray-600 hover:shadow-lg"
                    >
                        ✕ Cancelar
                    </Link>
                </div>
            </div>

            <!-- Sidebar com Selecionados -->
            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 shadow-lg lg:sticky lg:top-4 lg:h-fit">
                <h2
                    class="mb-2 flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-2xl font-bold text-transparent"
                >
                    📋 Selecionados
                </h2>
                <p class="mb-6 text-sm font-medium text-gray-600">
                    {{ selected.length }} <span class="text-gray-500">/</span> {{ game.total_players }} jogadores
                </p>

                <div class="mb-6 space-y-2">
                    <div v-if="selectedPlayers.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-100 py-8 text-center">
                        <p class="text-sm font-medium text-gray-600">👆 Selecione jogadores acima</p>
                    </div>
                    <div v-else class="space-y-3">
                        <template v-for="position in getPositionsFromSelected()" :key="position">
                            <div>
                                <h4 class="mb-2 rounded-lg bg-indigo-50 px-4 py-2 text-xs font-bold tracking-widest text-indigo-600 uppercase">
                                    {{ position }}
                                </h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="player in selectedPlayers.filter((p) => p.position_code === position)"
                                        :key="player.id"
                                        class="group flex items-center justify-between rounded-lg border-2 border-indigo-200 bg-gradient-to-r from-indigo-50 to-indigo-100 px-3 py-2 transition hover:shadow-md"
                                    >
                                        <div class="flex min-w-0 flex-1 items-center gap-2">
                                            <p class="truncate text-sm font-semibold text-gray-900">{{ player.name }}</p>
                                            <span class="flex-shrink-0 rounded bg-indigo-600 px-2 py-0.5 text-xs font-bold text-white">{{
                                                player.stats.total_games > 0
                                                    ? (player.stats.total_goals / player.stats.total_games).toFixed(2)
                                                    : '0.00'
                                            }}</span>
                                        </div>
                                        <button
                                            @click="togglePlayer(player.id)"
                                            class="ml-2 rounded-full p-1 text-red-500 opacity-0 transition group-hover:opacity-100 hover:bg-red-50 hover:text-red-700"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    game: Object,
});

const selected = ref([]);
const allPlayers = ref([]);
const filterPosition = ref('');
const searchQuery = ref('');

const positions = computed(() => {
    const posMap = new Map();
    allPlayers.value.forEach((p) => {
        if (!posMap.has(p.position_code)) {
            posMap.set(p.position_code, { code: p.position_code, available: 0 });
        }
        if (!selected.value.includes(p.id)) {
            posMap.get(p.position_code).available++;
        }
    });
    return Array.from(posMap.values()).sort((a, b) => a.code.localeCompare(b.code));
});

const totalAvailable = computed(() => allPlayers.value.length);

const filteredPlayers = computed(() => {
    return allPlayers.value.filter((p) => {
        const matchesPosition = !filterPosition.value || p.position_code === filterPosition.value;
        const matchesSearch = !searchQuery.value || p.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesPosition && matchesSearch;
    });
});

// Paginação
const perPage = ref(10);
const currentPage = ref(1);
const totalPages = computed(() => Math.ceil(filteredPlayers.value.length / perPage.value));
const startIndex = computed(() => (currentPage.value - 1) * perPage.value);
const endIndex = computed(() => Math.min(currentPage.value * perPage.value, filteredPlayers.value.length));
const availablePlayers = computed(() => {
    return paginatedPlayers.value.filter((p) => !selected.value.includes(p.id));
});

const paginatedPlayers = computed(() => filteredPlayers.value.slice(startIndex.value, endIndex.value));

const selectedPlayers = computed(() => {
    const selectedIds = allPlayers.value.filter((p) => selected.value.includes(p.id));
    return selectedIds.sort((a, b) => a.position_code.localeCompare(b.position_code));
});

const aggregatedStats = computed(() => {
    const players = selectedPlayers.value;
    if (players.length === 0) {
        return { totalGames: 0, totalGoals: 0, totalAssists: 0, goalsPerGame: '0.00' };
    }
    const totalGames = players.reduce((sum, p) => sum + (p.stats?.total_games || 0), 0);
    const totalGoals = players.reduce((sum, p) => sum + (p.stats?.total_goals || 0), 0);
    const totalAssists = players.reduce((sum, p) => sum + (p.stats?.total_assists || 0), 0);
    const goalsPerGame = totalGames > 0 ? (totalGoals / totalGames).toFixed(2) : '0.00';
    return { totalGames, totalGoals, totalAssists, goalsPerGame };
});

// Reset page when filters or page size change
watch([filterPosition, searchQuery, perPage], () => {
    currentPage.value = 1;
});

const isSelected = (id) => selected.value.includes(id);

const getPositionsFromSelected = () => {
    const positions = new Set(selectedPlayers.value.map((p) => p.position_code));
    return Array.from(positions).sort((a, b) => a.localeCompare(b));
};

const togglePlayer = (id) => {
    if (selected.value.includes(id)) {
        selected.value = selected.value.filter((pid) => pid !== id);
    } else if (selected.value.length < props.game.total_players) {
        selected.value.push(id);
    }
};

const generateTeams = () => {
    const form = useForm({ player_ids: selected.value });
    form.post(`/games/${props.game.id}/generate-teams`, {
        onSuccess: () => (window.location.href = `/games/${props.game.id}`),
    });
};

onMounted(async () => {
    try {
        const response = await fetch('/api/players');
        allPlayers.value = await response.json();
    } catch (error) {
        console.error('Error loading players:', error);
    }
});
</script>
