<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";   
import { useEncounterStore } from "@/stores/encounter";

const router = useRouter();
const encounterStore = useEncounterStore();

const form = ref({
  patient_id: null,
  encounter_type: "",
  status: "open",
  encounter_date: "",
  practitioner_id: null,
  reason: ""
});

const errors = ref({});

const submitForm = async () => {
  errors.value = {};
  try {
    const response = await encounterStore.create(form.value);
    if (response) router.push({ name: "encounter-index" });
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {};
  }
};
</script>

<template>
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Crear Encuentro</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: 'encounter-index' })">Volver</VBtn>
    </VCardTitle>
  </VCard>

  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">
          <!-- Paciente -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.patient_id" prepend-inner-icon="bx-user" 
              label="ID Paciente" placeholder="Ej: 123" density="comfortable" required
              :error="!!errors.patient_id" :error-messages="errors.patient_id"/>
          </VCol>

          <!-- Tipo -->
          <VCol cols="12" md="6">
            <VSelect v-model="form.encounter_type" prepend-inner-icon="bx-category" 
              label="Tipo de Encuentro" :items="['Consulta','Emergencia','Hospitalización']"
              density="comfortable" required
              :error="!!errors.encounter_type" :error-messages="errors.encounter_type"/>
          </VCol>

          <!-- Estado -->
          <VCol cols="12" md="6">
            <VSelect v-model="form.status" prepend-inner-icon="bx-check-circle" 
              label="Estado" :items="['open','closed','cancelled']" density="comfortable"
              :error="!!errors.status" :error-messages="errors.status"/>
          </VCol>

          <!-- Fecha -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.encounter_date" prepend-inner-icon="bx-calendar"
              label="Fecha/Hora" type="datetime-local" density="comfortable"
              :error="!!errors.encounter_date" :error-messages="errors.encounter_date"/>
          </VCol>

          <!-- Practicante -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.practitioner_id" prepend-inner-icon="bx-id-card"
              label="ID Practicante" placeholder="Ej: 45" density="comfortable"
              :error="!!errors.practitioner_id" :error-messages="errors.practitioner_id"/>
          </VCol>

          <!-- Motivo -->
          <VCol cols="12" md="12">
            <VTextarea v-model="form.reason" prepend-inner-icon="bx-message"
              label="Motivo o Notas" placeholder="Detalle breve del encuentro"
              density="comfortable" :error="!!errors.reason" :error-messages="errors.reason"/>
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn type="reset" color="secondary" variant="tonal" prepend-icon="bx-eraser">Limpiar</VBtn>
            <VBtn type="submit" color="primary" prepend-icon="bx-save">Guardar</VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
