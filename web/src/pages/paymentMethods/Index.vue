<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">Payment Methods</span>
        </div>
        <div class="col-sm-3 pull-right">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Payment Methods" />
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
              <q-btn color="primary" label="New" @click.native="$router.push('/payment-methods/new')" />
            </div>
          </div>

          <q-table
            flat
            bordered
            :data="data"
            :columns="columns"
            row-key="name"
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                <q-td key="icon" :props="props"><q-icon :name="props.row.icon" size="16px" /> {{ props.row.icon }}</q-td>
                <q-td key="ord" :props="props">{{ props.row.ord }}</q-td>
                <q-td key="actions" :props="props">
                  <q-btn color="primary" icon="fas fa-edit" flat @click.native="editSelectedRow(props.row.id)" size="10px" />
                  <q-btn color="primary" icon="fas fa-trash-alt" flat @click.native="deleteSelectedRow(props.row.id)" size="10px" />
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </div>
      </div>
    </div>

  </q-page>
</template>

<script>
import api from '../../commons/api.js'

export default {
  name: 'IndexPaymentMethods',
  data () {
    return {
      columns: [
        { name: 'id', align: 'left', label: '#', field: 'id', sortable: true },
        { name: 'name', align: 'left', label: 'Name', field: 'name', sortable: true },
        { name: 'icon', align: 'left', label: 'Icon', field: 'icon', sortable: true },
        { name: 'ord', align: 'right', label: 'Order', field: 'ord', sortable: true },
        { name: 'actions', align: 'center', label: 'Actions', field: 'actions', sortable: true }
      ],
      data: []
    }
  },
  mounted () {
    this.fetchFromServer()
  },
  methods: {
    fetchFromServer () {
      api.get('/payment-methods').then(({ data }) => {
        this.data = data.paymentMethods
      })
    },
    editSelectedRow (id) {
      this.$router.push(`/payment-methods/${id}`)
    },
    deleteSelectedRow (id) {
      this.$q.dialog({
        title: 'Confirm',
        message: 'Do you want to delete this payment method?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        api.delete(`/payment-methods/${id}`).then(({ data }) => {
          this.$q.dialog({
            title: data.message.title,
            message: data.message.content,
            persistent: true
          })
          if (data.result) {
            this.fetchFromServer()
          }
        })
      }).onCancel(() => {})
    }
  }
}
</script>

<style>
</style>
