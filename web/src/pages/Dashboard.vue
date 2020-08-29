<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-9">
          <span class="q-ml-md grey-8 fs28 page-title">Dashboard</span>
        </div>
        <div class="col-sm-3">
          <div class="q-pa-md q-gutter-sm">
            <q-breadcrumbs align="right">
              <q-breadcrumbs-el label="" icon="home" to="/" />
              <q-breadcrumbs-el label="Dashboard" to="/dashboard" />
            </q-breadcrumbs>
          </div>
        </div>
      </div>
    </div>

    <block-content first>
      <div class="row q-mb-sm q-col-gutter-md">
        <div class="col-sm-12">
          <span class="block-title-text">Expenses summary</span>
        </div>
        <div class="col-sm-8">
          <span class="block-subtitle-text">Overview of Latest Month</span>
        </div>
        <!-- <div class="col-sm-4">
          <q-select
            v-model="selectedMonth"
            filled
            dense
            emit-value
            map-options
            :options="monthsListOptions"
            options-dense
            label="Month"
          />
        </div> -->
        <div class="col-sm-12">
          <span class="block-title-text" style="font-size: 2rem;">
            <template v-if="loadingMonthly">
              <q-spinner color="primary" size="2rem" />
            </template>
            <template v-else>
              $ {{ $formatNumber(monthlyAmount) }}
            </template>
          </span>
          <br />
          <span class="block-undertitle-text">Current month expenses</span>
        </div>

        <div class="col-sm-12">
          <span class="block-title-text">$ {{ $formatNumber(dailyAmount) }}</span>
          <br />
          <span class="block-undertitle-text">Current daily expenses</span>
        </div>
      </div>
    </block-content>

    <block-content>
      <div class="row q-mb-sm q-col-gutter-md">
        <div class="col-sm-6">
          <span class="block-title-text">Expenses</span>
          <br />
          <span class="block-undertitle-text">Overview of Last five months of Expenses</span>
        </div>
      </div>
      <div class="row q-col-gutter-md">
        <div class="col-sm-6">
          <q-markup-table separator="horizontal" flat bordered>
            <thead>
              <tr>
                <th class="text-left">Year-Month</th>
                <th class="text-right">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="month in lastFiveMonths" :key="month.label">
                <td>{{ month.label }}</td>
                <td>{{ month.value }}</td>
              </tr>
            </tbody>
          </q-markup-table>
        </div>
      </div>
    </block-content>

    <block-content>
      <div class="row q-mb-sm q-col-gutter-md">
        <div class="col-sm-6">
          <span class="block-title-text">Categories</span>
          <br />
          <span class="block-undertitle-text">Overview of Expenses by Category</span>
        </div>
      </div>
      <div class="row q-col-gutter-md">
        <div class="col-sm-6">
          <q-markup-table separator="horizontal" flat bordered>
            <thead>
              <tr>
                <th class="text-left">Category</th>
                <th class="text-right">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in groupedByCategory" :key="expense.name">
                <td>{{ expense.name }}</td>
                <td>{{ expense.amount }}</td>
              </tr>
            </tbody>
          </q-markup-table>
        </div>
      </div>
    </block-content>

    <block-content>
      <div class="row q-mb-sm q-col-gutter-md">
        <div class="col-sm-6">
          <span class="block-title-text">Types</span>
          <br />
          <span class="block-undertitle-text">Overview of Expenses by Type</span>
        </div>
      </div>
      <div class="row q-col-gutter-md">
        <div class="col-sm-6">
          <q-markup-table separator="horizontal" flat bordered>
            <thead>
              <tr>
                <th class="text-left">Type</th>
                <th class="text-right">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in groupedByType" :key="expense.name">
                <td>{{ expense.name }}</td>
                <td>{{ expense.amount }}</td>
              </tr>
            </tbody>
          </q-markup-table>
        </div>
      </div>
    </block-content>

    <block-content>
      <div class="row q-mb-sm q-col-gutter-md">
        <div class="col-sm-6">
          <span class="block-title-text">Payment methods</span>
          <br />
          <span class="block-undertitle-text">Overview of Expenses by payment Payment method</span>
        </div>
      </div>
      <div class="row q-col-gutter-md">
        <div class="col-sm-6">
          <q-markup-table separator="horizontal" flat bordered>
            <thead>
              <tr>
                <th class="text-left">Payment Method</th>
                <th class="text-right">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in groupedByPaymentMethod" :key="expense.name">
                <td>{{ expense.name }}</td>
                <td>{{ expense.amount }}</td>
              </tr>
            </tbody>
          </q-markup-table>
        </div>
      </div>
    </block-content>

  </q-page>
