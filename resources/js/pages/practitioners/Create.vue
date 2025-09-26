<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import { WarningToast, SuccessToast } from "@/composables/Toast";

const router = useRouter();
const patientStore = usePatientStore();

const form = ref({
  nombre: "",
  apellidos: "",
  documento_identidad: "",
  fecha_nacimiento: "",
  sexo: "",
  direccion: "",
  contacto: "",
  correo: "",
});

const errors = ref({});

const submitForm = async () => {
  errors.value = {};
  try {
    const response = await patientStore.createPatient(form.value);
    SuccessToast(response.message);
    // componente Vue
    router.push({ name: "patients-index" });
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
    } else if (err.response?.status === 409) {
      WarningToast(err.response.data.message);
    } else {
      WarningToast(err.response?.data?.message || "Error creando paciente");
    }
  }
};
</script>

<template>
  <VCard class="pa-4">
    <VCardTitle>Crear Paciente</VCardTitle>
    <VCardText>
      <VForm @submit.prevent="submitForm">
        <VRow dense>
          <VCol cols="12" md="3"><label>Nombre</label></VCol>
          <VCol cols="12" md="9">
            <VTextField
              v-model="form.nombre"
              :error-messages="errors.nombre"
              required
            />
          </VCol>

          <VCol cols="12" md="3"><label>Apellidos</label></VCol>
          <VCol cols="12" md="9">
            <VTextField
              v-model="form.apellidos"
              :error-messages="errors.apellidos"
              required
            />
          </VCol>

          <VCol cols="12" md="3"><label>Documento</label></VCol>
          <VCol cols="12" md="9">
            <VTextField
              v-model="form.documento_identidad"
              :error-messages="errors.documento_identidad"
              required
            />
          </VCol>

          <VCol cols="12" md="3"><label>Fecha Nacimiento</label></VCol>
          <VCol cols="12" md="9">
            <VTextField
              type="date"
              v-model="form.fecha_nacimiento"
              :error-messages="errors.fecha_nacimiento"
              required
            />
          </VCol>

          <VCol cols="12" md="3"><label>Sexo</label></VCol>
          <VCol cols="12" md="9">
            <VSelect
              v-model="form.sexo"
              :items="['masculino', 'femenino', 'otro']"
              :error-messages="errors.sexo"
              required
            />
          </VCol>

          <VCol cols="12" md="3"><label>Dirección</label></VCol>
          <VCol cols="12" md="9"><VTextField v-model="form.direccion" /></VCol>

          <VCol cols="12" md="3"><label>Contacto</label></VCol>
          <VCol cols="12" md="9"><VTextField v-model="form.contacto" /></VCol>

          <VCol cols="12" md="3"><label>Correo</label></VCol>
          <VCol cols="12" md="9">
            <VTextField
              v-model="form.correo"
              :error-messages="errors.correo"
              required
            />
          </VCol>

          <VCol cols="12" md="3" />
          <VCol cols="12" md="9">
            <VBtn type="submit" color="primary" class="me-4">Guardar</VBtn>
            <!-- <VBtn type="reset" color="secondary">Limpiar</VBtn> -->
            <VBtn color="secondary" @click="router.push({ name: 'patients-index' })">
              Volver
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
