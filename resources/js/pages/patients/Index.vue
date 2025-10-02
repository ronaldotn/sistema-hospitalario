<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import { FlexibleTable, ConfirmModal } from "@/components";

const router = useRouter();
const patientStore = usePatientStore();

// Modal eliminar
const modalOpen = ref(false);
const patientToDelete = ref(null);

const confirmDelete = (uuid) => {
  patientToDelete.value = uuid;
  modalOpen.value = true;
};

const handleDelete = async () => {
  if (!patientToDelete.value) return;
  await patientStore.deletePatient(patientToDelete.value);
  patientToDelete.value = null;
  modalOpen.value = false;
};

// Filtros
const filters = ref({ identifier: "", name: "", _count: 10, _offset: 0 });

const applyFilters = () => {
  filters.value._offset = 0;
  patientStore.fetchPatients(filters.value);
};

// Paginación
const changePage = (page) => {
  filters.value._offset = (page - 1) * filters.value._count;
  patientStore.fetchPatients(filters.value);
};

onMounted(() => patientStore.fetchPatients(filters.value));
</script>

<template>
  <div class="p-6">
    <VCard class="mb-6">
      <VCardTitle class="d-flex justify-space-between align-center">
        <h2>Lista de Pacientes</h2>
        <VBtn color="primary" @click="router.push({ name: 'patients-create' })">Crear Paciente</VBtn>
      </VCardTitle>

      <!-- Filtros -->
      <VCardText class="d-flex gap-4 align-center">
        <VTextField v-model="filters.identifier" label="Documento" outlined dense clearable
          @click:clear="applyFilters" class="w-40" />
        <VTextField v-model="filters.name" label="Nombre/Apellido" outlined dense clearable
          @keyup.enter="applyFilters" @click:clear="applyFilters" class="w-48" />
        <VBtn color="primary" @click="applyFilters">Buscar</VBtn>
      </VCardText>
    </VCard>

    <!-- Tabla -->
 <FlexibleTable :data="patientStore" value="patients"
      :columns="['Nombre', 'DNI', 'Edad', 'Sexo', 'Dirección', 'Contacto', 'Acciones']" title="Sin pacientes"
      text="No hay pacientes registrados" icon="bx-user-x">
      <tr v-for="p in patientStore.patients" :key="p.uuid">
        <td>{{ p.nombre }} {{ p.apellidos }}</td>
        <td>{{ p.documento_identidad }}</td>
        <td>{{ p.edad ?? '-' }}</td>
        <td>{{ p.sexo ?? '-' }}</td>
        <td>{{ p.direccion ?? '-' }}</td>
        <td>{{ p.contacto ?? '-' }}</td>
        <td class="d-flex gap-2">
          <!-- Botón ver paciente -->
          <VBtn icon size="small" color="primary"
            @click="router.push({ name: 'patients-show', params: { uuid: p.uuid } })">
            <VIcon icon="bx-show" />
          </VBtn>

          <!-- Botón editar paciente -->
          <VBtn icon size="small" color="warning"
            @click="router.push({ name: 'patients-edit', params: { uuid: p.uuid } })">
            <VIcon icon="bx-edit" />
          </VBtn>

          <!-- Botón eliminar paciente -->
          <VBtn icon size="small" color="error" @click="confirmDelete(p.uuid)">
            <VIcon icon="bx-trash" />
          </VBtn>
        </td>


      </tr>
    </FlexibleTable>

    <ConfirmModal v-model="modalOpen" type="eliminar" title="Eliminar Paciente"
      message="¿Seguro que deseas eliminar este paciente?" @confirmed="handleDelete" />

    <!-- Paginación simple -->
    <div v-if="patientStore.total > filters._count" class="mt-4 d-flex justify-center gap-2">
      <VBtn v-for="page in Math.ceil(patientStore.total / filters._count)" :key="page" small outlined
        @click="changePage(page)">{{ page }}</VBtn>
    </div>
  </div>
</template>