</template>

<script>
import api from '../commons/api.js'
import BlockContent from '../components/BlockContent'

export default {
  components: {
    BlockContent
  },
  name: 'Dashboard',
  data () {
    return {
      dailyAmount: 0,
      monthlyAmount: 0,
      loadingDaily: false,
      loadingMonthly: false,
      selectedMonth: null,
      monthsListOptions: [
        { label: 'Jan', value: '01' },
        { label: 'Feb', value: '02' },
        { label: 'Mar', value: '03' },
        { label: 'Apr', value: '04' },
        { label: 'May', value: '05' },
        { label: 'Jun', value: '06' },
        { label: 'Jul', value: '07' },
        { label: 'Aug', value: '08' },
        { label: 'Sep', value: '09' },
        { label: 'Oct', value: '10' },
        { label: 'Nov', value: '11' },
        { label: 'Dec', value: '12' }
      ],
      fakeData: [
        { monthName: 'Jan', monthsExpenses: '$ 100.00', monthsPercentage: '% 19' },
        { monthName: 'Feb', monthsExpenses: '$ 301.47', monthsPercentage: '% 299' },
        { monthName: 'Mar', monthsExpenses: '$ 400.99', monthsPercentage: '% 15' }
      ],
      groupedByCategory: [],
      groupedByType: [],
      groupedByPaymentMethod: [],
      lastFiveMonths: []
    }
  },
  computed: {},
  created () {},
  mounted () {
    this.loadAll()
  },
  methods: {
    async loadAll () {
      await this.loadExpenses()
      await this.loadTables()
      await api.get('expenses/get-last-five-months').then(({ data }) => {
        this.lastFiveMonths = data.months
      })
    },
    async loadExpenses () {
      this.loadingDaily = true
      await api.get('expenses/daily').then(({ data }) => {
        this.dailyAmount = data.dailyAmount
      })
      this.loadingDaily = false

      this.loadingMonthly = true
      await api.get('expenses/monthly').then(({ data }) => {
        this.monthlyAmount = data.monthlyAmount
      })
      this.loadingMonthly = false
    },
    async loadTables () {
      await this.getGroupedByCategory()
      await this.getGroupedByType()
      await this.getGroupedByPaymentMethod()
    },
    async getGroupedByCategory () {
      await api.get('expenses/get-grouped-by-category').then(({ data }) => {
        this.groupedByCategory = data.expenses
      })
    },
    async getGroupedByType () {
      await api.get('expenses/get-grouped-by-type').then(({ data }) => {
        this.groupedByType = data.expenses
      })
    },
    async getGroupedByPaymentMethod () {
      await api.get('expenses/get-grouped-by-payment-method').then(({ data }) => {
        this.groupedByPaymentMethod = data.expenses
      })
    }
  }
}
</script>

<style lang="stylus" scoped>
@import '../css/own.colors.styl'

.my-card
  width 100%

.block-title-text
  font-size 1.25rem
  font-weight 700
  line-height 1.2
  color: $own-black

.block-subtitle-text
  font-size 1rem
  font-weight 400
  line-height 1.2
  color: $own-grey

.block-undertitle-text
  font-size 1rem
  font-weight 300
  line-height 1.2
  color: $own-grey
</style>
