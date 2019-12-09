<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">Edit Category {{ $route.params.id }}</span>
        </div>
        <div class="col-sm-3">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Types" to="/categories" />
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
              <q-btn color="primary" flat dense icon="fas fa-arrow-alt-circle-left" @click="$router.push('/categories')" />
            </div>
          </div>

          <div class="row q-col-gutter-xs">
            <div class="col-sm-4">
              <q-input
                v-model="category.fields.name"
                :error="$v.category.fields.name.$error"
                label="Name"
                filled
                :rules="nameRules"
              />
            </div>
            <div class="col-sm-4">
              <q-input
                v-model="category.fields.icon"
                label="Icon"
                filled
              >
                <template v-slot:append>
                  <q-icon color="primary" :name="category.fields.icon" />
                </template>
              </q-input>
            </div>
            <div class="col-sm-4">
              <q-input
                v-model="category.fields.ord"
                filled
                label="Order"
                mask="#"
                hint="Format: #"
              />
            </div>
          </div>

          <div class="row q-mb-sm q-mt-md">
            <div class="col-sm-1 offset-11 pull-right">
              <q-btn color="primary" label="Save" @click="editType()" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import api from '../../commons/api.js'
const { required } = require('vuelidate/lib/validators')

export default {
  name: 'EditCategory',
  validations: {
    category: {
      fields: {
        name: { required }
      }
    }
  },
  data () {
    return {
      category: {
        fields: {
          id: null,
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
        val => (this.$v.category.fields.name.required) || 'Name field is required.'
      ]
    }
  },
  created () {
    const id = this.$route.params.id
    api.get(`/categories/${id}`).then(({ data }) => {
      this.category.fields = data.category
      this.category.fields.ord = `${data.category.ord}`
    })
  },
  mounted () {},
  methods: {
    editType () {
      this.$v.category.fields.$reset()
      this.$v.category.fields.$touch()
      if (this.$v.category.fields.$error) {
        this.$q.dialog({
          title: 'Warning!',
          message: 'Please check validations.',
          persistent: true
        })
        return false
      }
      this.$q.dialog({
        title: 'Confirm',
        message: 'Do you want to edit the category?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        const params = { ...this.category.fields }
        api.put(`/categories/${params.id}`, params).then(({ data }) => {
          this.$q.dialog({
            title: data.message.title,
            message: data.message.content,
            persistent: true
          })
          if (data.result) {
            this.$router.push('/categories')
          }
        })
      }).onCancel(() => {})
    }
  }
}
</script>

<style>
</style>
