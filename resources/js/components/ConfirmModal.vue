<template>
  <VDialog
    v-model="isOpen"
    persistent
    max-width="420"
    transition="dialog-bottom-transition"
    aria-label="Confirmation dialog"
  >
    <VCard
      class="pa-8 rounded-xl elevation-10"
      :style="{ borderTop: `6px solid #B00020` }"
    >
      <div class="d-flex flex-column align-center mb-6 text-center">
        <VIcon
          icon="mdi-trash-can-outline"
          color="error"
          size="72"
          class="mb-4 animate__animated animate__zoomIn"
        />

        <VCardTitle class="text-h5 font-weight-bold mb-1">
          Eliminar Paciente
        </VCardTitle>
        
        <VCardText class="text-body-1 text-medium-emphasis">
          ¿Seguro que deseas eliminar este paciente? Esta acción no se puede deshacer.
        </VCardText>
      </div>

      <VCardActions class="justify-center mt-4 ga-4">
        <VBtn
          color="secondary"
          variant="outlined"
          rounded="xl"
          @click="cancel"
          aria-label="Cancel action"
          class="flex-grow-1"
        >
          Cancelar
        </VBtn>

        <VBtn
          color="error"
          rounded="xl"
          @click="confirmAction"
          aria-label="Confirm action"
          class="flex-grow-1"
        >
          Eliminar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue'

const props = defineProps({
  // Título y mensaje del modal. Puedes pasarlos desde el componente padre.
  title: {
    type: String,
    default: 'Eliminar Paciente'
  },
  message: {
    type: String,
    default: '¿Seguro que deseas eliminar este paciente?'
  },
  // La prop 'modelValue' es la que controla si el modal está abierto o cerrado.
  modelValue: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'confirmed'])
const isOpen = ref(props.modelValue)

// Usamos 'watch' para sincronizar el estado interno del modal (isOpen)
// con el valor que le pasas desde el componente padre (modelValue).
watch(() => props.modelValue, v => (isOpen.value = v))
watch(isOpen, v => emit('update:modelValue', v))

// Función que se ejecuta al confirmar la acción.
const confirmAction = () => {
  // Emitimos un evento llamado 'confirmed' para que el componente padre
  // sepa que la acción debe ejecutarse.
  emit('confirmed')
  // Cerramos el modal.
  isOpen.value = false
}

// Función para cancelar. Simplemente cierra el modal.
const cancel = () => {
  isOpen.value = false
}
</script>

<style scoped>
/* Animaciones para una entrada más dinámica */
.animate__animated {
  animation-duration: 0.5s;
}
@keyframes zoomIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate__zoomIn {
  animation-name: zoomIn;
}
</style>