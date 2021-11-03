<template>
  <section>
    <div class="price-container">
      <div class="head">
        <h2>Clients available</h2>
      </div>

      <InputSearch @filter="filtered = $event" style="margin-bottom: 20px;" />

      <div class="list-container">
        <ListCompanyUsers :currentPage="currentPage" :companyUsers="GET_COMPANY_USERS" />
      </div>

      <p v-if="GET_PAGINATE_COMPANY_USERS.total" style="margin-top:20px">
        Total Results: {{ GET_PAGINATE_COMPANY_USERS.total }}
      </p>

      <Paginate
        @prevPage="prevPage"
        @nextPage="nextPage"
        :page-count="GET_PAGINATE_COMPANY_USERS.last_page"
        :prev-text="'Prev'"
        :next-text="'Next'"
        :page-class="'page-item'"
        :page-link-class="'page-link'"
        :container-class="'pagination'"
        :prev-class="'page-item'"
        :prev-link-class="'page-link'"
        :next-class="'page-item'"
        :next-link-class="'page-link'"
        :initialPage="1"
        style="margin-bottom: 0!important;"
      />
    </div>

    <CreateModal v-if="create" @cancel="create = false" />
  </section>
</template>

<script>
import InputSearch from "../../../../components/common/InputSearch.vue";
import ListCompanyUsers from "../../../../components/Integrations/ApiCredentials/ListCompanyUsers.vue";
import Paginate from "../../../../../js/components/paginate.vue";
import CreateModal from "../../../../components/PriceLevel/CreateModal.vue";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  components: { InputSearch, ListCompanyUsers, Paginate, CreateModal },
  data: () => ({
    create: false,
    currentPage: 1,
  }),
  mounted() {
    this.$store.dispatch("getCompanyUsers", { page: this.currentPage });
  },
  methods: {
    prevPage() {
      if (this.currentPage > 1) {
        let prevpage = this.currentPage - 1;
        this.$store.dispatch("getCompanyUsers", { page: prevpage });
        this.currentPage = this.currentPage - 1;
      }
    },
    nextPage() {
      if (this.currentPage < this.GET_PAGINATE_COMPANY_USERS.last_page) {
        let nextPage = this.currentPage + 1;
        this.$store.dispatch("getCompanyUsers", { page: nextPage });
        this.currentPage = this.currentPage + 1;
      }
    },
  },
  computed: {
    ...mapGetters(["GET_COMPANY_USERS", "GET_PAGINATE_COMPANY_USERS"]),
  },
};
</script>

<style lang="scss" scoped>
section {
  width: 100%;
  height: 100%;
  padding: 20px;
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
  overflow: auto;
}

/* width */
::-webkit-scrollbar {
  width: 8px;
}

/* Track */
::-webkit-scrollbar-track {
  background: transparent;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #f2f2f2;
  border-radius: 5px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: rgb(184, 184, 184);
}
</style>
