<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// Áreas / especialidades
const areas = ref(["Cardiología", "Pediatría", "Traumatología", "General"]);
const selectedArea = ref(null);

// Búsqueda de pacientes
const searchQuery = ref("");

// Citas programadas
const citas = ref([
  { paciente: "Sofía Rodríguez", hora: "9:00 AM", medico: "Dr. Carlos López", estado: "Confirmada" },
  { paciente: "Diego Martínez", hora: "10:30 AM", medico: "Dra. Ana García", estado: "Pendiente" },
  { paciente: "Isabel Torres", hora: "11:45 AM", medico: "Dr. Carlos López", estado: "Confirmada" },
]);

// Pacientes en sala de espera
const salaEspera = ref([
  { paciente: "Juan Pérez", llegada: "8:55 AM", estado: "Esperando" },
  { paciente: "Laura Sánchez", llegada: "10:20 AM", estado: "Esperando" },
]);

// Tareas administrativas
const tareas = ref([
  { texto: "Verificar seguro de salud de nuevos pacientes", done: false },
  { texto: "Gestionar documentos pendientes de pacientes", done: false },
  { texto: "Archivar reportes del día anterior", done: true },
]);

// Filtrado de citas por búsqueda
const citasFiltradas = computed(() => {
  if (!searchQuery.value) return citas.value;
  return citas.value.filter(c =>
    c.paciente.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>

<template>
  <VContainer fluid>
    <!-- Header -->
    <VRow class="mb-6" align="center" justify="space-between">
      <VCol cols="12" md="6">
        <h1 class="text-h4 font-weight-bold">Panel de Control de Recepción</h1>
        <p class="text-body-2 text-medium-emphasis">Bienvenida, gestiona tus tareas del día.</p>
      </VCol>
      <VCol cols="12" md="6" class="d-flex justify-end gap-4">
        <VBtn color="primary" variant="tonal" class="d-flex align-center gap-2">
          <span class="material-symbols-outlined">add_circle</span>
          Registrar Cita
        </VBtn>
        <VBtn color="primary" variant="outlined">Registrar Paciente</VBtn>
      </VCol>
    </VRow>

    <!-- Filtros y select -->
    <VRow class="mb-6" align="center" justify="space-between">
      <VCol cols="12" md="4">
        <VSelect
          v-model="selectedArea"
          :items="areas"
          label="Área/Especialidad"
          dense
        />
      </VCol>
      <VCol cols="12" md="8">
        <VTextField
          v-model="searchQuery"
          label="Buscar Paciente por nombre o ID"
          prepend-inner-icon="search"
          dense
        />
      </VCol>
    </VRow>

    <!-- Citas programadas -->
    <VRow class="mb-8">
      <VCol cols="12">
        <VCard class="rounded-xl">
          <VCardTitle>
            <span class="text-h6 font-weight-bold">Citas Programadas para Hoy</span>
          </VCardTitle>
          <VCardText>
            <VTable dense>
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Hora</th>
                  <th>Médico</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(cita, i) in citasFiltradas" :key="i">
                  <td>{{ cita.paciente }}</td>
                  <td>{{ cita.hora }}</td>
                  <td>{{ cita.medico }}</td>
                  <td>
                    <VChip
                      :color="cita.estado === 'Confirmada' ? 'green' : 'yellow'"
                      :text-color="cita.estado === 'Confirmada' ? 'white' : 'black'"
                      small
                      rounded
                    >{{ cita.estado }}</VChip>
                  </td>
                </tr>
              </tbody>
            </VTable>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Sala de espera y tareas administrativas -->
    <VRow class="mb-8" dense>
      <!-- Sala de Espera -->
      <VCol cols="12" md="6">
        <VCard class="rounded-xl">
          <VCardTitle>
            <span class="text-h6 font-weight-bold">Pacientes en Sala de Espera</span>
          </VCardTitle>
          <VCardText>
            <VTable dense>
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Hora de Llegada</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(p, i) in salaEspera" :key="i">
                  <td>{{ p.paciente }}</td>
                  <td>{{ p.llegada }}</td>
                  <td>
                    <VChip color="blue" text-color="white" small rounded>{{ p.estado }}</VChip>
                  </td>
                </tr>
              </tbody>
            </VTable>
          </VCardText>
        </VCard>
      </VCol>

      <!-- Tareas Administrativas -->
      <VCol cols="12" md="6">
        <VCard class="rounded-xl">
          <VCardTitle>
            <span class="text-h6 font-weight-bold">Tareas Administrativas</span>
          </VCardTitle>
          <VCardText>
            <VCheckbox
              v-for="(tarea, i) in tareas"
              :key="i"
              v-model="tarea.done"
              :label="tarea.texto"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>
