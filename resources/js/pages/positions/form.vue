<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">📍 {{ isEdit ? 'Editar Posição' : 'Nova Posição' }}</h1>
        </div>

        <div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
            <form @submit.prevent="submitForm">
                <!-- Código -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Código</label>
                    <input
                        v-model="form.code"
                        type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="Ex: GK, DEF, MID"
                        maxlength="10"
                        required
                    />
                    <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                </div>

                <!-- Nome -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Nome</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="Ex: Guarda-redes"
                        required
                    />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Máximo por Equipa -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Máximo por Equipa</label>
                    <input
                        v-model.number="form.max_per_team"
                        type="number"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        min="1"
                        max="11"
                        required
                    />
                    <p v-if="form.errors.max_per_team" class="mt-1 text-sm text-red-600">{{ form.errors.max_per_team }}</p>
                </div>

                <!-- Ordem -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Ordem de Exibição</label>
                    <input
                        v-model.number="form.sort_order"
                        type="number"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        min="0"
                        required
                    />
                    <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 rounded-lg bg-indigo-600 px-6 py-2 font-bold text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ isEdit ? 'Atualizar' : 'Criar' }} Posição
                    </button>
                    <Link
                        href="/positions"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-2 text-center font-bold text-gray-700 hover:bg-gray-50"
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
import { computed } from 'vue';

const props = defineProps({
    position: Object,
});

const isEdit = computed(() => !!props.position);

const form = useForm({
    code: props.position?.code || '',
    name: props.position?.name || '',
    max_per_team: props.position?.max_per_team || 1,
    sort_order: props.position?.sort_order || 0,
});

const submitForm = () => {
    if (isEdit.value) {
        form.put(`/positions/${props.position.id}`);
    } else {
        form.post('/positions');
    }
};
</script>
