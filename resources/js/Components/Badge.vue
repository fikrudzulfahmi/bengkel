<template>
    <span :class="[
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide shadow-sm',
        typeClasses[type] || typeClasses.default
    ]">
        <slot />
    </span>
</template>

<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'default', 
    }
});

const typeClasses = {
    primary: 'bg-primary-light/20 text-primary-dark',
    success: 'bg-emerald-100 text-emerald-700',
    danger: 'bg-red-100 text-red-700',
    warning: 'bg-amber-100 text-amber-700',
    info: 'bg-blue-100 text-blue-700',
    default: 'bg-gray-100 text-gray-700',
};
</script>
