<script setup>
/* ============================================================
   🔹 IMPORTACIONES PRINCIPALES
============================================================ */
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useRalimitStore } from "@/stores/ralimit"; // Store para CRUD de pacientes

/* ============================================================
   ⚙️ CONFIGURACIÓN DEL RECURSO
============================================================ */
const router = useRouter();
const Store = useRalimitStore();

// 🎯 Form reactivo usando reactive
const form = reactive({
  patient_id: '',
  identifier: '',
  first_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  phone: '',
  email: '',
  address: ''
});

// 💥 Objeto para errores de validación
const errors = reactive({});

/* ============================================================
   🔹 Función para enviar el formulario
============================================================ */
const submitForm = async () => {
  // 🔹 Limpiar errores previos
  Object.keys(errors).forEach(key => errors[key] = '');

  try {
    const response = await Store.create(form);
    if (response) router.push({ path: '/patients-index' });
  } catch (err) {
    // 🔹 Captura errores de validación (HTTP 422)
    if (err.response?.status === 422) {
      Object.assign(errors, err.response.data.errors || {});
    }
  }
};

/* ============================================================
   🔹 Función para resetear el formulario
============================================================ */
const resetForm = () => {
  Object.keys(form).forEach(key => form[key] = '');
  Object.keys(errors).forEach(key => errors[key] = '');
};
</script>

<template>
  <!-- ============================================================
       Cabecera
  ============================================================ -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Crear Paciente</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ path: '/patients-index' })">
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- ============================================================
       Formulario
  ============================================================ -->
  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow>
          <!-- Identifier -->
          <VCol cols="12">
            <VTextField v-model="form.identifier" label="Identifier" placeholder="CI, pasaporte, seguro"
              required :error-messages="errors.identifier" />
          </VCol>

          <!-- First Name -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.first_name" label="First Name" placeholder="John" required
              :error-messages="errors.first_name" />
          </VCol>

          <!-- Last Name -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.last_name" label="Last Name" placeholder="Doe" required
              :error-messages="errors.last_name" />
          </VCol>

          <!-- Date of Birth -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.date_of_birth" label="Date of Birth" type="date"
              :error-messages="errors.date_of_birth" />
          </VCol>

          <!-- Gender -->
          <VCol cols="12" md="6">
            <VSelect v-model="form.gender" :items="['male', 'female', 'other', 'unknown']"
              label="Gender" placeholder="Select gender" :error-messages="errors.gender" />
          </VCol>

          <!-- Phone -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.phone" label="Phone" placeholder="+591 7 123 4567"
              :error-messages="errors.phone" />
          </VCol>

          <!-- Email -->
          <VCol cols="12" md="6">
            <VTextField v-model="form.email" label="Email" type="email" placeholder="example@mail.com"
              :error-messages="errors.email" />
          </VCol>

          <!-- Address -->
          <VCol cols="12">
            <VTextField v-model="form.address" label="Address" placeholder="Street, city, country" rows="2"
              multiline :error-messages="errors.address" />
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex gap-4">
            <VBtn type="submit" color="primary">Crear</VBtn>
            <VBtn type="reset" color="secondary" variant="tonal" @click="resetForm">Reset</VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
