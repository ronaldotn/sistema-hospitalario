// stores/organization.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
// ⭐ NUEVO: Importar el Store de Carga Global (Asegúrate que la ruta sea correcta) ⭐
import { useAppStore } from "@/stores/load"; 

/**
 * 🔹 Store de Organizations – Pinia
 * - CRUD completo
 */
export const useOrganizationStore = defineStore("organization", () => {
    // 🔹 Estado reactivo
    const model = ref([]); 
    const current = ref(null); 
    // MANTENEMOS: 'loading' SÓLO para la carga de la tabla (fetch)
    const loading = ref(false); 
    const meta = ref({});
    const links = ref([]);

    const _endpoint = "organizations";
    const _resourceName = "Organización";

    // ⭐ NUEVO: Inicializar el App Store Global ⭐
    const appStore = useAppStore();

    // 🔹 Función privada para manejo centralizado de errores
    const _handleError = (err) => {
        WarningToast(err.response?.data?.message || err.message);
    };

    // ==============================
    // 🔹 Funciones CRUD públicas (Actualizadas con lógica dual de loading)
    // ==============================

    /**
     * Listar registros (fetch) – Usa el loading LOCAL (para la tabla).
     */
    const fetch = async (params = {}) => {
        loading.value = true;
        try {
            const { data } = await Axios.get(_endpoint, { params });
            model.value = data.result?.data || [];
            // Tu lógica de meta/links
            meta.value = {
                current_page: data.result?.current_page,
                last_page: data.result?.last_page,
                per_page: data.result?.per_page,
                total: data.result?.total,
            };
            links.value = data.result?.links || [];
        } catch (err) {
            _handleError(err);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Obtener un registro específico (show) – Usa el loading GLOBAL (Header).
     */
    const show = async (id) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL
        try {
            const { data } = await Axios.get(`${_endpoint}/${id}`);
            current.value = data.result || null;
            return current.value;
        } catch (err) {
            _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Crear registro (create) – Usa el loading GLOBAL (Header).
     */
    const create = async (payload) => {
        appStore.startLoading(); // ⚡ Usa carga GLOBAL
        try {
            const { data } = await Axios.post(_endpoint, payload);
            SuccessToast(data.message || `${_resourceName} creado correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422) WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 409) WarningToast(err.response.data.message);
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Actualizar registro (update) – Usa el loading GLOBAL (Header).
     */
    const update = async (id, payload) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL
        try {
            const { data } = await Axios.put(`${_endpoint}/${id}`, payload);
            SuccessToast(data.message || `${_resourceName} actualizado correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422) WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 409) WarningToast(err.response.data.message);
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    /**
     * Eliminar registro (remove) – Usa el loading GLOBAL (Header).
     */
    const remove = async (id) => {
        if (!id) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL
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