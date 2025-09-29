<script setup>
import { ref, computed, onMounted } from "vue";
import { usePractitionerStore } from "@/stores/practitioner";
import { FlexibleTable, ConfirmModal, StatusBadge } from "@/components";

const practitionerStore = usePractitionerStore();

// Modal de confirmación
const modalOpen = ref(false);
const practitionerToDelete = ref(null);
const confirmDelete = (uuid) => {
  practitionerToDelete.value = uuid;
  modalOpen.value = true;
};
const handleDelete = () => {
  if (!practitionerToDelete.value) return;
  practitionerStore.deletePractitioner(practitionerToDelete.value);
  practitionerToDelete.value = null;
  modalOpen.value = false;
};

// Filtros
const search = ref("");
const filterEstado = ref("");
const filterEspecialidad = ref("");

// Computed filtrado
const filteredPractitioners = computed(() => {
  return practitionerStore.practitioners.filter(p => {
    const matchesSearch =
      p.nombre.toLowerCase().includes(search.value.toLowerCase()) ||
      p.apellidos.toLowerCase().includes(search.value.toLowerCase());
    const matchesEstado = filterEstado.value ? p.estado === filterEstado.value : true;
    const matchesEspecialidad = filterEspecialidad.value ? p.especialidad === filterEspecialidad.value : true;
    return matchesSearch && matchesEstado && matchesEspecialidad;
  });
});

// Opciones únicas de especialidades
const uniqueEspecialidades = computed(() => [...new Set(practitionerStore.practitioners.map(p => p.especialidad))]);

onMounted(() => practitionerStore.fetchPractitioners());
</script>

<template>
  <div class="pa-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between">
        <h2>Lista de Practitioners</h2>
        <RouterLink to="/practitioners/create">
          <VBtn color="primary" prepend-icon="bx-plus"> Crear </VBtn>
        </RouterLink>
      </VCardTitle>

      <!-- Filtros -->
      <VCardText class="d-flex gap-4">
        <VTextField v-model="search" label="Buscar nombre/apellido" clearable />
        <VSelect v-model="filterEstado" :items="['activo','inactivo']" label="Estado" clearable />
        <VSelect v-model="filterEspecialidad" :items="uniqueEspecialidades" label="Especialidad" clearable />
      </VCardText>
    </VCard>

    <!-- FlexibleTable -->
    <FlexibleTable
      :data="practitionerStore"
      value="practitioners"
      :columns="['Nombre', 'Especialidad', 'N° Colegiado', 'Email', 'Telefono', 'Estado', 'Acciones']"
      title="Sin Practitioners"
      text="No hay practitioners registrados aún"
      icon="bx-user-x"
    >
      <tr v-for="p in filteredPractitioners" :key="p.uuid">
        <td>{{ p.nombre }} {{ p.apellidos }}</td>
        <td>{{ p.especialidad }}</td>
        <td>{{ p.nro_colegiado }}</td>
        <td>{{ p.email }}</td>
        <td>{{ p.telefono }}</td>
        <td><StatusBadge :status="p.estado" /></td>
        <td>
          <VBtn icon size="small" color="info"><VIcon icon="bx-edit" /></VBtn>
          <VBtn icon size="small" color="error" @click="confirmDelete(p.uuid)">
            <VIcon icon="bx-trash" />
          </VBtn>
        </td>
      </tr>
    </FlexibleTable>

    <!-- Modal de confirmación -->
    <ConfirmModal
      v-model="modalOpen"
      type="eliminar"
      title="Eliminar Practitioner"
      message="¿Seguro que deseas eliminar este practitioner?"
      @confirmed="handleDelete"
    />
  </div>
</template>
