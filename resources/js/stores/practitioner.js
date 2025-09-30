// stores/practitioner.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePractitionerStore = defineStore("practitioner", () => {
  // 🔹 Estado principal
  const practitioners = ref([]);  // lista de practitioners
  const loading = ref(false);     // indicador de carga general

  /**
   * 🔹 fetchPractitioners
   * Obtiene la lista de practitioners
   */
  const fetchPractitioners = async () => {
    loading.value = true;
    try {
      const { data } = await Axios.get("practitioners");
      practitioners.value = data.result || [];
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 showPractitioner
   * Obtiene un practitioner específico por uuid
   */
  const showPractitioner = async (uuid) => {
    if (!uuid) return null;
    loading.value = true;

    try {
      const { data } = await Axios.get(`practitioners/${uuid}`);
      return data.result || null; // devuelve practitioner individual
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return null;
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 createPractitioner
   * Crea un nuevo practitioner
   */
  const createPractitioner = async (form) => {
    loading.value = true;
    try {
      const { data } = await Axios.post("practitioners", form);
      SuccessToast(data.message || "Practitioner creado correctamente");
      await fetchPractitioners(); // refresca lista
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false, errors: err.response?.data?.errors };
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 updatePractitioner
   * Actualiza un practitioner existente
   */
  const updatePractitioner = async (uuid, form) => {
    if (!uuid) return { success: false };
    loading.value = true;

    try {
      const { data } = await Axios.put(`practitioners/${uuid}`, form);
      SuccessToast(data.message || "Practitioner actualizado correctamente");
      await fetchPractitioners(); // refresca lista
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false, errors: err.response?.data?.errors };
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 deletePractitioner
   * Elimina un practitioner y refresca la lista
   */
  const deletePractitioner = async (uuid) => {
    if (!uuid) return { success: false };
    loading.value = true;

    try {
      await Axios.delete(`practitioners/${uuid}`);
      SuccessToast("Practitioner eliminado");
      await fetchPractitioners(); // refresca lista
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false };
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Retorno de estado y métodos
  return {
    practitioners,        // lista de practitioners
    loading,              // indicador de carga
    fetchPractitioners,   // obtener lista
    showPractitioner,     // obtener practitioner individual
    createPractitioner,   // crear
    updatePractitioner,   // actualizar
    deletePractitioner    // eliminar
  };
});
