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
    ...mapState('auth', ['token','user', 'companyUser' ])
  },
  methods: {
    ...mapActions('auth', ['setToken','setUser', 'setCompanyUser']),
    getData() {
      let url = "/users/data";
      api.getData({}, url, (err, data) => {
        this.initialData = data.data
         this.setCurrentUser(this.initialData.user)
         this.setCurrentToken(this.initialData.user.api_token)
         this.setCurrentCompanyUser(this.initialData.company_user)
      });
    },
    setCurrentToken(token) {
        axios.defaults.headers.common['Authorization'] = token
        this.setToken(token)
        this.tokenReady = true
    },
    setCurrentUser(user){
      this.setUser(user)
    },
    setCurrentCompanyUser(companyUser){
      this.setCompanyUser(companyUser)
    }
  },
}
</script>
