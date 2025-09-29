// stores/patient.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePatientStore = defineStore("patient", () => {
  const patients = ref([]);
  const total = ref(0);
  const loading = ref(false);
  const error = ref(null);

  // 🔹 Filtros y paginación
  const filters = ref({
    identifier: "",
    name: "",
    _count: 10,
    _offset: 0
  });

  /**
   * 🔹 fetchPatients
   * Recibe filtros extra y combina con los actuales
   */
  const fetchPatients = async (extra = {}) => {
    loading.value = true;
    error.value = null;

    filters.value = { ...filters.value, ...extra };

    try {
      const { data } = await Axios.get("patients", { params: filters.value });
      patients.value = data.result.items || [];
      total.value = data.result.total || 0;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      WarningToast(error.value);
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 deletePatient
   * Elimina paciente y refresca la lista automáticamente
   */
  const deletePatient = async (uuid) => {
    if (!uuid) return;
    loading.value = true;
    try {
      await Axios.delete(`patients/${uuid}`);
      SuccessToast("Paciente eliminado");
      await fetchPatients(); // refresca lista con filtros actuales
    } catch (err) {
      const msg = err.response?.data?.message || err.message;
      WarningToast(msg);
    } finally {
      loading.value = false;
    }
  };

  return {
    patients,
    total,
    loading,
    error,
    filters,
    fetchPatients,
    deletePatient
  };
});
