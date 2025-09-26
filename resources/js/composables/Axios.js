import axios from 'axios'
// 🔑 Helpers para saber si hay token y obtenerlo (localStorage/cookies, etc.)
import { hasToken, getToken } from '@/composables/Token'
// 📦 Pinia store con la información de usuario autenticado
import { useAuthStore } from '@/stores/auth'

/**
 * ✅ 1. Instancia global de Axios
 *    - baseURL: se arma con VITE_BASE_URL + '/api/'.
 *      ⚠️ IMPORTANTE: en el front **siempre se llama por URL**, 
 *      NO por el "name" de la ruta Laravel (->name('patients.show')).
 *      El nombre de ruta en Laravel solo sirve en el backend/PHP
 *      para helpers como route('patients.show', uuid).
 *    - headers: cabeceras por defecto para JSON.
 *    - withCredentials: true => envía cookies si el backend las requiere.
 */
const Axios = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL + '/api/',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true,
})

/**
 * ✅ 2. Interceptor de REQUEST
 *    - Se ejecuta antes de cada petición.
 *    - Si el usuario está logueado y existe token,
 *      agrega Authorization: Bearer <token> a los headers.
 */
Axios.interceptors.request.use(config => {
  const isLoggedIn = useAuthStore().userInformation.isLoggedIn
  if (hasToken() && isLoggedIn) {
    config.headers.Authorization = `Bearer ${getToken()}`
  }
  return config
})

/**
 * ✅ 3. Interceptor de RESPONSE
 *    - Se ejecuta después de recibir la respuesta.
 *    - Manejo global de errores de autenticación/autorización.
 *    - Si el backend responde 401 (no autorizado),
 *      403 (prohibido) o 419 (token/cookie expirada),
 *      elimina el token y redirige al login.
 */
Axios.interceptors.response.use(
  response => response,
  error => {
    console.log(error)
    if (
      error.response?.status === 401 ||
      error.response?.status === 403 ||
      error.response?.status === 419
    ) {
      const removeToken = useAuthStore().removeUserToken
      if (location.pathname !== '/login') {
        removeToken()
        location.assign('/login')
      }
    }
    return Promise.reject(error)
  }
)

export default Axios
