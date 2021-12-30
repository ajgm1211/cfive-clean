<template>
  <section>
    <div class="back-btn" @click="$router.back()">
      <LeftArrow /> <span>back</span>
    </div>
    <div class="i-container">
      <div class="inputs-container">
        <CustomInput
          label="Name"
          name="name"
          ref="name"
          v-model="price.name"
          :rules="{
            required: true,
          }"
          @blur="update('main')"
        />
        <CustomInput
          label="Display name"
          name="display name"
          ref="display_name"
          v-model="price.display_name"
          :rules="{
            required: true,
          }"
          @blur="update('main')"
        />
        <CustomInput
          :disabled="true"
          :value="selected"
          label="Price Level Type"
        />
      </div>
    </div>

    <div class="tabscontainer">
      <div class="tabs">
        <p
          @click="currentTab(tab)"
          :class="active === tab ? 'tab-active' : ''"
          v-for="tab in tabs"
          :key="tab"
        >
          {{ tab }}

          <span class="active-line"></span>
        </p>
      </div>

      <div class="tab-content-container">
        <h2>{{ active }}</h2>
        <ckeditor
          v-model="price.description"
          v-if="active == 'Description'"
          type="classic"
          @blur="update('description')"
        ></ckeditor>

        <div v-if="active == 'Only Apply To'">
          <h6>Companies:</h6>
          <multiselect
            v-model="price.company_restrictions"
            :options="datalists.companies"
            :multiple="true"
            :close-on-select="true"
            :clear-on-select="true"
            :show-labels="false"
            :searchable="true"
            track-by="business_name"
            label="business_name"
            placeholder="Select companies"
            @input="update('companies')"
            class="input-h"
            style="cursor:pointer"
          ></multiselect>
          <br />
          <div v-if="false">
            <h6>Groups:</h6>
            <multiselect
              v-model="price.group_restrictions"
              :options="datalists.company_groups"
              :multiple="true"
              :close-on-select="true"
              :clear-on-select="true"
              :show-labels="false"
              :searchable="true"
              track-by="name"
              label="name"
              placeholder="Select company groups"
              @input="update('groups')"
              class="input-h"
              style="cursor:pointer"
            ></multiselect>
          </div>
        </div>

        <div v-if="active == 'Detail'">
          <InputSearch style="margin-bottom:20px" />

          <ListPrices
            v-if="tableSet"
            :currentPage="currentPage"
            :rates="GET_PRICE_LEVEL_RATES"
            :filters="false"
            :thead="thead"
            :amount="amount"
            :dynamic="true"
            @editModal="setModalData"
          />

          <p style="margin-top:20px">
            Total Results: {{ GET_PAGINATE_RATES.meta.total }}
          </p>

          <Paginate
            @prevPage="prevPage"
            @nextPage="nextPage"
            @input="handlePageSelected($event)"
            :page-count="GET_PAGINATE_RATES.meta.last_page"
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
      </div>
    </div>

    <CreateModal
      v-if="GET_MODAL_EDIT"
      :fields="modal_fields"
      :title="'Price Level'"
      :action="'Edit'"
      :dispatch="'editPriceLevel'"
      :model="detail_to_edit"
      @cancel="close"
      @showCurrency="showCurrency($event)"
    />
  </section>
</template>

