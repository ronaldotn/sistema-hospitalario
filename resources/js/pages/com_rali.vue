<script setup>
import FiltersPanel from "@/views/pages/patients/filter.vue";
</script>

<template>
  <!-- Contenedor principal -->
  <div class="main-section-container">
    
    <!-- Título de la sección -->
    <div class="section-header">
      <h2>Área de Componentes Ralimit</h2>
      <p class="subtitle">Aquí se insertarán todos tus componentes</p>
    </div>

    <!-- Divider -->
    <div class="section-divider"></div>

    <!-- Contenido: espacio para tus componentes -->
    <div class="section-content">
         <p class="subtitle">Filtro para lista</p>
      <FiltersPanel />
      <!-- Aquí puedes agregar más componentes como tabla, gráficos, etc. -->
    </div>
  </div>
</template>

<style scoped>
.main-section-container {
  background-color: #ffffff; /* Fondo blanco */
  padding: 24px;             /* Espaciado interno */
  border-radius: 12px;       /* Bordes redondeados */
  box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Sombra suave */
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.section-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937; /* Gris oscuro */
}

.subtitle {
  font-size: 0.875rem;
  color: #6b7280; /* Gris medio */
}

.section-divider {
  height: 1px;
  background-color: #e5e7eb; /* Gris claro */
  margin: 16px 0;
}

.section-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
</style>
