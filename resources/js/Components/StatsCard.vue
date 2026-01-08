<template>
    <div class="rounded-lg bg-white p-4 shadow" :class="bgClass">
        <div class="mb-2 text-3xl">{{ icon }}</div>
        <p class="text-sm text-gray-600">{{ title }}</p>
        <p class="text-2xl font-bold text-gray-900">{{ value }}</p>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    bgClass: {
        type: String,
        default: 'bg-blue-50',
    },
});
</script>
