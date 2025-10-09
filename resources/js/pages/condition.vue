<script setup>
/**
 * 🔹 Componente genérico de lista de Conditions (Condiciones Médicas)
 *    - Conectado al store de Pinia `useConditionStore`
 *    - Columnas, rutas y acciones dinámicas
 *    - Compatible con FlexibleTable y Vuetify
 */

import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import FiltersPanel from "@pages/conditions/filter.vue"; // opcional
import { useConditionStore } from "@/stores/condition"; // store de condiciones

// ===============================
// 🔹 Configuración general
// ===============================
const router = useRouter();
const Store = useConditionStore();
const resourceName = "Condition";
const routePrefix = "conditions";

// ===============================
// 🔹 Columnas de la tabla
// ===============================
const columns = ref([
  "ID",
  "Paciente",
  "Código",
  "Descripción",
  "Fecha registrada",
  "Acciones",
]);

// ===============================
// 🔹 Acciones
// ===============================
const goCreate = () => router.push({ name: `${routePrefix}-create` });
const goShow = (id) => router.push({ name: `${routePrefix}-show`, params: { id } });
const goEdit = (id) => router.push({ name: `${routePrefix}-edit`, params: { id } });


</script>

<template>
  <!-- ============================== 
       Header
  ============================== -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2 class="text-h5 font-weight-bold">Lista de Condiciones Médicas</h2>
      <VBtn color="primary" @click="goCreate()">
        Crear {{ resourceName }}
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- ============================== 
       Panel de filtros (opcional)
  ============================== -->
  <FiltersPanel />

  <!-- ============================== 
       Tabla dinámica
  ============================== -->
  <FlexibleTable :store="Store" :columns="columns" icon="bx-heart">
    <tr v-for="item in Store.model" :key="item.id">
      <td>{{ item.id }}</td>
      <td>{{ item.patient?.last_name || '–' }}</td>
      <td>{{ item.code || '–' }}</td>
      <td>{{ item.description || '–' }}</td>
      <td>{{ item.recorded_date ? new Date(item.recorded_date).toLocaleString() : '–' }}</td>

      <td>
        <!-- Acciones desktop -->
        <div class="d-none d-md-flex gap-2">
          <VBtn icon size="small" color="primary" class="hover-light" @click="goShow(item.id)">
            <VIcon icon="bx-show" />
          </VBtn>
          <VBtn icon size="small" color="warning" class="hover-light" @click="goEdit(item.id)">
            <VIcon icon="bx-edit" />
          </VBtn>
          <VBtn icon size="small" color="error" class="hover-light" @click="Store.remove(item.id)">
            <VIcon icon="bx-trash" />
          </VBtn>
        </div>

        <!-- Acciones móviles -->
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
