// =============================================
// 🧭 STORE BASE PARA MÓDULOS CRUD (Pinia + Axios)
// Frameworks: Vue 3 + Laravel 12
// Autor: Michael 💪
// Descripción: Estructura base para crear stores modulares en Pinia,
// con soporte para CRUD completo, manejo de errores y toasts visuales.
// =============================================

// 🔹 Importaciones principales
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // ⚙️ Axios configurado con baseURL y token JWT
import { WarningToast, SuccessToast } from "@/composables/Toast"; // 🔔 Notificaciones visuales
// import { useAppStore } from "@/stores/app"; // 🔸 Descomenta si usas loading GLOBAL (App-wide)

// =============================================
// 🏷️ Definición del Store
// defineStore(<nombre_interno>, <callback_de_estado_y_acciones>)
// =============================================
export const useRalimitStore = defineStore("ralimit", () => {
  // ============================================================
  // 🧠 ESTADO REACTIVO (State)
  // Aquí se definen las variables observables que reaccionan a los cambios.
  // ============================================================
  const model = ref([]); // 📋 Contiene la lista de registros traídos desde el backend
  const current = ref(null); // 🎯 Contiene un solo registro seleccionado (para edición o detalle)
  const loading = ref(false); // ⏳ Controla el estado de carga local (para tablas o botones)

  // ============================================================
  // ⚙️ CONFIGURACIÓN LOCAL DEL MÓDULO
  // Aquí defines constantes internas que identifican el módulo.
  // ============================================================
  const _endpoint = "patients"; // 🌐 Ruta base del recurso en Laravel (api/encounters)
  const _resourceName = "Registro"; // 🏷️ Nombre genérico del recurso (para mensajes dinámicos)

  // ============================================================
  // 🧯 MANEJADOR DE ERRORES GLOBAL LOCAL
  // Centraliza el manejo de errores HTTP para evitar repetición de código.
  // ============================================================
  const _handleError = (err) => {
    WarningToast(err.response?.data?.message || err.message);
  };

  // ============================================================
  // 🧱 ACCIONES CRUD (Create, Read, Update, Delete)
  // Cada función corresponde a un endpoint REST del backend Laravel.
  // ============================================================

  // ------------------------------------------------------------
  // 📄 index() → Listar registros
  // Parámetros opcionales: filtros, paginación o búsqueda.
  // ------------------------------------------------------------
  const index = async () => {
    loading.value = true; // Activa indicador de carga
    try {
      const { data } = await Axios.get(_endpoint);
      // Laravel devuelve { result: { data: [...] } }
      model.value = data.data|| [];
    } catch (err) {
      _handleError(err);
    } finally {
      loading.value = false; // Desactiva carga al finalizar
    }
  };

  // ------------------------------------------------------------
  // 🎯 show(id) → Obtener un registro específico
  // Útil para ver detalles o llenar un formulario de edición.
  // ------------------------------------------------------------
const show = async (id) => {
  if (!id) return null;
  try {
    const DATA = await Axios.get(`${_endpoint}/${id}`);
    current.value = DATA.data.data || null; // LARAVEL DEVUELVE asigna paciente al store
    return DATA.status; // <--LARAVEL DEVUELVE  200, 404, etc.
  } catch (err) {
    _handleError(err);
    return err.response?.status || null; // si hubo error, devuelve status HTTP
  }
};


  // ------------------------------------------------------------
  // ✳️ create(payload) → Crear nuevo registro
  // payload = datos del formulario que serán enviados al backend.
  // ------------------------------------------------------------
  const create = async (payload) => {
    try {
      const { data } = await Axios.post(_endpoint, payload);
      // Laravel normalmente responde con { message, result }
      SuccessToast(data.message || `${_resourceName} creado correctamente`);
      return data; // Devuelve la respuesta completa
    } catch (err) {
      // ⚠️ Manejo de errores HTTP específicos
      if (err.response?.status === 422)
        WarningToast("Revisa los campos obligatorios (422)");
      else if (err.response?.status === 404)
        WarningToast(err.response.data.message || "No encontrado (404)");
      else if (err.response?.status === 409)
        WarningToast(err.response.data.message || "Conflicto de datos (409)");
      else _handleError(err); // Fallback para otros errores
      return null;
    }
  };

  // ------------------------------------------------------------
  // 🛠️ update(id, payload) → Actualizar un registro existente
  // Usa método PATCH en Laravel (actualización parcial)
  // ------------------------------------------------------------
  const update = async (id, payload) => {
    if (!id) return null;
    try {
      const { data } = await Axios.put(`${_endpoint}/${id}`, payload);
      SuccessToast(data.message || `${_resourceName} actualizado correctamente`);
      return data.code;
    } catch (err) {
      // Manejo de errores comunes
      if (err.response?.status === 422)
        WarningToast("Revisa los campos obligatorios (422)");
      else if (err.response?.status === 404)
        WarningToast(err.response.data.message || "No encontrado (404)");
      else if (err.response?.status === 409)
        WarningToast(
          err.response.data.message ||
            "Encuentro finalizado no editable (409)"
        );
      else _handleError(err);
      return null;
    }
  };

  // ------------------------------------------------------------
  // 🗑️ destroy(id) → Eliminar un registro
  // Envía una petición DELETE al backend Laravel.
  // ------------------------------------------------------------
  const destroy = async (id) => {
    if (!id) return null;
    try {
      const { data } = await Axios.delete(`${_endpoint}/${id}`);
      SuccessToast(data.message || `${_resourceName} eliminado correctamente`);
      return data;
    } catch (err) {
      _handleError(err);
      return null;
    }
  };

  // ============================================================
  // 🚀 RETORNO DE ESTADO Y ACCIONES PÚBLICAS
  // Solo se exponen las propiedades y funciones necesarias.
  // ============================================================
  return {
    model, // Lista principal de registros
    current, // Registro actual en uso
    loading, // Indicador de carga local
    index, // Listar registros
    show, // Mostrar un registro
    create, // Crear un nuevo registro
    update, // Actualizar un registro existente
    destroy, // Eliminar registro
  };
});
