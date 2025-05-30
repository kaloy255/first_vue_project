<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div class="mt-6">
        <component
            v-for="(page, idx) in links"
            :is="page.url ? Link : 'span'"
            :key="idx"
            v-bind="page.url ? { href: page.url } : {}"
            v-html="page.label"
            class="mx-1 px-2 py-1 rounded border"
            :class="
                page.active
                    ? 'bg-blue-100 border-blue-500 text-blue-600'
                    : 'bg-white border-gray-300 text-gray-700'
            "
            preserve-scroll
        />
    </div>
</template>
