<script>
export default {
    name: 'CrearPedido'
}
</script>

<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    bebidas: {
        type: Object,
        required: true
    },
    ingredientes: {
        type: Object,
        required: true
    }
});
const marquesitas = ref([]);
const bebidasSelection = ref([]);
const sumaTotal = ref(0.0);
const error = ref(null);
const selection = ref([]);
const sumaIndividual = ref(40.0);
const metodoPago = ref('Efectivo');
const nombreComprador = ref('');


// Helper function to get a unique identifier for a set of ingredientes
const getIngredientsKey = (ingredientes) => {
    return ingredientes.map(ing => ing.id).sort().join('-');
};

const handleAddToSelection = (ingrediente) => {
    selection.value.push(ingrediente);
    sumaIndividual.value += parseFloat(ingrediente.precio);
};

const handleRemoveSelection = (id) => {
    const index = selection.value.findIndex(item => item.id === id);
    if (index !== -1) {
        sumaIndividual.value -= parseFloat(selection.value[index].precio);
        selection.value.splice(index, 1);
    }
};

const handleAddToSelectionMarquesa = () => {
    const ingredientsKey = getIngredientsKey(selection.value);
    const existingMarquesita = marquesitas.value.find(m => getIngredientsKey(m.ingredientes) === ingredientsKey);

    if (existingMarquesita) {
        existingMarquesita.cantidad++;
        existingMarquesita.precio += sumaIndividual.value;
    } else {
        marquesitas.value.push({
            ingredientes: [...selection.value],
            precio: sumaIndividual.value,
            cantidad: 1
        });
    }

    sumaTotal.value += sumaIndividual.value;
    selection.value = [];
    sumaIndividual.value = 40.0;
};

const handleRemoveMarquesita = (index) => {
    sumaTotal.value -= marquesitas.value[index].precio;
    marquesitas.value.splice(index, 1);
};

const handleAddBebida = (bebida) => {
    const existingBebida = bebidasSelection.value.find(b => b.id === bebida.id);

    if (existingBebida) {
        existingBebida.cantidad++;
    } else {
        bebidasSelection.value.push({
            ...bebida,
            cantidad: 1
        });
    }

    sumaTotal.value += parseFloat(bebida.precio);
};

const handleRemoveBebida = (id) => {
    const index = bebidasSelection.value.findIndex(item => item.id === id);
    if (index !== -1) {
        sumaTotal.value -= parseFloat(bebidasSelection.value[index].precio) * bebidasSelection.value[index].cantidad;
        bebidasSelection.value.splice(index, 1);
    }
};

const handleAddOneItem = (index) => {
    marquesitas.value[index].cantidad++;
    sumaTotal.value += marquesitas.value[index].precio;
};

const handleRemoveOneItem = (index) => {
    if (marquesitas.value[index].cantidad > 1) {
        marquesitas.value[index].cantidad--;
        sumaTotal.value -= marquesitas.value[index].precio;
    } else {
        handleRemoveMarquesita(index);
    }
};

const handleAddOneItemBebida = (index) => {
    bebidasSelection.value[index].cantidad++;
    sumaTotal.value += parseFloat(bebidasSelection.value[index].precio);
};

const handleRemoveOneItemBebida = (index) => {
    if (bebidasSelection.value[index].cantidad > 1) {
        bebidasSelection.value[index].cantidad--;
        sumaTotal.value -= parseFloat(bebidasSelection.value[index].precio);
    } else {
        handleRemoveBebida(bebidasSelection.value[index].id);
    }
}

const pedido = ref({
    marquesitas: [],
    bebidas: [],
    total: 0.0,
    metodo: String
});

