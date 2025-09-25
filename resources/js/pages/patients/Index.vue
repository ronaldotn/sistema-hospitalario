<script setup>
import { ref, onMounted } from "vue";
import { usePatientStore } from "@/stores/patient";
import ConfirmModal from "@/components/ConfirmModal.vue";

const patientStore = usePatientStore();

// Estado del modal de confirmación
const modalOpen = ref(false);
const patientToDelete = ref(null);

// Modal o navegación para crear paciente
const createPatient = () => {
  // 👉 Aquí decides: abrir modal, navegar a una ruta, etc.
  // Ejemplo: router.push({ name: 'patient-create' })
  console.log("Crear nuevo paciente");
};

// Abrir modal desde botón eliminar
const confirmDelete = (uuid) => {
  patientToDelete.value = uuid;
  modalOpen.value = true;
};

// Ejecutar eliminación solo si se confirma
const handleDelete = () => {
  if (!patientToDelete.value) return;
  patientStore.deletePatient(patientToDelete.value);
  patientToDelete.value = null;
};

onMounted(() => {
  patientStore.fetchPatients();
});
</script>

<template>
  <div class="pa-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between">
        <h2>Lista de Pacientes</h2>

        <!-- 🔹 Botón Crear Paciente -->
        <RouterLink to="/patients/create">
          <VBtn color="primary" prepend-icon="bx-plus"> Crear Paciente </VBtn>
        </RouterLink>
      </VCardTitle>
    </VCard>

    <VTable>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Dni</th>
          <th>Edad</th>
          <th>Sexo</th>
          <th>Dirección</th>
          <th>Contacto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in patientStore.patients" :key="p.uuid">
          <td>{{ p.nombre }} {{ p.apellidos }}</td>
          <td>{{ p.documento_identidad }}</td>
          <td>{{ p.edad }}</td>
          <td>{{ p.sexo }}</td>
          <td>{{ p.direccion }}</td>
          <td>{{ p.contacto }}</td>
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
