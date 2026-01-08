<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">🏟️ Jogos</h1>
            <Link href="/games/create" class="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-700"> + Novo Jogo </Link>
        </div>

        <!-- Abas de Status -->
        <div class="mb-6 rounded-lg bg-white shadow">
            <div class="flex border-b">
                <button
                    v-for="status in statuses"
                    :key="status.id"
                    @click="filterStatus = status.id"
                    :class="[
                        'px-6 py-3 font-semibold transition',
                        filterStatus === status.id ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600 hover:text-gray-900',
                    ]"
                >
                    {{ status.name }}
                </button>
            </div>

            <div class="space-y-4 p-6">
                <div v-if="filteredGames.length === 0" class="py-8 text-center text-gray-500">Sem jogos para exibir</div>

                <div v-for="game in filteredGames" :key="game.id" class="rounded-lg border p-4 transition hover:bg-gray-50">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500">{{ formatDate(game.date) }}</p>
                            <p class="text-xs text-gray-400">{{ formatTime(game.date) }}</p>
                        </div>
                        <span :class="['rounded-full px-3 py-1 text-sm font-semibold', getStatusClass(game.status)]">
                            {{ game.status }}
                        </span>
                    </div>

                    <!-- Teams -->
                    <div v-if="game.team1 && game.team2" class="mb-4 grid grid-cols-3 gap-4">
                        <div class="rounded-lg bg-blue-50 p-4">
                            <p class="mb-2 font-semibold text-gray-900">Team 1</p>
                            <p class="text-sm text-gray-600">{{ game.team1.players.length }} jogadores</p>
                            <div v-if="game.status === 'completed'" class="mt-2 text-2xl font-bold text-blue-600">
                                {{ game.team1.goals }}
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-400">VS</span>
                        </div>

                        <div class="rounded-lg bg-red-50 p-4">
                            <p class="mb-2 font-semibold text-gray-900">Team 2</p>
                            <p class="text-sm text-gray-600">{{ game.team2.players.length }} jogadores</p>
                            <div v-if="game.status === 'completed'" class="mt-2 text-2xl font-bold text-red-600">
                                {{ game.team2.goals }}
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="flex gap-2">
                        <Link
                            v-if="game.status === 'pending'"
                            :href="`/games/${game.id}/select-players`"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-900"
                        >
                            Selecionar Jogadores
                        </Link>
                        <Link
                            v-if="game.status === 'team_generation'"
                            :href="`/games/${game.id}`"
                            class="text-sm font-semibold text-green-600 hover:text-green-900"
                        >
                            Registar Resultados
                        </Link>
                        <Link
                            v-if="game.status === 'completed'"
                            :href="`/games/${game.id}`"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-900"
                        >
                            Ver Resumo
                        </Link>
                        <Link :href="`/games/${game.id}/edit`" class="text-sm font-semibold text-gray-600 hover:text-gray-900"> Editar </Link>
                        <button @click="deleteGame(game.id)" class="text-sm font-semibold text-red-600 hover:text-red-900">Deletar</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    games: Array,
});

const filterStatus = ref('');

const statuses = [
    { id: '', name: 'Todos' },
    { id: 'pending', name: '⏳ Pendentes' },
    { id: 'team_generation', name: '⚙️ Em Preparação' },
    { id: 'completed', name: '✅ Completados' },
];

const filteredGames = computed(() => {
    if (!filterStatus.value) return props.games;
    return props.games.filter((g) => g.status === filterStatus.value);
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        team_generation: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const deleteGame = (id) => {
    if (confirm('Tem certeza que quer deletar este jogo?')) {
        const form = useForm({});
        form.delete(`/games/${id}`, {
            onSuccess: () => window.location.reload(),
        });
    }
};
</script>
