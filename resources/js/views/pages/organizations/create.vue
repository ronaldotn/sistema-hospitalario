<script setup>
// 📦 Imports
import { ref } from "vue";
import { useRouter } from "vue-router";   
import { useOrganizationStore } from "@/stores/organization"; // Store global

// 🚦 Router y Store
const router = useRouter();
const organizationStore = useOrganizationStore();

// 🔹 Estado local del formulario
const form = ref({});

// 🔹 Errores de validación
const errors = ref({});

// 🔹 Función al enviar formulario
const submitForm = async () => {
  errors.value = {}; // limpiamos errores previos
  try {
    const response = await organizationStore.create(form.value);
    if (response) {
      router.push({ name: "organization-index" });
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    }
  }
};
</script>

<template>
  <!-- 🏷️ Encabezado -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Crear Organización</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ name: 'organization-index' })">
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- 📋 Formulario -->
  <VCard class="elevation-1 rounded-lg">
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow :gap="4">

          <!-- Nombre -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.name" 
              prepend-inner-icon="bx-building" 
              label="Nombre de la Organización"
              placeholder="Mi Empresa S.A." 
              density="comfortable" 
              required
              :error="!!errors.name"
              :error-messages="errors.name"
            />
          </VCol>

          <!-- Tipo -->
          <VCol cols="12" md="6">
            <VSelect 
              v-model="form.type" 
              prepend-inner-icon="bx-list-check" 
              label="Tipo de Organización" 
              :items="[
                { title: 'Privada', value: 'private' },
                { title: 'Pública', value: 'public' },
                { title: 'ONG', value: 'ngo' },
                { title: 'Otro', value: 'other' }
              ]" 
              item-title="title" 
              item-value="value" 
              density="comfortable" 
              required
              :error="!!errors.type"
              :error-messages="errors.type"
            />
          </VCol>

          <!-- Dirección -->
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

          <!-- Teléfono -->
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

          <!-- Correo -->
          <VCol cols="12" md="6">
            <VTextField 
              v-model="form.email" 
              prepend-inner-icon="bx-envelope" 
              label="Correo"
              placeholder="contacto@empresa.com" 
              type="email" 
              density="comfortable"
              :error="!!errors.email"
              :error-messages="errors.email"
            />
          </VCol>

          <!-- Botones -->
          <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
            <VBtn type="reset" color="secondary" variant="tonal" prepend-icon="bx-eraser">
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
