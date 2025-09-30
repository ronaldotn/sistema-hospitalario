<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";
import { WarningToast } from "@/composables/Toast";

const route = useRoute();
const router = useRouter();
const practitionerStore = usePractitionerStore();

const practitioner = ref(null);
const loading = ref(false);

const loadPractitioner = async () => {
  const uuid = route.params.uuid;
  if (!uuid) return router.push({ name: "practitioners-index" });

  loading.value = true;
  const result = await practitionerStore.showPractitioner(uuid);
  loading.value = false;

  if (!result) {
    WarningToast("Practitioner no encontrado");
    router.push({ name: "practitioners-index" });
  } else {
    practitioner.value = result;
  }
};

onMounted(loadPractitioner);
</script>

<template>
  <VCard class="pa-4" v-if="practitioner">
    <VCardTitle>
      Practitioner: {{ practitioner.nombre }} {{ practitioner.apellidos }}
    </VCardTitle>
    <VCardText>
      <p><strong>Documento:</strong> {{ practitioner.documento_identidad }}</p>
      <p><strong>Especialidad:</strong> {{ practitioner.especialidad }}</p>
      <p><strong>Contacto:</strong> {{ practitioner.contacto }}</p>
      <p><strong>Correo:</strong> {{ practitioner.correo }}</p>

      <VBtn color="primary" @click="router.push({ name: 'practitioners-index' })">
        Volver
      </VBtn>
    </VCardText>
  </VCard>

  <div v-else class="text-center pa-4">
    Cargando practitioner...
  </div>
</template>
