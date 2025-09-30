<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { usePatientStore } from "@/stores/patient";
import { WarningToast } from "@/composables/Toast";

const route = useRoute();
const router = useRouter();
const patientStore = usePatientStore();

const patient = ref(null);
const loading = ref(false);

const loadPatient = async () => {
  const uuid = route.params.uuid;
  if (!uuid) return router.push({ name: "patients-index" });

  loading.value = true;
  const result = await patientStore.showPatient(uuid);
  loading.value = false;

  if (!result) {
    WarningToast("Paciente no encontrado");
    router.push({ name: "patients-index" });
  } else {
    patient.value = result;
  }
};

onMounted(loadPatient);
</script>

<template>
  <VCard class="pa-4" v-if="patient">
    <VCardTitle>Paciente: {{ patient.nombre }} {{ patient.apellidos }}</VCardTitle>
    <VCardText>
      <p><strong>Documento:</strong> {{ patient.documento_identidad }}</p>
      <p><strong>Fecha de nacimiento:</strong> {{ patient.fecha_nacimiento }}</p>
      <p><strong>Edad:</strong> {{ patient.edad }} años</p>
      <p><strong>Sexo:</strong> {{ patient.sexo }}</p>
      <p><strong>Dirección:</strong> {{ patient.direccion }}</p>
      <p><strong>Contacto:</strong> {{ patient.contacto }}</p>
      <p><strong>Correo:</strong> {{ patient.correo }}</p>

      <VBtn color="primary" @click="router.push({ name: 'patients-index' })">
        Volver
      </VBtn>
    </VCardText>
  </VCard>

  <div v-else class="text-center pa-4">
    Cargando paciente...
  </div>
</template>
