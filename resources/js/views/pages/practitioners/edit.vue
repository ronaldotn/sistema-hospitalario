<script setup>
// 📦 Imports
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";

const router = useRouter();
const practitionerStore = usePractitionerStore();

// 🔹 Estado del formulario
const form = reactive({
  first_name: "",
  last_name: "",
  identifier: "",
  specialty: "",
  email: "",
  phone: "",
  active: 1,
});

// 🔹 Errores de validación
const errors = ref({});

// 🔹 Catálogo de especialidades
const specialties = ref([
  "Cardiología", "Dermatología", "Pediatría", "Medicina General", "Ginecología"
]);

// 🔹 Reset form
const resetForm = () => {
  Object.keys(form).forEach(key => {
    if (key === "active") {
      form[key] = 1;
    } else {
      form[key] = "";
    }
  });
  errors.value = {};
};

// 🔹 Función al enviar
const submitForm = async () => {
  errors.value = {};
  try {
    const response = await practitionerStore.create(form);
    if (response) {
      router.push({ name: "practitioners-index" });
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    }
  }
};
</script>

<template>
  <!-- 🔹 Encabezado -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Registrar Profesional</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal" @click="router.push({ name: 'practitioners-index' })">
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- 🔹 Formulario -->
  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">

          <!-- Nombre -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.first_name"
              label="Nombre"
              prepend-inner-icon="bx-user"
              :error="!!errors.first_name"
              :error-messages="errors.first_name"
              required
            />
          </VCol>

          <!-- Apellidos -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.last_name"
              label="Apellidos"
              prepend-inner-icon="bx-user"
              :error="!!errors.last_name"
              :error-messages="errors.last_name"
              required
            />
          </VCol>

          <!-- Nro de colegiado -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.identifier"
              label="Nro de Colegiado"
              prepend-inner-icon="bx-id-card"
              :error="!!errors.identifier"
              :error-messages="errors.identifier"
              required
            />
          </VCol>

          <!-- Especialidad -->
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="form.specialty"
              :items="specialties"
              label="Especialidad"
              prepend-inner-icon="bx-briefcase"
              :error="!!errors.specialty"
              :error-messages="errors.specialty"
              required
            />
          </VCol>

          <!-- Email -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.email"
              label="Email"
              type="email"
              prepend-inner-icon="bx-envelope"
              :error="!!errors.email"
              :error-messages="errors.email"
            />
          </VCol>

          <!-- Teléfono -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.phone"
              label="Teléfono"
              prepend-inner-icon="bx-phone"
              :error="!!errors.phone"
              :error-messages="errors.phone"
            />
          </VCol>

          <!-- Estado -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="form.active"
              :items="[{title:'Activo', value:1},{title:'Inactivo', value:0}]"
              item-title="title"
              item-value="value"
              label="Estado"
              :error="!!errors.active"
              :error-messages="errors.active"
            />
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn color="secondary" variant="tonal" @click="resetForm">
              Reset
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
