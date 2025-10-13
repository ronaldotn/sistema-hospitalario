// src/components/index.js
export { default as ConfirmModal } from './ConfirmModal.vue'
export { default as StatusMessage } from './StatusMessage.vue'
export { default as StatusBadge } from './StatusBadge.vue'
export { default as FlexibleTable } from './FlexibleTable.vue'
/**
 * 🔹 Este archivo sirve como punto central de exportación
 *    para todos los componentes de esta carpeta.
 *
 * 🔹 Ventajas:
 * 1. Permite importar múltiples componentes en una sola línea:
 *      import { ConfirmModal, NoDataTable, StatusBadge } from '@/components'
 * 2. Mantiene limpio tu código y evita múltiples imports individuales.
 * 3. Escalable: si agregas nuevos componentes, solo los añades aquí.
 *
 * 🔹 Uso:
 *  - En cualquier componente Vue:
 *      <script setup>
 *?      import { ConfirmModal, NoDataTable, StatusBadge } from '@/components'
 *      </script>
 *
 *  - Luego los puedes usar directamente en tu template:
 *      <ConfirmModal />
 *      <NoDataTable />
 *      <StatusBadge />
 * ! ⚠️ Nota: cada vez que agregues un nuevo componente, solo agrega una línea aquí:
 export { default as NewComponent } from './NewComponent.vue'
 */

