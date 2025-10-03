// 📦 Importaciones necesarias
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";

export const usePatientStore = defineStore("patient", () => {
  const patients = ref([]);
  const current = ref(null);
  const loading = ref(false);
  const total = ref(0);
  const count = ref(10); // Default
  const offset = ref(0);
  const sortColumn = ref("created_at");
  const sortDirection = ref("desc");

  // ==============================
  // 🔹 1️⃣ Obtener lista de pacientes
  // ==============================
  const fetchPatients = async (params = {}) => {
    loading.value = true;
    try {
      const response = await Axios.get("patients", {
        params: {
          _count: count.value,
          _offset: offset.value,
          sort: sortColumn.value,
          direction: sortDirection.value,
          identifier: params.identifier || undefined,
          name: params.name || undefined,
        },
      });

      const data = response.data.result || response.data.data || {};
      patients.value = data.patients || [];
      total.value = data.total || 0;
      count.value = data.count || 10;
      offset.value = data.offset || 0;
      sortColumn.value = data.sort || "created_at";
      sortDirection.value = data.direction || "desc";
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
    } finally {
      loading.value = false;
    }
  };

  // ==============================
  // 🔹 2️⃣ Mostrar un paciente específico por UUID
  // ==============================
  const showPatient = async (uuid) => {
    if (!uuid) return null;
    loading.value = true;
    try {
      const { data } = await Axios.get(`patients/${uuid}`);
      current.value = data.result || null;
      return current.value;
    } catch (err) {
      WarningToast(err.response?.data?.message || err.message);
      return null;
    } finally {
      loading.value = false;
    }
  };

  // ==============================
  // 🔹 3️⃣ Crear un paciente
  // ==============================
  const createPatient = async (payload) => {
    loading.value = true;
    try {
      const { data } = await Axios.post("patients", payload);
      SuccessToast(data.message || "Paciente creado correctamente");
      return data;
    } catch (err) {
      if (err.response?.status === 422) {
        WarningToast("Revisa los campos obligatorios");
        throw err;
      } else if (err.response?.status === 409) {
        WarningToast(err.response.data.message);
      } else {
        WarningToast(err.response?.data?.message || err.message);
      }
      return null;
    } finally {
      loading.value = false;
    }
  };

  return {
    patients,
    current,
    loading,
    total,
    count,
    offset,
    sortColumn,
    sortDirection,
    fetchPatients,
    showPatient,
    createPatient,
  };
});
