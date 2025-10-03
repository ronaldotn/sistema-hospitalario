<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import FiltersPanel from "@/views/pages/patients/filter.vue";

const router = useRouter();
const patientStore = usePatientStore();

// 🔹 Estado de filtros
const filters = ref({ identifier: "", name: "" });

// 🔹 Cargar pacientes al montar
onMounted(() => {
  patientStore.fetchPatients(filters.value);
});

// 🔹 Función para aplicar filtros desde Filter.vue
const applyFilters = (newFilters) => {
  filters.value = { ...newFilters };
  patientStore.offset = 0;
  patientStore.fetchPatients(filters.value);
};

// 🔹 Paginación
const changePage = (page) => {
  patientStore.offset = (page - 1) * patientStore.count;
  patientStore.fetchPatients(filters.value);
};
</script>

<template>
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>Lista de Pacientes</h2>
      <div class="d-flex gap-2">
        <VBtn color="primary" @click="router.push({ name: 'patients-create' })">Crear Paciente</VBtn>
      </div>
    </VCardTitle>
  </VCard>

  <!-- Filtros rápidos + drawer -->
  <FiltersPanel v-model="filters" @apply-filters="applyFilters" />

  <!-- Tabla -->
  <FlexibleTable
    :data="patientStore"
    value="patients"
    :columns="['Nombre','DNI','Edad','Sexo','Dirección','Contacto','Acciones']"
    title="Sin pacientes"
    text="No hay pacientes registrados"
    icon="bx-user-x"
  >
    <tr v-for="p in patientStore.patients" :key="p.id">
      <td>{{ p.first_name }} {{ p.last_name }}</td>
      <td>{{ p.identifier }}</td>
      <td>{{ p.age ?? '-' }}</td>
      <td>{{ p.gender ?? '-' }}</td>
      <td>{{ p.address ?? '-' }}</td>
      <td>{{ p.phone ?? '-' }}</td>
      <td class="d-flex gap-2">
        <VBtn icon size="small" color="primary" @click="router.push({ name:'patients-show', params:{uuid:p.id} })">
          <VIcon icon="bx-show" />
        </VBtn>
        <VBtn icon size="small" color="warning" @click="router.push({ name:'patients-edit', params:{uuid:p.id} })">
          <VIcon icon="bx-edit" />
        </VBtn>
        <VBtn icon size="small" color="error">
          <VIcon icon="bx-trash" />
        </VBtn>
      </td>
    </tr>
  </FlexibleTable>

  <!-- Paginación -->
  <VPagination
    v-if="patientStore.total > patientStore.count"
    :model-value="Math.floor(patientStore.offset / patientStore.count) + 1"
    :length="Math.ceil(patientStore.total / patientStore.count)"
    @update:model-value="changePage"
  />
</template>
