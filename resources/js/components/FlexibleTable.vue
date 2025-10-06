<script setup>
import { ref, computed, watch, onMounted } from "vue";

/**
 * =====================================
 * 📘 FlexibleTable Component
 * =====================================
 * Tabla reutilizable para mostrar datos con:
 *  - Encabezados dinámicos
 *  - Estado de carga
 *  - Estado vacío
 *  - Paginación integrada
 *
 * 👉 Ideal para trabajar con Pinia Stores que tengan:
 *   - Un array de datos
 *   - Un meta con paginación
 *   - Un método de fetch (ej: fetch, fetchPatients, etc.)
 *
 * -------------------------------------
 * 🔹 Props disponibles
 * -------------------------------------
 * | Prop        | Tipo     | Default       | Descripción                                    |
 * |-------------|----------|---------------|------------------------------------------------|
 * | store       | Object   | (requerido)   | Store Pinia con datos, meta y métodos fetch.   |
 * | value       | String   | "model"       | Nombre del array de datos en el store.         |
 * | columns     | Array    | (requerido)   | Encabezados de tabla.                         |
 * | title       | String   | "Sin datos"   | Texto del título cuando no hay información.    |
 * | text        | String   | "No hay..."   | Texto descriptivo si no hay información.       |
 * | icon        | String   | "bx-info..."  | Ícono a mostrar cuando no hay datos.           |
 * | method      | String   | "fetch"       | Método del store para cargar datos.            |
 *
 * -------------------------------------
 * 🔹 Ejemplo de uso
 * -------------------------------------
 *
 * ✅ Caso 1: Store con `fetch()`
 * <FlexibleTable
 *   :store="patientStore"
 *   :columns="['Nombre', 'DNI', 'Edad']"
 *   value="patients"
 * />
 *
 * ✅ Caso 2: Store con `fetchPatients()`
 * <FlexibleTable
 *   :store="patientStore"
 *   method="fetchPatients"
 *   :columns="['Nombre', 'DNI', 'Edad']"
 *   value="patients"
 * />
 *
 * ✅ Caso 3: Otro módulo (ej. practitioners)
 * <FlexibleTable
 *   :store="practitionerStore"
 *   method="fetchPractitioners"
 *   :columns="['Nombre', 'Especialidad', 'Contacto']"
 *   value="practitioners"
 * />
 *
 */

const props = defineProps({
  store: { type: Object, required: true },
  value: { type: String, default: "model" },
  columns: { type: Array, required: true },
  title: { type: String, default: "Sin datos" },
  text: { type: String, default: "No hay información disponible" },
  icon: { type: String, default: "bx-info-circle" },

  // 🔹 Método a invocar dentro del store
  method: { type: String, default: "fetch" },
});

const currentPage = ref(1);
const totalPages = computed(() => props.store.meta?.last_page || 1);

const displayData = computed(() => props.store[props.value] ?? []);

const loadData = (page = 1) => {
  const method = props.store[props.method];
  if (typeof method === "function") {
    method({ page });
    currentPage.value = page;
  } else {
    console.error(`❌ El store no tiene el método "${props.method}"`);
  }
};

const changePage = (page) => loadData(page);

watch(() => props.store.filters, () => loadData(1), { deep: true });
onMounted(() => loadData());
</script>

<template>
  <VCard>
    <!-- ==========================
         🔹 Tabla principal
    =========================== -->
    <VTable height="500" fixed-header class="table-custom">
      <thead>
        <tr>
          <th v-for="(col, index) in columns" :key="index">
            {{ col }}
          </th>
        </tr>
      </thead>

      <tbody>
        <!-- Estado: Loading -->
        <tr v-if="store.loading">
          <td :colspan="columns.length" class="text-center py-6">
            <VProgressLinear indeterminate color="primary" />
          </td>
        </tr>

        <!-- Estado: Sin datos -->
        <tr v-else-if="displayData.length === 0">
          <td :colspan="columns.length" class="text-center py-6">
            <div class="items-center ">
              <VIcon :icon="icon" size="36" class="text-gray-400" />
              <h3 class="text-lg font-semibold">{{ title }}</h3>
              <p class="text-gray-500">{{ text }}</p>
            </div>
          </td>
        </tr>

        <!-- Estado: Datos dinámicos -->
        <slot v-else></slot>
      </tbody>
    </VTable>

    <!-- Separador antes de la paginación -->
    <VDivider thickness="2" class="divider-footer" />

    <!-- Footer de paginación -->
    <VCardActions class="justify-center footer-pagination">
      <VPagination v-if="store.meta?.total > store.meta?.per_page" v-model="currentPage" :length="totalPages"
        @update:model-value="changePage" rounded />
    </VCardActions>
  </VCard>
</template>

