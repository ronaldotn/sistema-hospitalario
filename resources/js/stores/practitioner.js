// 🏥 Store de Practitioners – Pinia
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
import { useAppStore } from "@/stores/load"; // Carga global

export const usePractitionerStore = defineStore("practitioner", () => {
    // 🔹 Estado general
    const model = ref([]);
    const current = ref(null);
    const loading = ref(false);       // Carga local (tabla)
    const lookupList = ref([]);       // Lookup rápido
    const meta = ref({});
    const links = ref([]);

    // Catálogo de especialidades
    const specialties = ref([
        "Oftalmología", "Radiología", "Psiquiatría",
        "Endocrinología", "Dermatología", "Gastroenterología",
        "Laboratorio Clínico", "Cardiología", "Pediatría", "Neurología", "Anestesiología"
    ]);

    // Store de carga global
    const appStore = useAppStore();

    // =========================
    // 🔹 Fetch paginado con filtros
    // =========================
    const fetch = async (params = {}) => {
        loading.value = true;
        try {
            const { data } = await Axios.get("practitioners", { params });
            model.value = data.result?.data || [];
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
            loading.value = false;
        }
    };

    // =========================
    // 🔹 Mostrar un profesional específico
    // =========================
    const show = async (uuid) => {
        if (!uuid) return null;
        appStore.startLoading();
        try {
            const { data } = await Axios.get(`practitioners/${uuid}`);
            current.value = data.result || null;
            return current.value;
        } catch (err) {
            WarningToast(err.response?.data?.message || err.message);
            return null;
        } finally {
            appStore.stopLoading();
        }
    };

    // =========================
    // 🔹 Lookup rápido
    // =========================
    const lookup = async () => {
        try {
            const { data } = await Axios.get("practitioners/lookup");
            lookupList.value = data.result;
            return data.result || [];
        } catch (err) {
            WarningToast(err.response?.data?.message || err.message);
            return [];
        }
    };

    // =========================
    // 🔹 Verificar campo único en tiempo real
    // =========================
    const checkUnique = async (field, value) => {
        if (!value) return false;
        try {
            const { data } = await Axios.post("practitioners/check", { field, value });
            return data.result.exists;
        } catch (err) {
            return false;
        }
    };

    // =========================
    // 🔹 Crear profesional
    // =========================
    const create = async (payload) => {
        appStore.startLoading();
        try {
            const { data } = await Axios.post("practitioners", payload);
            SuccessToast(data.message || "Profesional creado correctamente");
            return data.result || null;
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
            appStore.stopLoading();
        }
    };

    // =========================
    // 🔹 Actualizar profesional
    // =========================
    const update = async (uuid, payload) => {
        appStore.startLoading();
        try {
            const { data } = await Axios.put(`practitioners/${uuid}`, payload);
            SuccessToast(data.message || "Profesional actualizado correctamente");
            return data.result || null;
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
            appStore.stopLoading();
        }
    };

    // =========================
    // 🔹 Exportar estados y funciones
    // =========================
    return {
        model,
        current,
        loading,
        lookupList,
        meta,
        links,
        specialties,
        fetch,
        show,
        lookup,
        checkUnique,
        create,
        update,
    };
});
