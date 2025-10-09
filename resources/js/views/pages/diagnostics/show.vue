<script setup>
import { onMounted, computed, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useEncounterStore } from "@/stores/encounter";
import { WarningToast } from "@/composables/Toast";

const route = useRoute();
const router = useRouter();
const Store = useEncounterStore();

// 💡 El ID lo tomamos del parámetro de la URL
const encounterId = route.params.id;

// ⭐ Bandera que indica si la petición 'show' ha finalizado (Con o sin datos)
const isDataFetched = ref(false); 

// ⭐ Usamos una propiedad computada para acceder al registro
const encounter = computed(() => Store.current);

// 🔹 Lógica Central: Cargar datos al montar el componente
onMounted(async () => {
    // ⭐⭐ 1. LIMPIEZA INICIAL: Aseguramos que el estado esté vacío ANTES de la petición. ⭐⭐
    // Esto fuerza a que el 'VCard' de detalle se oculte inmediatamente
    // si había un dato anterior cargado en el Store.
    Store.current = null; 
    isDataFetched.value = false; // Reiniciamos la bandera

    if (encounterId) {
        // 2. Llamamos al store para hacer la petición HTTP
        const resultCode = await Store.show(encounterId); 

        // 3. Manejo de Redirección y Feedback
        if (resultCode !== 200) {
            // Si la respuesta no es 200, el Store.show(id) ya debería haber dejado Store.current = null.
            // La VCard "No encontrado" se mostrará gracias al v-else-if="isDataFetched".
        }
    } else {
        // Si no hay ID en la URL, se asume un error de ruta.
        WarningToast("Falta el identificador del encuentro.");
        // Aquí SÍ redirigimos, ya que la URL es inválida.
        router.push({ name: 'encounter-index' });
    }
    
    // ⭐ 4. AL FINALIZAR: Marcamos que la carga ha terminado.
    isDataFetched.value = true;
});

// Función para volver a la lista
const goBack = () => {
    router.push({ name: 'encounter-index' });
};
</script>

<template>
    <VCard v-if="encounter" class="elevation-1 rounded-lg">
        <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
            <h2 class="text-h5 font-weight-bold text-primary">Detalle del Encuentro #{{ encounter.id }}</h2>
            <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal" @click="goBack">Volver</VBtn>
        </VCardTitle>
        <VCardText>
            <VRow>
                <VCol cols="12" md="6">
                    <p class="font-weight-bold">Paciente:</p>
                    <p>{{ encounter.patient?.first_name }} {{ encounter.patient?.last_name }}</p>
                </VCol>
                <VCol cols="12" md="6">
                    <p class="font-weight-bold">Tipo:</p>
                    <p>{{ encounter.encounter_type }}</p>
                </VCol>
            </VRow>

            <div class="mt-6 d-flex justify-end">
                <VBtn color="warning" @click="router.push({ name: 'encounter-edit', params: { id: encounter.id } })">
                    Editar
                    <VIcon end icon="bx-edit" />
                </VBtn>
            </div>
        </VCardText>
    </VCard>

    <VCard v-else-if="isDataFetched" class="text-center py-10">
        <VCardText>
            <VIcon icon="bx-error-circle" size="60" color="error" />
            <h3 class="mt-4">El encuentro solicitado no fue encontrado.</h3>
        </VCardText>
    </VCard>
    
    <VCard v-else class="text-center py-10" min-height="300" />
</template>