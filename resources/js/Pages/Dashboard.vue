<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
defineProps({
    bebidas: {
        type: Object,
        required: true
    },

    ingredientes: {
        type: Object,
        required: true
    },
    orders: {
        type: Object,
        required: true
    },
})
</script>

<template>
    <AppLayout title="Tablero">
        <div class="py-4">
            <div class="max-w mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden  sm:rounded-lg">
                    <Welcome :bebidas="bebidas" :ingredientes="ingredientes" :orders="orders" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
