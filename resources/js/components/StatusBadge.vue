<script setup>
import { defineProps } from "vue";

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
});

const statusConfig = {
  activo: { color: "green", icon: "bx-check-circle" },
  inactivo: { color: "red", icon: "bx-x-circle" },
  pendiente: { color: "orange", icon: "bx-time" },
};

const current = statusConfig[props.status] || { color: "gray", icon: "bx-question-circle" };
</script>

<template>
  <div class="d-flex align-center gap-1" :style="{ color: current.color}">
    <VIcon :icon="current.icon"  size="20" />
    <span :style="{  fontWeight: 600 }">
      {{ props.status.toUpperCase() }}
    </span>
  </div>
</template>
<!-- uso de este
<td>
  <StatusBadge :status="p.estado" />
</td> -->
 