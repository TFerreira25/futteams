<template>
    <AppLayout>
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-4">
            <!-- Stats Cards -->
            <StatsCard title="Total Jogadores" :value="totalPlayers" icon="👥" />
            <StatsCard title="Jogos Completados" :value="completedGames" icon="✅" />
            <StatsCard title="Jogos Pendentes" :value="pendingGames" icon="⏳" />
            <StatsCard title="Golos Esta Semana" :value="goalsThisWeek" icon="⚽" />
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Top Scorers -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-900">🔥 Top Goleadores</h2>
                <div class="space-y-3">
                    <div v-for="(player, idx) in topGoalScorers" :key="idx" class="flex items-center justify-between border-b pb-3 last:border-b-0">
                        <span class="text-gray-700">{{ idx + 1 }}. {{ player.name }}</span>
                        <span class="font-bold text-indigo-600">{{ player.goals_per_game.toFixed(2) }} /jogo</span>
                    </div>
                </div>
                <Link href="/rankings/goals-per-game" class="mt-4 inline-block font-semibold text-indigo-600 hover:text-indigo-800">
                    Ver mais →
                </Link>
            </div>

            <!-- Top Assistants -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-900">🤝 Top Assistências</h2>
                <div class="space-y-3">
                    <div v-for="(player, idx) in topAssistants" :key="idx" class="flex items-center justify-between border-b pb-3 last:border-b-0">
                        <span class="text-gray-700">{{ idx + 1 }}. {{ player.name }}</span>
                        <span class="font-bold text-green-600">{{ player.assists_per_game.toFixed(2) }} /jogo</span>
                    </div>
                </div>
                <Link href="/rankings/assists-per-game" class="mt-4 inline-block font-semibold text-indigo-600 hover:text-indigo-800">
                    Ver mais →
                </Link>
            </div>

            <!-- Recent Games -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-900">📅 Últimos Jogos</h2>
                <div class="space-y-3">
                    <div v-for="game in recentGames" :key="game.id" class="border-b pb-3 last:border-b-0">
                        <p class="text-sm text-gray-600">{{ formatDate(game.date) }}</p>
                        <p v-if="game.teams && game.teams.length === 2" class="font-bold">
                            {{ game.teams[0].team_number }} vs {{ game.teams[1].team_number }}
                        </p>
                        <p v-if="game.status === 'completed' && game.teams && game.teams.length === 2" class="text-sm text-gray-700">
                            {{ game.teams[0].goals }} - {{ game.teams[1].goals }}
                        </p>
                        <p v-else class="text-sm text-yellow-600">{{ game.status }}</p>
                    </div>
                </div>
                <Link href="/games" class="mt-4 inline-block font-semibold text-indigo-600 hover:text-indigo-800"> Ver todos → </Link>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8 rounded-lg bg-white p-6 shadow-md">
            <h2 class="mb-4 text-xl font-bold text-gray-900">⚡ Ações Rápidas</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <Link
                    href="/players/create"
                    class="rounded-lg bg-indigo-600 px-4 py-3 text-center font-bold text-white transition hover:bg-indigo-700"
                >
                    + Novo Jogador
                </Link>
                <Link href="/games/create" class="rounded-lg bg-green-600 px-4 py-3 text-center font-bold text-white transition hover:bg-green-700">
                    + Novo Jogo
                </Link>
                <Link href="/rankings" class="rounded-lg bg-blue-600 px-4 py-3 text-center font-bold text-white transition hover:bg-blue-700">
                    📊 Rankings
                </Link>
                <Link href="/players" class="rounded-lg bg-purple-600 px-4 py-3 text-center font-bold text-white transition hover:bg-purple-700">
                    👥 Jogadores
                </Link>
            </div>
        </div>

        <!-- Presence Chart -->
        <div class="rounded-lg bg-white p-6 shadow-md">
            <h2 class="mb-4 text-xl font-bold text-gray-900">🎯 Mais Presentes</h2>
            <div class="space-y-3">
                <div v-for="(player, idx) in topPresence" :key="idx" class="flex items-center justify-between">
                    <span class="text-gray-700">{{ idx + 1 }}. {{ player.name }}</span>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-48 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-indigo-600" :style="{ width: (player.games_played / maxGames) * 100 + '%' }"></div>
                        </div>
                        <span class="w-12 text-right font-bold text-gray-700">{{ player.games_played }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import StatsCard from '@/Components/StatsCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    topGoalsPerGame: Array,
    topAssistsPerGame: Array,
    topPresence: Array,
    recentGames: Array,
});

const totalPlayers = ref(0);
const completedGames = ref(0);
const pendingGames = ref(0);
const goalsThisWeek = ref(0);

const topGoalScorers = computed(() => props.topGoalsPerGame?.slice(0, 5) || []);
const topAssistants = computed(() => props.topAssistsPerGame?.slice(0, 5) || []);
const maxGames = computed(() => Math.max(...(props.topPresence?.map((p) => p.games_played) || [1])));

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

onMounted(async () => {
    try {
        const playersRes = await fetch('/api/players');
        const players = await playersRes.json();
        totalPlayers.value = players.length;

        const gamesRes = await fetch('/api/games');
        const games = await gamesRes.json();
        completedGames.value = games.filter((g) => g.status === 'completed').length;
        pendingGames.value = games.filter((g) => g.status !== 'completed').length;
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
});
</script>
