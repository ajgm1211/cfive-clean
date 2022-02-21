<template>
  <section>
    <div class="price-container">
      <div class="head">
        <h2>Price levels</h2>

        <MainButton
          @click="create = true"
          text="Add Price Levels"
          :add="true"
        />
      </div>

      <InputSearch @filter="filtered = $event" style="margin-bottom: 20px;" />

      <div class="list-container" v-if="GET_PRICE_LEVELS">
        <ListPrices :filtered="filtered" :currentPage="currentPage" :prices="GET_PRICE_LEVELS" />
      </div>

      <p v-if="GET_PAGINATE_PRICE_LEVELS.total" style="margin-top:20px">
        Total Results: {{ GET_PAGINATE_PRICE_LEVELS.total }}
      </p>

      <Paginate
        @prevPage="prevPage"
        @nextPage="nextPage"
        @input="handlePageSelected($event)"
        :page-count="GET_PAGINATE_PRICE_LEVELS.last_page"
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

    <CreateModal
      v-if="create"
      :fields="modal_fields"
      :title="'Price Level'"
      :action="'Add'"
      :dispatch="'createPriceLevel'"
      @cancel="create = false"
    />
  </section>
</template>

<script>
import MainButton from "../../../components/common/MainButton.vue";
import InputSearch from "../../../components/common/InputSearch.vue";
import ListPrices from "../../../components/PriceLevel/ListPrices.vue";
import Paginate from "../../../../js/components/paginate.vue";
import CreateModal from "../../../components/PriceLevel/CreateModal.vue";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  components: { MainButton, InputSearch, ListPrices, Paginate, CreateModal },
  data: () => ({
    filtered: "",
    create: false,
    prices: [],
    currentPage: 1,
    modal_fields: [
      {
        type: "input",
        label: "Name",
        name: "name",
        error:false,
        rules: {
          required: true,
        },
      },
      {
        type: "input",
        label: "Display name",
        name: "display_name",
        error: false,
        rules: {
          required: true,
        },
      },
      {
        type: "dropdown",
        label: "Price Level Type",
        name: "price_level_type",
        items: ["FCL", "LCL"],
        show_by: "",
        rules: {
          required: true,
        },
      },
    ],
  }),
  mounted() {
    this.$store.dispatch("getPriceLevels", { page: this.currentPage });
  },
  methods: {
    prevPage() {
      if (this.currentPage > 1) {
        let prevpage = this.currentPage - 1;
        this.$store.dispatch("getPriceLevels", { page: prevpage });
        this.currentPage = this.currentPage - 1;
      }
    },
    nextPage() {
      if (this.currentPage < this.GET_PAGINATE_PRICE_LEVELS.last_page) {
        let nextPage = this.currentPage + 1;
        this.$store.dispatch("getPriceLevels", { page: nextPage });
        this.currentPage = this.currentPage + 1;
      }
    },
    handlePageSelected(page) {
      this.$store.dispatch("getPriceLevels", { page: page });
    },
  },
  computed: {
    ...mapGetters(["GET_PRICE_LEVELS", "GET_PAGINATE_PRICE_LEVELS"]),
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
