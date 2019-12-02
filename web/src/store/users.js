import api from '../commons/api.js'

export default {
  namespaced: true,
  state: {
    id: null,
    roleId: null,
    firstName: null,
    wasLoaded: false
  },
  mutations: {
    setId (state, id) {
      state.id = id
    },
    setFirstName (state, firstName) {
      state.firstName = firstName
    },
    setWasLoaded (state, wasLoaded) {
      state.wasLoaded = wasLoaded
    },
    setRoleId (state, roleId) {
      state.roleId = roleId
    }
  },
  actions: {
    getProfile: async (context) => {
      let response = await api.get('users/profile')
      if (response.data.result) {
        context.commit('setId', response.data.user.id)
        context.commit('setRoleId', response.data.user.role_id)
        context.commit('setFirstName', response.data.user.first_name)
        context.commit('setWasLoaded', true)
      }
      return response
    }
  },
  getters: {
    id: state => state.id,
    roleId: state => state.roleId,
    firstName: state => state.firstName,
    wasLoaded: state => state.wasLoaded
  }
}
