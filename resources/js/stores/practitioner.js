import { ref } from "vue";
import { defineStore } from "pinia";
import Axios from "@/composables/Axios";

export const usePractitionerStore = defineStore("practitioner", () => {
    // 🔹 Colección de practitioners (plural)
    const practitioners = ref([]); 
    // 🔹 Estado de carga
    const loading = ref(false); 
    // 🔹 Mensaje de error
    const error = ref(null); 

    // 🔹 Fetch: traer todos los practitioners → index
    const fetchPractitioners = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await Axios.get("practitioners");
            practitioners.value = response.data.result; // "result" viene de tu API
        } catch (err) {
            // En caso de error, usamos message de la API si existe
            error.value = err.response?.data?.message || err.message;
        } finally {
            loading.value = false;
        }
    };

    // 🔹 Create: crear un nuevo practitioner → store
    const createPractitioner = async (form) => {
        try {
            const response = await Axios.post("practitioners", form);
            practitioners.value.push(response.data.result); // agregamos a la lista
            return response.data; // devuelvo toda la respuesta de la API
        } catch (err) {
            // Podemos usar sendError de la API para manejar errores
            throw err;
        }
    };

    // 🔹 Delete: eliminar un practitioner → destroy
    const deletePractitioner = async (uuid) => {
        try {
            await Axios.delete(`practitioners/${uuid}`);
            practitioners.value = practitioners.value.filter(p => p.uuid !== uuid);
        } catch (err) {
            console.error("Error eliminando practitioner:", err);
        }
    };

    // 🔹 Show/GET específico si lo necesitas
    const getPractitioner = async (uuid) => {
        try {
            const response = await Axios.get(`practitioners/${uuid}`);
            return response.data.result; // trae un practitioner específico
        } catch (err) {
            error.value = err.response?.data?.message || err.message;
        }
    };

    return {
        practitioners,
        loading,
        error,
        fetchPractitioners,
        createPractitioner,
        deletePractitioner,
        getPractitioner,
    };
});
