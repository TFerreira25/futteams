<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">🏆 Rankings</h1>
        </div>

        <!-- Abas de Rankings -->
        <div class="mb-6 rounded-lg bg-white shadow">
            <div class="flex border-b">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-6 py-3 font-semibold transition',
                        activeTab === tab.id ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600 hover:text-gray-900',
                    ]"
                >
                    {{ tab.name }}
                </button>
            </div>

            <!-- Conteúdo das Abas -->
            <div class="p-6">
                <!-- Golos por Jogo -->
                <div v-if="activeTab === 'gpg'" class="space-y-3">
                    <div
                        v-for="(player, idx) in goalsPerGame"
                        :key="idx"
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-4 hover:bg-gray-100"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-8 text-2xl font-bold text-indigo-600">{{ idx + 1 }}</div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ player.name }}</p>
                                <p class="text-sm text-gray-500">{{ player.position_code }} • {{ player.games_played }} jogos</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">{{ player.goals_per_game.toFixed(2) }}</p>
                            <p class="text-sm text-gray-500">golos/jogo</p>
                        </div>
                    </div>
                </div>

                <!-- Assistências por Jogo -->
                <div v-if="activeTab === 'apg'" class="space-y-3">
                    <div
                        v-for="(player, idx) in assistsPerGame"
                        :key="idx"
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-4 hover:bg-gray-100"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-8 text-2xl font-bold text-green-600">{{ idx + 1 }}</div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ player.name }}</p>
                                <p class="text-sm text-gray-500">{{ player.position_code }} • {{ player.games_played }} jogos</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">{{ player.assists_per_game.toFixed(2) }}</p>
                            <p class="text-sm text-gray-500">assist/jogo</p>
                        </div>
                    </div>
                </div>

                <!-- Golos Totais -->
                <div v-if="activeTab === 'tg'" class="space-y-3">
                    <div
                        v-for="(player, idx) in totalGoals"
                        :key="idx"
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-4 hover:bg-gray-100"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-8 text-2xl font-bold text-blue-600">{{ idx + 1 }}</div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ player.name }}</p>
                                <p class="text-sm text-gray-500">{{ player.position_code }} • {{ player.games_played }} jogos</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">{{ player.total_goals }}</p>
                            <p class="text-sm text-gray-500">golos totais</p>
                        </div>
                    </div>
                </div>

                <!-- Presenças -->
                <div v-if="activeTab === 'presence'" class="space-y-3">
                    <div
                        v-for="(player, idx) in presence"
                        :key="idx"
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-4 hover:bg-gray-100"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-8 text-2xl font-bold text-purple-600">{{ idx + 1 }}</div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ player.name }}</p>
                                <p class="text-sm text-gray-500">{{ player.position_code }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-purple-600">{{ player.games_played }}</p>
                            <p class="text-sm text-gray-500">jogos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

const activeTab = ref('gpg');
const goalsPerGame = ref([]);
const assistsPerGame = ref([]);
const totalGoals = ref([]);
const presence = ref([]);

const tabs = [
    { id: 'gpg', name: '⚽ Golos/Jogo' },
    { id: 'apg', name: '🤝 Assist/Jogo' },
    { id: 'tg', name: '🔥 Golos Totais' },
    { id: 'presence', name: '🎯 Presenças' },
];

onMounted(async () => {
    try {
        const dashboard = await fetch('/api/rankings/dashboard').then((r) => r.json());
        goalsPerGame.value = dashboard.top_goals_per_game;
        assistsPerGame.value = dashboard.top_assists_per_game;
        totalGoals.value = dashboard.top_total_goals;
        presence.value = dashboard.top_presence;
    } catch (error) {
        console.error('Error loading rankings:', error);
    }
});
</script>
