<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">New Payment Method</span>
        </div>
        <div class="col-sm-3">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Payment Methods" to="/payment-methods" />
              <q-breadcrumbs-el label="New" />
            </q-breadcrumbs>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white content-panel">
        <div class="col q-pa-md">
          <div class="row q-mb-sm">
            <div class="col-sm-1 offset-11 pull-right">
              <q-btn color="primary" flat dense icon="fas fa-arrow-alt-circle-left" @click="$router.push('/payment-methods')" />
            </div>
          </div>

          <div class="row q-col-gutter-xs">
            <div class="col-sm-4">
              <q-input
                v-model.trim="paymentMethod.fields.name"
                :error="$v.paymentMethod.fields.name.$error"
                label="Name"
                filled
                :rules="nameRules"
              />
            </div>
            <div class="col-sm-4">
              <q-input
                v-model.trim="paymentMethod.fields.icon"
                :error="$v.paymentMethod.fields.icon.$error"
                label="Icon"
                filled
                :rules="iconRules"
              >
                <template v-slot:append>
                  <q-icon color="primary" :name="paymentMethod.fields.icon" />
                </template>
              </q-input>
            </div>
            <div class="col-sm-4">
              <q-input
                v-model.trim="paymentMethod.fields.ord"
                :error="$v.paymentMethod.fields.ord.$error"
                filled
                label="Order"
                mask="#"
                hint="Format: #"
                :rules="ordRules"
              />
            </div>
          </div>

          <div class="row q-mb-sm q-mt-md">
            <div class="col-sm-1 offset-11 pull-right">
              <q-btn color="primary" label="Save" @click="createType()" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import api from '../../commons/api.js'
const { required, integer, between } = require('vuelidate/lib/validators')

export default {
  name: 'NewPaymentMethod',
  validations: {
    paymentMethod: {
      fields: {
        name: { required },
        icon: { required },
        ord: { required, integer, between: between(0, 9) }
      }
    }
  },
  data () {
    return {
      paymentMethod: {
        fields: {
          name: null,
          icon: null,
          ord: null
        }
      }
    }
  },
  computed: {
    nameRules (val) {
      return [
        val => (this.$v.paymentMethod.fields.name.required) || 'Name field is required.'
      ]
    },
    iconRules (val) {
      return [
        val => (this.$v.paymentMethod.fields.icon.required) || 'Icon field is required.'
      ]
    },
    ordRules (val) {
      return [
        val => this.$v.paymentMethod.fields.ord.required || 'Order field is required.',
        val => this.$v.paymentMethod.fields.ord.integer || 'Order field must be an integer.',
        val => this.$v.paymentMethod.fields.ord.between || 'Order field value must be between 0 and 9.'
      ]
    }
  },
  created () {},
  mounted () {},
  methods: {
    createType () {
      this.$v.paymentMethod.fields.$reset()
      this.$v.paymentMethod.fields.$touch()
      if (this.$v.paymentMethod.fields.$error) {
        this.$q.dialog({
          title: 'Warning!',
          message: 'Please check validations.',
          persistent: true
        })
        return false
      }
      this.$q.dialog({
        title: 'Confirm',
        message: 'Do you want to create the payment method?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        let params = { ...this.paymentMethod.fields }
        api.post('/payment-methods', params).then(({ data }) => {
          this.$q.dialog({
            title: data.message.title,
            message: data.message.content,
            persistent: true
          })
          if (data.result) {
            this.$router.push('/payment-methods')
          }
        })
      }).onCancel(() => {})
    }
  }
}
</script>

<style>
</style>
