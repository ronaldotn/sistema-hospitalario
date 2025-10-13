<script setup>
import { useRouter } from "vue-router";
import { usePractitionerStore } from "@/stores/practitioner";
import FiltersPanel from "@/views/pages/practitioners/filter.vue";

const router = useRouter();
const Store = usePractitionerStore();
</script>

<template>
  <!-- ============================== 
   🔹 Header 
   ============================== -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>Lista de Profecionales</h2>
      <VBtn color="primary" @click="router.push({ name: 'practitioners-create' })"> Crear Paciente </VBtn>
    </VCardTitle>
  </VCard>
  <FiltersPanel />

  <FlexibleTable :store="Store" :columns="['Nombre', 'DNI', 'Especialidad', 'Email', 'Teléfono', 'Activo', 'Acciones']"
    title="Sin profesionales" text="No hay profesionales registrados" icon="bx-user-x">
    <tr v-for="p in Store.model" :key="p.id">
      <td>{{ p.first_name }} {{ p.last_name }}</td>
      <td>{{ p.identifier }}</td>
      <td>{{ p.specialty ?? '-' }}</td>
      <td>{{ p.email ?? '-' }}</td>
      <td>{{ p.phone ?? '-' }}</td>
      <td>
        <span v-if="p.active" class="text-green-600 font-semibold">Activo</span>
        <span v-else class="text-red-600 font-semibold">Inactivo</span>
      </td>
      <td class="d-flex gap-2">
        <VBtn icon size="small" color="primary"
          @click="router.push({ name: 'practitioners-show', params: { id: p.id } })">
          <VIcon icon="bx-show" />
        </VBtn>
        <VBtn icon size="small" color="warning"
          @click="router.push({ name: 'practitioners-edit', params: { id: p.id } })">
          <VIcon icon="bx-edit" />
        </VBtn>
        <VBtn icon size="small" color="error">
          <VIcon icon="bx-trash" />
        </VBtn>
      </td>
    </tr>
  </FlexibleTable>

</template>
