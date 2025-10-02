<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import { StatusMessage } from "@/components";

// Avatar por defecto
import avatar1 from '@images/avatars/avatar-1.png';

const route = useRoute();
const patientStore = usePatientStore();

// Referencia para el input de archivo
const refInputEl = ref();

// Función para cambiar avatar
const changeAvatar = (event) => {
    const files = event.target.files;
    if (files && files.length) {
        const reader = new FileReader();
        reader.readAsDataURL(files[0]);
        reader.onload = () => {
            if (typeof reader.result === 'string') {
                patientStore.current.avatar = reader.result;
            }
        };
    }
};

// Resetear avatar
const resetAvatar = () => {
    patientStore.current.avatar = '';
};

// Cargar paciente al montar
onMounted(() => {
    if (route.params.uuid) patientStore.showPatient(route.params.uuid);
});
</script>

<template>
  <div class="p-6">
    <StatusMessage
      :data="patientStore"
      value="current"
      title="Paciente no encontrado"
      text="No se encontró ningún paciente"
    >
      <VCard>
        <VCardTitle class="mb-6 ml-2">
          <h2>Show Pacientes</h2>
        </VCardTitle>

        <VCardText>
          <!-- Avatar + botones solo visual -->
          <!--
          <div class="d-flex align-center gap-3 pb-6">
            <VAvatar rounded="lg" size="100" :image="patientStore.current.avatar || avatar1" />

            <form class="d-flex flex-column gap-3">
              <div class="d-flex gap-2 flex-wrap">
                <VBtn color="primary" disabled>Upload new photo</VBtn>
                <VBtn color="error" variant="tonal" disabled>Reset</VBtn>
              </div>
              <p class="text-body-1 mb-0">
                Allowed JPG, GIF or PNG. Max size of 800K
              </p>
            </form>
          </div>
          -->

          <!-- Información solo lectura -->
          <VRow>
            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.nombre" label="Nombre" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.apellidos" label="Apellidos" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.documento_identidad" label="DNI" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.edad" label="Edad" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.sexo" label="Sexo" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.direccion" label="Dirección" readonly />
            </VCol>

            <VCol md="6" cols="12">
              <VTextField v-model="patientStore.current.contacto" label="Contacto" readonly />
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </StatusMessage>
  </div>
</template>
