<template>
  <section>
    <div class="back-btn" @click="$router.back()">
        <LeftArrow /> <span>back</span>
    </div> <br><br>
    <div class="price-container">
      <div class="head">
        <h2>Api Providers of {{GET_COMPANY_USER.name}}"</h2>
      </div>

      <div class="list-container">{{GET_COMPANY_USER.providers}}
        <ListApiProviders :companyUser="GET_COMPANY_USER.providers" />
      </div>
    </div>

    <CreateModal v-if="create" @cancel="create = false" />
  </section>
</template>

<script>
import LeftArrow from "../../../../components/Icons/LeftArrow.vue";
import ListApiProviders from "../../../../components/Integrations/ApiCredentials/ListApiProviders.vue";
import { mapGetters } from "vuex";
export default {
  components: {
    LeftArrow,
    ListApiProviders
  },
  data: () => ({
    create: false,
  }),
  mounted() {
    this.$store.dispatch("getApiProviders", {
      id: this.$route.params.id
    });
  },
  methods: {
    
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
  padding: 40px;
}

h2 {
  color: #006bfa;
  font-size: 16px;
  font-weight: 500;
  margin: 0;
  margin-bottom: 20px;
}

.inputs-container {
  display: grid;
  grid-template-columns: 200px 200px 200px 200px;
  column-gap: 20px;
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

.tabscontainer {
  margin: 0 100px;
}

.tabs {
  display: flex;
  align-items: center;
  margin-top: 80px;
  border-bottom: 1px solid #d9d9d9;

  & > p {
    margin: 0 100px 0 0;
    cursor: pointer;
    padding: 8px 0;
    font-size: 14px;
  }

  & > .tab-active {
    color: #006bfa;
    position: relative;
    font-weight: 500;

    & > .active-line {
      height: 4px;
      width: calc(100% + 10px);
      position: absolute;
      background-color: #006bfa;
      bottom: -3px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 4px;
    }
  }
}

.tab-content-container {
  width: 100%;
  height: 100%;
  padding: 20px;
  margin-top: 40px;
  background-color: white;
  border-radius: 5px;
}

.i-container {
  padding: 0 100px;
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
