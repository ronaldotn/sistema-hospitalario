<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import FiltersPanel from "@/views/pages/patients/filter.vue";

const router = useRouter();
const patientStore = usePatientStore();

// 🔹 Estado de filtros
const filters = ref({ identifier: "", name: "" });

// 🔹 Paginación
const currentPage = ref(1);
const totalPages = computed(() => Math.ceil(patientStore.total / patientStore.count));

// 🔹 Cargar pacientes al montar
onMounted(() => {
  patientStore.fetchPatients(filters.value);
});

// 🔹 Aplicar filtros
const applyFilters = (newFilters) => {
  filters.value = { ...newFilters };
  patientStore.offset = 0;
  currentPage.value = 1;
  patientStore.fetchPatients(filters.value);
};

// 🔹 Cambiar página
const changePage = (page) => {
  patientStore.offset = (page - 1) * patientStore.count;
  patientStore.fetchPatients(filters.value);
};

// 🔹 Mantener currentPage sincronizado si cambia offset
watch(
  () => patientStore.offset,
  (newOffset) => {
    currentPage.value = Math.floor(newOffset / patientStore.count) + 1;
  }
);
</script>

<template>
  <!-- Header -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>Lista de Pacientes</h2>
      <VBtn color="primary" @click="router.push({ name: 'patients-create' })">Crear Paciente</VBtn>
    </VCardTitle>
  </VCard>

  <!-- Filtros -->
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
    v-model="currentPage"
    :length="totalPages"
    @update:model-value="changePage"
  />
</template>
