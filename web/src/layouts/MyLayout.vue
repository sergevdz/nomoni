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
            <q-icon name="fas fa-chart-bar" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
            <!-- <q-item-label caption>quasar.dev</q-item-label> -->
          </q-item-section>
        </q-item>
        <q-item to="/spends">
          <q-item-section avatar>
            <q-icon name="fa fa-shopping-cart" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Spends</q-item-label>
            <!-- <q-item-label caption>quasar.dev</q-item-label> -->
          </q-item-section>
        </q-item>
        <q-item-label header>Configuration</q-item-label>
        <q-item to="/types">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Types</q-item-label>
            <!-- <q-item-label caption>quasar.dev</q-item-label> -->
          </q-item-section>
        </q-item>
        <q-item to="/payment-methods">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Payment Methods</q-item-label>
            <!-- <q-item-label caption>quasar.dev</q-item-label> -->
          </q-item-section>
        </q-item>
        <q-item to="/categories">
          <q-item-section avatar>
            <q-icon name="fa fa-asterisk" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Categories</q-item-label>
            <!-- <q-item-label caption>quasar.dev</q-item-label> -->
          </q-item-section>
        </q-item>
        <q-item clickable tag="a" target="_blank" href="https://twitter.com/quasarframework">
          <q-item-section avatar>
            <q-icon name="rss_feed" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Twitter</q-item-label>
            <q-item-label caption>@quasarframework</q-item-label>
          </q-item-section>
        </q-item>
        <q-item clickable tag="a" target="_blank" href="https://quasar.dev/start/pick-quasar-flavour">
          <q-item-section avatar>
            <q-icon name="fas fa-book-open" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Quasar</q-item-label>
            <q-item-label caption>Quasar Docs</q-item-label>
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

<style>
</style>
