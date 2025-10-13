<script setup>
import { ref } from "vue";

// --- Datos simulados ---
const citas = ref([
  { paciente: "Sofía Rodríguez", hora: "09:00 AM", motivo: "Consulta General", estado: "Confirmada" },
  { paciente: "Carlos López", hora: "10:30 AM", motivo: "Seguimiento", estado: "En Espera" },
  { paciente: "Ana Martínez", hora: "11:45 AM", motivo: "Vacunación", estado: "Completada" },
]);

const pendientes = ref([
  { paciente: "Ricardo García", llegada: "09:15 AM", motivo: "Dolor de garganta" },
  { paciente: "Isabel Fernández", llegada: "10:00 AM", motivo: "Fiebre" },
]);

const alertas = ref([
  {
    color: "error",
    icon: "mdi-flask-outline",
    titulo: "Resultado Crítico",
    descripcion: "Resultado de laboratorio de Sofía Rodríguez requiere atención inmediata.",
    accion: "Ver Resultado",
  },
  {
    color: "warning",
    icon: "mdi-clipboard-check-outline",
    titulo: "Seguimiento",
    descripcion: "Seguimiento pendiente para Carlos López.",
    accion: "Ver Ficha",
  },
  {
    color: "info",
    icon: "mdi-pill",
    titulo: "Prescripción",
    descripcion: "Aprobación de prescripción pendiente para Jorge Diaz.",
    accion: "Revisar",
  },
]);
</script>

<template>
  <v-container fluid class="py-8 px-8 bg-background-light dark:bg-background-dark">
    <!-- 🧠 Encabezado -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
        Panel de Control
      </h1>

      <div class="flex gap-4">
        <v-btn variant="tonal" color="primary" prepend-icon="mdi-magnify">
          Buscar Paciente
        </v-btn>
        <v-btn color="primary" prepend-icon="mdi-plus">
          Registrar Consulta
        </v-btn>
      </div>
    </div>

    <!-- 🧩 Sección principal dividida en dos -->
    <v-row dense>
      <!-- 🔹 Columna izquierda: Citas + Pendientes -->
      <v-col cols="12" xl="8" class="space-y-8">
        <!-- 🩺 Mis Citas -->
        <v-card class="p-6 rounded-xl shadow-md">
          <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
            Mis Citas del Día
          </h3>

          <v-table density="comfortable">
            <thead>
              <tr class="text-sm text-gray-500 border-b border-primary/20">
                <th>Paciente</th>
                <th>Hora</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th class="text-center">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(cita, i) in citas"
                :key="i"
                class="text-gray-700 dark:text-gray-300 border-b border-primary/10"
              >
                <td class="py-3 font-semibold">{{ cita.paciente }}</td>
                <td>{{ cita.hora }}</td>
                <td>{{ cita.motivo }}</td>
                <td>
                  <v-chip
                    :color="
                      cita.estado === 'Confirmada'
                        ? 'success'
                        : cita.estado === 'En Espera'
                        ? 'warning'
                        : 'grey'
                    "
                    size="small"
                    label
                    variant="flat"
                  >
                    {{ cita.estado }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <v-btn
                    v-if="cita.estado === 'En Espera'"
                    size="small"
                    color="primary"
                    rounded="lg"
                  >
                    Iniciar Consulta
                  </v-btn>
                  <v-btn
                    v-else
                    size="small"
                    variant="text"
                    color="primary"
                    class="font-semibold"
                  >
                    Ver Expediente
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>

        <!-- 🧾 Pacientes Pendientes -->
        <v-card class="p-6 rounded-xl shadow-md">
          <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
            Pacientes Pendientes
          </h3>

          <v-table density="comfortable">
            <thead>
              <tr class="text-sm text-gray-500 border-b border-primary/20">
                <th>Paciente</th>
                <th>Hora de Llegada</th>
                <th>Motivo</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(p, i) in pendientes"
                :key="i"
                class="text-gray-700 dark:text-gray-300 border-b border-primary/10"
              >
                <td class="py-3 font-semibold">{{ p.paciente }}</td>
                <td>{{ p.llegada }}</td>
                <td>{{ p.motivo }}</td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>

      <!-- 🔸 Columna derecha: Alertas -->
      <v-col cols="12" xl="4" class="space-y-8">
        <v-card class="p-6 rounded-xl shadow-md space-y-4">
          <h3 class="text-xl font-bold text-gray-800 dark:text-white">
            Alertas y Tareas
          </h3>

          <v-alert
            v-for="(a, i) in alertas"
            :key="i"
            :color="a.color"
            :icon="a.icon"
            border="start"
            variant="outlined"
            class="rounded-lg"
          >
            <p class="font-bold mb-1">{{ a.titulo }}</p>
            <p class="text-sm mb-2">{{ a.descripcion }}</p>
            <v-btn variant="text" color="primary" size="small">
              {{ a.accion }}
            </v-btn>
          </v-alert>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.text-gray-800 {
  color: #1f2937;
}
.dark .text-white {
  color: #ffffff;
}
.space-y-8 > * + * {
  margin-top: 2rem;
}
</style>
