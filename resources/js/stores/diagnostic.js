// stores/encounter.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
// ⭐ NUEVO: Importar el Store de Carga Global ⭐
import { useAppStore } from "@/stores/load"; 

/**
 * 🔹 Store de Encounters – Pinia
 * - CRUD completo
 */
export const useDiagnosticStore = defineStore("diagnostics", () => {
    // 🔹 Estados reactivos
    const model = ref([]); 
    const current = ref(null); 
    // ⭐ MANTENEMOS: 'loading' SÓLO para la carga de la tabla (fetch) ⭐
    const loading = ref(false); 
    const meta = ref({});
    const links = ref([]);

    const _endpoint = "diagnostics";
    const _resourceName = "Diagnostic";

    // ⭐ NUEVO: Inicializar el App Store Global ⭐
    const appStore = useAppStore();

    // ... (Tu función _handleError)
    const _handleError = (err) => {
        WarningToast(err.response?.data?.message || err.message);
    };

    // ==============================
    // 🔹 Funciones CRUD públicas (Actualizadas)
    // ==============================

    /**
     * Listar registros (fetch) – Usa el loading LOCAL para la tabla.
     */
    const fetch = async (params = {}) => {
        // 💡 Usa loading.value LOCAL
        loading.value = true;
        try {
            const { data } = await Axios.get(_endpoint, { params });
            model.value = data.result?.data || [];
            meta.value = data.result?.meta || {};
            links.value = data.result?.links || [];
        } catch (err) {
            _handleError(err);
        } finally {
            // 💡 Usa loading.value LOCAL
            loading.value = false;
        }
    };

    /**
     * Obtener un registro específico (show) – Usa el loading GLOBAL.
     */
    const show = async (id) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.get(`${_endpoint}/${id}`);
            current.value = data.result || null;
            return data.code;
        } catch (err) {
            _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Crear un encounter (create) – Usa el loading GLOBAL.
     */
    const create = async (payload) => {
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.post(_endpoint, payload);
            SuccessToast(data.message || `${_resourceName} creado correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422) WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 404) WarningToast(err.response.data.message || "Paciente o profesional no encontrado");
            else if (err.response?.status === 409) WarningToast(err.response.data.message || "Conflicto al crear el encounter");
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Actualizar encounter (update) – Usa el loading GLOBAL.
     */
    const update = async (id, payload) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.patch(`${_endpoint}/${id}`, payload);
            SuccessToast(data.message || `${_resourceName} actualizado correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422) WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 404) WarningToast(err.response.data.message || "Paciente o profesional no encontrado");
            else if (err.response?.status === 409) WarningToast(err.response.data.message || "Encuentro finalizado no editable");
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Eliminar encounter (remove) – Usa el loading GLOBAL.
     */
    const remove = async (id) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.delete(`${_endpoint}/${id}`);
            SuccessToast(data.message || `${_resourceName} eliminado correctamente`);
            return data;
        } catch (err) {
            _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    // 🔹 Exportamos estados y funciones públicas
    return { model, current, loading, meta, links, fetch, show, create, update, remove };
});