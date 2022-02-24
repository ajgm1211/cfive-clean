<template>
  <body style="background-color: #f5f5f5;" id="main">
    <router-view v-if="tokenReady"></router-view>
  </body>
</template>

<script>
import axios from "axios";

export default {
  data: () => ({
    initialData: {},
    apiToken: "",
    tokenReady: false,
  }),
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      let url = "/users/data";
      api.getData({}, url, (err, data) => {
        this.initialData = data.data;
        this.setToken();
      });
    },
    setToken() {
      this.apiToken = this.initialData.user.api_token;
      axios.defaults.headers.common['Authorization'] = this.apiToken;
      this.tokenReady = true;
    },
  },
}
</script>
