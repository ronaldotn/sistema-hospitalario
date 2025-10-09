<template>
  <!-- Card de filtros rápidos -->
  <VCard class="mb-4 pa-3 d-flex flex-wrap gap-4 justify-space-between align-center elevation-2">
    
    <!-- Botón filtros avanzados -->
    <VBtn icon color="secondary" @click="filtersDrawerOpen = true">
      <VIcon icon="bx-filter-alt" size="24" />
    </VBtn>

    <!-- Select registros por página -->
    <VSelect 
      v-model="perPage" 
      :items="[10,25,50,100]" 
      label="Page" 
      @update:model-value="changePerPage"
      style="min-width:95px; max-width:90px" 
    />

    <!-- Filtros rápidos -->
    <div class="d-flex flex-wrap gap-3 align-center flex-grow-1 justify-start ">
      <VTextField v-model="quickFilters.identifier" label="Documento" placeholder="12345678"
                  prepend-inner-icon="bx-id-card" density="comfortable" clearable style="min-width:180px; max-width:250px"/>
      <VTextField v-model="quickFilters.first_name" label="Nombre" placeholder="John"
                  prepend-inner-icon="bx-user" density="comfortable" clearable style="min-width:180px; max-width:250px"/>
      <VTextField v-model="quickFilters.last_name" label="Apellidos" placeholder="Doe"
                  prepend-inner-icon="bx-user" density="comfortable" clearable style="min-width:180px; max-width:250px"/>
      <VBtn color="primary" height="40" @click="applyQuickFilters">Filtrar</VBtn>
    </div>
  </VCard>

  <!-- Drawer filtros avanzados -->
  <VNavigationDrawer v-model="filtersDrawerOpen" location="right" temporary width="400" class="bg-white shadow-lg rounded-l-lg">
    <VToolbar flat class="px-6 py-4 border-b border-gray-200">
      <VToolbarTitle class="text-h6 font-semibold text-gray-800">Filtros Avanzados</VToolbarTitle>
      <VSpacer />
      <VBtn icon variant="text" color="gray" @click="filtersDrawerOpen = false">
        <VIcon icon="bx-x" size="24" />
      </VBtn>
    </VToolbar>

    <VCard flat class="px-6 py-4">
      <h3 class="text-subtitle-1 font-medium mb-3">Datos Personales</h3>
      <VRow class="mb-4 gap-4" dense>
        <!-- Aquí cada input dinámicamente crea su propiedad -->
        <VCol cols="12"><VTextField v-model="advancedFilters.identifier" label="Documento" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.first_name" label="Nombre" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.last_name" label="Apellidos" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.date_of_birth" label="Fecha de nacimiento" type="date" /></VCol>
        <VCol cols="12"><VSelect v-model="advancedFilters.gender" :items="['Masculino','Femenino']" label="Sexo" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.address" label="Dirección" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.phone" label="Teléfono" /></VCol>
        <VCol cols="12"><VTextField v-model="advancedFilters.email" label="Email" type="email" /></VCol>
        <VCol cols="12" class="d-flex  justify-end">
          <VBtn color="primary" @click="applyAdvancedFilters">Aplicar filtros</VBtn>
        </VCol>
      </VRow>

    
      
    </VCard>
  </VNavigationDrawer>
</template>

<script setup>
import { ref } from "vue";
import { usePatientStore } from "@/stores/patient";

const patientStore = usePatientStore();
const filtersDrawerOpen = ref(false);
const perPage = ref(patientStore.meta.per_page || 10);

// Filtros rápidos
const quickFilters = ref({});

// Filtros avanzados (solo un objeto reactivo, vacío al inicio)
const advancedFilters = ref({});

// Funciones
const applyQuickFilters = () => {
  // Envía solo filtros rápidos al backend al presionar el botón
  patientStore.fetchPatients({ ...quickFilters.value, _page:1, _count: perPage.value });
};

const applyAdvancedFilters = () => {
  // Merge filtros rápidos + avanzados
  const mergedFilters = { ...quickFilters.value, ...advancedFilters.value };
  patientStore.fetchPatients({ ...mergedFilters, _page:1, _count: perPage.value });
};
const changePerPage = (value) => {
  perPage.value = value;
  patientStore.fetchPatients({ _page:1, _count: perPage.value });
};
</script>
