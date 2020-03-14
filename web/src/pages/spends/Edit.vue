<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">Edit Spend</span>
        </div>
        <div class="col-sm-3">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Spends" to="/spends" />
              <q-breadcrumbs-el label="Edit" />
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
              <q-btn color="primary" flat dense icon="fas fa-arrow-alt-circle-left" @click="$router.push('/spends')" />
            </div>
          </div>

          <div class="row q-col-gutter-xs">
            <div class="col-sm-3">
              <q-input
                v-model="spend.fields.amount"
                :error="$v.spend.fields.amount.$error"
                label="Amount"
                filled
                prefix="$"
                maxlength="8"
                :rules="amountRules"
              />
            </div>
            <div class="col-sm-3">
              <q-input
                v-model="spend.fields.concept"
                :error="$v.spend.fields.concept.$error"
                label="Concept"
                filled
                :rules="conceptRules"
                maxlength="60"
              />
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="spend.fields.category_id"
                :error="$v.spend.fields.category_id.$error"
                filled
                emit-value
                map-options
                label="Category"
                :options="spend.categoriesOptions"
                :loading="spend.categoriesLoading"
              />
            </div>
            <div class="col-sm-3">
              <q-input
                v-model="spend.fields.date"
                label="Date"
                stack-label
                type="date"
                filled
              />
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="spend.fields.type_id"
                filled
                emit-value
                map-options
                label="Type (Optional)"
                :options="spend.typesOptions"
                :loading="spend.typesLoading"
              />
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="spend.fields.payment_method_id"
                filled
                emit-value
                map-options
                label="Payment method (Optional)"
                :options="spend.paymentMethodsOptions"
                :loading="spend.paymentMethodsLoading"
              />
            </div>
            <div class="col-sm-6">
              <q-input
                v-model="spend.fields.note"
                :error="$v.spend.fields.note.$error"
                label="Note (Optional)"
                filled
                :rules="noteRules"
                maxlength="100"
              />
            </div>
          </div>

          <div class="row q-mb-sm q-mt-md">
            <div class="col-sm-1 offset-11 pull-right">
              <q-btn color="primary" label="Save" @click="editSpend()" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import api from '../../commons/api.js'
const { required, decimal, between, maxLength } = require('vuelidate/lib/validators')

export default {
  name: 'EditSpend',
  validations: {
    spend: {
      fields: {
        amount: { required, decimal, between: between(0, 99999.99) },
        concept: { required, maxLength: maxLength(60) },
        note: { maxLength: maxLength(60) },
        // type_id: { required },
        // payment_method_id: { required },
        category_id: { required }
      }
    }
  },
  data () {
    return {
      spend: {
        fields: {
          amount: null,
          date: null,
          concept: null,
          note: null,
          type_id: null,
          payment_method_id: null,
          category_id: null
        },
        fieldsLoading: false,
        typesOptions: [],
        typesLoading: false,
        paymentMethodsOptions: [],
        paymentMethodsLoading: false,
        categoriesOptions: [],
        categoriesLoading: false
      }
    }
  },
  computed: {
    amountRules (val) {
      return [
        val => (this.$v.spend.fields.amount.required) || 'Amount field is required',
        val => (this.$v.spend.fields.amount.between) || 'Amount must be a value between 0 and 99999.99'
      ]
    },
    conceptRules (val) {
      return [
        val => (this.$v.spend.fields.concept.required) || 'Concept field is required',
        val => (this.$v.spend.fields.concept.maxLength) || 'Concept length must be less than 60 characters'
      ]
    },
    noteRules (val) {
      return [
        val => (this.$v.spend.fields.note.maxLength) || 'Description length must be less than 60 characters'
      ]
    },
    categoryIdRules (val) {
      return [
        val => (this.$v.spend.fields.category_id.required) || 'Category field is required.'
      ]
    }
  },
  created () {},
  mounted () {
    this.spend.fieldsLoading = true
    const id = this.$route.params.id
    api.get(`/spends/${id}`).then(({ data }) => {
      if (data.result) {
        this.spend.fields = data.spend
        this.spend.fields.date = `${data.spend.date}`
        this.fetchFromServer()
      } else {
        this.$showMessage(data.message.title, data.message.content)
      }
    })
    this.spend.fieldsLoading = false
  },
  methods: {
    async fetchFromServer () {
      this.spend.typesLoading = true
      await api.get('/types/options').then(({ data }) => {
        this.spend.typesOptions = data.options
      })
      this.spend.typesLoading = false

      this.spend.paymentMethodsLoading = true
      await api.get('/payment-methods/options').then(({ data }) => {
        this.spend.paymentMethodsOptions = data.options
      })
      this.spend.paymentMethodsLoading = false

      this.spend.categoriesLoading = true
      await api.get('/categories/options').then(({ data }) => {
        this.spend.categoriesOptions = data.options
      })
      this.spend.categoriesLoading = false
    },
    editSpend () {
      if (this.spend.fields.id > 0) {
        this.$v.spend.fields.$reset()
        this.$v.spend.fields.$touch()
        if (this.$v.spend.fields.$error) {
          this.$q.dialog({
            title: 'Warning!',
            message: 'Please check validations.',
            persistent: true
          })
          return false
        }
        this.$q.dialog({
          title: 'Confirm',
          message: 'Do you want to edit the spend?',
          cancel: true,
          persistent: true
        }).onOk(() => {
          let params = { ...this.spend.fields }
          api.put(`/spends/${params.id}`, params).then(({ data }) => {
            this.$q.notify({
              // color: 'primary',
              // textColor,
              icon: 'far fa-check-circle',
              message: data.message.title + ' ' + data.message.content,
              // caption: data.message.content,
              position: 'top-right',
              // avatar,
              multiLine: true,
              actions: [ { label: 'Dismiss', color: 'positive', handler: () => {} } ],
              timeout: 2500
            })
            // this.$q.dialog({
            //   title: data.message.title,
            //   message: data.message.content,
            //   persistent: true
            // })
            if (data.result) {
              this.$router.push('/spends')
            }
          })
        }).onCancel(() => {})
      }
    }
  }
}
</script>

<style>
</style>
