<script setup>
import { ref, computed, onMounted } from "vue";
import { usePractitionerStore } from "@/stores/practitioner";
import ConfirmModal from "@/components/ConfirmModal.vue";
import NoDataTable from "@/components/NoDataTable.vue";
import StatusBadge from "@/components/StatusBadge.vue";

const practitionerStore = usePractitionerStore();

// 🔹 Estados de modal
const modalOpen = ref(false);
const practitionerToDelete = ref(null);

// 🔹 Filtros de búsqueda
const search = ref('');
const filterEstado = ref('');
const filterEspecialidad = ref('');

// 🔹 Modal y acciones
const confirmDelete = (uuid) => {
  practitionerToDelete.value = uuid;
  modalOpen.value = true;
};
const handleDelete = () => {
  if (!practitionerToDelete.value) return;
  practitionerStore.deletePractitioner(practitionerToDelete.value);
  practitionerToDelete.value = null;
};

// 🔹 Computed con filtro combinado
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

// 🔹 Especialidades únicas para el select
const uniqueEspecialidades = computed(() => {
  return [...new Set(practitionerStore.practitioners.map(p => p.especialidad))];
});

onMounted(() => {
  practitionerStore.fetchPractitioners();
});
</script>

<template>
  <div class="pa-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between">
        <h2>Lista de Practitioners</h2>
        <RouterLink to="/practitioners/create">
          <VBtn color="primary" prepend-icon="bx-plus"> Crear Practitioner </VBtn>
        </RouterLink>
      </VCardTitle>

      <!-- 🔹 Filtros -->
      <VCardText class="d-flex gap-4">
        <!-- Buscar por nombre/apellido -->
        <VTextField 
          v-model="search" 
          label="Buscar por nombre/apellido" 
          clearable 
          :clear-icon="search ? 'bx-x' : ''" 
        />

        <!-- Filtro por estado -->
         <!-- Solo muestra 'x' si hay valor -->
        <VSelect 
          v-model="filterEstado" 
          :items="['activo', 'inactivo']" 
          label="Estado" 
          :clearable="!!filterEstado"  
        />

        <!-- Filtro por especialidad -->
          <!-- Solo muestra 'x' si hay valor -->
        <VSelect 
          v-model="filterEspecialidad" 
          :items="uniqueEspecialidades" 
          label="Especialidad" 
          :clearable="!!filterEspecialidad"
        />
      </VCardText>
    </VCard>

    <VTable>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Especialidad</th>
          <th>N° Colegiado</th>
          <th>Email</th>
          <th>Telefono</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in filteredPractitioners" :key="p.uuid">
          <td>{{ p.nombre }} {{ p.apellidos }}</td>
          <td>{{ p.especialidad }}</td>
          <td>{{ p.nro_colegiado }}</td>
          <td>{{ p.email }}</td>
          <td>{{ p.telefono }}</td>
          <td>
            <StatusBadge :status="p.estado" />
          </td>
          <td>
            <VBtn icon size="small" color="info" class="me-2">
              <VIcon icon="bx-edit" />
            </VBtn>
            <VBtn icon size="small" color="error" @click="confirmDelete(p.uuid)">
              <VIcon icon="bx-trash" />
            </VBtn>
          </td>
        </tr>

        <!-- Mensaje cuando no hay datos -->
        <NoDataTable 
          :data="filteredPractitioners" 
          :cols="7" 
          title="Sin Practitioners"
          text="No hay practitioners registrados aún" 
          icon="bx-user-x" 
        />
      </tbody>
    </VTable>

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
