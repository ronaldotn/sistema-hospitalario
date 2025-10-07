<!-- ============================================================
 🧭 COMPONENTE BASE LIST VIEW (Vista de listado)
 Framework: Vue 3 + Pinia + Laravel 12 API
 Autor: Michael 💪
 Descripción:
 Este componente consume el store "useRalimitStore" (módulo CRUD)
 y muestra:
 - Tabla de registros
 - Botones de acción (ver, editar, eliminar)
 - Navegación dinámica con router
 - Loader de estado mientras se cargan datos
============================================================ -->
<script setup>
// ============================================================
// 🔹 IMPORTACIONES PRINCIPALES
// ============================================================
// useRouter → para manejar la navegación entre vistas
import { useRouter } from "vue-router";
// onMounted → hook de ciclo de vida de Vue
import { onMounted } from "vue";

// useRalimitStore → nuestro store modular CRUD de Pinia
import { useRalimitStore } from "@/stores/ralimit";

// ============================================================
// ⚙️ CONFIGURACIÓN DEL RECURSO
// ============================================================
// Router: instancia para redirección de rutas dinámicas
const router = useRouter();

// Store: instancia reactiva del store Pinia
// Contiene estados reactivos y funciones CRUD
const Store = useRalimitStore();

// Prefijo base para rutas (coincide con la ruta del router)
const routePrefix = "patients";

// ============================================================
// 🚀 FUNCIONES DE NAVEGACIÓN / UTILIDAD
// ============================================================

// 🔹 Redirige a la vista de creación de un nuevo registro
const goCreate = () => {
  router.push({ path: `${routePrefix}-create` });
};

// 🔹 Redirige a la vista de detalle de un registro específico
const goShow = (id) => {
  router.push({ path: `${routePrefix}-show/${id}`});
};

// 🔹 Redirige al formulario de edición de un registro
const goEdit = (id) => {
//   router.push({ path: `${routePrefix}-edit`, params: { id } });
   router.push({ path: `/${routePrefix}-edit/${id}` });
};

// 🔹 Función para eliminar un registro
// Llama a Store.destroy(id) y refresca la lista automáticamente
const goDelete = async (id) => {
  // Confirmación antes de eliminar
  const confirmDelete = confirm("¿Seguro que deseas eliminar este registro?");
  if (!confirmDelete) return;

  // Llamada al store para eliminar
  await Store.destroy(id);

  // Actualiza automáticamente la lista de registros
  await Store.index();
};

// ============================================================
// ⚡ Hook onMounted
// ============================================================
// Se ejecuta automáticamente cuando el componente se monta
// Ideal para cargar datos iniciales desde el backend
onMounted(async () => {
  await Store.index(); // Llama la acción index() del store para llenar la tabla
});
</script>

<!-- ============================================================
 💻 PLANTILLA VISUAL (Template)
============================================================ -->
<template>
  <!-- ======================================================
       🔹 CABECERA DEL LISTADO
       Contiene título y botón de acción para crear un registro
  ====================================================== -->
  <VCard class="mb-4">
    <VCardTitle class="d-flex justify-space-between align-center">
      <h2>📋 Lista de Pacientes</h2>
      <VBtn color="primary" @click="goCreate">Crear nuevo</VBtn>
    </VCardTitle>
  </VCard>

  <!-- ======================================================
       🔹 TABLA DE REGISTROS
       Itera sobre Store.model (lista reactiva) y renderiza cada registro
  ====================================================== -->
  <VTable>
    <thead>
      <tr>
        <th>ID</th>
        <th>Identificador</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Fecha Nac.</th>
        <th>Sexo</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Dirección</th>
        <th class="text-center">Acciones</th>
      </tr>
    </thead>

    <tbody>
      <!-- 🔁 Iteración reactiva sobre los registros del store -->
      <tr v-for="item in Store.model" :key="item.id">
        <td>{{ item.id }}</td>
        <td>{{ item.identifier || "–" }}</td>
        <td>{{ item.first_name || "–" }}</td>
        <td>{{ item.last_name || "–" }}</td>
        <td>{{ item.date_of_birth || "–" }}</td>
        <td>{{ item.gender || "–" }}</td>
        <td>{{ item.phone || "–" }}</td>
        <td>{{ item.email || "–" }}</td>
        <td>{{ item.address || "–" }}</td>

        <!-- ==================================================
             🎯 COLUMNA DE ACCIONES
             Botones para ver, editar y eliminar registros
        ================================================== -->
        <td>
          <!-- 🔹 Versión Desktop: botones directos -->
          <div class="d-none d-md-flex gap-2 justify-center">
            <VBtn icon size="small" color="primary" class="hover-light" @click="goShow(item.id)">
              <VIcon icon="bx-show" />
            </VBtn>
            <VBtn icon size="small" color="warning" class="hover-light" @click="goEdit(item.id)">
              <VIcon icon="bx-edit" />
            </VBtn>
            <VBtn icon size="small" color="error" class="hover-light" @click="goDelete(item.id)">
              <VIcon icon="bx-trash" />
            </VBtn>
          </div>

          <!-- 🔹 Versión Móvil: menú desplegable -->
          <div class="d-flex d-md-none justify-center">
            <VMenu offset-y>
              <template #activator="{ props }">
                <VBtn icon v-bind="props" color="grey-dark" class="hover-light">
                  <VIcon icon="bx-dots-vertical" />
                </VBtn>
              </template>

              <VList>
                <VListItem @click="goShow(item.id)" class="hover-light-list">
                  <VIcon icon="bx-show" color="primary" class="me-2" /> Ver
                </VListItem>
                <VListItem @click="goEdit(item.id)" class="hover-light-list">
                  <VIcon icon="bx-edit" color="warning" class="me-2" /> Editar
                </VListItem>
                <VListItem @click="goDelete(item.id)" class="hover-light-list">
                  <VIcon icon="bx-trash" color="error" class="me-2" /> Eliminar
                </VListItem>
              </VList>
            </VMenu>
          </div>
        </td>
      </tr>
    </tbody>
  </VTable>

  <!-- ======================================================
       🔹 LOADER LOCAL (opcional)
       Se muestra mientras Store.loading = true
  ====================================================== -->
  <VProgressLinear
    v-if="Store.loading"
    indeterminate
    color="primary"
    height="3"
    class="mt-2"
  />
</template>
