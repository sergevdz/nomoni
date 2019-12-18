<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
        >
          <q-icon name="menu" />
        </q-btn>

        <q-toolbar-title>
          Quasar App
        </q-toolbar-title>

        <!-- <div>Quasar v{{ $q.version }}</div> -->
        <div>
          <template>
            <div class="">
              <q-btn-dropdown color="white" flat :label="`${firstName}`">
                <q-list>

                  <q-item clickable v-close-popup @click="logOut()">
                    <q-item-section>
                      <q-item-label>Perfil</q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item clickable v-close-popup @click="logOut()">
                    <q-item-section>
                      <q-item-label>Cerrar sesión</q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
            </div>
          </template>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      content-class="bg-white"
      :width="256"
      :breakpoint="700"
      :mini="false"
    >
      <q-list>
        <q-item-label header>Main</q-item-label>
        <q-item to="/dashboard">
          <q-item-section avatar>
            <q-icon name="fas fa-chart-bar" size="1rem" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/spends">
          <q-item-section avatar>
            <q-icon name="fa fa-shopping-cart" size="1rem" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Spends</q-item-label>
          </q-item-section>
        </q-item>

        <q-item-label header>Configuration</q-item-label>
        <q-item to="/payment-methods">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" size="1rem" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Payment Methods</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/categories">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" size="1rem" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Categories</q-item-label>
          </q-item-section>
        </q-item>

        <q-item-label header>System</q-item-label>
        <q-item to="/types">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" size="1rem" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Types</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container style="background-color: #f5f5f5;">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { openURL } from 'quasar'

export default {
  name: 'MyLayout',
  data () {
    return {
      leftDrawerOpen: this.$q.platform.is.desktop
    }
  },
  computed: {
    firstName () {
      return this.$store.getters['users/firstName']
    },
    roleId () {
      return this.$store.getters['users/roleId']
    }
  },
  methods: {
    openURL,
    logOut () {
      localStorage.removeItem('JWT')
      window.location.reload()
    }
  }
}
</script>

<style scoped>
.q-item__section--avatar {
  min-width: 1rem !important;
}
</style>
