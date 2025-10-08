<script setup>
/* ============================================================
   🔹 IMPORTACIONES PRINCIPALES
   ============================================================ */
import { ref, onMounted } from "vue";                  // onMounted → hook ciclo de vida
import { useRouter, useRoute } from "vue-router";      // useRouter → navegación, useRoute → params de la ruta
import { useRalimitStore } from "@/stores/ralimit";    // Store modular Pinia para CRUD

/* ============================================================
   ⚙️ CONFIGURACIÓN DEL RECURSO
   ============================================================ */
const router = useRouter();    // Para redireccionar
const route = useRoute();      // Para obtener params de la ruta (ej: id del paciente)
const Store = useRalimitStore(); // Instancia del store

/* ============================================================
   🔹 Control para mostrar el contenido solo cuando los datos
      del paciente se hayan cargado correctamente
   ============================================================ */
const isLoaded = ref(false); 

/* ============================================================
   🔹 Cargar datos del paciente existente al montar el componente
   ============================================================ */
onMounted(async () => {
   const id = route.params.id;
  if (!id) {
    console.error("ID del paciente no definido en la ruta");
    return;
  }
  if (!id) return console.error("ID del paciente no definido en la ruta");

  try {
    // show() llena Store.current y retorna el status HTTP
    const code = await Store.show(id);

    if (code === 200) {
      console.log("Datos del paciente cargados:", Store.current);
      isLoaded.value = true; // marcar como cargado para mostrar los datos
    } else {
      console.error("No se pudieron obtener los datos del paciente.");
    }
  } catch (err) {
    console.error("Error al cargar paciente:", err);
  }
});
</script>

<template>
  <!-- ============================================================
       Cabecera con botón para regresar
       ============================================================ -->
  <VCard class="mb-4 elevation-2 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Detalles del Paciente</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
        @click="router.push({ path: '/patients-index' })">
        Volver
      </VBtn>
    </VCardTitle>
  </VCard>

  <!-- ============================================================
       Loader mientras se cargan los datos
       ============================================================ -->
  <VProgressLinear
    v-if="!isLoaded"
    indeterminate
    color="primary"
    height="3"
    class="mt-2"
  />

  <!-- ============================================================
       Mostrar datos del paciente solo lectura
       ============================================================ -->
<VCard v-if="isLoaded" class="elevation-1 rounded-lg">
  <VCardText>
    <VRow class="mb-2">
      <VCol cols="4"><strong>Identifier:</strong></VCol>
      <VCol cols="8">{{ Store.current?.identifier }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>First Name:</strong></VCol>
      <VCol cols="8">{{ Store.current?.first_name }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Last Name:</strong></VCol>
      <VCol cols="8">{{ Store.current?.last_name }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Date of Birth:</strong></VCol>
      <VCol cols="8">{{ Store.current?.date_of_birth }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Gender:</strong></VCol>
      <VCol cols="8">{{ Store.current?.gender }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Phone:</strong></VCol>
      <VCol cols="8">{{ Store.current?.phone }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Email:</strong></VCol>
      <VCol cols="8">{{ Store.current?.email }}</VCol>
    </VRow>

    <VRow class="mb-2">
      <VCol cols="4"><strong>Address:</strong></VCol>
      <VCol cols="8">{{ Store.current?.address }}</VCol>
    </VRow>
  </VCardText>
</VCard>

</template>
