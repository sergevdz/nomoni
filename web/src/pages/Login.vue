<template>
  <q-layout>
    <q-page-container style="background-color: #f5f5f5;">
      <q-page>
        <div class="row justify-center">
          <h2>Nomoni</h2>
        </div>
        <div class="row justify-center">
          <div class="col-sm-4 q-pa-md">

            <div class="row q-pa-sm bg-white shadow-1">
              <div class="col-sm-12">
                <q-input v-model="email" label="Email" stack-label @keyup.enter="logIn()" />
              </div>
              <div class="col-sm-12">
                <q-input v-model="password" label="Password" stack-label :type="isPwd ? 'password' : 'text'" @keyup.enter="logIn()">
                  <template v-slot:append>
                    <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
              </div>
              <div class="col-sm-12 q-mt-sm pull-right">
                <q-btn color="green" label="Sign Up" :loading="loading" @click="goToSignUp()" class="full-width" />
              </div>
              <div class="col-sm-12 q-mt-sm pull-right">
                <q-btn color="primary" label="Log In" :loading="loading" @click="logIn()" class="full-width" />
              </div>
            </div>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import api from '../commons/api.js' // El api pasa los params por: 'qs.stringify'

export default {
  name: 'Login',
  data () {
    return {
      email: null,
      password: null,
      isPwd: true,
      loading: false
    }
  },
  // computed: {},
  // beforeCreate () {},
  mounted () {
    const JWT = localStorage.getItem('JWT')
    if (JWT !== null && JWT !== '') {
      this.$axios.defaults.headers.common['Authorization'] = `Bearer ${JWT}`

      this.$store.dispatch('users/getProfile').then(() => {
        const userWasLoaded = this.$store.getters['users/wasLoaded']
        if (userWasLoaded) {
          this.$router.push('/dashboard')
        } else {
          // It stays in LOGIN
        }
      }).catch(error => {
        localStorage.removeItem('JWT')
        window.location.reload()
        console.error(error)
      })
    } else {
      // It stays in LOGIN
    }
  },
  methods: {
    logIn () {
      let params = {
        email: this.email,
        password: this.password
      }
      this.loading = true
      api.post('/auth/login', params).then(({ data }) => {
        this.loading = false
        if (data.result) {
          localStorage.setItem('JWT', data.jwt)
          this.$axios.defaults.headers.common['Authorization'] = `Bearer ${data.jwt}`

          this.$store.dispatch('users/getProfile').then(({ data }) => {
            this.$router.push(data.result ? '/' : '/login')
          }).catch(error => {
            localStorage.removeItem('JWT')
            window.location.reload()
            console.error(error)
          })
        } else {
          this.$q.dialog({
            title: data.message.title,
            message: data.message.content,
            persistent: true
          })
        }
      }).catch(error => {
        this.loading = false
        console.error(error)
      })
    },
    goToSignUp () {
      this.$router.push('/signup')
    },
    nope () {
      // NOPE
    }
  }
}
</script>

<style>
</style>
