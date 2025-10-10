// ============================================================
// 🧭 STORE BASE CRUD (Pinia + Axios)
// Framework: Vue 3 + Laravel 12 API REST
// Autor: Michael 💪 (arquitectura escalable)
// ============================================================

import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const useRalimitStore = defineStore("ralimit", () => {
  /* ============================================================
     ⚙️ ESTADO REACTIVO
     ============================================================ */
  const model = ref([]);
  const current = ref(null);
  const loading = ref(false);

  /* ============================================================
     🔗 CONFIGURACIÓN LOCAL
     ============================================================ */
  const _endpoint = "ralimits"; // Endpoint base
  const _resourceName = "Registro"; // Nombre genérico del recurso

  /* ============================================================
     🚨 MANEJO GLOBAL DE ERRORES
     ============================================================ */
  const _handleError = (err) => {
    const status = err.response?.status;
    const devMessage =
      err.response?.data?.message || err.message || "Error de conexión";

    // 💻 DEV MODE → mostrar mensaje real
    WarningToast(`[${status ?? "ERR"}] ${devMessage}`);

    /* 🛡️ PROD MODE (cambiar esto antes de deploy)
       if (process.env.NODE_ENV === "production") {
         WarningToast("Error inesperado. Consulte al soporte técnico.");
       } else {
         WarningToast(`[${status}] ${devMessage}`);
       }
    */
  };

  /* ============================================================
     🔹 MÉTODOS CRUD
     ============================================================ */
  const index = async (params = {}) => {
    loading.value = true;
    try {
      const { data } = await Axios.get(_endpoint, { params });
      model.value = data.data || [];
    } catch (err) {
      _handleError(err);
    } finally {
      loading.value = false;
    }
  };

  const show = async (id) => {
    if (!id) return null;
    try {
      const { data, status } = await Axios.get(`${_endpoint}/${id}`);
      current.value = data.data || null;
      return status;
    } catch (err) {
      _handleError(err);
      return null;
    }
  };

  const create = async (payload) => {
    try {
      const { data, status } = await Axios.post(_endpoint, payload);
      SuccessToast(data.message || `${_resourceName} creado correctamente`);
      return status;
    } catch (err) {
      const code = err.response?.status;
      if (code === 422) return Promise.reject(err);
      _handleError(err);
      return null;
    }
  };

  const update = async (id, payload) => {
    if (!id) return null;
    try {
      const { data, status } = await Axios.put(`${_endpoint}/${id}`, payload);
      SuccessToast(data.message || `${_resourceName} actualizado correctamente`);
      return status;
    } catch (err) {
      if (err.response?.status === 422) return Promise.reject(err);
      _handleError(err);
      return null;
    }
  };

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

  /* ============================================================
     🚀 EXPORTACIÓN PÚBLICA
     ============================================================ */
  return {
    model,
    current,
    loading,
    index,
    show,
    create,
    update,
    destroy,
  };
});
