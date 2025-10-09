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
// 🔹 Formulario reactivo
// ==========================

// editableForm: campos que se pueden modificar y enviar al backend
const editableForm = ref({
  patient_id: null,
  encounter_id: null,
  code: "",
  description: "",
  recorded_date: "",
});

// readonlyData: campos que solo se muestran, provenientes de relaciones (patient y encounter)
const readonlyData = ref({
  patient_name: "",      // nombre del paciente
  encounter_type: "",    // tipo de encuentro
});

const errors = ref({});   // errores de validación
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
      // --------------------------
      // 1️⃣ Datos editables (enviamos estos al backend)
      // --------------------------
      editableForm.value = {
        patient_id: c.patient_id,
        encounter_id: c.encounter_id,
        code: c.code,
        description: c.description,
        recorded_date: c.recorded_date?.split("T")[0] ?? "", // formato YYYY-MM-DD
      };

      // --------------------------
      // 2️⃣ Datos de solo visualización (readonly), provenientes de relaciones
      // --------------------------
      readonlyData.value = {
        patient_name: c.patient ? `${c.patient.first_name} ${c.patient.last_name}` : "",
        encounter_type: c.encounter?.encounter_type ?? "",
      };

      hasData.value = true; // indicar que hay datos para mostrar
    } else {
      hasData.value = false; // no hay datos
      console.error("❌ No se encontraron datos para esta condición");
    }
  } catch (err) {
    hasData.value = false;
    console.error("❌ Error al cargar condición:", err);
  }
});

// ==========================
// 🔹 Enviar actualización
// ==========================
const submitForm = async () => {
  errors.value = {}; // limpiar errores previos
  try {
    // Solo enviamos los campos editables
    const response = await Store.update(route.params.id, editableForm.value);

    if (response) router.push({ name: "conditions-index" }); // redirigir al listado
  } catch (err) {
    if (err.response?.status === 422) {
      // validar errores de backend
      errors.value = err.response.data.errors ?? {};
    } else {
      console.error("❌ Error al actualizar:", err);
    }
  }
};
</script>

<template>
  <VCard class="elevation-1 rounded-lg">
    <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
      <h2 class="text-h5 font-weight-bold text-primary">Editar Condición</h2>
      <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal" @click="router.push({ name: 'conditions-index' })">
        Volver
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Mostrar formulario solo si hay datos -->
      <template v-if="hasData">
        <VForm @submit.prevent="submitForm">
          <VRow :gap="4">

            <!-- ======================
                 Campos de solo visualización
                 ====================== -->
            <VCol cols="12" md="6">
              <VTextField 
                :value="readonlyData.patient_name" 
                label="Paciente" 
                readonly 
                persistent-placeholder
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField 
                :value="readonlyData.encounter_type" 
                label="Tipo de Encuentro" 
                readonly 
                persistent-placeholder
              />
            </VCol>

            <!-- ======================
                 Campos editables
                 ====================== -->
            <VCol cols="12" md="6">
              <VTextField 
                v-model="editableForm.code" 
                label="Código" 
                required
                :error="!!errors.code" 
                :error-messages="errors.code" 
              />
            </VCol>

            <VCol cols="12">
              <VTextarea 
                v-model="editableForm.description" 
                label="Descripción" 
                rows="4"
                :error="!!errors.description" 
                :error-messages="errors.description" 
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField 
                v-model="editableForm.recorded_date" 
                label="Fecha" 
                type="date"
                :error="!!errors.recorded_date" 
                :error-messages="errors.recorded_date" 
              />
            </VCol>

            <!-- ======================
                 Botones
                 ====================== -->
            <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
              <VBtn color="secondary" @click="router.push({ name: 'conditions-index' })">
                Cancelar
              </VBtn>
              <VBtn type="submit" color="primary">Actualizar</VBtn>
            </VCol>

          </VRow>
        </VForm>
      </template>

      <!-- Mensaje si no hay datos -->
      <template v-else>
        <p class="text-center text-subtitle-1">
          ❌ No se encontraron datos para esta condición.
        </p>
      </template>
    </VCardText>
  </VCard>
</template>

<style scoped>
.text-primary { color: #1976d2; }
.text-center { text-align: center; }
</style>
