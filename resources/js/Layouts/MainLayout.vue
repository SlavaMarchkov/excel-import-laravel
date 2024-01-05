<template>
    <Navigation></Navigation>
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <slot></slot>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';

export default {
    name: 'MainLayout',
    components: { Link, Navigation },
};
</script>

<style scoped>

</style>
