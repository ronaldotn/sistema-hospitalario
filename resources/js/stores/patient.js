import { ref } from "vue"; // 📦 API de composición de Vue (para variables reactivas)
import { defineStore } from "pinia"; // 🏪 defineStore: crea un store de Pinia
import Axios from "@/composables/Axios"; // 🌐 Instancia global de Axios para llamadas HTTP

/**
 * 🏗️ BASE/PLANTILLA DEL STORE
 * ----------------------------------------------
 * Esta estructura se repite en la mayoría de stores Pinia:
 *
 * 1️⃣ defineStore("nombre", () => { ... })
 *     - "nombre": ID único del store, usado al importarlo.
 *     - Callback: función que retorna los estados (state),
 *       propiedades computadas (getters) y acciones (actions).
 *
 * 2️⃣ State Reactivo:
 *     - const algo = ref(valorInicial)
 *       Cada ref representa una propiedad reactiva del estado global.
 *
 * 3️⃣ Retorno:
 *     - return { ... }
 *       Expones los states y métodos para poder usarlos en componentes.
 *
 * ⚠️ Los métodos (fetchPatients, createPatient, etc.) cambian según el módulo;
 *     pero esta “base” (imports, defineStore, state con ref, return) se repite.
 * todo:estructure
 // Traer todos los practitioners (fetch = "obtener/traer") → index
const fetchPractitioners = async () => Axios.get("practitioners"); 

// Crear un nuevo practitioner → store
const createPractitioner = async (form) => Axios.post("practitioners", form); 

// Actualizar un practitioner existente → update
const updatePractitioner = async (id, form) => Axios.put(`practitioners/${id}`, form); 

// Eliminar un practitioner → destroy
const deletePractitioner = async (id) => Axios.delete(`practitioners/${id}`); 

// Traer un practitioner específico (fetch = "obtener/traer") → show
const getPractitioner = async (id) => Axios.get(`practitioners/${id}`); 

 */
export const usePatientStore = defineStore("patient", () => {
  // ---------- 🌱 STATE GLOBAL ----------
  const patients = ref([]); // Lista reactiva de pacientes
  const loading = ref(false); // Indicador de carga
  const error = ref(null); // Manejo de errores

  // ---------- 🔄 ACCIONES ESPECÍFICAS ----------
  // (Estos métodos cambian según el caso de uso)
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
  // ---------- 🏁 RETORNO DEL STORE ----------
  return {
    patients,
    loading,
    error,
    fetchPatients,
    createPatient,
    deletePatient,
  };
});
