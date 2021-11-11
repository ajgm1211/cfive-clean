<template>
  <b-table-simple checkable class="table table-striped table-responsive">
    <thead>
      <tr v-if="filters == true">
        <th scope="col" style="width:40px">
          <b-form-checkbox
            v-model="allSelected"
            aria-describedby="prices"
            aria-controls="prices"
            @change="toggleAll"
          >
          </b-form-checkbox>
        </th>
        <th scope="col" style="width:60px">ID</th>
        <th scope="col" style="width:93">Price Type</th>
        <th scope="col">Name</th>
        <th scope="col">Display name</th>
        <th scope="col" style="width: 300px;">Description</th>
        <th scope="col">Created at</th>
        <th scope="col">Updated at</th>
        <th scope="col" style="width:40px">
          <OptionsButton @option="action($event)" :standar="false" />
        </th>
      </tr>
      <tr v-else-if="dynamic">
        <th>
          <b-form-checkbox
            v-model="allRatesSelected"
            aria-describedby="rates"
            aria-controls="rates"
            @change="toggleAll2"
          >
          </b-form-checkbox>
        </th>

        <th v-for="item in thead" :key="item">{{ item }}</th>

        <th scope="col" style="width:40px; position: relative;">
          <OptionsButton
            @option="action($event)"
            :standar="false"
            style="right:-84px;"
          />
        </th>
      </tr>
    </thead>
    <thead v-if="dynamic">
      <tr>
        <th></th>
        <th>
          <Selectable
            :defaultFirstOption="true"
            style="width: 100px"
            @selected="setDirection($event)"
            :selected="GET_PRICE_LEVEL_DATA.directions[0]"
            :options="GET_PRICE_LEVEL_DATA.directions"
          />
        </th>
        <th>
          <Selectable
            :defaultFirstOption="true"
            style="width: 120px"
            @selected="setApply($event)"
            :selected="GET_PRICE_LEVEL_DATA.applies[0]"
            :options="GET_PRICE_LEVEL_DATA.applies"
          />
        </th>
        <th>
          <div class="d-flex" style="width: 100px">
            <CustomInput
              v-model="amount.type_20.amount"
              :mixed="true"
              type="number"
              :placeholder="null"
              :showLabel="false"
            />
            <Selectable
              :defaultFirstOption="true"
              :options="price_types"
              background_color="#006bfa"
              border_color="#006bfa"
              font_color="white"
              :icon="false"
              :mixed="true"
              @selected="set20Markup($event)"
            />
          </div>
        </th>
        <th>
          <div class="d-flex" style="width: 100px">
            <CustomInput
              v-model="amount.type_40.amount"
              :mixed="true"
              type="number"
              :placeholder="null"
              :showLabel="false"
            />
            <Selectable
              :defaultFirstOption="true"
              :options="price_types"
              background_color="#006bfa"
              border_color="#006bfa"
              font_color="white"
              :icon="false"
              :mixed="true"
              @selected="set40Markup($event)"
            />
          </div>
        </th>
        <th>
          <SorteableDropdown
            v-show="!only_percent"
            @selected="setCurrency($event)"
            @reset="currency = {}"
            :itemList="GET_PRICE_LEVEL_DATA.currency"
            :error="selectable_error"
          />
        </th>
        <th style="position: relative;">
          <MainButton
            @click="addRate()"
            :save="true"
            text="Save"
            style="right:-84px;"
          />
        </th>
      </tr>
    </thead>
    <tbody v-if="dynamic">
      <tr v-for="(item, index) in rates" :key="index">
        <td scope="row">
          <b-form-checkbox-group>
            <b-form-checkbox
              v-bind:value="item.id"
              v-bind:id="'check' + item.id"
              v-model="selectedRate"
            >
            </b-form-checkbox>
          </b-form-checkbox-group>
        </td>
        <td>{{ item.direction.name }}</td>
        <td>{{ item.price_level_apply.name }}</td>
        <td>
          {{
            item.amount.type_20.markup == "Percent Markup"
              ? item.amount.type_20.amount + " %"
              : item.amount.type_20.amount + " $"
          }}
        </td>
        <td>
          {{
            item.amount.type_40.markup == "Percent Markup"
              ? item.amount.type_40.amount + " %"
              : item.amount.type_40.amount + " $"
          }}
        </td>
        <td>{{ item.currency.alphacode }}</td>
        <td style="position: relative;">
          <OptionsButton @option="action($event, item)" style="right:-84px;" />
        </td>
      </tr>
    </tbody>
    <tbody v-else>
      <tr v-for="(item, index) in prices" :key="index">
        <td scope="row">
          <b-form-checkbox-group>
            <b-form-checkbox
              v-bind:value="item.id"
              v-bind:id="'check' + item.id"
              v-model="selected"
            >
            </b-form-checkbox>
          </b-form-checkbox-group>
        </td>
        <td>{{ item.id }}</td>
        <td>{{ item.type }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.display_name }}</td>
        <td v-html="item.description"></td>
        <td>{{ item.created_at }}</td>
        <td>{{ item.updated_at }}</td>
        <td scope="col"><OptionsButton @option="action($event, item.id)" /></td>
      </tr>
    </tbody>
  </b-table-simple>
