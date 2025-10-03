<template>
  <div class="w-full py-6 flex flex-col items-center justify-center gap-2">
    <!-- Barra de carga -->
    <VProgressLinear
      v-if="data?.loading"
      indeterminate
      color="primary"
      class="w-full"
    />

    <!-- Mensaje si no hay datos -->
    <div
      v-else-if="!computedValue || (isArray && computedValue.length === 0)"
      class="flex flex-col items-center justify-center gap-2"
    >
      <VIcon v-if="icon" :icon="icon" size="36" class="text-gray-400" />
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <p class="text-gray-500">{{ text }}</p>
    </div>

    <!-- Slot simple para datos -->
    <div v-else>
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: { type: Object, required: true },           // La store completa
  value: { type: String, default: '' },             // Nombre de la propiedad en data (ej: 'current')
  title: { type: String, default: "Sin datos" },
  text: { type: String, default: "No hay información disponible" },
  icon: { type: String, default: "bx-search-alt-2" } // Icono por defecto para "sin datos"
})

// Computed dinámico: si se pasa 'value' usamos data[value], si no, usamos data.current
const computedValue = computed(() => {
  if (!props.data) return null
  if (props.value) return props.data[props.value] ?? null
  return props.data.current ?? null
})

// Detectar si es array para mostrar mensaje “sin datos” si está vacío
const isArray = computed(() => Array.isArray(computedValue.value))
</script>
  <!-- Todo el contenido del paciente -->
<!--     <StatusMessage :data="patientStore" value="current" title="Paciente no encontrado"
            text="No se encontró ningún paciente" />
   <template #default>
      <VCard>
         <VCardTitle>{{ patientStore.current.nombre }} {{ patientStore.current.apellidos }}</VCardTitle>
         <VCardText>
          más información 
         </VCardText>
      </VCard>
   </template>
</StatusMessage> -->