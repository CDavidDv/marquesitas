<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

import { defineComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    
    total: {
        type: Number,
        required: true
    },
    orders: {
        type: Object,
        required: true
    },
    numeroDeOrdenes: {
        type: Number,
        required: true
    },
    hoy: {
        type: String,
        required: false
    },
    sucursal: {
        type: Number,
        required: false
    },
})
</script>

<template>
    <AppLayout title="Tablero">
        <template #header>
            <div class="flex justify-between flex-col md:flex-row">
                <div>
                    <h1 class="text-xl font-bold">Corte de caja de sucursal {{ sucursal }}</h1>
                    <p>Corte del dia {{ hoy.split('T')[0].split('-')[2] +"/"+ hoy.split('T')[0].split('-')[1] +"/"+ hoy.split('T')[0].split('-')[0] }}</p>
                </div>
                <div class="">
                    <p class="text-lg my-3">Total en efectivo: <span class="font-bold text-white md:text-2xl bg-green-500  px-2 py-1 rounded-lg">${{ total }}</span></p>
                    <p class="text-lg ">Número de órdenes: <span class="font-bold text-white md:text-2xl bg-teal-400 px-2 py-1 rounded-lg">{{ numeroDeOrdenes }}</span></p>
                </div>
            </div>
        </template> 
        <div class="py-4">
            <div class="max-w mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg">
                    <div>
                        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                            <h2 class="text-2xl text-gray-500 font-bold mb-5 ">Pedidos atendidos de hoy</h2>
                            <div class="grid grid-col-1 md:grid-cols-2 lg:grid-cols-3  gap-4">
                                <div v-for="order in orders" :key="order.id" class="mb-4 shadow-xl bg-gray-100 p-4 rounded md">
                                    <div class="flex justify-between items-center">
                                        <p>No. {{ order.id }} - {{ order.nombre_comprador }} - Total: ${{ order.total }}</p> 
                                        
                                    </div>
                                    <div id="" class="" v-if="order.marquesitas && order.marquesitas.length">
                                        <p class="font-bold mt-2">Marquesitas:</p>
                                        <ul class="list-disc pl-5">
                                            <li v-for="marquesita in order.marquesitas" :key="marquesita.id">
                                                Precio: ${{ marquesita.precio_marquesita }} ({{ marquesita.cantidad }})
                                                <ul class="list-disc pl-5">
                                                    <div v-if="marquesita.ingredientes.length > 0 ">
                                                        <li v-for="ingrediente in marquesita.ingredientes" :key="ingrediente.id">
                                                            Ingrediente: {{ ingrediente.nombre }}
                                                        </li>
                                                    </div>
                                                    <div v-else>
                                                        <li>
                                                            Sencillas.
                                                        </li>
                                                    </div>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="order.bebidas && order.bebidas.length">
                                        <p class="font-bold mt-2">Bebidas:</p>
                                        <ul class="list-disc pl-5">
                                            <li v-for="bebida in order.bebidas" :key="bebida.id">
                                                {{ bebida.nombre }} - ${{ bebida.precio }} ({{ bebida.cantidad }})
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
