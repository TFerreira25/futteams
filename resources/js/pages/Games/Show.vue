<template>
    <AppLayout>
        <div class="mb-6">
            <Link href="/games" class="text-indigo-600 hover:text-indigo-800">← Voltar</Link>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">📋 Registar Resultados</h1>
        </div>

        <!-- Teams Overview -->
        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Team 1 -->
            <div class="rounded-lg bg-blue-50 p-6">
                <h2 class="mb-4 text-2xl font-bold text-blue-600">Team 1</h2>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Golos</label>
                    <input
                        v-model.number="team1Goals"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                </div>

                <div class="rounded-lg bg-white p-4">
                    <p class="mb-3 font-semibold text-gray-900">Jogadores ({{ game.team1.players.length }})</p>
                    <div class="space-y-2">
                        <div v-for="player in game.team1.players" :key="player.id" class="flex items-center justify-between text-sm text-gray-700">
                            <span
                                >{{ player.name }} <span class="text-gray-500">({{ player.position_code }})</span></span
                            >
                            <button @click="openEventForm(player.id, 1)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                + Evento
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team 2 -->
            <div class="rounded-lg bg-red-50 p-6">
                <h2 class="mb-4 text-2xl font-bold text-red-600">Team 2</h2>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Golos</label>
                    <input
                        v-model.number="team2Goals"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />
                </div>

                <div class="rounded-lg bg-white p-4">
                    <p class="mb-3 font-semibold text-gray-900">Jogadores ({{ game.team2.players.length }})</p>
                    <div class="space-y-2">
                        <div v-for="player in game.team2.players" :key="player.id" class="flex items-center justify-between text-sm text-gray-700">
                            <span
                                >{{ player.name }} <span class="text-gray-500">({{ player.position_code }})</span></span
                            >
                            <button @click="openEventForm(player.id, 2)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                + Evento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eventos Registados -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-bold text-gray-900">📊 Eventos Registados</h2>
            <div v-if="events.length === 0" class="py-4 text-center text-gray-500">Sem eventos registados</div>
            <div v-else class="space-y-2">
                <div v-for="(event, idx) in events" :key="idx" class="flex items-center justify-between rounded bg-gray-50 p-3">
                    <span class="text-gray-700">
                        {{ event.player_name }} - {{ event.event_type }}
                        <span class="text-gray-500">(Team {{ event.team }})</span>
                    </span>
                    <button @click="removeEvent(idx)" class="text-sm font-semibold text-red-600 hover:text-red-800">Remover</button>
                </div>
            </div>
        </div>

        <!-- Modal de Evento -->
        <div v-if="showEventModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-xl font-bold text-gray-900">Registar Evento</h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Tipo de Evento</label>
                        <select
                            v-model="eventForm.type"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option value="">Selecione</option>
                            <option value="goal">⚽ Golo</option>
                            <option value="assist">🤝 Assistência</option>
                            <option value="yellow_card">🟨 Cartão Amarelo</option>
                            <option value="red_card">🔴 Cartão Vermelho</option>
                            <option value="goal_conceded">🎯 Golo Sofrido (GR)</option>
                        </select>
                    </div>

                    <div class="flex gap-4">
                        <button
                            @click="confirmRecordEvent"
                            class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white transition hover:bg-indigo-700"
                        >
                            Registar
                        </button>
                        <button
                            @click="showEventModal = false"
                            class="flex-1 rounded-lg bg-gray-300 px-4 py-2 font-bold text-gray-900 transition hover:bg-gray-400"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação Genérico -->
        <div v-if="confirm.show" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-2 text-xl font-bold text-gray-900">{{ confirm.title }}</h3>
                <p class="mb-6 text-gray-600">{{ confirm.description }}</p>
                <div class="flex gap-3">
                    <button @click="confirmCancel" class="flex-1 rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-800 hover:bg-gray-300">
                        {{ confirm.cancelLabel || 'Cancelar' }}
                    </button>
                    <button @click="confirmProceed" class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700">
                        {{ confirm.confirmLabel || 'Confirmar' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast de Sucesso/Erro -->
        <div v-if="toast.show" class="fixed top-4 right-4 z-50">
            <div
                :class="[
                    'flex items-center gap-3 rounded-lg px-4 py-3 shadow-md',
                    toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white',
                ]"
            >
                <span v-if="toast.type === 'success'">✅</span>
                <span v-else>⚠️</span>
                <span class="font-semibold">{{ toast.message }}</span>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex gap-4">
            <button @click="completeGame" class="flex-1 rounded-lg bg-green-600 px-4 py-3 font-bold text-white transition hover:bg-green-700">
                ✅ Completar Jogo
            </button>
            <Link href="/games" class="flex-1 rounded-lg bg-gray-300 px-4 py-3 text-center font-bold text-gray-900 transition hover:bg-gray-400">
                Cancelar
            </Link>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    game: Object,
    events: Array,
});

// Usar estado local mutável para refletir atualizações de eventos
const game = ref(props.game);

const team1Goals = ref(props.game.team1?.goals || 0);
const team2Goals = ref(props.game.team2?.goals || 0);
const events = ref([]);
const showEventModal = ref(false);
const eventForm = ref({ type: '', player_id: null, team: null });
const toast = ref({ show: false, message: '', type: 'success' });
const confirm = ref({ show: false, title: '', description: '', confirmLabel: 'Confirmar', cancelLabel: 'Cancelar', onConfirm: null });

function showToast(message, type = 'success') {
    toast.value = { show: true, message, type };
    setTimeout(() => (toast.value.show = false), 2500);
}

function openConfirm(options) {
    confirm.value = { show: true, ...options };
}

function confirmProceed() {
    const action = confirm.value.onConfirm;
    confirm.value.show = false;
    if (typeof action === 'function') action();
}

