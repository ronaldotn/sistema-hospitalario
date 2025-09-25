<script setup>
import { onMounted } from "vue";
import { usePatientStore } from "@/stores/patient";

const patientStore = usePatientStore();

onMounted(() => {
  patientStore.fetchPatients(); // solo llamamos al store
});
</script>

<template>
  <div class="pa-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between">
        <h2>Lista de Pacientes</h2>
        <VBtn
          color="primary"
          @click="
            addPatient({
              /* datos */
            })
          "
        >
          <VIcon start icon="bx-plus" />
          Agregar Paciente
        </VBtn>
      </VCardTitle>
    </VCard>

    <VTable>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Dni</th>
          <th>Edad</th>
          <th>Sexo</th>
          <th>Direccion</th>
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
            <VBtn
              icon
              size="small"
              color="info"
              @click="editPatient(patient.uuid)"
              class="me-2"
            >
              <VIcon icon="bx-edit" />
            </VBtn>
            <VBtn
              icon
              size="small"
              color="error"
              @click="deletePatient(patient.uuid)"
            >
              <VIcon icon="bx-trash" />
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>
  </div>
</template>
