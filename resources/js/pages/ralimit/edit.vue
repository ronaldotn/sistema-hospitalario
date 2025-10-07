<script setup>
/* ============================================================
   🔹 IMPORTACIONES PRINCIPALES
 ============================================================ */
import { ref, onMounted } from "vue";                  // onMounted → hook ciclo de vida
import { useRouter, useRoute } from "vue-router";      // useRouter → navegación, useRoute → params de la ruta
import { useRalimitStore } from "@/stores/ralimit";    // Store modular Pinia para CRUD


/* ============================================================
   ⚙️ CONFIGURACIÓN DEL RECURSO
   ============================================================ */
const router = useRouter();    // Para redireccionar
const route = useRoute();      // Para obtener params de la ruta (ej: id del paciente)
const Store = useRalimitStore(); // Instancia del store
// 🎯 Form reactivo
const form = ref({
    patient_id: '',
    identifier: '',
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: '',
    phone: '',
    email: '',
    address: ''
});

// 💥 Objeto para errores
const errors = ref({});

// 🔹 Control para mostrar el formulario solo cuando los datos estén cargados
const isLoaded = ref(false);

// 🔹 Cargar datos del paciente existente
onMounted(async () => {
    const id = route.params.id;
    try {
        const code = await Store.show(id); // show() llena Store.current y retorna code
        if (code === 200) {
            console.log("Datos del paciente cargados:", Store.current);
            form.value = { ...Store.current };
            isLoaded.value = true;
        } else {
            console.error("No se pudieron obtener los datos del paciente.");
        }
    } catch (err) {
        console.error("Error al cargar paciente:", err);
    }
});

// ============================================================
// 🔹 Función para enviar la actualización
// ============================================================
const submitForm = async () => {
    errors.value = {};
    try {
        const id = route.params.id;
        const response = await Store.update(id, form.value);
        if (response) router.push({ path: '/patients-index' });
    } catch (err) {
        if (err.response?.status === 422) errors.value = err.response.data.errors || {};
    }
};
</script>

<template>
    <!-- Cabecera -->
    <VCard class="mb-4 elevation-2 rounded-lg">
        <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
            <h2 class="text-h5 font-weight-bold text-primary">Editar Paciente</h2>
            <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
                @click="router.push({ path: '/patients-index' })">
                Volver
            </VBtn>
        </VCardTitle>
    </VCard>

    <!-- Loader mientras se cargan los datos -->
    <VProgressLinear v-if="!isLoaded" indeterminate color="primary" height="3" class="mt-2" />

    <!-- Formulario -->
    <VCard v-if="isLoaded" class="elevation-1 rounded-lg">
        <VCardText>
            <VForm @submit.prevent="submitForm">
                <VRow>
                    <VCol cols="12">
                        <VTextField v-model="form.identifier" label="Identifier" placeholder="CI, pasaporte, seguro"
                            required :error-messages="errors.value?.identifier" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VTextField v-model="form.first_name" label="First Name" placeholder="John" required
                            :error-messages="errors.value?.first_name" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VTextField v-model="form.last_name" label="Last Name" placeholder="Doe" required
                            :error-messages="errors.value?.last_name" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VTextField v-model="form.date_of_birth" label="Date of Birth" type="date"
                            :error-messages="errors.value?.date_of_birth" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VSelect v-model="form.gender" :items="['male', 'female', 'other', 'unknown']" label="Gender"
                            placeholder="Select gender" :error-messages="errors.value?.gender" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VTextField v-model="form.phone" label="Phone" placeholder="+591 7 123 4567"
                            :error-messages="errors.value?.phone" />
                    </VCol>

                    <VCol cols="12" md="6">
                        <VTextField v-model="form.email" label="Email" type="email" placeholder="example@mail.com"
                            :error-messages="errors.value?.email" />
                    </VCol>

                    <VCol cols="12">
                        <VTextField v-model="form.address" label="Address" placeholder="Street, city, country" rows="2"
                            multiline :error-messages="errors.value?.address" />
                    </VCol>

                    <VCol cols="12" class="d-flex gap-4">
                        <VBtn type="submit" color="primary">Guardar</VBtn>
                        <VBtn type="reset" color="secondary" variant="tonal" @click="form.value = {}">
                            Limpiar
                        </VBtn>
                    </VCol>
                </VRow>
            </VForm>
        </VCardText>
    </VCard>
</template>
