<script setup>
import { ref, onMounted } from "vue";
import { usePractitionerStore } from "@/stores/practitioner";
import ConfirmModal from "@/components/ConfirmModal.vue";
import NoDataTable from "@/components/NoDataTable.vue"; // ✅ Importamos nuestro nuevo componente

const practitionerStore = usePractitionerStore(); // ✅ correcto

// Estado del modal de confirmación
const modalOpen = ref(false);
const practitionerToDelete = ref(null);

// Modal o navegación para crear practitioner
const createPractitioner = () => {
  console.log("Crear nuevo practitioner");
};

// Abrir modal desde botón eliminar
const confirmDelete = (uuid) => {
  practitionerToDelete.value = uuid;
  modalOpen.value = true;
};

// Ejecutar eliminación solo si se confirma
const handleDelete = () => {
  if (!practitionerToDelete.value) return;
  practitionerStore.deletePractitioner(practitionerToDelete.value);
  practitionerToDelete.value = null;
};

onMounted(() => {
  practitionerStore.fetchPractitioners(); // ✅ método correcto
});
</script>

<template>
  <div class="pa-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between">
        <h2>Lista de Pacientes</h2>

        <!-- 🔹 Botón Crear Paciente -->
        <RouterLink to="/practitioners/create">
          <VBtn color="primary" prepend-icon="bx-plus"> Crear Doctores </VBtn>
        </RouterLink>
      </VCardTitle>
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
        <tr v-for="p in practitionerStore.practitioners" :key="p.uuid">
          <td>{{ p.nombre }} {{ p.apellidos }}</td>
          <td>{{ p.especialidad }}</td>
          <td>{{ p.nro_colegiado }}</td>
          <td>{{ p.email }}</td>
          <td>{{ p.telefono }}</td>
          <td>{{ p.estado }}</td>
          <td>
            <VBtn icon size="small" color="info" class="me-2">
              <VIcon icon="bx-edit" />
            </VBtn>
            <VBtn
              icon
              size="small"
              color="error"
              @click="confirmDelete(p.uuid)"
            >
              <VIcon icon="bx-trash" />
            </VBtn>
          </td>
        </tr>
        <!-- Si no hay datos, mostramos un icono o mensaje -->
        <NoDataTable
          :data="practitionerStore.practitioners"
          :cols="7"
          title="Sin Practitioners"
          text="No hay practitioners registrados aún"
          icon="bx-user-x"
        />
      </tbody>
    </VTable>

    <!-- Modal reutilizable -->
    <ConfirmModal
      v-model="modalOpen"
      type="eliminar"
      title="Eliminar Paciente"
      message="¿Seguro que deseas eliminar este paciente?"
      @confirmed="handleDelete"
    />
  </div>
</template>
