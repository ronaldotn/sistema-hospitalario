<script setup>
import { onMounted, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useRalimitStore } from "@/stores/ralimit";

const router = useRouter();
const route = useRoute();
const Store = useRalimitStore();

const loading = ref(false);
const error = ref(false);

// Prefijo base para rutas (coincide con la ruta del router)
const routePrefix = "components";

// ============================================================
// 🚀 Cargar datos al montar
// ============================================================
onMounted(async () => {
  loading.value = true;
  error.value = false;

  try {
    // Limpia el estado previo
    Store.current = null;

    const status = await Store.show(route.params.id);

    // Si la respuesta no es válida, marcamos error
    if (status !== 200 || !Store.current) {
      error.value = true;
    }
  } catch (err) {
    console.error("Error cargando paciente:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <StatusMessage
    :loading="loading"
    :error="error"
    title="Paciente no encontrado"
    text="No se encontró ningún paciente"
    icon="bx-user-x"
  >
    <!-- ============================================================
         🧭 CABECERA
         ============================================================ -->
    <VCard class="mb-4 elevation-2 rounded-lg">
      <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
        <h2 class="text-h5 font-weight-bold text-primary">Detalles del Registro</h2>
        <VBtn
          prepend-icon="bx-arrow-back"
          color="secondary"
          variant="tonal"
          @click="router.push({ name: `${routePrefix}-index` })"
        >
          Volver
        </VBtn>
      </VCardTitle>
    </VCard>

    <!-- ============================================================
         📋 DETALLES DEL REGISTRO
         ============================================================ -->
    <VCard class="elevation-1 rounded-lg">
      <VCardText>
        <VRow class="mb-2">
          <VCol cols="4"><strong>Identifier:</strong></VCol>
          <VCol cols="8">{{ Store.current?.identifier || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>First Name:</strong></VCol>
          <VCol cols="8">{{ Store.current?.first_name || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>Last Name:</strong></VCol>
          <VCol cols="8">{{ Store.current?.last_name || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>Date of Birth:</strong></VCol>
          <VCol cols="8">{{ Store.current?.date_of_birth || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>Gender:</strong></VCol>
          <VCol cols="8">{{ Store.current?.gender || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>Phone:</strong></VCol>
          <VCol cols="8">{{ Store.current?.phone || "—" }}</VCol>
        </VRow>

        <VRow class="mb-2">
          <VCol cols="4"><strong>Email:</strong></VCol>
          <VCol cols="8">{{ Store.current?.email || "—" }}</VCol>
        </VRow>

        <VRow>
          <VCol cols="4"><strong>Address:</strong></VCol>
          <VCol cols="8">{{ Store.current?.address || "—" }}</VCol>
        </VRow>
      </VCardText>
    </VCard>
  </StatusMessage>
</template>
