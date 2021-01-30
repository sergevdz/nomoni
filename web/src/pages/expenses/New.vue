<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">New Expense</span>
        </div>
        <div class="col-sm-3">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Expenses" to="/expenses" />
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
              <q-btn color="primary" flat dense icon="fas fa-arrow-alt-circle-left" @click="$router.push('/expenses')" />
            </div>
          </div>

          <div class="row q-col-gutter-xs">
            <div class="col-sm-3">
              <q-input
                v-model="expense.fields.amount"
                :error="$v.expense.fields.amount.$error"
                label="Amount"
                filled
                prefix="$"
                maxlength="8"
                :rules="amountRules"
              />
            </div>
            <div class="col-sm-3">
              <q-input
                v-model="expense.fields.concept"
                :error="$v.expense.fields.concept.$error"
                label="Concept"
                filled
                :rules="conceptRules"
                maxlength="60"
              />
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="expense.fields.category_id"
                :error="$v.expense.fields.category_id.$error"
                filled
                emit-value
                map-options
                label="Category"
                :options="expense.categoriesOptions"
                :loading="expense.categoriesLoading"
              />
            </div>
            <div class="col-sm-3">
              <q-input filled v-model="expense.fields.date" mask="date" :rules="['date']">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="expense.fields.date" @input="closeDialog">
                        <div class="row items-center justify-end">
                          <!-- <q-btn v-close-popup label="Close" color="primary" flat @input="closeDialog" /> -->
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="expense.fields.type_id"
                filled
                emit-value
                map-options
                label="Type (Optional)"
                :options="expense.typesOptions"
                :loading="expense.typesLoading"
              />
            </div>
            <div class="col-sm-3">
              <q-select
                v-model="expense.fields.payment_method_id"
                filled
                emit-value
                map-options
                label="Payment method (Optional)"
                :options="expense.paymentMethodsOptions"
                :loading="expense.paymentMethodsLoading"
              />
            </div>
            <div class="col-sm-6">
              <q-input
                v-model="expense.fields.note"
                :error="$v.expense.fields.note.$error"
                label="Note (Optional)"
                filled
                :rules="noteRules"
                maxlength="100"
              />
            </div>
          </div>

          <div class="row q-mb-sm q-mt-md">
            <div class="col-sm-4 offset-8 pull-right">
              <q-btn color="orange" label="Save & Continue" @click="createExpenseNContinue()" />
              &nbsp;
              <q-btn color="primary" label="Save" @click="createExpense()" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import api from '../../commons/api.js'
import mix from '../../commons/mix.js'
const { required, decimal, between, maxLength } = require('vuelidate/lib/validators')

export default {
  name: 'NewExpense',
  mixins: [ mix ],
  validations: {
    expense: {
      fields: {
        amount: { required, decimal, between: between(0, 99999.99) },
        concept: { required, maxLength: maxLength(60) },
        note: { maxLength: maxLength(100) },
        category_id: { required }
      }
    }
  },
  data () {
    return {
      expense: {
        fields: {
          amount: null,
          date: this.$today(),
          concept: null,
          note: null,
          type_id: 1,
          payment_method_id: 3,
          category_id: null
        },
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
        val => (this.$v.expense.fields.amount.required) || 'Amount field is required',
        val => (this.$v.expense.fields.amount.between) || 'Amount must be a value between 0 and 99999.99'
      ]
    },
    conceptRules (val) {
      return [
        val => (this.$v.expense.fields.concept.required) || 'Concept field is required',
        val => (this.$v.expense.fields.concept.maxLength) || 'Concept length must be less than 60 characters'
      ]
    },
    noteRules (val) {
      return [
        val => (this.$v.expense.fields.note.maxLength) || 'Description length must be less than 60 characters'
      ]
    },
    categoryIdRules (val) {
      return [
        val => (this.$v.expense.fields.category_id.required) || 'Category field is required.'
      ]
    }
  },
  created () {},
  mounted () {
    this.fetchFromServer()
  },
  methods: {
    async fetchFromServer () {
      this.expense.typesLoading = true
      await api.get('/types/options').then(({ data }) => {
        this.expense.typesOptions = data.options
      })
      this.expense.typesLoading = false

      this.expense.paymentMethodsLoading = true
      await api.get('/payment-methods/options').then(({ data }) => {
        this.expense.paymentMethodsOptions = data.options
      })
      this.expense.paymentMethodsLoading = false

      this.expense.categoriesLoading = true
      await api.get('/categories/options').then(({ data }) => {
        this.expense.categoriesOptions = data.options
      })
      this.expense.categoriesLoading = false
    },
    createExpense () {
      this.$v.expense.fields.$reset()
      this.$v.expense.fields.$touch()
      if (this.$v.expense.fields.$error) {
        this.$q.dialog({
          title: 'Warning!',
          message: 'Please check validations.',
          persistent: true
        })
        return false
      }
      let params = { ...this.expense.fields }
      api.post('/expenses', params).then(({ data }) => {
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
          this.$router.push('/expenses')
        }
      })
    },
    createExpenseNContinue () {
      this.$v.expense.fields.$reset()
      this.$v.expense.fields.$touch()
      if (this.$v.expense.fields.$error) {
        this.$showMessage('Warning!', 'Please check validations.')
        // this.$q.dialog({
        //   title: 'Warning!',
        //   message: 'Please check validations.',
        //   persistent: true
        // })
        return false
      }
      let params = { ...this.expense.fields }
      api.post('/expenses', params).then(({ data }) => {
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
          this.expense.fields.amount = null
          this.expense.fields.concept = null
          this.expense.fields.note = null
          // this.$router.push('/expenses')
        }
      })
    }
  }
}
</script>

<style>
</style>
