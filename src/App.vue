<template>
  <v-app>
    <v-app-bar app color="secondary" dark>
      <v-toolbar-title class="mr-5">Partidos de Europa</v-toolbar-title>
      <v-btn class="mr-3" to="/" text>
        <v-icon left>mdi-magnify</v-icon>
        <span>Buscar</span>
      </v-btn>
      <v-btn to="/saved" text>
        <span>Guardados</span>
        <v-icon right>mdi-cards-heart</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
  </v-app>
</template>

<script>
import axios from "axios";

export default {
  name: "App",

  data: () => ({
    loading: false,
  }),

  created() {
    this.onRequest();
    this.onResponse();
  },

  methods: {
    onRequest() {
      axios.interceptors.request.use(
        (request) => {
          this.loading = true;
          return request;
        },
        (error) => {
          this.loading = false;
          return Promise.reject(error);
        }
      );
    },

    onResponse() {
      axios.interceptors.response.use(
        (response) => {
          this.loading = false;
          return response;
        },
        (error) => {
          this.loading = false;
          return Promise.reject(error);
        }
      );
    }
  },
};
</script>
