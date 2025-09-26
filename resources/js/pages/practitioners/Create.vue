<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";

const router = useRouter();
const practitionerStore = usePractitionerStore();

const form = ref({
  nombre: "",
  apellidos: "",
  especialidad: "",
  nro_colegiado: "",
  email: "",
  telefono: "",
  estado: "activo",
});

const errors = ref({}); // ⚠️ Debe existir para el VTextField

const submitForm = async () => {
  const result = await practitionerStore.createPractitioner(form.value);

  if (result.success) {
    router.push({ name: "practitioners-index" }); // redirige solo si todo ok
  } else if (result.errors) {
    errors.value = result.errors; // llena errores de validación si los hay
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
            <VTextField v-model="form.nombre" :error-messages="errors.nombre" required />
          </VCol>

          <VCol cols="12" md="3"><label>Apellidos</label></VCol>
          <VCol cols="12" md="9">
            <VTextField v-model="form.apellidos" :error-messages="errors.apellidos" required />
          </VCol>

          <VCol cols="12" md="3"><label>N° Colegiado</label></VCol>
          <VCol cols="12" md="9">
            <VTextField v-model="form.nro_colegiado" :error-messages="errors.nro_colegiado" required />
          </VCol>


          <VCol cols="12" md="3"><label>Especialidad</label></VCol>
          <VCol cols="12" md="9">
            <VTextField   v-model="form.especialidad" :error-messages="errors.especialidad" required />
          </VCol>
          <VCol cols="12" md="3"><label>Contacto</label></VCol>
          <VCol cols="12" md="9">
            <VTextField  type="number" v-model="form.telefono" />
          </VCol>

          <VCol cols="12" md="3"><label>Correo</label></VCol>
          <VCol cols="12" md="9">
            <VTextField  type="email" v-model="form.email" :error-messages="errors.email" required />
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
