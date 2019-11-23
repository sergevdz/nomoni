<template>
  <q-layout>
    <q-page-container style="background-color: #f5f5f5;">
      <q-page>
        <div class="row justify-center">
          <h2>NTV Account Login</h2>
        </div>
        <div class="row justify-center">
          <div class="col-sm-4 q-pa-md">
            <div class="row q-mb-sm">
              <div class="col-sm-1 offset-11 pull-right">
                <q-btn color="primary" label="New" style="visibility: hidden;" />
              </div>
            </div>

            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-input v-model="email" filled label="Email" v-on:keyup.enter="logIn()" />
              </div>
              <div class="col-sm-12">
                <!-- <q-input v-model="password" filled label="Password" /> -->
                <q-input v-model="password" filled label="Password" :type="isPwd ? 'password' : 'text'" v-on:keyup.enter="logIn()">
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
                <q-btn color="primary" label="Log In" :loading="loading" @click="logIn()" />
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
  created () {
    const JWT = localStorage.getItem('JWT')
    if (JWT !== null && JWT !== '') {
      this.$axios.defaults.headers.common['Authorization'] = `Bearer ${JWT}`

      this.$store.dispatch('users/getProfile').then(() => {
        const userWasLoaded = this.$store.getters['users/wasLoaded']
        if (userWasLoaded) {
          this.$router.push('/dashboard')
        } else {
          // Se queda en LOGIN
        }
      }).catch(error => {
        localStorage.removeItem('JWT')
        window.location.reload()
        console.error(error)
      })
    } else {
      // Se queda en LOGIN
    }
  },
  // mounted () {},
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
    nope () {
      // NOPE
    }
  }
}
</script>

<style>
</style>
