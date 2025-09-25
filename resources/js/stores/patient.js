import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";

export const usePatientStore = defineStore("patient", () => {
  const patients = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchPatients = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await Axios.get("patients");
      patients.value = response.data.result;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
    } finally {
      loading.value = false;
    }
  };

  const createPatient = async (form) => {
    try {
      const response = await Axios.post("patients", form);
      patients.value.push(response.data.result);
      return response.data;
    } catch (err) {
      throw err;
    }
  };

  const deletePatient = async (uuid) => {
    try {
      await Axios.delete(`patients/${uuid}`);
      patients.value = patients.value.filter((p) => p.uuid !== uuid);
    } catch (err) {
      console.error("Error eliminando paciente:", err);
    }
  };

  return {
    patients,
    loading,
    error,
    fetchPatients,
    createPatient,
    deletePatient,
  };
});
