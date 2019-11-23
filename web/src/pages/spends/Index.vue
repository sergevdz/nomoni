<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9 col-xs-12">
          <span class="q-ml-md grey-8 fs28 page-title">Spends</span>
        </div>
        <div class="col-sm-3 col-xs-12 pull-right">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Spends" />
            </q-breadcrumbs>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white content-panel">
        <div class="col q-pa-md">
          <div class="row q-mb-sm">
            <div class="col-sm-4">
              <q-input borderless dense filled debounce="334" v-model="filter" placeholder="Search">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
            <div class="col-sm-2 offset-sm-6 pull-right">
              <q-btn color="primary" label="New" @click.native="$router.push('/spends/new')" />
            </div>
          </div>

          <div class="row">
            <div class="col">
              <q-table
                class="my-sticky-header-table"
                :data="data"
                :columns="columns"
                row-key="id"
                :pagination.sync="pagination"
                :rows-per-page-options="rowsOptions"
                :filter="filter"
                :loading="loading"
                @request="onRequest"
                flat
                bordered
                binary-state-sort>
                <!-- <template v-slot:top-left>
                  <q-input borderless dense filled debounce="334" v-model="filter" placeholder="Search">
                    <template v-slot:append>
                      <q-icon name="search" />
                    </template>
                  </q-input>
                </template> -->

                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="format_date" :props="props">
                      {{ props.row.format_date }}
                      <q-tooltip anchor="top middle" self="center middle">
                        {{ props.row.date }}
                      </q-tooltip>
                    </q-td>
                    <q-td key="amount" :props="props">$ {{ $formatNumber(props.row.amount, 2) }}</q-td>
                    <q-td key="concept" :props="props">{{ props.row.concept }}</q-td>
                    <q-td key="type" :props="props">{{ props.row.type }}</q-td>
                    <q-td key="payment_method" :props="props">{{ props.row.payment_method }}</q-td>
                    <q-td key="category" :props="props">
                      {{ props.row.category }}
                      </q-td>
                    <q-td key="actions" :props="props">
                      <q-btn class="q-px-sm" color="primary" icon="fas fa-edit" flat size="10px" @click="editSelectedRow(props.row.id)"/>
                      <q-btn class="q-px-sm" color="primary" icon="fas fa-trash-alt" flat size="10px" @click="deleteSelectedRow(props.row.id)" />
                    </q-td>
                  </q-tr>
                </template>

              </q-table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </q-page>
</template>

<script>
import generals from '../../commons/generals.js'
import api from '../../commons/api.js'

export default {
  name: 'IndexSpends',
  data () {
    return {
      data: [],
      columns: [
        { name: 'format_date', label: 'Date', field: 'format_date', sortable: true, align: 'left' },
        { name: 'amount', label: 'Amount', field: 'amount', sortable: true, align: 'left' },
        { name: 'concept', label: 'Concept', field: 'concept', sortable: true, align: 'right' },
        { name: 'type', label: 'Type', field: 'type', sortable: true, align: 'right' },
        { name: 'payment_method', label: 'Payment Method', field: 'payment_method', sortable: true, align: 'right' },
        { name: 'category', label: 'Category', field: 'category', sortable: true, align: 'right' },
        { name: 'actions', label: 'Actions', field: 'actions', sortable: false, align: 'center' }
      ],
      pagination: generals.pagination,
      rowsOptions: generals.rowsOptions,
      filter: null,
      loading: false
    }
  },
  created () {},
  mounted () {
    this.fetchFromServer()
  },
  methods: {
    async fetchFromServer () {
      this.onRequest({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    async onRequest (props) {
      this.loading = true
      let { page, rowsPerPage, sortBy, descending } = props.pagination
      let params = {
        page: page,
        rowsPerPage: rowsPerPage,
        sortBy: sortBy,
        descending: descending,
        filter: props.filter
      }
      await api.post('spends/spends', params).then(({ data }) => {
        if (data.result) {
          this.pagination.rowsNumber = data.count
          this.data = data.spends
          this.pagination.page = page
          this.pagination.rowsPerPage = rowsPerPage
          this.pagination.sortBy = sortBy
          this.pagination.descending = descending
        }
      })
      this.loading = false
    },
    editSelectedRow (id) {
      this.$router.push(`/spends/${id}`)
    },
    deleteSelectedRow (id) {
      this.$q.dialog({
        title: 'Confirm',
        message: 'Do you want to delete this spend?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        api.delete(`/spends/${id}`).then(({ data }) => {
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
