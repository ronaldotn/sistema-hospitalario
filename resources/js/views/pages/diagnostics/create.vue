<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { useEncounterStore } from "@/stores/encounter";
import { usePractitionerStore } from "@/stores/practitioner";

const router = useRouter();
const Store = useEncounterStore();
const practitioner = usePractitionerStore();

const form = ref({
  patient_id: "",
  encounter_type: "",
  status: "",
  encounter_date: "",
  reason: "",
  practitioner_id: "",
});

const errors = ref({});
const isLoading = ref(false);
const isLoadingPractitioners = ref(false);
const searchPractitioner = ref("");
const routePrefix = "encounter";

// 🔹 Computed para VSelect
const practitionerOptions = computed(() =>
  practitioner.lookupList.map(item => ({
    title: item.full_name, // viene desde la vista
    value: item.id
  }))
);

// 🔹 Enviar formulario
const submitForm = async () => {
  errors.value = {};
  isLoading.value = true;
  try {
    const payload = { ...form.value };
    if (payload.practitioner_id && typeof payload.practitioner_id === "object") {
      payload.practitioner_id = payload.practitioner_id.value;
    }
    const response = await Store.create(payload);
    if (response) router.push({ name: `${routePrefix}-index` });
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {};
    else errors.value.general = "Error inesperado en el servidor.";
  } finally {
    isLoading.value = false;
  }
};

// 🔹 onMounted → cargar lista inicial
onMounted(async () => {
  isLoadingPractitioners.value = true;
  try {
    await practitioner.lookup();
  } catch (err) {
    console.error("Error cargando practicantes:", err);
  } finally {
    isLoadingPractitioners.value = false;
  }
});

// 🔹 Watch + debounce → búsqueda server-side
let debounceTimer;
watch(searchPractitioner, val => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(async () => {
    if (val.length >= 2 || val.length === 0) {
      isLoadingPractitioners.value = true;
      try {
        await practitioner.lookup(val); // envía search al backend
      } finally {
        isLoadingPractitioners.value = false;
      }
    }
  }, 300); // 300ms debounce
});
</script>

<template>
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Crear Encuentro</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: `${routePrefix}-index` })">Volver</VBtn>
    </VCardTitle>
  </VCard>

  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">
          <!-- ID Paciente -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.patient_id" prepend-inner-icon="bx-user" 
              label="ID Paciente" placeholder="Ej: 123" density="comfortable" required
              :error="!!errors.patient_id" :error-messages="errors.patient_id"/>
          </VCol>

          <!-- Tipo de Encuentro -->
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

          <!-- Fecha / Hora -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.encounter_date" prepend-inner-icon="bx-calendar"
              label="Fecha/Hora" type="datetime-local" density="comfortable"
              :error="!!errors.encounter_date" :error-messages="errors.encounter_date"/>
          </VCol>

          <!-- Practicante con búsqueda server-side -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="form.practitioner_id"
              :items="practitionerOptions"
              v-model:search="searchPractitioner"
              prepend-inner-icon="bx-user-voice"
              label="Profesional Asignado"
              item-title="title"
              item-value="value"
              :loading="isLoadingPractitioners"
              :disabled="isLoadingPractitioners"
              density="comfortable"
              clearable
              required
              :error="!!errors.practitioner_id"
              :error-messages="errors.practitioner_id"
            />
          </VCol>

          <!-- Motivo o Notas -->
          <VCol cols="12">
            <VTextarea v-model="form.reason" prepend-inner-icon="bx-message"
              label="Motivo o Notas" placeholder="Detalle breve del encuentro"
              density="comfortable" :error="!!errors.reason" :error-messages="errors.reason"/>
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn type="reset" color="secondary" variant="tonal" prepend-icon="bx-eraser"
              @click="form = {}">Limpiar</VBtn>
            <VBtn type="submit" color="primary" prepend-icon="bx-save" :loading="isLoading">Guardar</VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
