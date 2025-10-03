<template>
  <VDialog
    v-model="isOpen"
    persistent
    max-width="400"
    transition="dialog-bottom-transition"
    aria-label="Confirmation dialog"
  >
    <VCard
      class="text-center pa-8 rounded-lg"
      :style="{ '--modal-color': modalColor }"
    >
      <VCardTitle class="text-h5 font-weight-bold mb-2">{{ title }}</VCardTitle>
      <div class="flex justify-center mb-6">
        <VIcon
          v-if="computedIcon"
          :icon="computedIcon"
          :color="modalColor"
          size="64"
        />
      </div>

      <VCardText class="text-body-1 opacity-80">{{ message }}</VCardText>

      <VCardActions class="justify-center mt-6 gap-4">
        <VBtn
          color="grey-darken-3"
          @click="cancel"
          aria-label="Cancel action"
          class="default-button-shadow"
        >
          Cancelar
        </VBtn>
        <VBtn
          :color="modalColor"
          dark
          @click="confirmAction"
          aria-label="Confirm action"
          class="default-button-shadow"
        >
          Confirmar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, computed } from "vue";

const props = defineProps({
  title: { type: String, default: "Confirmación" },
  message: { type: String, default: "¿Estás seguro de continuar?" },
  type: {
    type: String,
    default: "confirmar",
    validator: (v) =>
      ["eliminar", "actualizar", "confirmar", "custom"].includes(v),
  },
  customIcon: { type: String, default: null },
  modelValue: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "confirmed"]);
const isOpen = ref(props.modelValue);

watch(
  () => props.modelValue,
  (v) => (isOpen.value = v)
);
watch(isOpen, (v) => emit("update:modelValue", v));

const computedIcon = computed(() => {
  if (props.type === "eliminar") return "bx-trash";
  if (props.type === "actualizar") return "bx-edit";
  if (props.type === "confirmar") return "bx-check-circle";
  if (props.customIcon) return props.customIcon;
  return "bx-help-circle";
});

const modalColor = computed(() => {
  if (props.type === "eliminar") return "#EF4444";
  if (props.type === "actualizar") return "#3B82F6";
  if (props.type === "confirmar") return "#22C55E";
  return "#1e90ff";
});

const confirmAction = () => {
  emit("confirmed");
  isOpen.value = false;
};

const cancel = () => {
  isOpen.value = false;
};

</script>

<style scoped>
.v-card {
  box-shadow: 0 0 0 2px var(--modal-color) !important;
  transition: box-shadow 0.3s ease-in-out;
}

.default-button-shadow {
  box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2);
  transition: box-shadow 0.3s ease-in-out;
}

.default-button-shadow:hover {
  box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
}

.v-btn.v-btn--active.default-button-shadow {
  box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
}
</style>
<!-- /**
 *! 📄 DOCUMENTACIÓN DE USO DEL COMPONENTE: ConfirmModal
 *
 * Este componente es un modal de confirmación genérico y reutilizable.
 * Se puede adaptar a diferentes tipos de acciones (eliminar, actualizar, etc.)
 * y gestiona su propia lógica interna de comportamiento.
 *
 * 🚀 PRÁCTICAS RECOMENDADAS:
 * - Utilizar siempre `v-model` para controlar la visibilidad.
 * - Usar la prop `type` para manejar estados predefinidos.
 *
 *
 * 📋 PROPS DISPONIBLES
 * | Propiedad     | Tipo       | Valor por defecto  | Descripción                                                  |
 * |---------------|------------|--------------------|--------------------------------------------------------------|
 * | `title`       | `String`   | 'Confirmación'     | El título que se muestra en la parte superior del modal.     |
 * | `message`     | `String`   | '¿Estás seguro...?'| El mensaje principal que describe la acción.                 |
 * | `type`        | `String`   | 'confirmar'        | Define el estilo (ícono y color) del modal. Valores: `eliminar`, `actualizar`, `confirmar`, `custom`. |
 * | `customIcon`  | `String`   | `null`             | Un ícono personalizado para la acción de tipo 'custom'.      |
 * | `modelValue`  | `Boolean`  | `false`            | Controla si el modal está abierto o cerrado (`v-model`).     |
 *
 *
 * 📣 EVENTOS DISPONIBLES
 * | Evento         | Descripción                                              |
 * |----------------|----------------------------------------------------------|
 * | `update:modelValue` | Emite el estado de visibilidad del modal para `v-model`. |
 * | `confirmed`      | Se emite cuando el usuario hace clic en 'Confirmar'.     |
 *
 *
 * 💡 EJEMPLO DE USO EN UN COMPONENTE PADRE
 *
 * ```html
 * <template>
 * <VBtn @click="modalOpen = true">Abrir Modal de Confirmación</VBtn>
 * <ConfirmModal
 * v-model="modalOpen"
 * title="Eliminar Elemento"
 * message="¿Estás seguro que quieres eliminar este elemento de forma permanente?"
 * type="eliminar"
 * @confirmed="handleDeleteItem"
 * />
 * </template>
 *
 * <script setup>
 * import { ref } from 'vue'
 * import ConfirmModal from './ConfirmModal.vue' // Asegúrate de importar la ruta correcta
 *
 * const modalOpen = ref(false)
 *
 * const handleDeleteItem = () => {
 * // Aquí va la lógica para eliminar el elemento
 * console.log('¡Elemento eliminado!')
 * }
 * </script>
 * ```
 */ -->