<template>
  
        <!-- ============================================================
         🩺 TÍTULO PRINCIPAL
    ============================================================ -->
        <VRow>
            <VCol cols="12">
                <h2 class="text-h4 font-weight-bold text-primary">Departamento Clínico</h2>
                <p class="text-body-2 text-medium-emphasis mt-1">
                    Gestión integral de la información del paciente.
                </p>
            </VCol>
        </VRow>

        <!-- ============================================================
         🧩 SECCIONES CLÍNICAS
    ============================================================ -->
        <VRow >
            <!-- 🧾 Encuentros / Consultas -->
            <VCol cols="12" md="6">
                <VCard class="rounded-lg elevation-2">
                    <VCardTitle class="d-flex justify-space-between align-center">
                        <span class="text-h6 font-weight-bold">Encuentros / Consultas</span>
                        <div class="d-flex gap-2">
                            <VBtn color="primary" size="small" variant="flat">Añadir nuevo</VBtn>
                            <VBtn color="primary" variant="text" size="small">Ver todo</VBtn>
                        </div>
                    </VCardTitle>
                    <VCardText>
                        <p class="text-body-2 text-medium-emphasis mb-2">Última consulta:</p>
                        <VSheet class="pa-4 rounded ">
                            <p class="font-weight-medium">2024-07-26 - Consulta General</p>
                            <p class="text-body-2">Médico: Dr. Carlos Pérez</p>
                            <p class="text-body-2 mt-1">Notas: Revisión anual.</p>
                        </VSheet>
                    </VCardText>
                </VCard>
            </VCol>

            <!-- ⚕️ Diagnósticos -->
            <VCol cols="12" md="6">
                <VCard class="rounded-lg elevation-2">
                    <VCardTitle class="d-flex justify-space-between align-center">
                        <span class="text-h6 font-weight-bold">Diagnósticos</span>
                        <div class="d-flex gap-2">
                            <VBtn color="primary" size="small" variant="flat">Añadir nuevo</VBtn>
                            <VBtn color="primary" variant="text" size="small">Ver todo</VBtn>
                        </div>
                    </VCardTitle>
                    <VCardText>
                        <p class="text-body-2 text-medium-emphasis mb-2">Diagnósticos activos:</p>
                        <VSheet class="pa-4 rounded ">
                            <p class="font-weight-medium">
                                Hipertensión
                                <VChip color="success" size="x-small" class="ml-2">Activo</VChip>
                            </p>
                            <p class="text-body-2">Fecha: 2024-07-26</p>
                            <p class="text-body-2 mt-1">Tratamiento: Medicación.</p>
                        </VSheet>
                    </VCardText>
                </VCard>
            </VCol>

            <!-- 📝 Consentimientos -->
            <VCol cols="12" md="6">
                <VCard class="rounded-lg elevation-2">
                    <VCardTitle class="d-flex justify-space-between align-center">
                        <span class="text-h6 font-weight-bold">Consentimientos</span>
                        <div class="d-flex gap-2">
                            <VBtn color="primary" size="small" variant="flat">Añadir nuevo</VBtn>
                            <VBtn color="primary" variant="text" size="small">Ver todo</VBtn>
                        </div>
                    </VCardTitle>
                    <VCardText>
                        <p class="text-body-2 text-medium-emphasis mb-2">Último consentimiento:</p>
                        <VSheet class="pa-4 rounded ">
                            <p class="font-weight-medium">Consentimiento Quirúrgico</p>
                            <p class="text-body-2">Fecha: 2024-07-26</p>
                            <p class="text-body-2 mt-1">
                                Estado:
                                <VChip color="info" size="x-small" class="ml-2">Firmado</VChip>
                            </p>
                        </VSheet>
                    </VCardText>
                </VCard>
            </VCol>

            <!-- ⚙️ Condiciones -->
            <VCol cols="12" md="6">
                <VCard class="rounded-lg elevation-2">
                    <VCardTitle class="d-flex justify-space-between align-center">
                        <span class="text-h6 font-weight-bold">Condiciones</span>
                        <VBtn color="primary" variant="text" size="small">Ver todo</VBtn>
                    </VCardTitle>
                    <VCardText>
                        <p class="text-body-2 text-medium-emphasis mb-2">Condiciones registradas:</p>
                        <VSheet class="pa-4 rounded ">
                            <p class="font-weight-medium">Alergia a la penicilina</p>
                            <p class="text-body-2">Fecha: 2024-07-26</p>
                            <p class="text-body-2 mt-1">Notas: Documentada.</p>
                        </VSheet>
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>
  
</template>

<script setup>
// Componente base sin lógica por ahora — solo vista estructural
</script>

<style scoped>
.gap-2 {
    display: flex;
    gap: 0.5rem;
}
</style>
