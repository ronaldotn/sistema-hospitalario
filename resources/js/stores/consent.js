// stores/consent.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
import { useAppStore } from "@/stores/load";

/**
 * 🔹 Store de Consents – Pinia
 * CRUD completo para la tabla `consents`
 */
export const useConsentStore = defineStore("consent", () => {

    // ==========================
    // 🔹 Estados reactivos
    // ==========================
    const model = ref([]);       // lista de consentimientos
    const current = ref(null);   // consentimiento actual
    const loading = ref(false);  // loader local
    const meta = ref({});        // metadatos de paginación
    const links = ref([]);       // links de paginación

    // ==========================
    // 🔹 Configuración base
    // ==========================
    const _endpoint = "consents";   // endpoint API Laravel
    const _resourceName = "Consentimiento";
    const appStore = useAppStore();

    // ==========================
    // 🔹 Manejo de errores
    // ==========================
    const _handleError = (err) => {
        WarningToast(err.response?.data?.message || err.message);
    };

    // ==========================
    // 🔹 CRUD PRINCIPAL
    // ==========================

    /**
     * Listar consentimientos
     */
    const fetch = async (params = {}) => {
        loading.value = true;
        try {
            const { data } = await Axios.get(_endpoint, { params });
            model.value = data.result?.data || [];
            meta.value = data.result?.meta || {};
            links.value = data.result?.links || [];
        } catch (err) {
            _handleError(err);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Obtener un consentimiento específico
     */
    const show = async (id) => {
        if (!id) return null;
        appStore.startLoading();
        try {
            const { data } = await Axios.get(`${_endpoint}/${id}`);
            current.value = data.result || null;
            return data.code;
        } catch (err) {
            _handleError(err);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    /**
     * Crear nuevo consentimiento
     */
    const create = async (payload) => {
        appStore.startLoading();
        try {
            const { data } = await Axios.post(_endpoint, payload);
            SuccessToast(data.message || `${_resourceName} creada correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422)
                WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 404)
                WarningToast(err.response.data.message || "Paciente u organización no encontrado");
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    /**
     * Actualizar consentimiento
     */
    const update = async (id, payload) => {
        if (!id) return null;
        appStore.startLoading();
        try {
            const { data } = await Axios.patch(`${_endpoint}/${id}`, payload);
            SuccessToast(data.message || `${_resourceName} actualizada correctamente`);
            return data;
        } catch (err) {
            if (err.response?.status === 422)
                WarningToast("Revisa los campos obligatorios");
            else if (err.response?.status === 404)
                WarningToast(err.response.data.message || "Registro no encontrado");
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    /**
     * Eliminar consentimiento
     */
    const remove = async (id) => {
        if (!id) return null;
        appStore.startLoading();
        try {
            const { data } = await Axios.delete(`${_endpoint}/${id}`);
            SuccessToast(data.message || `${_resourceName} eliminada correctamente`);
            return data;
        } catch (err) {
            _handleError(err);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    // ==========================
    // 🔹 Exportar
    // ==========================
    return { model, current, loading, meta, links, fetch, show, create, update, remove };
});
