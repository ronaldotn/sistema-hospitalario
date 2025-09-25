import { defineStore } from 'pinia'
import Axios from '@/composables/Axios'
export const usePatientStore = defineStore('patient', () => {
  // Estado reactivo
  const patients = ref([])

  // Obtener pacientes
  const fetchPatients = async () => {
    try {
      const response = await Axios.get('patients')
      patients.value = response.data.data
    } catch (error) {
      console.error('Error fetching patients:', error)
    }
  }

  // Eliminar paciente
  const deletePatient = async (uuid) => {
    if (!confirm('¿Seguro que deseas eliminar este paciente?')) return
    try {
      await Axios.delete(`patients/${uuid}`)
      patients.value = patients.value.filter(p => p.uuid !== uuid)
    } catch (error) {
      console.error('Error eliminando paciente:', error)
    }
  }

  return { patients, fetchPatients, deletePatient }
})
