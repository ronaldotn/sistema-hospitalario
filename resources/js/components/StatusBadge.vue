<script setup>
import { defineProps, computed } from "vue";

const props = defineProps({
  status: { type: [String, Number], required: true },
});

// 🔹 Configuración de estados
const STATUS_CONFIG = {
  1: { color: "success", icon: "bx-check-circle", label: "Activo" },
  0: { color: "danger", icon: "bx-x-circle", label: "Inactivo" },
  "activo": { color: "success", icon: "bx-check-circle", label: "Activo" },
  "inactivo": { color: "danger", icon: "bx-x-circle", label: "Inactivo" },
  "in-progress": { color: "info", label: "En Progreso" },
  consulta: { color: "success", label: "Completado" },
  emergencia: { color: "danger", label: "Cancelado" },
  hospitalización: { color: "purple", label: "Abierto" },
  closed: { color: "gray", label: "Cerrado" },
};

// 🔹 Computed para estado actual
const current = computed(() => STATUS_CONFIG[props.status] || { color: "muted", label: props.status });
</script>

<template>
  <div class="status-badge" :class="current.color">
    <!-- Solo renderizamos icono si existe -->
    <VIcon v-if="current.icon" :icon="current.icon" size="20" class="status-icon" />
    <span class="status-label">{{ current.label }}</span>
  </div>
</template>

<style scoped lang="scss">
.status-badge {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-weight: 600;
  font-size: 0.95rem;

  /* 🔹 Paleta profesional categorizada */
  /* Aceptables / positivos */
  &.success { color: #43a047; }      // verde: activo, completado
  &.info { color: #1e88e5; }         // azul: in-progress, info
  &.teal { color: #00897b; }         // teal: agendado, telemedicina

  /* 🔹 Precaución / warnings */
  &.warning { color: #fb8c00; }      // naranja: pendiente, seguimiento

  /* 🔹 Negativos / errores */
  &.danger { color: #e53935; }       // rojo: cancelado, inactivo

  /* 🔹 Neutros */
  &.gray { color: #757575; }         // cerrado, neutral
  &.purple { color: #8e24aa; }       // abierto, especial

  /* 🔹 Otros / fallback */
  &.muted { color: #9e9e9e; }        // valor desconocido / fallback

  .status-icon { flex-shrink: 0; }
  .status-label { text-transform: uppercase; }
}
</style>
