<script setup>
import { reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const { props } = usePage();

const ingredientes = ref(props.ingredientes || []);
const bebidas = ref(props.bebidas || []);
const ingredientesInventario = ref(props.ingredientesInventario || []);
const bebidasInventario = ref(props.bebidasInventario || []);
const sucursal = ref(props.sucursal || '');

const form = reactive({
  sucursal_id: sucursal.value,
  ingredientes: ingredientes.value.map(i => ({
    id: i.id,
    nombre: i.nombre,
    cantidad: (ingredientesInventario.value.find(inv => inv.id === i.id)?.cantidad) || 0,
    precio: i.precio || 0
  })),
  bebidas: bebidas.value.map(b => ({
    id: b.id,
    nombre: b.nombre,
    cantidad: (bebidasInventario.value.find(inv => inv.id === b.id)?.cantidad) || 0
  })),
  ingredientesInventario: ingredientesInventario.value.map(i => ({
    id: i.id,
    nombre: i.nombre,
    cantidad: i.cantidad || 0,
    precio: i.precio || 0
  })),
  bebidasInventario: bebidasInventario.value.map(b => ({
    id: b.id,
    nombre: b.nombre,
    cantidad: b.cantidad || 0
  }))
});

const updateCantidad = (id, value, type) => {
  const item = form[type].find(i => i.id === id);
  if (item) {
    item.cantidad = parseInt(value, 10) || 0;
  }
};

const updatePrecio = (id, value) => {
  const item = form.ingredientes.find(i => i.id === id);
  if (item) {
    item.precio = parseFloat(value) || 0;
  }
};

const submit = () => {
  console.log('Datos enviados:', form);  // Depuración
  router.post('/inventario', form, {
    onSuccess: () => {
      console.log('Inventario actualizado con éxito');
    },
    onError: (errors) => {
      console.error('Error al actualizar el inventario:', errors);
    }
  });
};

const agregarIngrediente = () => {
  const nombreInput = document.getElementById('nombre');
  const nombre = nombreInput ? nombreInput.value.trim() : '';

  const precioInput = document.getElementById('precio');
  const precio = precioInput ? precioInput.value.trim() : '';

  if (!nombre || !precio) {
    console.error('El nombre y el precio son requeridos.');
    return;
  }

  const nuevoIngrediente = { nombre: nombre, precio: parseFloat(precio), cantidad: 0, id: Date.now() };

  router.post('/ingredientes', { 
    nombre: nombre,
    precio: parseFloat(precio)
  }, {
    onSuccess: () => {
      console.log('Ingrediente agregado con éxito');
      nombreInput.value = '';
      precioInput.value = '';
      form.ingredientes.push(nuevoIngrediente);
    },
    onError: (errors) => {
      console.error('Error al agregar el ingrediente:', errors);
    }
  });
};

const agregarBebida = () => {
  const nuevaBebida = { nombre: '', cantidad: 0, id: Date.now() };
  form.bebidas.push(nuevaBebida);
  
  router.post('/bebidas', { 
    sucursal_id: form.sucursal_id, 
    ingredientes: [], 
    bebidas: [nuevaBebida]
  }, {
    onSuccess: () => {
      console.log('Bebida agregada con éxito');
    },
    onError: (errors) => {
      console.error('Error al agregar la bebida:', errors);
    }
  });
};

const editarIngrediente = (id) => {
  const ingrediente = form.ingredientes.find(i => i.id === id);
  if (ingrediente) {
    router.put(`/ingredientes/${id}`, ingrediente, {
    onSuccess: () => {
      console.log('Ingrediente actualizado con éxito');
    },
    onError: (errors) => {
      console.error('Error al actualizar el ingrediente:', errors);
    }
  });
  }
};

const editarBebida = (id) => {
  const bebida = form.bebidas.find(b => b.id === id);
  if (bebida) {
    router.put(`/bebidas/${id}`, bebida, {
    onSuccess: () => {
      console.log('Bebida actualizada con éxito');
    },
    onError: (errors) => {
      console.error('Error al actualizar la bebida:', errors);
    }
  });
  }
};

const eliminarIngrediente = (id) => {
  router.delete(`/ingredientes/${id}`, {
    onSuccess: () => {
      form.ingredientes = form.ingredientes.filter(i => i.id !== id);
      console.log('Ingrediente eliminado con éxito');
    },
    onError: (errors) => {
      console.error('Error al eliminar el ingrediente:', errors);
    }
  });
};

const eliminarBebida = (id) => {
  router.delete(`/bebidas/${id}`, {
    onSuccess: () => {
      form.bebidas = form.bebidas.filter(b => b.id !== id);
      console.log('Bebida eliminada con éxito');
    },
    onError: (errors) => {
      console.error('Error al eliminar la bebida:', errors);
    }
  });
};


</script>

<template>
  <AppLayout title="Inventario">
    <template #header>
      <div class="flex justify-between flex-col md:flex-row">
        <div>
          <h1 class="text-xl font-bold">Inventario - Sucursal {{ sucursal }}</h1>
        </div>
      </div>
    </template>

    <div class="py-4">
      <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <h2 class="text-xl font-bold mb-4">Ingredientes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4">
                  <div v-for="ingrediente in form.ingredientes" :key="ingrediente.id" class="bg-white shadow rounded p-4  ">
                    <label class="block">
                      <div class="flex justify-between items-center lg:flex-row flex-col">
                        <span class=" font-light text-sm lg:text-base">{{ ingrediente.nombre }}:</span>
                      </div>
                      
                      <input v-if="$page.props.auth.user.sucursal_id > 0" type="number"
                        v-model.number="form.ingredientes.find(i => i.id === ingrediente.id).cantidad"
                        class="mt-1 block w-full border rounded p-2" :min="0" />
                        <div v-else>
                          <input type="text" v-model.trim="ingrediente.nombre"
                          class="mt-1 block w-full border rounded p-2"  />
                          <input type="number" v-model.number="form.ingredientes.find(i => i.id === ingrediente.id).precio"
           class="mt-1 block w-full border rounded p-2" :min="0" />
                        </div>
                        <div v-if="$page.props.auth.user.sucursal_id == 0" class="flex flex-col gap-1 pt-2 justify-center items-center">
                          <span @click="editarIngrediente(ingrediente.id)"
                            class=" text-center text-xs px-10 py-2 w-fit bg-blue-500 hover:bg-blue-600 text-white  font-bold rounded-lg cursor-pointer">Actualizar</span>
                          <span @click="eliminarIngrediente(ingrediente.id)"
                            class="text-center text-xs px-11 py-2 w-fit bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg cursor-pointer">Eliminar</span>
                        </div>
                    </label>
                  </div>
                  <div v-if="$page.props.auth.user.sucursal_id == 0"
                    class="bg-white shadow rounded p-4 items-center flex ">
                    <label class="block w-full">
                      <span class="font-medium text-gray-500">Nuevo item</span>
                      <input id="nombre" type="text" placeholder="Ingrediente" class="mt-1 block w-full border rounded p-2"/>
                      <input id="precio" type="number" placeholder="$0.0" class="mt-1 block w-full border rounded p-2" min="0"/>
                      <div class="flex justify-center w-full items-center">
                        <div class="flex flex-col gap-1 mt-2">
                          <span @click="agregarIngrediente"
                            class="text-center text-xs px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg cursor-pointer">Agregar</span>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <h2 class="text-xl font-bold">Bebidas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4">
                  <div v-for="bebida in form.bebidas" :key="bebida.id" class="bg-white shadow rounded p-4">
                    <label class="block">
                      <div class="flex justify-between items-center lg:flex-row flex-col">
                        <span class="font-medium">{{ bebida.nombre }}:</span>
                        
                      </div>
                      <input v-if="$page.props.auth.user.sucursal_id > 0" type="number" v-model.number="form.bebidas.find(b => b.id === bebida.id).cantidad"
                        class="mt-1 block w-full border rounded p-2" :min="0" />
                      <input v-else type="text" v-model.trim="bebida.nombre"
                      class="mt-1 block w-full border rounded p-2"  />
                      <div v-if="$page.props.auth.user.sucursal_id == 0" class="flex flex-col gap-1 mt-2 justify-center items-center ">
                          <span @click="editarBebida(bebida.id)"
                            class=" text-center w-fit text-xs px-10 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg cursor-pointer">Actualizar</span>
                          <span @click="eliminarBebida(bebida.id)"
                            class="text-center w-fit text-xs px-11 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg cursor-pointer">Eliminar</span>
                        </div>
                    </label>
                    
                  </div>
                  <div v-if="$page.props.auth.user.sucursal_id == 0"
                    class="bg-white shadow rounded p-4 items-center flex ">
                    <label class="block h-full ">
                      <div class="flex justify-between w-full items-center">
                        <span class="font-medium text-gray-500">Nuevo item</span>
                      </div>
                      <input type="text" placeholder="Bebida" class="mt-1 block w-full border rounded p-2"
                        :min="0" />
                        <div class="flex justify-center items-center w-full mt-2">
                          <span @click="agregarBebida"
                            class=" text-center text-xs px-10 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg cursor-pointer">Agregar</span>
                        </div>
                    </label>
                  </div>
                </div>
              </div>

              <div v-if="$page.props.auth.user.sucursal_id > 0" class="w-full flex justify-end">
                <button type="submit" class="py-2 px-3 rounded-lg bg-orange-500 text-white font-bold">Actualizar
                  Inventario</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>