<script setup>
/**
 * 🔹 Componente genérico de lista de recursos
 *    - Compatible con cualquier store tipo "BaseResource"
 *    - Columnas, campos y rutas dinámicas según recurso
 *    - Uso de FlexibleTable y Vuetify
 */

import { ref } from "vue";
import { useRouter } from "vue-router";
import FiltersPanel from "@pages/organizations/filter.vue"; // Aquí puedes reemplazar según recurso
import { useOrganizationStore } from "@/stores/organization";

// ===============================
// 🔹 Configuración del recurso
// ===============================
const router = useRouter();
// ===============================
// 🔹 Store genérico
// ===============================
const Store = useOrganizationStore();
// Nombre del recurso para toasts y textos
const resourceName = "Organización";

// Columnas de la tabla visibles
const columns = ref([
  "Nombre",
  "DNI",
  "Especialidad",
  "Email",
  "Teléfono",
  "Activo",
  "Activo",
  "Acciones",
]);
// Prefijo para rutas dinámicas
const routePrefix = "organization";

// ===============================
// 🔹 Funciones de utilidad
// ===============================

// Redirige al crear nuevo registro
const goCreate = () => router.push({ name: `${routePrefix}-create` });

// Redirige a detalle
const goShow = (id) => router.push({ name: `${routePrefix}-show`, params: { id } });

// Redirige a edición
const goEdit = (id) => router.push({ name: `${routePrefix}-edit`, params: { id } });



</script>

<template>
  <!-- ============================== 
   🔹 Header
   ============================== -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>Lista de {{ resourceName }}</h2>
      <VBtn color="primary" @click="goCreate()"> Crear {{ resourceName }} </VBtn>
    </VCardTitle>
  </VCard>

  <!-- ============================== 
   🔹 Panel de filtros (opcional)
   ============================== -->
  <FiltersPanel />

  <!-- ============================== 
   🔹 Tabla flexible
   ============================== -->
  <FlexibleTable
    :store="Store"
    :columns="columns"

    icon="bx-user-x"
  >
<tr v-for="item in Store.model" :key="item.id">
    <td>{{ item.id }}</td> 
    
    <td>{{ item.name }}</td>
    
    <td>{{ item.type }}</td>
    
    <td>{{ item.address }}</td>
    
    <td>{{ item.phone }}</td>
    
    <td>{{ item.email }}</td>
    
    <td>{{ item.created_at }}</td>
<td>
  <!-- ==============================
       Botones visibles en Desktop
  ============================== -->
  <div class="d-none d-md-flex gap-2">
    <VBtn
      icon
      size="small"
      color="primary"
      class="hover-light"
      @click="goShow(item.id)"
    >
      <VIcon icon="bx-show" />
    </VBtn>

    <VBtn
      icon
      size="small"
      color="warning"
      class="hover-light"
      @click="goEdit(item.id)"
    >
      <VIcon icon="bx-edit" />
    </VBtn>

    <VBtn
      icon
      size="small"
      color="error"
      class="hover-light"
      @click="Store.remove(item.id)"
    >
      <VIcon icon="bx-trash" />
    </VBtn>
  </div>

  <!-- ==============================
       Dropdown para móviles
  ============================== -->
  <div class="d-flex d-md-none">
    <VMenu offset-y>
      <template #activator="{ props }">
        <VBtn icon v-bind="props" color="grey-dark" class="hover-light">
          <VIcon icon="bx-dots-vertical" />
        </VBtn>
      </template>
      <VList>
        <VListItem @click="goShow(item.id)" class="hover-light-list">
          <VIcon icon="bx-show" color="primary" class="me-2" /> Ver
        </VListItem>
        <VListItem @click="goEdit(item.id)" class="hover-light-list">
          <VIcon icon="bx-edit" color="warning" class="me-2" /> Editar
        </VListItem>
        <VListItem @click="Store.remove(item.id)" class="hover-light-list">
          <VIcon icon="bx-trash" color="error" class="me-2" /> Eliminar
        </VListItem>
      </VList>
    </VMenu>
  </div>
</td>






</tr>
  </FlexibleTable>
</template>
