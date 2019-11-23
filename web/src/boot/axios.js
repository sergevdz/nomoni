import axios from 'axios'

export default async ({ Vue }) => {
  axios.defaults.baseURL = process.env.API
  Vue.prototype.$axios = axios
}
