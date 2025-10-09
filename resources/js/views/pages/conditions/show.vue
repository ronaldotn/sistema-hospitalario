<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useConditionStore } from "@/stores/condition";

// ==========================
// 🔹 Instancias de Vue Router y Pinia Store
// ==========================
const route = useRoute();
const router = useRouter();
const Store = useConditionStore();

// ==========================
// 🔹 Datos de solo visualización
// ==========================
// Aquí se almacenan los campos provenientes de la condición
// y de sus relaciones (patient y encounter)
const showData = ref({
  patient_name: "",      // Nombre del paciente
  encounter_type: "",    // Tipo de encuentro
  code: "",              // Código de la condición
  description: "",       // Descripción
  recorded_date: "",     // Fecha de registro
  // Se pueden agregar más campos relacionados aquí en el futuro
});

const hasData = ref(false); // indica si se cargaron los datos correctamente

// ==========================
// 🔹 Cargar condición con relaciones (patient y encounter)
// ==========================
onMounted(async () => {
  try {
    // Llamada al store para traer la condición
    const status = await Store.show(route.params.id);
    const c = Store.current;

    if (status === 200 && c) {
      // Mapear los datos para solo visualización
      showData.value = {
        patient_name: c.patient ? `${c.patient.first_name} ${c.patient.last_name}` : "",
        encounter_type: c.encounter?.encounter_type ?? "",
        code: c.code,
        description: c.description,
        recorded_date: c.recorded_date?.split("T")[0] ?? "",
        // 🔹 Futuras relaciones: agregar aquí si se necesitan
      };

      hasData.value = true;
    } else {
      hasData.value = false;
      console.error("❌ No se encontraron datos para esta condición");
    }
  } catch (err) {
    hasData.value = false;
    console.error("❌ Error al cargar condición:", err);
  }
});
</script>

<template>
  <VCard class="elevation-1 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Detalle de la Condición</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal" @click="router.push({ name: 'conditions-index' })">
        Volver
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Mostrar datos solo si existen -->
      <template v-if="hasData">
        <VRow :gap="4">

          <!-- ======================
               Campos de solo lectura
               ====================== -->
          <VCol cols="12" md="6">
            <VTextField :value="showData.patient_name" label="Paciente" readonly persistent-placeholder />
          </VCol>

          <VCol cols="12" md="6">
            <VTextField :value="showData.encounter_type" label="Tipo de Encuentro" readonly persistent-placeholder />
          </VCol>

          <VCol cols="12" md="6">
            <VTextField :value="showData.code" label="Código" readonly persistent-placeholder />
          </VCol>

          <VCol cols="12">
            <VTextarea :value="showData.description" label="Descripción" rows="4" readonly persistent-placeholder />
          </VCol>

          <VCol cols="12" md="6">
            <VTextField :value="showData.recorded_date" label="Fecha" readonly persistent-placeholder />
          </VCol>

          <!-- 🔹 Futuras relaciones / campos -->
          <!-- Ejemplo: mostrar doctor, notas adicionales, estado, etc. -->

        </VRow>
      </template>

      <!-- Mensaje si no hay datos -->
      <template v-else>
        <p class="text-center text-subtitle-1">❌ No se encontraron datos para esta condición.</p>
      </template>
    </VCardText>
  </VCard>
</template>

<style scoped>
.text-primary { color: #1976d2; }
.text-center { text-align: center; }
</style>
