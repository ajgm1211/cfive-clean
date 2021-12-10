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
          <OptionsButton
            :options="['delete']"
            @option="action($event)"
            :standar="false"
          />
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
            :options="['delete']"
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

        <th v-for="(amountObject, amountKey) in amount" :key="amountKey">
          <div class="d-flex" style="width: 100px">
            <CustomInput
              v-model="amountObject.amount"
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
              @selected="setMarkup(amountObject, $event)"
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
            ref="currencyDropdown"
          />
        </th>
        <th>
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
        <td
          v-for="(itemAmount, itemAmountKey) in item.amount"
          :key="itemAmountKey"
        >
          {{
            itemAmount.markup == "Percent Markup"
              ? itemAmount.amount + " %"
              : itemAmount.amount + " $"
          }}
        </td>
        <td>{{ item.currency != null ? item.currency.alphacode : "-" }}</td>
        <td style="position: absolute;">
          <OptionsButton
            :options="['edit', 'delete']"
            @option="action($event, item, 'modal')"
            style="right:-84px;"
          />
        </td>
      </tr>
    </tbody>
    <tbody v-else>
      <tr
        v-for="(item, index) in prices"
        v-show="itemVisible(item)"
        :key="index"
      >
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
        <td style="position:absolute;">
          <OptionsButton
            :options="['edit', 'duplicate', 'delete']"
            @option="action($event, item.id)"
          />
        </td>
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
    amount: {
      type: Object,
      default() {
        return {};
      },
    },
    filtered: {},
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
    only_percent: false,
  }),
  mounted() {
    setTimeout(() => {
      this.direction = this.GET_PRICE_LEVEL_DATA.directions[0];
      this.price_level_apply = this.GET_PRICE_LEVEL_DATA.applies[0];
    }, 1000);
  },
  methods: {
    async addRate() {
      await this.$store.dispatch("createRate", {
        id: this.$route.params.id,
        body: {
          amount: this.amount,
          currency: this.only_percent ? null : this.currency,
          price_level_apply: this.price_level_apply,
          direction: this.direction,
          only_percent: this.only_percent,
        },
        page: this.currentPage,
        currentId: this.$route.params.id,
      });

      this.clearDisplay();
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
    action(option, id, target = "view") {
      if (option == "edit") {
        if (target == "view") {
          this.$router.push({
            name: "price-rates",
            params: { id: id },
          });
        } else if (target == "modal") {
          this.$store.commit("SET_MODAL_EDIT", true);
          this.$emit("editModal", id);
        }
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
    setMarkup(object, option) {
      object.markup = option;

      this.checkIfOnlyPercent();
    },
    checkIfOnlyPercent() {
      var fixedMatch = false;
      let component = this;

      for (const amountObject in this.amount) {
        if (component.amount[amountObject].markup == "Fixed Markup") {
          fixedMatch = true;
        }
      }

      this.only_percent = !fixedMatch;
    },
    clearDisplay() {
      let component = this;

      for (const amountObject in this.amount) {
        component.amount[amountObject].markup = "Fixed Markup";
        component.amount[amountObject].amount = 0;
      }

      this.$refs.currencyDropdown.resetSelection();
      this.checkIfOnlyPercent();
    },
    itemVisible(toFilter) {
      let currentInput = this.filtered.toLowerCase();

      let type = toFilter.type ? toFilter.type.toLowerCase() : "";
      let name = toFilter.name ? toFilter.name.toLowerCase() : "";
      let display_name = toFilter.display_name
        ? toFilter.display_name.toLowerCase()
        : "";
      let description = toFilter.description
        ? toFilter.description.toLowerCase()
        : "";

      return (
        type.includes(currentInput) ||
        name.includes(currentInput) ||
        display_name.includes(currentInput) ||
        description.includes(currentInput)
      );
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
.table {
  min-height: 200px;
}
</style>
