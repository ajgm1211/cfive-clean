<template>
  <section class="body-container">
    <router-view v-if="tokenReady"></router-view>
  </section>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from 'vuex'

export default {
  data: () => ({
    initialData: {},
    apiToken: "",
    tokenReady: false,
  }),
  mounted() {
    this.getData();
  },
  computed: {
    ...mapState('auth', ['token'])
  },
  methods: {
    ...mapActions('auth', ['setToken','setCurrentUser']),
    getData() {
      let url = "/users/data";
      api.getData({}, url, (err, data) => {
        this.initialData = data.data
         this.setCurrentUser(this.initialData.user)
         this.setCurrentToken(this.initialData.user.api_token)
      });
    },
    setCurrentToken(token) {
        axios.defaults.headers.common['Authorization'] = token
        this.setToken(token)
        this.tokenReady = true
    }
  },
}
</script>
