<script setup>
// 📦 Imports
import { ref, reactive, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";

const router = useRouter();
const route = useRoute();
const practitionerStore = usePractitionerStore();

// 🔹 Estado del formulario (reactive para binding)
const form = ref({
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

// 🔹 Lista de especialidades (para autocomplete)
const specialties = ref([]);

// 🔹 Edit mode: si hay id en params
const editId = route.params.id || null;

// =======================
// 🔹 Funciones
// =======================

// Reset form
const resetForm = () => {
  Object.keys(form).forEach((key) => {
    form[key] = key === "active" ? 1 : "";
  });
  errors.value = {};
};

// Fetch de especialidades para autocomplete
const fetchSpecialties = async () => {
  specialties.value = practitionerStore.specialties; // o lookup si quieres live
};

// Validación en tiempo real (identifier/email)
const validateUnique = async (field) => {
  if (!form[field]) return;
  const exists = await practitionerStore.checkUnique(field, form[field]);
  if (exists) {
    errors.value[field] = `El ${field} ya está registrado.`;
  } else {
    errors.value[field] = null;
  }
};

// Submit form (create/update)
const submitForm = async () => {
  errors.value = {};
  try {
    if (editId) {
      await practitionerStore.update(editId, form);
    } else {
      await practitionerStore.create(form);
    }
    router.push({ name: "practitioners-index" });
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    }
  }
};

// Load professional data if edit mode
const loadProfessional = async () => {
  if (!editId) return;
  const data = await practitionerStore.show(editId);
  if (data) Object.assign(form, data);
};

// =======================
// 🔹 Lifecycle
// =======================
onMounted(async () => {
  await fetchSpecialties();
  await loadProfessional();
});
</script>

<template>
  <!-- 🔹 Card Formulario -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">
        {{ editId ? "Editar Profesional" : "Registrar Profesional" }}
      </h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal" @click="router.push({ name: 'practitioners-index' })">
        Volver
      </VBtn>
    </VCardTitle>
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
          <!-- Nro Colegiado -->
          <VCol cols="12" md="6">
            <VTextField
              v-model="form.identifier"
              label="Nro de Colegiado"
              prepend-inner-icon="bx-id-card"
              :error="!!errors.identifier"
              :error-messages="errors.identifier"
              required
              @blur="validateUnique('identifier')"
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
              @blur="validateUnique('email')"
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
              {{ editId ? "Actualizar" : "Guardar" }}
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
