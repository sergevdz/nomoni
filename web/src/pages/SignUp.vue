<template>
  <q-layout>
    <q-page-container style="background-color: #f5f5f5;">
      <q-page>
        <div class="row justify-center">
          <h2>Sign Up</h2>
        </div>
        <div class="row justify-center">
          <div class="col-sm-4 q-pa-md">

            <div class="row q-pa-sm bg-white shadow-1">
              <div class="col-xs-12">
                <q-input
                  v-model="first_name"
                  label="First name"
                  stack-label
                  :error="$v.first_name.$error"
                  :rules="firstNameRules" />
              </div>
              <div class="col-xs-12">
                  <q-input
                  v-model="last_name"
                  label="Last name"
                  stack-label
                  :error="$v.last_name.$error"
                  :rules="lastNameRules" />
              </div>
              <div class="col-xs-12">
                <q-input
                  v-model="email"
                  label="Email"
                  stack-label
                  :error="$v.email.$error"
                  :rules="emailRules" />
              </div>
              <div class="col-xs-12">
                <q-input
                  v-model="password"
                  label="Password"
                  stack-label
                  :error="$v.password.$error"
                  :rules="passwordRules"
                  :type="isPwd ? 'password' : 'text'">
                  <template v-slot:append>
                    <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
              </div>
              <div class="col-xs-12">
                 <q-input
                  v-model="confirmPassword"
                  label="Confirm password"
                  stack-label
                  :error="$v.confirmPassword.$error"
                  :rules="confirmPasswordRules"
                  :type="isPwd ? 'password' : 'text'">
                  <template v-slot:append>
                    <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
              </div>
              <div class="col-xs-12 q-mt-sm">
                <q-btn color="red" label="Back to login" :loading="loading" @click="$router.push('/login')" class="full-width" />
              </div>
              <div class="col-xs-12 q-mt-sm">
                <q-btn color="green" label="Sign Up" :loading="loading" @click="signUp()" class="full-width" />
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
const { required, email } = require('vuelidate/lib/validators')

export default {
  name: 'SignUp',
  validations: {
    first_name: { required },
    last_name: { required },
    email: { required, email },
    password: { required },
    confirmPassword: { required }
  },
  computed: {
    firstNameRules (val) {
      return [
        val => (this.$v.first_name.required) || 'First name is required.'
      ]
    },
    lastNameRules (val) {
      return [
        val => (this.$v.last_name.required) || 'Last name is required.'
      ]
    },
    emailRules (val) {
      return [
        val => (this.$v.email.required) || 'Email is required.',
        val => (this.$v.email.email) || 'Please enter a valid email.'
      ]
    },
    passwordRules (val) {
      return [
        val => (this.$v.password.required) || 'Password is required'
      ]
    },
    confirmPasswordRules (val) {
      return [
        val => (this.$v.confirmPassword.required) || 'Confirm password is required'
      ]
    }
  },
  data () {
    return {
      email: null,
      password: null,
      first_name: null,
      last_name: null,
      isPwd: true,
      confirmPassword: null,
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
          // It stays in SIGNUP
        }
      }).catch(error => {
        localStorage.removeItem('JWT')
        window.location.reload()
        console.error(error)
      })
    } else {
      // It stays in SIGNUP
    }
  },
  methods: {
    signUp () {
      this.$v.$touch()
      if (this.$v.$error) {
        // this.$q.dialog({
        //   title: 'Warning!',
        //   message: 'Please check validations.',
        //   persistent: true
        // })
        return false
      }
      let params = {
        email: this.email,
        password: this.password,
        first_name: this.first_name,
        last_name: this.last_name,
        confirmPassword: this.confirmPassword
      }
      this.loading = true
      api.post('/auth/signup', params).then(({ data }) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result) {
          this.$router.push('/login')
        }
        this.loading = false
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
