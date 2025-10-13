<script setup>
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";

const router = useRouter();
const practitionerStore = usePractitionerStore();

const form = ref({
  first_name: "",
  last_name: "",
  identifier: "",
  specialty: "",
  new_specialty: "",
  email: "",
  phone: "",
  active: null,
});

const errors = ref({});
const verifying = ref(false);
const identifierTitle = ref("");
let debounceTimeout = null;

const specialties = [
  "Cardiología",
  "Dermatología",
  "Pediatría",
  "Medicina General",
  "Ginecología",
  "Cirugía General",
  "Oncología",
];

// Watch para validación con debounce 2s
watch(
  () => form.value.identifier,
  (val) => {
    clearTimeout(debounceTimeout);
    identifierTitle.value = "";
    form.value.active = null;

    if (!val) return;

    verifying.value = true;
    identifierTitle.value = "Verificando...";

    debounceTimeout = setTimeout(() => validarColegiado(val), 2000);
  }
);

// Validación simulada
const validarColegiado = async (identifier) => {
  try {
    await new Promise((r) => setTimeout(r, 500));
    const activo = parseInt(identifier.slice(-1)) % 2 === 0;
    form.value.active = activo ? 1 : 0;
    identifierTitle.value = activo
      ? "Puede registrarse"
      : "Número ya en uso o inválido";
  } catch {
    form.value.active = null;
    identifierTitle.value = "Error al verificar";
  } finally {
    verifying.value = false;
  }
};
// ===============================
// 🔹 Enviar formulario
// ===============================
const submitForm = async () => {
  errors.value = {};

  // 🔹 Sobrescribir especialidad si es "Otra"
  if (form.value.specialty === "otra" && form.value.new_specialty) {
    form.value.specialty = form.value.new_specialty;
    form.value.new_specialty = "";
  }

  try {
    const response = await practitionerStore.create(form.value);
    if (response) router.push({ name: "practitioners-index" });
  } catch (err) {
    if (err.response?.status === 422)
      errors.value = err.response.data.errors || {};
  }
};
</script>

<template>
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Registrar Profesional</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: 'practitioners-index' })">
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
              v-model="form.first_name" label="Nombre" prepend-inner-icon="bx-user"
              :error="!!errors.first_name" :error-messages="errors.first_name" required
            />
          </VCol>

          <!-- Apellidos -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.last_name" label="Apellidos" prepend-inner-icon="bx-user"
              :error="!!errors.last_name" :error-messages="errors.last_name" required
            />
          </VCol>

          <!-- Nro de Colegiado -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.identifier" label="Nro de Colegiado" placeholder="MED-63461" prepend-inner-icon="bx-id-card"
              :append-inner-icon="
                verifying
                  ? 'bx-loader-alt bx-spin'
                  : form.active === 1
                  ? 'bx-check-circle text-success'
                  : form.active === 0
                  ? 'bx-error-circle text-error'
                  : ''
              "
              :title="identifierTitle"
              :error="!!errors.identifier"
              :error-messages="errors.identifier"
              required
            />
            <small v-if="identifierTitle" :class="form.active === 1 ? 'text-success' : 'text-error'">
              {{ identifierTitle }}
            </small>
          </VCol>
         <!-- Especialidad -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="form.specialty"
              :items="[
                ...specialties.map(s => ({ title: s, value: s })),
                { title: 'Otra...', value: 'otra' }
              ]"
              item-title="title"
              item-value="value"
              label="Especialidad"
              prepend-inner-icon="bx-briefcase"
              :error="!!errors.specialty"
              :error-messages="errors.specialty"
              required
            />
            <VTextField
              v-if="form.specialty === 'otra'"
              v-model="form.new_specialty"
              label="Nueva Especialidad"
              prepend-inner-icon="bx-pencil"
              clearable
            />
          </VCol>

          <!-- Email y Teléfono -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.email" label="Email" type="email" prepend-inner-icon="bx-envelope"
              :error="!!errors.email" :error-messages="errors.email"
            />
          </VCol>

          <VCol cols="12" md="6">
            <VTextField
              v-model="form.phone" label="Teléfono" prepend-inner-icon="bx-phone"
              :error="!!errors.phone" :error-messages="errors.phone"
            />
          </VCol>

          <!-- Estado -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="form.active"
              :items="[{ title: 'Activo', value: 1 }, { title: 'Inactivo', value: 0 }]"
              label="Estado" prepend-inner-icon="bx-toggle-left"
              :error="!!errors.active" :error-messages="errors.active"
            />
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
