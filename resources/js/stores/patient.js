// stores/patient.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePatientStore = defineStore("patient", () => {
  // 🔹 Estado principal
  const patients = ref([]);  // lista de pacientes
  const total = ref(0);      // total de registros
  const loading = ref(false); // indicador general de carga

  // 🔹 Filtros y paginación
  const filters = ref({
    identifier: "",
    name: "",
    _count: 10,
    _offset: 0
  });

  /**
   * 🔹 fetchPatients
   * Obtiene la lista de pacientes con filtros
   */
  const fetchPatients = async (extra = {}) => {
    loading.value = true;

    filters.value = { ...filters.value, ...extra };

    try {
      const { data } = await Axios.get("patients", { params: filters.value });
      patients.value = data.result.items || [];
      total.value = data.result.total || 0;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 showPatient
   * Obtiene un paciente específico por uuid
   */
  const showPatient = async (uuid) => {
    if (!uuid) return null;
    loading.value = true;

    try {
      const { data } = await Axios.get(`patients/${uuid}`);
      return data.result || null;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      return null;
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 createPatient
   * Crea un nuevo paciente
   */
  const createPatient = async (payload) => {
    loading.value = true;

    try {
      const { data } = await Axios.post("patients", payload);
      SuccessToast(data.message || "Paciente creado correctamente");
      await fetchPatients(); // refresca la lista automáticamente
      return data;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 updatePatient
   * Actualiza un paciente existente por uuid
   */
  const updatePatient = async (uuid, payload) => {
    if (!uuid) return;
    loading.value = true;

    try {
      const { data } = await Axios.put(`patients/${uuid}`, payload);
      SuccessToast(data.message || "Paciente actualizado correctamente");
      await fetchPatients(); // refresca la lista para reflejar cambios
      return data;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * 🔹 deletePatient
   * Elimina un paciente y refresca la lista automáticamente
   */
  const deletePatient = async (uuid) => {
    if (!uuid) return;
    loading.value = true;

    try {
      await Axios.delete(`patients/${uuid}`);
      SuccessToast("Paciente eliminado");
      await fetchPatients(); // refresca lista con filtros actuales
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Retorno de estado y métodos
  return {
    patients,       // lista de pacientes
    total,          // total de registros
    loading,        // indicador de carga
    filters,        // filtros para fetch
    fetchPatients,  // método para obtener lista
    showPatient,    // método para obtener paciente específico
    createPatient,  // método para crear paciente
    updatePatient,  // método para actualizar paciente
    deletePatient   // método para eliminar paciente
  };
});
