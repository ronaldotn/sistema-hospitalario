<script setup>
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import FiltersPanel from "@/views/pages/patients/filter.vue";

const router = useRouter();
const patientStore = usePatientStore();
</script>

<template>
  <!-- ============================== 
   🔹 Header 
   ============================== -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>Lista de Pacientes</h2>
      <VBtn color="primary" @click="router.push({ name: 'patients-create' })"> Crear Paciente </VBtn>
    </VCardTitle>
  </VCard>
  <FiltersPanel />

  <FlexibleTable :store= "patientStore" value="patients" method="fetchPatients"
    :columns="['Nombre', 'DNI', 'Edad', 'Sexo', 'Dirección', 'Contacto', 'Acciones']" title="Sin pacientes"
    text="No hay pacientes registrados" icon="bx-user-x">

      <tr v-for="p in patientStore.patients" :key="p.id">
        <td>{{ p.first_name }} {{ p.last_name }}</td>
        <td>{{ p.identifier }}</td>
        <td>{{ p.age ?? '-' }}</td>
        <td>{{ p.gender ?? '-' }}</td>
        <td>{{ p.address ?? '-' }}</td>
        <td>{{ p.phone ?? '-' }}</td>
        <td class="d-flex gap-2 ">
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
</template>
