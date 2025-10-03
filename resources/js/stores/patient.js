// 📦 Importaciones necesarias
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios configurado con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";

// 🏥 Definimos un Store llamado "patient"
export const usePatientStore = defineStore("patient", () => {
  // 🔹 Estado global que podrá ser usado en cualquier componente
  const patients = ref([]);      // Lista de pacientes para tablas
  const current = ref(null);     // Paciente seleccionado (show / edit)
  const loading = ref(false);    // Indicador de carga (spinner, botones)
  const total = ref(0);          // Total de registros (para paginación)
  const count = ref(0);          // Cantidad de registros por página (backend define si no se envía)
  const offset = ref(0);         // Offset desde el inicio de registros
  const sortColumn = ref("created_at"); // Columna por defecto para ordenar
  const sortDirection = ref("desc");    // Dirección por defecto de ordenamiento

  // ==============================
  // 🔹 1️⃣ Obtener lista de pacientes
  // ==============================
  const fetchPatients = async (params = {}) => {
    loading.value = true; // Activamos el estado de carga
    try {
      // Hacemos GET al backend
      const response = await Axios.get("patients", {
        params: {
          _count: params._count,        // opcional: frontend puede enviar, si no backend decide
          _offset: params._offset,      // opcional: frontend puede enviar, si no backend decide
          sort: sortColumn.value,
          direction: sortDirection.value,
          identifier: params.identifier || undefined, // filtro por documento
          name: params.name || undefined,             // filtro por nombre/apellido
        },
      });

      // 🔹 Normalizamos la respuesta del backend
      const data = response.data.result || response.data.data || {};
      patients.value = data.patients || [];
      total.value = data.total || 0;
      count.value = data.count || 20;   // si backend no envía
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
        throw err; // El formulario maneja los errores
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

  // ==============================
  // 🔹 Exportamos el store
  // ==============================
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
