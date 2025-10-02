import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePatientStore = defineStore("patient", () => {
  // 🔹 Estado global del store
  const patients = ref([]);      // Lista completa de pacientes (index.vue)
  const current = ref(null);     // Paciente seleccionado (show/update)
  const total = ref(0);          // Total de registros para paginación
  const loading = ref(false);    // Indicador de carga general

  // 🔹 Filtros para la lista de pacientes
  const filters = ref({ identifier: "", name: "", _count: 10, _offset: 0 });

  // 🔹 Obtener lista de pacientes con filtros y paginación
  const fetchPatients = async (extra = {}) => {
    loading.value = true;
    filters.value = { ...filters.value, ...extra };

    try {
      const { data } = await Axios.get("patients", { params: filters.value });
      patients.value = data.result.items || [];  // Guardar lista
      total.value = data.result.total || 0;      // Guardar total de registros
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Obtener un paciente específico para show/update
  const showPatient = async (uuid) => {
    if (!uuid) return null;
    loading.value = true;
    try {
      const { data } = await Axios.get(`patients/${uuid}`);
      current.value = data.result || null;      // Guardar paciente actual
      return current.value;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      return null;
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Crear un nuevo paciente
  const createPatient = async (payload) => {
    loading.value = true;
    try {
      const { data } = await Axios.post("patients", payload);
      SuccessToast(data.message || "Paciente creado correctamente");
      await fetchPatients(); // Refresca lista automáticamente
      return data;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Actualizar un paciente existente
  const updatePatient = async (uuid, payload) => {
    if (!uuid) return;
    loading.value = true;
    try {
      const { data } = await Axios.put(`patients/${uuid}`, payload);
      SuccessToast(data.message || "Paciente actualizado correctamente");
      return data;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Eliminar un paciente
  const deletePatient = async (uuid) => {
    if (!uuid) return;
    loading.value = true;
    try {
      await Axios.delete(`patients/${uuid}`);
      SuccessToast("Paciente eliminado");
      await fetchPatients(); // Refresca lista después de eliminar
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
    } finally {
      loading.value = false;
    }
  };

  // 🔹 Retorno de estado y métodos para usar en componentes
  return {
    patients,       // Lista de pacientes (index)
    current,        // Paciente actual (show/update)
    total,          // Total de registros
    loading,        // Indicador de carga
    filters,        // Filtros de búsqueda y paginación
    fetchPatients,  // Obtener lista
    showPatient,    // Obtener paciente individual
    createPatient,  // Crear paciente
    updatePatient,  // Actualizar paciente
    deletePatient   // Eliminar paciente
  };
});
