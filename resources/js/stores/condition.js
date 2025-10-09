// stores/condition.js
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
import { useAppStore } from "@/stores/load"; // Carga global

/**
 * 🔹 Store de Conditions – Pinia
 * CRUD completo para la tabla `conditions`
 *
 * 📘 Convenciones y buenas prácticas:
 * - El nombre del store sigue el patrón `use<Nombre>MóduloStore`.
 *   Ejemplo: `useConditionStore` → "use" + "Condition" + "Store".
 * - Este patrón hace más fácil la lectura y autocompletado en proyectos grandes.
 * - El primer argumento de `defineStore()` es un identificador único en Pinia.
 *   Se recomienda usar el nombre en singular o igual al módulo (en este caso: "condition"),
 *   ya que actúa como **ID interno** del store dentro de Pinia.
 * - `defineStore` es una función nativa de Pinia que crea un "store" reactivo y reutilizable,
 *   equivalente a un módulo Vuex pero más ligero, declarativo y escalable.
 */
export const useConditionStore = defineStore("condition", () => {

    // ==========================
    // 🔹 Estados reactivos
    // ==========================

    // Contiene la lista completa de registros obtenidos desde la API (paginados o filtrados).
    // Se usa principalmente para mostrar en tablas o listados (ej. FlexibleTable).
    const model = ref([]);

    // Contiene los datos de una sola condición (registro individual seleccionado).
    // Se usa para ver detalles, editar o mostrar en formularios.
    const current = ref(null);

    // Controla el estado de carga local del listado (fetch).
    // No afecta el loader global del encabezado.
    const loading = ref(false);

    // Metadatos de paginación (Laravel paginated response: meta, links)
    const meta = ref({});
    const links = ref([]);


    // ==========================
    // 🔹 Configuración base
    // ==========================
    const _endpoint = "conditions"; // endpoint API Laravel
    const _resourceName = "Condición"; // nombre para mensajes

    // Store global de carga
    const appStore = useAppStore();

    // ==========================
    // 🔹 Manejador de errores
    // ==========================
    const _handleError = (err) => {
        WarningToast(err.response?.data?.message || err.message);
    };

    // ==========================
    // 🔹 CRUD PRINCIPAL
    // ==========================

    /**
     * Listar registros (fetch)
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
     * Obtener una condición (show)
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
     * Crear nueva condición (create)
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
                WarningToast(err.response.data.message || "Paciente o encuentro no encontrado");
            else if (err.response?.status === 409)
                WarningToast(err.response.data.message || "Conflicto al crear la condición");
            else _handleError(err);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    /**
     * Actualizar condición (update)
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
     * Eliminar condición (remove)
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