</template>

<script>
import OptionsButton from "../common/OptionsButton.vue";
import Selectable from "../common/Selectable.vue";
import MainButton from "../common/MainButton.vue";
import CustomInput from "../common/CustomInput.vue";
import { mapGetters } from "vuex";
import SorteableDropdown from "../common/SorteableDropdown.vue";

export default {
  props: {
    prices: {
      type: Array,
      default() {
        return [];
      },
    },
    rates: {
      type: Array,
      default() {
        return [];
      },
    },
    filters: {
      type: Boolean,
      default: true,
    },
    rate: {
      type: Boolean,
      default: true,
    },
    dynamic: {
      type: Boolean,
      default: false,
    },
    currentPage: {
      default: 0,
    },
    thead: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  components: {
    OptionsButton,
    Selectable,
    MainButton,
    SorteableDropdown,
    CustomInput,
  },
  data: () => ({
    select: "",
    selectable_error: false,
    SorteableDropdownion: [],
    selected: [],
    direction: {},
    currency: {},
    price_level_apply: {},
    selectedRate: [],
    allSelected: false,
    allRatesSelected: false,
    indeterminate: false,
    price_types: ["Fixed Markup", "Percent Markup"],
    directions: [],
    restrictions: [],
    currencies: [],
    amount: {
      type_20: {
        amount: "0",
        markup: "Fixed Markup",
      },
      type_40: {
        amount: "0",
        markup: "Fixed Markup",
      },
    },
    only_percent: false,
  }),
  mounted() {
    setTimeout(() => {
      this.direction = this.GET_PRICE_LEVEL_DATA.directions[0];
      this.price_level_apply = this.GET_PRICE_LEVEL_DATA.applies[0];
    }, 1000);
    console.log(this.amount)
  },
  methods: {
    addRate() {
      this.$store.dispatch("createRate", {
        id: this.$route.params.id,
        body: {
          amount: this.amount,
          currency: this.currency,
          price_level_apply: this.price_level_apply,
          direction: this.direction,
          only_percent: this.only_percent,
        },
        page: this.currentPage,
        currentId: this.$route.params.id,
      });
    },
    toggleAll(checked) {
      this.allSelected = checked;
      if (checked) {
        this.prices.forEach((item) => {
          this.selected.push(item.id);
        });
      }
      if (!checked) {
        this.selected = [];
      }
    },
    toggleAll2(checked) {
      if (checked) {
        this.rates.forEach((item) => {
          this.selectedRate.push(item.id);
        });
      }
      if (!checked) {
        this.selectedRate = [];
      }
    },
    action(option, id) {
      if (option == "edit") {
        this.$router.push({
          name: "price-rates",
          params: { id: id },
        });
      }
      if (option == "duplicate") {
        if (this.dynamic === true) {
          this.$store.dispatch("duplicateRate", {
            id: id.id,
            page: this.currentPage,
            currentId: this.$route.params.id,
          });
        } else {
          this.$store.dispatch("duplicatePriceLevel", {
            id: id,
            page: this.currentPage,
            currentId: this.$route.params.id,
          });
        }
      }
      if (option == "delete") {
        if (this.dynamic === true) {
          this.$store.dispatch("deleteRate", {
            id: id.id,
            page: this.currentPage,
            currentId: this.$route.params.id,
          });
        } else {
          this.$store.dispatch("deletePriceLevel", {
            id: id,
            page: this.currentPage,
          });
        }
      }
      if (option == "deleteSelected") {
        if (this.dynamic === true) {
          this.$store.dispatch("deleteMultiple", {
            body: {
              ids: this.selectedRate,
            },
            id: this.$route.params.id,
            page: this.currentPage,
          });
        } else {
          this.$store.dispatch("deleteSelectedPriceLevel", {
            body: {
              ids: this.selected,
            },
            page: this.currentPage,
          });
        }
      }
    },
    setDirection(option) {
      this.direction = option;
    },
    setApply(option) {
      this.price_level_apply = option;
    },
    setCurrency(option) {
      this.currency = option;
    },
    set20Markup(option) {
      this.amount.type_20.markup = option;
      this.checkIfOnlyPercent();
    },
    set40Markup(option) {
      this.amount.type_40.markup = option;
      this.checkIfOnlyPercent();
    },
    checkIfOnlyPercent() {
      if(this.amount.type_20.markup == "Percent Markup" && this.amount.type_40.markup  == "Percent Markup"){
        this.only_percent = true;
      }else{
        this.only_percent = false;
      }
    },
  },
  computed: {
    ...mapGetters(["GET_PRICE_LEVEL_DATA", "GET_PRICE_LEVEL_RATES"]),
  },
};
</script>

<style lang="scss" scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>
