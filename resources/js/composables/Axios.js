import axios from 'axios'
import { hasToken, getToken } from '@/composables/Token'
import { useAuthStore } from '@/stores/auth'

const Axios = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL + '/api/',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true,
})

Axios.interceptors.request.use(config => {
  const isLoggedIn = useAuthStore().userInformation.isLoggedIn
  if (hasToken() && isLoggedIn) {
    config.headers.Authorization = `Bearer ${getToken()}`
  }

  return config
})

Axios.interceptors.response.use(response => response, error => {
  console.log(error)
  if(error.response?.status === 401 || error.response?.status === 403 || error.response?.status === 419) {
    const removeToken = useAuthStore().removeUserToken
    if (location.pathname !== '/login'){
      removeToken()
      location.assign('/login')
    }
  }

  return Promise.reject(error)
})

export default Axios