function confirmCancel() {
    confirm.value.show = false;
}
// Expand player stats into a flat event list
function expandPlayerEvents(player, team) {
    const list = [];
    const map = [
        { key: 'goals', type: 'goal' },
        { key: 'assists', type: 'assist' },
        { key: 'yellow_cards', type: 'yellow_card' },
        { key: 'red_cards', type: 'red_card' },
        { key: 'goals_conceded', type: 'goal_conceded' },
    ];
    for (const { key, type } of map) {
        const count = Number(player[key] ?? 0);
        for (let i = 0; i < count; i++) {
            list.push({
                player_id: player.player_id ?? player.id,
                player_name: player.player_name ?? player.name,
                event_type: type,
                team,
            });
        }
    }
    return list;
}

onMounted(async () => {
    try {
        // Ensure CSRF cookie (Sanctum)
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });

        // Load current summary to prefill goals
        const res = await fetch(`/games/${props.game.id}/statistics`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        const summary = await res.json();

        if (summary?.team1 && summary?.team2) {
            team1Goals.value = summary.team1.goals ?? team1Goals.value;
            team2Goals.value = summary.team2.goals ?? team2Goals.value;
        }

        // Usar eventos vindo de props (Inertia) ou fetch se vazio
        if (Array.isArray(props.events) && props.events.length > 0) {
            events.value = props.events;
        } else {
            // Fallback: fetch se não vieram nos props
            const evRes = await fetch(`/games/${props.game.id}/events`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const evts = await evRes.json();
            if (Array.isArray(evts)) {
                events.value = evts;
            }
        }
    } catch (e) {
        console.warn('Falha ao carregar estatísticas iniciais:', e);
    }
});

const openEventForm = (playerId, team) => {
    eventForm.value = { type: '', player_id: playerId, team };
    showEventModal.value = true;
};

function getCookie(name) {
    const match = document.cookie.split('; ').find((row) => row.startsWith(name + '='));
    return match ? decodeURIComponent(match.split('=')[1]) : null;
}

const doRecordEvent = async () => {
    if (!eventForm.value.type) return;

    try {
        const res = await fetch(`/games/${props.game.id}/record-event`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') || '',
            },
            body: JSON.stringify({
                player_id: eventForm.value.player_id,
                event_type: eventForm.value.type,
                quantity: 1,
            }),
            credentials: 'same-origin',
        });

        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Erro ao registar evento');

        // Atualizar eventos visuais
        const player =
            eventForm.value.team === 1
                ? game.value.team1.players.find((p) => p.id === eventForm.value.player_id)
                : game.value.team2.players.find((p) => p.id === eventForm.value.player_id);

        events.value.push({
            player_id: eventForm.value.player_id,
            player_name: player?.name || 'Jogador',
            event_type: eventForm.value.type,
            team: eventForm.value.team,
        });

        // Atualizar golos totais apresentados
        team1Goals.value = data.game.team1?.goals ?? team1Goals.value;
        team2Goals.value = data.game.team2?.goals ?? team2Goals.value;

        // Atualizar estado do jogo local (para refletir jogadores/equipas se necessário)
        game.value = data.game;

        showEventModal.value = false;
        showToast('Evento registado com sucesso', 'success');
    } catch (err) {
        console.error(err);
        showToast('Falha ao registar evento: ' + err.message, 'error');
    }
};

const confirmRecordEvent = () => {
    if (!eventForm.value.type) return;
    const tipo = eventForm.value.type;
    openConfirm({
        title: 'Confirmar registo de evento',
        description: `Vai registar \"${tipo}\" para o jogador selecionado. Deseja continuar?`,
        confirmLabel: 'Guardar',
        onConfirm: () => doRecordEvent(),
    });
};

const performRemoveEvent = async (idx) => {
    const e = events.value[idx];
    if (!e) return;
    try {
        const res = await fetch(`/games/${props.game.id}/undo-event`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') || '',
            },
            body: JSON.stringify({
                player_id: e.player_id,
                event_type: e.event_type,
                quantity: 1,
            }),
            credentials: 'same-origin',
        });

        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Erro ao desfazer evento');

        events.value.splice(idx, 1);

        team1Goals.value = data.game.team1?.goals ?? team1Goals.value;
        team2Goals.value = data.game.team2?.goals ?? team2Goals.value;
        game.value = data.game;
        showToast('Evento removido com sucesso', 'success');
    } catch (err) {
        console.error(err);
        showToast('Falha ao desfazer evento: ' + err.message, 'error');
    }
};

const removeEvent = (idx) => {
    const e = events.value[idx];
    if (!e) return;
    openConfirm({
        title: 'Remover evento',
        description: 'Tem a certeza que pretende remover este evento?',
        confirmLabel: 'Remover',
        onConfirm: () => performRemoveEvent(idx),
    });
};

const performCompleteGame = async () => {
    const form = useForm({
        team1_goals: team1Goals.value,
        team2_goals: team2Goals.value,
        events: events.value.map((e) => ({
            player_id: e.player_id,
            event_type: e.event_type,
        })),
    });

    form.post(`/games/${props.game.id}/complete`, {
        preserveScroll: true,
        onSuccess: () => {
            // Inertia seguirá o redirect para games.show e mantém a página
            // Opcionalmente, poderíamos refetch das estatísticas aqui se necessário
            showToast('Jogo finalizado com sucesso', 'success');
        },
    });
};

const completeGame = () => {
    openConfirm({
        title: 'Confirmar resultado',
        description: `Vai guardar o resultado: Team 1 ${team1Goals.value} - ${team2Goals.value} Team 2. Deseja continuar?`,
        confirmLabel: 'Guardar',
        onConfirm: () => performCompleteGame(),
    });
};
</script>
