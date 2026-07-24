<template>
    <div :class="[
        'bg-white rounded-2xl border border-gray-100 shadow-soft transition-all duration-300 hover:shadow-glass hover:-translate-y-1',
        paddingClass
    ]">
        <div v-if="$slots.header" class="border-b border-gray-50 px-6 py-5">
            <slot name="header" />
        </div>
        
        <div :class="{ 'p-6': !noPadding }">
            <slot />
        </div>

        <div v-if="$slots.footer" class="border-t border-gray-50 px-6 py-4 bg-gray-50/50 rounded-b-2xl">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    noPadding: {
        type: Boolean,
        default: false,
    },
});

const paddingClass = computed(() => {
    return props.noPadding ? '' : '';
});
</script>
