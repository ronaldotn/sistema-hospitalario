import { defineStore } from 'pinia'
import Axios from '@/composables/Axios'
import { setToken, removeToken, hasToken } from '@/composables/Token'

export const useAuthStore = defineStore('auth', {
  state: () => {
    return {
      isLoggedIn: false,
      name: null,
      id: null,
      email: null,
      loading: false,
    }
  },
  actions: {
    async login(email, password, remember) {
      const credentials = { email, password, remember }

      this.loading = true
      try {
        
        const response = await Axios.post('login', credentials)

        this.name = response.data.result.name

        // this.id = response.data.user.id
        this.email = response.data.result.email
        this.isLoggedIn = true

        return response.data
      } catch (ex) {

        const error = { status: ex.status, message: ex.response.data.message }

        return Promise.reject(error)
      } finally {
        this.loading = false
      }
    },
    async register(name, email, password, password_confirmation) {
      const user = { name, email, password, password_confirmation }
      try {
        
        const response = await Axios.post('register', user)

        console.log(response)

        return response.data
      } catch (ex) {
        console.log(ex)
        const error = { status: ex.status, message: ex.response.data }

        return Promise.reject(error)
      } finally {
        this.loading = false
      }
    },
    async logout() {
      try {
        const response = await Axios.post('logout')

        this.isLoggedIn = false
        this.id = null
        this.name = null
        this.email = null

        return response.data
      } catch (ex) {

        const error = { status: ex.status, message: ex.response.data.message }

        return Promise.reject(error)
      }
    },
    setUserToken(token) {
      setToken(token)
    },
    removeUserToken() {
      removeToken()
    },
  },
  getters: {
    userInformation(state) {
      return { isLoggedIn: state.isLoggedIn, name: state.name, id: state.id, email: state.email }
    },
  },
  persist: true,
})
