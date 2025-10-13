<template>
  <!-- Card de filtros rápidos -->
  <VCard class="mb-4 pa-3 d-flex flex-wrap gap-4 justify-space-between align-center elevation-2">
    <!-- Select registros por página -->
    <VSelect v-model="perPage" :items="[10, 25, 50, 100]" label="Page" @update:model-value="changePerPage"
      style="min-width:95px; max-width:90px" />
    <!-- Filtros rápidos -->
    <div class="d-flex flex-wrap gap-3 align-center flex-grow-1 justify-start ">
      <VTextField v-model="quickFilters.first_name" label="Nombre" placeholder="John" prepend-inner-icon="bx-user"
        density="comfortable" clearable style="min-width:180px; max-width:250px" />
      <VTextField v-model="quickFilters.last_name" label="Apellido" placeholder="Doe" prepend-inner-icon="bx-user"
        density="comfortable" clearable style="min-width:180px; max-width:250px" />
      <VSelect v-model="quickFilters.specialty" :items="Store.specialties" label="Especialidad"
        placeholder="Selecciona especialidad" clearable style="min-width:180px; max-width:250px" />

      <VSelect v-model="quickFilters.active" :items="[{ title: 'Activo', value: 1 }, { title: 'Inactivo', value: 0 }]"
        label="Estado" clearable style="min-width:120px; max-width:150px" />
      <VBtn color="primary" height="40" @click="applyQuickFilters">Filtrar</VBtn>
    </div>
  </VCard>


</template>

<script setup>
import { ref } from "vue";
import { usePractitionerStore } from "@/stores/practitioner";
const Store = usePractitionerStore();
const perPage = ref(Store.meta.per_page || 10);

// Filtros rápidos
const quickFilters = ref({});

// Filtros avanzados (solo un objeto reactivo, vacío al inicio)
const advancedFilters = ref({});

// Funciones
const applyQuickFilters = () => {
  // Envía solo filtros rápidos al backend al presionar el botón
  Store.fetch({ ...quickFilters.value, _page: 1, _count: perPage.value });
};

const changePerPage = (value) => {
  perPage.value = value;
  Store.fetch({ _page: 1, _count: perPage.value });
};
</script>
