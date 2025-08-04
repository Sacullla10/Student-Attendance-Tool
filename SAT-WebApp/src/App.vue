<template>
  <v-app>
    <v-app-bar color="primary">
      <v-btn icon="mdi-menu" @click.stop="drawer = !drawer"></v-btn>
      <v-app-bar-title>
        Registro de Presença
      </v-app-bar-title>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item to="/">
          <v-list-item-title>Alunos</v-list-item-title>
        </v-list-item>

        <v-list-item to="/class-session">
          <v-list-item-title>Aulas</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <div class="page-wrapper">
        <router-view />
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'

const drawer = ref(false)
</script>