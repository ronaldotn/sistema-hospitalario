import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePractitionerStore = defineStore("practitioner", () => {
  const practitioners = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchPractitioners = async () => {
    loading.value = true;
    error.value = null;
    try {
      const { data } = await Axios.get("practitioners");
      practitioners.value = data.result || [];
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      error.value = msg;
    } finally {
      loading.value = false;
    }
  };

  const createPractitioner = async (form) => {
    try {
      const { data } = await Axios.post("practitioners", form);
      SuccessToast(data.message || "Practitioner creado");
      await fetchPractitioners();
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false, errors: err.response?.data?.errors };
    }
  };

  const updatePractitioner = async (uuid, form) => {
    try {
      const { data } = await Axios.put(`practitioners/${uuid}`, form);
      SuccessToast(data.message || "Practitioner actualizado");
      await fetchPractitioners();
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false, errors: err.response?.data?.errors };
    }
  };

  const deletePractitioner = async (uuid) => {
    try {
      await Axios.delete(`practitioners/${uuid}`);
      SuccessToast("Practitioner eliminado");
      await fetchPractitioners();
      return { success: true };
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
      return { success: false };
    }
  };

  return {
    practitioners,
    loading,
    error,
    fetchPractitioners,
    createPractitioner,
    updatePractitioner,
    deletePractitioner,
  };
});