<script>
import CustomInput from "../../../components/common/CustomInput.vue";
import Selectable from "../../../components/common/Selectable.vue";
import LeftArrow from "../../../components/Icons/LeftArrow.vue";
import actions from "../../../../../resources/js/actions";
import ListPrices from "../../../components/PriceLevel/ListPrices.vue";
import InputSearch from "../../../components/common/InputSearch.vue";
import SorteableDropdown from "../../../components/common/SorteableDropdown.vue";
import Paginate from "../../../../js/components/paginate.vue";
import MainButton from "../../../components/common/MainButton.vue";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import CreateModal from "../../../components/PriceLevel/CreateModal.vue";
import { mapGetters } from "vuex";
export default {
  components: {
    CustomInput,
    Selectable,
    LeftArrow,
    ListPrices,
    InputSearch,
    Paginate,
    MainButton,
    SorteableDropdown,
    Multiselect,
    CreateModal,
  },
  data: () => ({
    actions: actions,
    datalists: null,
    currentData: {},
    active: "Detail",
    tabs: ["Detail", "Only Apply To", "Description"],
    price: {
      name: "",
      display_name: "",
      description: "",
      company_restrictions: [],
      group_restrictions: [],
    },
    selected: "",
    value: "",
    selectable_error: false,
    thead: [],
    amount: {},
    tableSet: false,
    rates: [],
    currentPage: 1,
    modal_fields: [],
    editing: false,
    detail_to_edit: {},
  }),
  mounted() {
    this.$store.dispatch("getPriceLevelDetail", {
      id: this.$route.params.id,
      body: {
        name: this.price.name,
        display_name: this.price.display_name,
        price_level_type: this.selected,
        company_restrictions: this.price.company_restrictions,
        group_restrictions: this.price.group_restrictions,
      },
    });

    this.$store.dispatch("listPriceLevelRates", {
      id: this.$route.params.id,
      page: this.currentPage,
    });

    this.$store.dispatch("getPriceLevelData");

    setTimeout(() => {
      this.datalists = this.GET_PRICE_LEVEL_DATA;
      this.price.name = this.GET_CURRENT_PRICE_LEVEL.name;
      this.price.display_name = this.GET_CURRENT_PRICE_LEVEL.display_name;
      this.price.description = this.GET_CURRENT_PRICE_LEVEL.description;
      this.selected = this.GET_CURRENT_PRICE_LEVEL.type;
      this.price.company_restrictions = this.GET_CURRENT_PRICE_LEVEL.company_restrictions;
      this.price.group_restrictions = this.GET_CURRENT_PRICE_LEVEL.group_restrictions;

      this.rates = this.GET_PRICE_LEVEL_RATES;

      this.setTable();
    }, 1000);
  },
  methods: {
    handlePageSelected(page) {
      this.$store.dispatch("listPriceLevelRates", {
        id: this.$route.params.id,
        page: page,
      });
    },
    update(key = null) {
      if (key == "main") {
        var updateBody = {
          name: this.price.name,
          display_name: this.price.display_name,
          price_level_type: this.selected,
        };
      } else if (key == "description") {
        var updateBody = {
          description: this.price.description,
        };
      } else if (key == "companies") {
        var updateBody = {
          companies: this.price.company_restrictions,
        };
      } else if (key == "groups") {
        var updateBody = {
          groups: this.price.group_restrictions,
        };
      }

      this.$store.dispatch("updatePriceLevel", {
        id: this.$route.params.id,
        body: updateBody,
      });
      this.$forceUpdate();
    },
    currentTab(tab) {
      this.active = tab;
    },
    prevPage() {
      if (this.currentPage > 1) {
        let prevpage = this.currentPage - 1;
        this.$store.dispatch("listPriceLevelRates", {
          id: this.$route.params.id,
          page: prevpage,
        });
        this.currentPage = this.currentPage - 1;
        this.$forceUpdate();
      }
    },
    nextPage() {
      if (this.currentPage < this.GET_PAGINATE_RATES.meta.last_page) {
        let nextPage = this.currentPage + 1;
        this.$store.dispatch("listPriceLevelRates", {
          id: this.$route.params.id,
          page: nextPage,
        });
        this.currentPage = this.currentPage + 1;
        this.$forceUpdate();
      }
    },
    setTable() {
      if (this.selected == "FCL") {
        this.thead = ["Direction", "Apply to", "20", "40", "Currency"];

        this.amount = {
          type_20: {
            amount: "0",
            markup: "Fixed Markup",
          },
          type_40: {
            amount: "0",
            markup: "Fixed Markup",
          },
        };
      } else if (this.selected == "LCL") {
        this.thead = ["Direction", "Apply to", "Amount", "Currency"];

        this.amount = {
          type_lcl: {
            amount: "0",
            markup: "Fixed Markup",
          },
        };
      }

      this.tableSet = true;
    },
    setModalData(detail) {
      let formattedDetail = this.formatDetail(detail);

      this.modal_fields = [
        {
          type: "dropdown",
          label: "Direction",
          name: "direction",
          items: this.datalists.directions,
          rules: {
            required: true,
          },
          show_by: "name",
        },
        {
          type: "dropdown",
          label: "Apply to",
          name: "price_level_apply",
          items: this.datalists.applies,
          rules: {
            required: true,
          },
          show_by: "name",
        },
      ];

      if (this.selected == "FCL") {
        this.modal_fields.push(
          {
            type: "input",
            label: "20",
            name: "type_20",
            input_type: 'number',
            rules: {
              required: true,
              minValue: 1,
            },
          },
          {
            type: "dropdown",
            label: "Type",
            name: "type_20_t",
            items: ["Fixed Markup", "Percent Markup"],
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "40",
            name: "type_40",
            input_type: 'number',
            rules: {
              required: true,
              minValue: 1,
            },
          },
          {
            type: "dropdown",
            label: "Type",
            name: "type_40_t",
            items: ["Fixed Markup", "Percent Markup"],
            rules: {
              required: true,
            },
          }
        );
      } else if (this.selected == "LCL") {
        this.modal_fields.push(
          {
            type: "input",
            label: "Amount",
            name: "type_lcl",
            input_type: 'number',
            rules: {
              required: true,
              minValue: 1,
            },
          },
          {
            type: "dropdown",
            label: "Type",
            name: "type_lcl_t",
            items: ["Fixed Markup", "Percent Markup"],
            rules: {
              required: true,
            },
          }
        );
      }

      if (formattedDetail.showModalCurrency) {
        this.modal_fields.push({
          type: "dropdown",
          label: "Currency",
          name: "currency",
          items: this.datalists.currency,
          rules: {
            required: true,
          },
        });
      }

      this.detail_to_edit = formattedDetail;
      this.editing = true;
    },
    formatDetail(detail) {
      var formatted = {};

      formatted.id = detail.id;
      formatted.direction = detail.direction;
      formatted.price_level_apply = detail.price_level_apply;
      formatted.showModalCurrency = true;

      if (this.selected == "FCL") {
        formatted.type_20 = detail.amount.type_20.amount;
        formatted.type_20_t = detail.amount.type_20.markup;
        formatted.type_40 = detail.amount.type_40.amount;
        formatted.type_40_t = detail.amount.type_40.markup;
      } else if (this.selected == "LCL") {
        formatted.type_lcl = detail.amount.type_lcl.amount;
        formatted.type_lcl_t = detail.amount.type_lcl.markup;
      }

      if (formatted.showModalCurrency) {
        formatted.currency = detail.currency;
      }

      return formatted;
    },
    close() {
      this.$store.commit("SET_MODAL_EDIT", false);
    },
  },
  computed: {
    ...mapGetters([
      "GET_CURRENT_PRICE_LEVEL",
      "GET_PRICE_LEVEL_RATES",
      "GET_PRICE_LEVEL_DATA",
      "GET_PAGINATE_RATES",
      "GET_MODAL_EDIT",
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
