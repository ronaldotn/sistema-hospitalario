<script setup>
/**
 * 🔹 Componente: Crear Condición
 * CRUD frontend conectado a la tabla `conditions`
 * Framework: Vue 3 + Vuetify + Pinia + Laravel API
 */

import { ref } from "vue";
import { useRouter } from "vue-router";
import { useConditionStore } from "@/stores/condition"; // store de conditions

const router = useRouter();
const conditionStore = useConditionStore();

/**
 * 🔹 Formulario reactivo
 * Los campos coinciden con la estructura de la tabla `conditions`
 */
const form = ref({
  encounter_id: null,
  patient_id: null,
  code: "",
  description: "",
  recorded_date: "",
});

/**
 * 🔹 Errores de validación
 * Se llenan con los errores 422 del backend Laravel
 */
const errors = ref({});

/**
 * 🔹 Enviar formulario (crear condición)
 */
const submitForm = async () => {
  errors.value = {};
  try {
    const response = await conditionStore.create(form.value);
    if (response) router.push({ name: "conditions-index" }); // redirige al listado
  } catch (err) {
    if (err.response?.status === 422)
      errors.value = err.response.data.errors || {};
  }
};
</script>

<template>
  <!-- ==============================
       CABECERA
  ============================== -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Registrar Condición</h2>
      <VBtn
        prepend-icon="bx-arrow-back"
        color="secondary"
        variant="tonal"
        @click="router.push({ name: 'conditions-index' })"
      >
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- ==============================
       FORMULARIO
  ============================== -->
  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">
          <!-- Encuentro -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.encounter_id"
              prepend-inner-icon="bx-transfer-alt"
              label="ID del Encuentro"
              placeholder="Ej: 101"
              density="comfortable"
              type="number"
              required
              :error="!!errors.encounter_id"
              :error-messages="errors.encounter_id"
            />
          </VCol>

          <!-- Paciente -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.patient_id"
              prepend-inner-icon="bx-user"
              label="ID del Paciente"
              placeholder="Ej: 205"
              density="comfortable"
              type="number"
              required
              :error="!!errors.patient_id"
              :error-messages="errors.patient_id"
            />
          </VCol>

          <!-- Código (ICD-10 o SNOMED) -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.code"
              prepend-inner-icon="bx-barcode"
              label="Código Diagnóstico (ICD-10 / SNOMED)"
              placeholder="Ej: J18.9"
              density="comfortable"
              maxlength="50"
              :error="!!errors.code"
              :error-messages="errors.code"
            />
          </VCol>

          <!-- Fecha registrada -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.recorded_date"
              prepend-inner-icon="bx-calendar"
              label="Fecha registrada"
              type="datetime-local"
              density="comfortable"
              :error="!!errors.recorded_date"
              :error-messages="errors.recorded_date"
            />
          </VCol>

          <!-- Descripción -->
          <VCol cols="12">
            <VTextarea
              v-model="form.description"
              prepend-inner-icon="bx-detail"
              label="Descripción o detalle clínico"
              placeholder="Ej: Infección respiratoria aguda sin complicaciones"
              rows="4"
              density="comfortable"
              :error="!!errors.description"
              :error-messages="errors.description"
            />
          </VCol>

          <!-- Botones de acción -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn
              type="reset"
              color="secondary"
              variant="tonal"
              prepend-icon="bx-eraser"
              @click="form = { encounter_id: null, patient_id: null, code: '', description: '', recorded_date: '' }"
            >
              Limpiar
            </VBtn>

            <VBtn type="submit" color="primary" prepend-icon="bx-save">
              Guardar
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>

<style scoped>
.text-primary {
  color: #1976d2;
}
</style>
