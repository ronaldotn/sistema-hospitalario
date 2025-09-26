import { ref } from "vue"; // 📦 API de composición de Vue, para variables reactivas
import { defineStore } from "pinia"; // 🏪 defineStore crea un store de Pinia
import Axios from "@/composables/Axios"; // 🌐 Instancia global de Axios con interceptores y token
import { WarningToast, SuccessToast } from "@/composables/Toast"; // 🟢 Manejo de notificaciones

// ✅ Store de Practitioners
export const usePractitionerStore = defineStore("practitioner", () => {
  
  // ---------- 🌱 STATE GLOBAL ----------
  const practitioners = ref([]); // Lista reactiva de practitioners
  const loading = ref(false);     // Indicador de carga global: siempre presente en fetch o create

  // ---------- 🔄 ACCIONES / MÉTODOS ----------

  // 🔹 Traer todos los practitioners → Index
  const fetchPractitioners = async () => {
    loading.value = true;
    try {
      const response = await Axios.get("practitioners");
      practitioners.value = response.data.result;
      return { success: true, data: practitioners.value };
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message); // ⚠️ Toast de error
      return { success: false, message: err.response?.data?.message || err.message };
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Crear un practitioner → Store maneja errores y mensajes
  const createPractitioner = async (form) => {
    try {
      const response = await Axios.post("practitioners", form);
      practitioners.value.push(response.data.result);
      SuccessToast(response.data.message); // ✅ Mostrar toast de éxito
      return { success: true };
    } catch (err) {
      if (err.response?.status === 422) {
        WarningToast("Errores de validación"); // ⚠️ Toast de validación
        return { success: false, errors: err.response.data.errors };
      } else if (err.response?.status === 409) {
        WarningToast(err.response.data.message); // ⚠️ Toast de conflicto
        return { success: false };
      } else {
        WarningToast(err.response?.data?.message || "Error desconocido"); // ⚠️ Otro error
        return { success: false };
      }
    }
  };

  // 🔹 Eliminar un practitioner → Destroy
  const deletePractitioner = async (uuid) => {
    try {
      await Axios.delete(`practitioners/${uuid}`);
      practitioners.value = practitioners.value.filter(p => p.uuid !== uuid);
      SuccessToast("Practitioner eliminado"); // ✅ Toast de éxito
      return { success: true };
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message); // ⚠️ Toast de error
      return { success: false };
    }
  };

  // 🔹 Obtener un practitioner específico → Show
  const getPractitioner = async (uuid) => {
    try {
      const response = await Axios.get(`practitioners/${uuid}`);
      return { success: true, data: response.data.result };
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message); // ⚠️ Toast de error
      return { success: false, message: err.response?.data?.message || err.message };
    }
  };

  // ---------- 🏁 RETORNO DEL STORE ----------
  return {
    practitioners,
    loading,
    fetchPractitioners,
    createPractitioner,
    deletePractitioner,
    getPractitioner,
  };
});
