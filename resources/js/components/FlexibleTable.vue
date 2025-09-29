<template>
  <VTable>
    <!-- Encabezado dinámico -->
    <thead>
      <tr>
        <th v-for="(col, index) in columns" :key="index">
          {{ col }}
        </th>
      </tr>
    </thead>

    <!-- Cuerpo de la tabla -->
    <tbody>
      <!-- Loading interno (opcional, puede manejarse fuera) -->
      <tr v-if="props.data.loading">
        <td :colspan="columns.length" class="text-center py-6">
          <VProgressLinear indeterminate color="primary" />
        </td>
      </tr>

      <!-- Sin datos interno (opcional) -->
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

// Props
const props = defineProps({
  data: { type: Object, required: true },       // Store completo
  value: { type: String, default: "patients" }, // Nombre del array en el store
  columns: { type: Array, required: true },     // Array de strings de encabezados
  title: { type: String, default: "Sin datos" },
  text: { type: String, default: "No hay información disponible" },
  icon: { type: String, default: "bx-info-circle" },
  filters: { type: Object, default: null },    // Opcional
});

// Array dinámico de datos
const displayData = computed(() => props.data[props.value] ?? []);
</script>

<style scoped>
.text-center {
  text-align: center;
}
</style>
<!-- <FlexibleTable
  :data="patientStore"
  value="patients"
  :columns="['Nombre', 'DNI', 'Edad', 'Sexo', 'Dirección', 'Contacto', 'Acciones']"
  title="Sin pacientes"
  text="No hay pacientes registrados aún"
  icon="bx-user-x"
>
  Filas manuales con control completo 
  <tr v-for="p in patientStore.patients" :key="p.uuid">
    <td>{{ p.nombre }} {{ p.apellidos }}</td>
    <td>{{ p.documento_identidad }}</td>
    <td>{{ p.edad ?? '-' }}</td>
    <td>{{ p.sexo ?? '-' }}</td>
    <td>{{ p.direccion ?? '-' }}</td>
    <td>{{ p.contacto ?? '-' }}</td>
    <td>
      <VBtn icon size="small" color="error" @click="patientStore.deletePatient(p.uuid, filters.value)">
        <VIcon icon="bx-trash" />
      </VBtn>
    </td>
  </tr>
</FlexibleTable> -->
