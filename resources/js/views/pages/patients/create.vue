<script setup>
// 📦 Imports
import { ref } from "vue";
import { useRouter } from "vue-router";   // Para redirecciones
import { usePatientStore } from "@/stores/patient"; // Nuestro Store global

// 🚦 Router y Store
const router = useRouter();
const patientStore = usePatientStore();

// 🔹 Estado local del formulario
const form = ref({
    identifier: "",   // Documento
    first_name: "",   // Nombre
    last_name: "",    // Apellidos
    date_of_birth: "",// Fecha de nacimiento
    gender: "",       // Sexo
    address: "",      // Dirección
    phone: "",        // Teléfono
    email: "",        // Correo
});

// 🔹 Función al enviar formulario
const submitForm = async () => {
    // Usamos el store para crear el paciente
    const response = await patientStore.createPatient(form.value);

    // Si se creó correctamente, redirigimos a la lista
    if (response) {
        router.push({ name: "patients-index" });
    }
};
</script>

<template>
    <!-- 🏷️ Encabezado -->
    <VCard class="mb-4 elevation-2 rounded-lg">
        <VCardTitle class="d-flex justify-space-between align-center px-6 py-4">
            <h2 class="text-h5 font-weight-bold text-primary">Crear Paciente</h2>
            <VBtn prepend-icon="bx-arrow-back" color="secondary" variant="tonal"
                @click="router.push({ name: 'patients-index' })">
                Volver
            </VBtn>
        </VCardTitle>
    </VCard>

    <!-- 📋 Formulario -->
    <VCard class="elevation-1 rounded-lg">
        <VCardText>
            <!-- Evitamos submit nativo con prevent -->
            <VForm @submit.prevent="submitForm">
                <VRow :gap="4">
                    <!-- Nombre -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.first_name" prepend-inner-icon="bx-user" label="Nombre"
                            placeholder="John" density="comfortable" required />
                    </VCol>

                    <!-- Apellidos -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.last_name" prepend-inner-icon="bx-user" label="Apellidos"
                            placeholder="Doe" density="comfortable" required />
                    </VCol>

                    <!-- Documento -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.identifier" prepend-inner-icon="bx-id-card"
                            label="Documento de Identidad" placeholder="12345678" density="comfortable" required />
                    </VCol>

                    <!-- Fecha de nacimiento -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.date_of_birth" prepend-inner-icon="bx-calendar"
                            label="Fecha de Nacimiento" type="date" density="comfortable" required />
                    </VCol>

                    <!-- Sexo -->
                    <VCol cols="12" md="6">
                        <VSelect v-model="form.gender" prepend-inner-icon="bx-gender-male-female" label="Sexo" :items="[
                            { title: 'Masculino', value: 'male' },
                            { title: 'Femenino', value: 'female' },
                            { title: 'Otro', value: 'other' },
                            { title: 'Desconocido', value: 'unknown' }
                        ]" item-title="title" item-value="value" density="comfortable" required />
                    </VCol>


                    <!-- Dirección -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.address" prepend-inner-icon="bx-map" label="Dirección"
                            placeholder="Calle 123, Ciudad" density="comfortable" />
                    </VCol>

                    <!-- Teléfono -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.phone" prepend-inner-icon="bx-phone" label="Teléfono"
                            placeholder="+591 7 1234567" density="comfortable" />
                    </VCol>

                    <!-- Correo -->
                    <VCol cols="12" md="6">
                        <VTextField v-model="form.email" prepend-inner-icon="bx-envelope" label="Correo"
                            placeholder="correo@ejemplo.com" type="email" density="comfortable" />
                    </VCol>

                    <!-- Botones -->
                    <VCol cols="12" class="d-flex justify-end gap-3 mt-4">
                        <VBtn type="reset" color="secondary" variant="tonal" prepend-icon="bx-eraser">
                            Limpiar
                        </VBtn>
                        <VBtn type="submit" color="primary" prepend-icon="bx-save">
                            Guardar
                        </VBtn>
                    </VCol>
                </VRow>
            </VForm>
        </VCardText>
    </VCard>
</template>

<style scoped>
/* 🎨 Mejorar estética de los inputs con icono */
.v-input__prepend-inner .v-icon {
    color: #4f46e5;
    /* Indigo moderno */
}

/* 🎨 Animación de las cards */
.v-card {
    transition: all 0.2s ease;
}

.v-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}
</style>