const enviarPedido = () => {
    if (marquesitas.value.length === 0 && bebidasSelection.value.length === 0) {
        error.value = 'Debe agregar al menos una marquesita o bebida al pedido.';
        return;
    }

    if (!nombreComprador.value.trim()) {
        error.value = 'El nombre del comprador es requerido.';
        return;
    }

    // Preparar los datos del pedido
    const pedido = {
        nombre_comprador: nombreComprador.value == '' ? 'Usuario' : nombreComprador.value,
        estado: 'Pagado',
        metodo: metodoPago.value,
        total: sumaTotal.value,
        marquesitas: marquesitas.value.map(marquesita => ({
            precio: marquesita.precio,
            ingredientes: marquesita.ingredientes.map(ingrediente => ingrediente.id),
            cantidad: marquesita.cantidad
        })),
        bebidas: bebidasSelection.value.map(bebida => ({
            id: bebida.id,
            nombre: bebida.nombre,
            precio: bebida.precio,
            cantidad: bebida.cantidad
        }))
    };

    // Enviar el pedido
    router.post('/orders', pedido, {
        onSuccess: () => {
            error.value = null;
            console.log('Pedido enviado con éxito');

            marquesitas.value = [];
            bebidasSelection.value = [];
            sumaTotal.value = 0.0;
            error.value = null;
            selection.value = [];
            sumaIndividual.value = 40.0;
            metodoPago.value = 'Efectivo';
            nombreComprador.value = '';
        },
        onError: (errors) => {
            console.error('Error al enviar el pedido:', errors);
        }
    });
};


</script>

