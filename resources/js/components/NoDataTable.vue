<template>
  <!-- Solo muestra el mensaje si el array está vacío -->
  <tr v-if="!data || !data.length">
    <td :colspan="cols" class="text-center py-6">
      <div class="flex flex-col items-center justify-center gap-2">
        <VIcon v-if="icon" :icon="icon" size="36" class="text-gray-400" />
        <h3 class="text-lg font-semibold" v-if="title">{{ title }}</h3>
        <p class="text-gray-500">{{ text }}</p>
      </div>
    </td>
  </tr>
</template>

<script setup>
defineProps({
  data: { type: Array, required: true }, // Array de datos del store
  cols: { type: Number, required: true }, // Número de columnas que ocupa
  title: { type: String, default: "Sin datos" },
  text: { type: String, default: "No hay información disponible" },
  icon: { type: String, default: "bx-info-circle" }, // Icono por defecto
});
</script>

<style scoped>
.text-center {
  text-align: center;
}
</style>
<!-- uso del componente: -->
<!-- 2️⃣ Aquí llamas al componente que mostrará el mensaje si no hay datos -->
<!-- <NoDataTable
      :data="practitionerStore.practitioners"
      :cols="7"
      title="Sin Practitioners"
      text="No hay practitioners registrados aún"
      icon="bx-user-x"
    /> -->
