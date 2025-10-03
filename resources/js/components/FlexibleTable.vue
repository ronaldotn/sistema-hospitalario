<template>
  <VTable height="520" fixed-header>
    <!-- Encabezado dinámico -->
    <thead>
      <tr>
        <th
          v-for="(col, index) in columns"
          :key="index"
          class="text-uppercase text-left px-4 py-2 bg-gray-100"
        >
          {{ col }}
        </th>
      </tr>
    </thead>

    <!-- Cuerpo de la tabla -->
    <tbody>
      <!-- Loading interno -->
      <tr v-if="props.data.loading">
        <td :colspan="columns.length" class="text-center py-6">
          <VProgressLinear indeterminate color="primary" />
        </td>
      </tr>

      <!-- Sin datos -->
      <tr v-else-if="!displayData.length">
        <td :colspan="columns.length" class="text-center py-6">
          <div class="flex flex-col items-center justify-center gap-2">
            <VIcon :icon="icon" size="36" class="text-gray-400" />
            <h3 class="text-lg font-semibold">{{ title }}</h3>
            <p class="text-gray-500">{{ text }}</p>
          </div>
        </td>
      </tr>

      <!-- Slot para filas dinámicas -->
      <slot v-else></slot>
    </tbody>
  </VTable>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  data: { type: Object, required: true },       // Store completo
  value: { type: String, default: "patients" }, // Nombre del array en el store
  columns: { type: Array, required: true },     // Encabezados
  title: { type: String, default: "Sin datos" },
  text: { type: String, default: "No hay información disponible" },
  icon: { type: String, default: "bx-info-circle" },
});

const displayData = computed(() => props.data[props.value] ?? []);
</script>

<style scoped>
.text-center {
  text-align: center;
}

thead th {
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
  background-color: #f9f9f9;
}

td {
  padding: 0.5rem 1rem;
}

.text-uppercase {
  text-transform: uppercase;
  font-weight: 600;
  font-size: 0.875rem;
}
</style>
