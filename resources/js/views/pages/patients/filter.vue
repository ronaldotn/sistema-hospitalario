<script setup>
import { ref, toRefs } from "vue";

const props = defineProps({
    modelValue: { type: Object, required: true }
});

const { modelValue } = toRefs(props);
const emit = defineEmits(["update:modelValue", "apply-filters"]);

const filtersDrawerOpen = ref(false);

const updateFilter = (key, value) => {
    emit("update:modelValue", { ...modelValue.value, [key]: value });
};

const applyQuickFilters = () => {
    emit("apply-filters", { ...modelValue.value });
};
</script>

<template>
    <!-- Barra de filtros rápidos -->
    <VCard class="mb-4 pa-3 d-flex justify-space-between align-center elevation-2">
        <!-- Botón abrir filtros avanzados -->
        <VBtn icon color="secondary" title="Filtros Avanzados" class="rounded-circle" @click="filtersDrawerOpen = true">
            <VIcon icon="bx-filter-alt" size="24" />
        </VBtn>

        <div class="d-flex flex-wrap gap-3 align-center flex-grow-1 justify-center">
            <VTextField v-model="modelValue.identifier" label="Documento" placeholder="12345678"
                prepend-inner-icon="bx-id-card" density="comfortable" clearable
                @update:model-value="updateFilter('identifier', $event)" class="flex-grow-0"
                style="min-width:180px;max-width:250px" />
            <VTextField v-model="modelValue.name" label="Nombre/Apellido" placeholder="John Doe"
                prepend-inner-icon="bx-user" density="comfortable" clearable
                @update:model-value="updateFilter('name', $event)" class="flex-grow-0"
                style="min-width:180px;max-width:250px" />
            <VBtn color="primary" height="40" @click="applyQuickFilters">
                Filtrar
            </VBtn>
        </div>
    </VCard><!-- Drawer lateral oficial de Vuetify -->
    <VNavigationDrawer v-model="filtersDrawerOpen" location="right" temporary width="380">
        <!-- Header plano con línea inferior -->
        <VToolbar flat class="px-4 py-3" style="border-bottom: 1px solid #e0e0e0;">
            <VToolbarTitle class="text-h6 font-weight-medium">Filtros Avanzados</VToolbarTitle>
            <VSpacer />
            <VBtn icon variant="tonal" color="grey-lighten-2" @click="filtersDrawerOpen = false">
                <VIcon icon="bx-x" size="22" />
            </VBtn>
        </VToolbar>

        <!-- Contenido -->
        <VCard flat>
            <VCardText>
                <p class="text-center">Filtros avanzados próximamente</p>
            </VCardText>
            <VCardActions class="justify-end pa-4">
                <VBtn variant="tonal" color="primary" @click="filtersDrawerOpen = false">
                    Aplicar
                </VBtn>
            </VCardActions>
        </VCard>
    </VNavigationDrawer>

</template>
