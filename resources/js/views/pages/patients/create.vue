<script setup>
import { ref, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import { useAppStore } from "@/stores/load";

const router = useRouter();
const patientStore = usePatientStore();
const appStore = useAppStore();

// Estado del formulario
const form = ref({
  first_name: '',
  last_name: '',
  identifier: '',
  date_of_birth: '',
  gender: '',
  address: '',
  phone: '',
  email: ''
});

// Errores de validación
const errors = ref({});
const duplicateError = ref('');

// Clave para autoguardado en localStorage
const STORAGE_KEY = "patient_form";

// 🔹 Cargar datos guardados al montar
onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY);
  if (saved) form.value = JSON.parse(saved);
});

// 🔹 Autoguardado cada vez que cambia el formulario
watch(form, (newVal) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal));
}, { deep: true });

// Función submit
const submitForm = async () => {
  errors.value = {};
  duplicateError.value = '';
  try {
    const response = await patientStore.createPatient(form.value);
    if (response) {
      localStorage.removeItem(STORAGE_KEY); // limpiar autoguardado
      router.push({ name: "patients-index" });
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    } else if (err.response?.status === 409) {
      duplicateError.value = err.response.data.message || "Documento duplicado";
    }
  }
};
</script>

<template>
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Crear Paciente</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: 'patients-index' })">
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">

          <!-- Nombre -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.first_name" 
              prepend-inner-icon="bx-user" 
              label="Nombre"
              aria-label="Nombre del paciente"
              placeholder="John"
              density="comfortable" 
              required
              :error="!!errors.first_name"
              :error-messages="errors.first_name"
            />
          </VCol>

          <!-- Apellidos -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.last_name" 
              prepend-inner-icon="bx-user" 
              label="Apellidos"
              aria-label="Apellidos del paciente"
              placeholder="Doe"
              density="comfortable" 
              required
              :error="!!errors.last_name"
              :error-messages="errors.last_name"
            />
          </VCol>

          <!-- Documento -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.identifier" 
              prepend-inner-icon="bx-id-card"
              label="Documento de Identidad"
              aria-label="Documento de Identidad del paciente"
              placeholder="12345678" 
              density="comfortable" 
              required
              :error="!!errors.identifier || !!duplicateError"
              :error-messages="errors.identifier || duplicateError"
            />
          </VCol>

          <!-- Fecha de nacimiento -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.date_of_birth" 
              prepend-inner-icon="bx-calendar"
              label="Fecha de Nacimiento" 
              aria-label="Fecha de Nacimiento"
              type="date" 
              density="comfortable" 
              required
              :error="!!errors.date_of_birth"
              :error-messages="errors.date_of_birth"
            />
          </VCol>

          <!-- Sexo -->
          <VCol cols="12" md="6">
            <VSelect 
              v-model="form.gender" 
              prepend-inner-icon="bx-gender-male-female" 
              label="Sexo" 
              aria-label="Sexo del paciente"
              :items="[
                { title: 'Masculino', value: 'male' },
                { title: 'Femenino', value: 'female' },
                { title: 'Otro', value: 'other' },
                { title: 'Desconocido', value: 'unknown' }
              ]" 
              item-title="title" 
              item-value="value" 
              density="comfortable" 
              required
              :error="!!errors.gender"
              :error-messages="errors.gender"
            />
          </VCol>

          <!-- Campos opcionales colapsables -->
          <VCol cols="12">
            <VExpansionPanels multiple>
              <VExpansionPanel>
                <VExpansionPanelTitle>Opcionales</VExpansionPanelTitle>
                <VExpansionPanelText>
                  <VRow :gap="4">
                    <VCol cols="12" md="6">
                      <VTextField 
                        v-model="form.address" 
                        prepend-inner-icon="bx-map" 
                        label="Dirección"
                        placeholder="Calle 123, Ciudad" 
                        density="comfortable"
                        :error="!!errors.address"
                        :error-messages="errors.address"
                      />
                    </VCol>
                    <VCol cols="12" md="6">
                      <VTextField 
                        v-model="form.phone" 
                        prepend-inner-icon="bx-phone" 
                        label="Teléfono"
                        placeholder="+591 7 1234567" 
                        density="comfortable"
                        :error="!!errors.phone"
                        :error-messages="errors.phone"
                      />
                    </VCol>
                    <VCol cols="12" md="6">
                      <VTextField 
                        v-model="form.email" 
                        prepend-inner-icon="bx-envelope" 
                        label="Correo"
                        placeholder="correo@ejemplo.com" 
                        type="email" 
                        density="comfortable"
                        :error="!!errors.email"
                        :error-messages="errors.email"
                      />
                    </VCol>
                  </VRow>
                </VExpansionPanelText>
              </VExpansionPanel>
            </VExpansionPanels>
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn type="reset" color="secondary" variant="tonal" prepend-icon="bx-eraser"
              @click="form = { first_name: '', last_name: '', identifier: '', date_of_birth: '', gender: '', address: '', phone: '', email: '' }">
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
