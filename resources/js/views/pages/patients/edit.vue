<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import FiltersPanel from "@/views/pages/patients/filter.vue";

const router = useRouter();
const patientStore = usePatientStore();

// 🔹 Filtros
const filters = ref({
  identifier: "",
  name: "",
  gender: null,
  maritalStatus: null,
  country: null,
  date_from: null,
  date_to: null,
  age_from: null,
  age_to: null,
  city: "",
  profession: null,
});

const countries = ["Bolivia", "Colombia", "Perú", "Argentina", "Chile", "México"];
const professions = ["Ingeniero", "Doctor", "Abogado", "Profesor", "Estudiante"];

// 🔹 Observador para recargar datos automáticamente al cambiar filtros
watch(filters, (newFilters) => {
  patientStore.offset = 0;
  patientStore.fetchPatients(newFilters);
});

// 🔹 Cargar datos al montar
onMounted(() => {
  patientStore.fetchPatients(filters.value);
});

// 🔹 Cambio de página en paginación
const changePage = (page) => {
  patientStore.offset = (page - 1) * patientStore.count;
  patientStore.fetchPatients(filters.value);
};

// 🔹 Aplicar filtros avanzados desde el componente
const applyFilters = (appliedFilters) => {
  patientStore.offset = 0;
  patientStore.fetchPatients(appliedFilters);
};
</script>

<template>
  <div class="index-page p-6">
    <!-- 🔹 Header -->
    <VCard class="mb-4">
      <VCardTitle class="d-flex justify-space-between align-center">
        <h2>Lista de Pacientes</h2>
        <VBtn color="primary" @click="router.push({ name: 'patients-create' })">
          Crear Paciente
        </VBtn>
      </VCardTitle>
    </VCard>

    <!-- 🔹 Filtros -->
    <FiltersPanel
      v-model="filters"
      :countries="countries"
      :professions="professions"
      @apply="applyFilters"
    />

  <!-- 🔹 Tabla de pacientes -->
  <FlexibleTable 
    :data="patientStore" 
    value="patients" 
    :columns="['Nombre', 'DNI', 'Edad', 'Sexo', 'Dirección', 'Contacto', 'Acciones']"
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
        <VBtn icon size="small" color="primary" @click="router.push({ name: 'patients-show', params: { uuid: p.id } })">
          <VIcon icon="bx-show" />
        </VBtn>
        <VBtn icon size="small" color="warning" @click="router.push({ name: 'patients-edit', params: { uuid: p.id } })">
          <VIcon icon="bx-edit" />
        </VBtn>
        <VBtn icon size="small" color="error">
          <VIcon icon="bx-trash" />
        </VBtn>
      </td>
    </tr>
  </FlexibleTable>

  <!-- 🔹 Paginación -->
  <VPagination
    v-if="patientStore.total > patientStore.count"
    :model-value="Math.floor(patientStore.offset / patientStore.count) + 1"
    :length="Math.ceil(patientStore.total / patientStore.count)"
    @update:model-value="changePage"
  />
  </div>
</template>

<style scoped>
.index-page {
  max-width: 1200px;
  margin: auto;
}

.patients-table th,
.patients-table td {
  border: 1px solid #e0e0e0;
}

.patients-table th {
  background-color: #f7f7f7;
}
</style>
