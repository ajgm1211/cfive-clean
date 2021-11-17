<template>
  <section>
    <div class="back-btn" @click="$router.back()">
        <LeftArrow /> <span>back</span>
    </div>
    <div class="price-container">
      <div class="head">
        <h2>{{GET_COMPANY_USER.name}} Api Providers</h2>
        <button @click="addIntegration = true" class="btn btn-primary">+ Add Integration</button>
      </div>

      <div class="list-container">
        <ListApiProviders :providers="GET_COMPANY_USER.providers" @show="showSeeApiCredentials"/>
      </div>
    </div>
    
    <AddIntegrationModal v-if="addIntegration" @cancel="addIntegration = false" />
    <SeeApiCredentialsModal v-if="seeApiCredentials" :provider="editingProvider" @cancel="seeApiCredentials = false" />
  </section>
</template>

<script>
import LeftArrow from "../../../../components/Icons/LeftArrow.vue";
import ListApiProviders from "../../../../components/Integrations/ApiCredentials/ListApiProviders.vue";
import AddIntegrationModal from "../../../../components/Integrations/ApiCredentials/AddIntegrationModal.vue";
import SeeApiCredentialsModal from "../../../../components/Integrations/ApiCredentials/SeeApiCredentialsModal.vue";
import { mapGetters } from "vuex";
export default {
  components: {
    LeftArrow,
    ListApiProviders,
    AddIntegrationModal,
    SeeApiCredentialsModal
  },
  props: {
  },
  data: () => ({
    addIntegration: false,
    seeApiCredentials: false,
    editingProvider: null
  }),
  mounted() {
    this.$store.dispatch("getApiProvidersByCompanyUser", {
      id: this.$route.params.id
    });
  },
  methods: {
    showSeeApiCredentials(provider) {
      this.editingProvider = provider;
      this.seeApiCredentials = true;
    }
  },
  computed: {
    ...mapGetters([
      "GET_COMPANY_USER",
    ]),
  },
};
</script>

<style lang="scss" scoped>
section {
  width: 100%;
  height: 100%;
  padding: 20px;
}
.back-btn{
  margin-bottom: 15px;
}
h2 {
  color: #006bfa;
  font-size: 24px;
  font-weight: 500;
  margin: 0;
}

.head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.price-container {
  background-color: #fff;
  border-radius: 10px;
  width: 100%;
  height: fit-content;
  padding: 20px;
}

.list-container {
  padding: 10px 10px 0 0;
  max-height: 415px;
}

.back-btn {
  cursor: pointer;
  display: flex;
  align-items: center;
  width: fit-content;

  & > span {
    margin-left: 5px;
    font-weight: bold;
    text-transform: capitalize;
  }
}
</style>