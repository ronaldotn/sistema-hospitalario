// stores/patient.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; 
import { WarningToast, SuccessToast } from "@/composables/Toast";
// ⭐ NUEVO: Importar el Store de Carga Global ⭐
import { useAppStore } from "@/stores/load"; // Asumiendo que tu App Store está aquí

export const usePatientStore = defineStore("patient", () => {
    // 🔹 0️⃣ Estado general
    const patients = ref([]); 
    const current = ref(null); 
    // MANTENEMOS: 'loading' SÓLO para la carga de la tabla (fetchPatients)
    const loading = ref(false); 

    // 🔹 1️⃣ Información de paginación (Laravel paginate)
    const meta = ref({}); 
    const links = ref([]); 

    // ⭐ NUEVO: Inicializar el App Store Global ⭐
    const appStore = useAppStore(); // Léase: "app stor"-Almacén de aplicación

    // ===============================
    // 🔹 3️⃣ Función – Obtener lista de pacientes (Usa Carga LOCAL)
    // ===============================
    const fetchPatients = async (params = {}) => {
        // 💡 Usa loading.value LOCAL para la tabla
        loading.value = true;
        try {
            const { data } = await Axios.get("patients", { params });

            patients.value = data.result?.data || [];
            meta.value = {
                current_page: data.result?.current_page,
                last_page: data.result?.last_page,
                per_page: data.result?.per_page,
                total: data.result?.total
            };
            links.value = data.result?.links || [];
        } catch (err) {
            WarningToast(err.response?.data?.message || err.message);
        } finally {
            // 💡 Usa loading.value LOCAL
            loading.value = false;
        }
    };

    // 🔹 4️⃣ Obtener un paciente específico (show) – Usa Carga GLOBAL
    const showPatient = async (uuid) => {
        if (!uuid) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.get(`patients/${uuid}`);
            current.value = data.result || null;
            return current.value;
        } catch (err) {
            WarningToast(err.response?.data?.message || err.message);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    // 🔹 5️⃣ Crear paciente (create) – Usa Carga GLOBAL
    const createPatient = async (payload) => {
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
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
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };
    
    // ⭐ ⭐ NOTA IMPORTANTE: Faltaría implementar 'update' y 'remove' si el paciente los tiene, 
    // y DEBEN usar appStore.startLoading/stopLoading si existen.

    // ===============================
    // 🔹 Exportar estados y funciones
    // ===============================
    return {
        patients,
        current,
        loading,
        meta,
        links,
        fetchPatients,
        showPatient,
        createPatient,
    };
});