<template>
    <div class="flex flex-col p-4">
        
            <div class="flex justify-between">
                <h1 class="text-2xl uppercase font-bold text-sky-800/90 mb-4">Crear pedido</h1>

                <input
                    class="border border-gray-300 rounded-lg p-1 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="text" v-model="nombreComprador" placeholder="Nombre del comprador">
            </div>

            <div class="border flex md:flex-row p-4 gap-8 divide-x md:divide-gray-550 flex-col">
                <!-- Sección de Marquesitas y Bebidas -->
                <div class="flex flex-col flex-1 gap-4">
                    <!-- Marquesitas -->
                    <div class="flex flex-col">
                        <h2 class="font-bold text-lg">Marquesitas</h2>
                        <h3 class="text-sm">Ingredientes</h3>
                        <div class="mt-2">
                            <ul
                                class="list-disc pl-4 mt-1 h-44 overflow-scroll custom-scrollbar divide-y divide-gray-150">
                                <li class="flex justify-between items-center py-1" v-for="ingrediente in ingredientes"
                                    :key="ingrediente.id">
                                    <div>
                                        <p class="font-semibold text-gray-600">{{ ingrediente.nombre }}</p>
                                        <p class="text-sm text-gray-600 pl-3">{{ ingrediente.detalles || ' ' }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <p class="text-green px-4 text-green-700 font-semibold">${{ ingrediente.precio
                                            }}
                                        </p>
                                        <span @click="handleAddToSelection(ingrediente)"
                                            class="px-2 py-1 text-xs font-semibold bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">Añadir</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex divide-gray-200 border p-2 flex-col">
                        <!-- Marquesitas creadas -->
                        <p class="font-bold">Marquesita a crear</p>
                        <ul
                            class="divide-y divide-gray-200 text-base max-h-44 overflow-scroll custom-scrollbar text-gray-600 font-semibold">
                            <li class="flex w-full justify-between items-center py-1" v-for="(item, index) in selection"
                                :key="index" :value="item">
                                <div>
                                    <p>{{ item.nombre }}</p>
                                    <p class="text-xs text-gray-600 pl-3">{{ item.detalles || ' ' }}</p>
                                </div>
                                <span @click="handleRemoveSelection(item.id)"
                                    class="text-sm rounded-lg text-white bg-red-500 py-1 px-2 cursor-pointer">Quitar</span>
                            </li>
                        </ul>
                        <div class="flex w-full items-end py-1 justify-between">
                            <div>
                                <p class="text-sm">Precio por marquesita</p>
                                ${{ sumaIndividual }}
                            </div>
                            <span @click="handleAddToSelectionMarquesa"
                                class="text-sm px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 cursor-pointer">Crear
                                marquesita</span>
                        </div>
                    </div>
                    <!-- Bebidas -->
                    <div class="flex flex-col">
                        <h2 class="font-bold text-lg">Bebidas</h2>
                        <div class="mt-2">
                            <ul
                                class="list-disc pl-4 mt-1 h-48 overflow-scroll custom-scrollbar divide-y divide-gray-150">
                                <li class="flex justify-between items-center py-1" v-for="bebida in bebidas"
                                    :key="bebida.id">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ bebida.nombre }}</p>
                                        <p class="text-sm text-gray-600 pl-3">{{ bebida.detalles }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <p class="text-green px-2 text-green-700 font-semibold">${{ bebida.precio }}</p>
                                        <span @click="handleAddBebida(bebida)"
                                            class="px-2 py-1 bg-blue-500 text-xs text-white rounded hover:bg-blue-600 cursor-pointer">Añadir</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Sección Total Pedido -->
                <div class="flex flex-col flex-1 p-2">
                    <h2 class="font-bold text-2xl">Pedido Total</h2>
                    <div class="pl-3 min-h-[30rem] max-h-[30rem] justify-between overflow-scroll custom-scrollbar">

                        <div class="mb-4">
                            <p class="font-bold text-xl" v-if="marquesitas.length > 0">Marquesitas</p>
                            <ul class="divide-y divide-gray-200 ">
                                <li v-for="(marquesita, index) in marquesitas" :key="index">
                                    <div class="flex justify-between items-center py-1">
                                        <div>
                                            <p class="font-semibold">Marquesita {{ index + 1 }} ({{ marquesita.cantidad
                                                }})
                                            </p>
                                            <ul>
                                                <li v-for="(ingrediente, idx) in marquesita.ingredientes" :key="idx">
                                                    {{ ingrediente.nombre }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="flex flex-col justify-center items-center">
                                            <p class="font-semibold">${{ marquesita.precio * marquesita.cantidad }}</p>
                                            <div class="flex ml-2">
                                                <span v-if="marquesita.cantidad > 1"
                                                    @click="handleRemoveOneItem(index)"
                                                    class="text-xs rounded-2xl text-white bg-red-500 hover:bg-red-600 px-2 py-1 cursor-pointer m-1">-1</span>
                                                <span @click="handleAddOneItem(index)"
                                                    class="text-xs rounded-2xl text-white bg-green-500 hover:bg-green-600 px-2 py-1 cursor-pointer m-1">+1</span>
                                            </div>
                                            <span @click="handleRemoveMarquesita(index)"
                                                class="text-sm rounded-lg text-white bg-red-500 hover:bg-red-600 py-1 px-2 cursor-pointer">Quitar</span>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-bold text-xl" v-if="bebidasSelection.length > 0">Bebidas</p>
                            <ul class="divide-y divide-gray-200">
                                <li v-for="(bebida, index) in bebidasSelection" :key="index">
                                    <div class="flex justify-between items-center py-1">
                                        <div>
                                            <p class="font-semibold">{{ bebida.nombre }} ({{ bebida.cantidad }})</p>
                                            <p class="text-sm text-gray-600 pl-3">{{ bebida.detalles || ' ' }}</p>
                                        </div>
                                        <div class="flex flex-col justify-center items-center">
                                            <p class="font-semibold">${{ bebida.precio * bebida.cantidad }}</p>
                                            <div class="flex ml-2">
                                                <span v-if="bebida.cantidad > 1"
                                                    @click="handleRemoveOneItemBebida(index)"
                                                    class="text-xs rounded-2xl text-white bg-red-500 hover:bg-red-600 px-2 py-1 cursor-pointer m-1">-1</span>
                                                <span @click="handleAddOneItemBebida(index)"
                                                    class="text-xs rounded-2xl text-white bg-green-500 hover:bg-green-600 px-2 py-1 cursor-pointer m-1">+1</span>
                                            </div>
                                            <span @click="handleRemoveBebida(bebida.id)"
                                                class="text-sm rounded-lg text-white bg-red-500 hover:bg-red-600 py-1 px-2 cursor-pointer">Quitar</span>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="">
                            <label for="options" class="block text-sm font-bold text-gray-700">Método de pago</label>
                            <select id="options" v-model="metodoPago"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>

                        <p class="font-bold text-3xl">Total: ${{ sumaTotal }}</p>
                    </div>
                    <div v-if="error" class="text-red-500 font-bold">{{ error }}</div>
                    <button @click.prevent="enviarPedido"
                            class="justify-end items-end mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Crear pedido
                    </button>

                </div>
            </div>
        
    </div>
</template>



<style>
.custom-scrollbar {
    overflow-x: hidden;
    /* Oculta el scroll horizontal */
    overflow-y: auto;
    /* Permite el scroll vertical */
}

.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Para Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}
</style>