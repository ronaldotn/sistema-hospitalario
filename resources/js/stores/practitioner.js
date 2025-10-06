// 📦 Importaciones necesarias
import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios"; // Axios con baseURL y token
import { WarningToast, SuccessToast } from "@/composables/Toast";
// ⭐ NUEVO: Importar el Store de Carga Global ⭐
import { useAppStore } from "@/stores/load"; // Léase: "iús app stor"-Usar almacén de aplicación

// 🏥 Store de Practitioners – Pinia
export const usePractitionerStore = defineStore("practitioner", () => {
    // 🔹 0️⃣ Estado general
    const model = ref([]); 
    const current = ref(null); 
    // MANTENEMOS: 'loading' SÓLO para la carga de la tabla (fetch)
    const loading = ref(false); 

    // 🔹 1️⃣ Información de paginación (Laravel paginate)
    const meta = ref({}); 
    const links = ref([]); 

    // ⭐ NUEVO: Inicializar el App Store Global ⭐
    const appStore = useAppStore();

    // ===============================
    // 🔹 3️⃣ Función – Obtener lista (Usa Carga LOCAL)
    // ===============================
    const fetch = async (params = {}) => {
        // 💡 Usa loading.value LOCAL para la tabla
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
            // 💡 Usa loading.value LOCAL
            loading.value = false;
        }
    };

    // 🔹 4️⃣ Obtener un profesional específico (show) – Usa Carga GLOBAL
    const show = async (uuid) => {
        if (!uuid) return null;
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        try {
            const { data } = await Axios.get(`practitioner/${uuid}`);
            current.value = data.result || null;
            return current.value;
        } catch (err) {
            WarningToast(err.response?.data?.message || err.message);
            return null;
        } finally {
            appStore.stopLoading(); // ⚡ Detiene carga GLOBAL
        }
    };

    // 🔹 5️⃣ Crear profesional (create) – Usa Carga GLOBAL
    const create = async (payload) => {
        appStore.startLoading(); // ⚡ Usa carga GLOBAL (Header)
        console.log(payload);
        try {
            const { data } = await Axios.post("practitioners", payload);
            SuccessToast(data.message || "Profesional creado correctamente");
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

    // ==============================
    // 🔹 6️⃣ Exportar estados y funciones
    // ==============================
    return {
        model,
        current,
        loading,
        meta,
        links,
        fetch,
        show,
        create,
    };
});