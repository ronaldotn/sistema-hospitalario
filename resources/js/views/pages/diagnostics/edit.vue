<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";   
import { useEncounterStore } from "@/stores/encounter";

const route = useRoute();
const router = useRouter();
const encounterStore = useEncounterStore();

const form = ref({});
const errors = ref({});

onMounted(async () => {
  const data = await encounterStore.show(route.params.id);
  if (data) form.value = { ...data };
});

const submitForm = async () => {
  errors.value = {};
  try {
    const response = await encounterStore.update(route.params.id, form.value);
    if (response) router.push({ name: "encounter-index" });
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {};
  }
};
</script>

<template>
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Editar Encuentro</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: 'encounter-index' })">Volver</VBtn>
    </VCardTitle>
  </VCard>

  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">
          <!-- mismos campos que Create -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.patient_id" label="ID Paciente" density="comfortable"/>
          </VCol>
          <VCol cols="12" md="6">
            <VSelect v-model="form.encounter_type" label="Tipo de Encuentro"
              :items="['Consulta','Emergencia','Hospitalización']" density="comfortable"/>
          </VCol>
          <VCol cols="12" md="6">
            <VSelect v-model="form.status" label="Estado"
              :items="['open','closed','cancelled']" density="comfortable"/>
          </VCol>
          <VCol cols="12" md="6">
            <VTextField v-model="form.encounter_date" type="datetime-local" label="Fecha/Hora"/>
          </VCol>
          <VCol cols="12" md="6">
            <VTextField v-model="form.practitioner_id" label="ID Practicante"/>
          </VCol>
          <VCol cols="12" md="12">
            <VTextarea v-model="form.reason" label="Motivo o Notas"/>
          </VCol>

          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn type="submit" color="primary" prepend-icon="bx-save">Actualizar</VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
