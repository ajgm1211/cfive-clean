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
            @change="toggleAll"
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
            :selected="directions[0]"
            :options="directions"
          />
        </th>
        <th>
          <Selectable
            :defaultFirstOption="true"
            style="width: 120px"
            :selected="restrictions[0]"
            :options="restrictions"
          />
        </th>
        <th>
          <MixedInput :v_model="new_rate.price_20" :options="price_types" />
        </th>
        <th>
          <MixedInput :v_model="new_rate.price_40" :options="price_types" />
        </th>
        <th>
          <Selectable
            :defaultFirstOption="true"
            style="width: 100px"
            :selected="currencies[0]"
            :options="currencies"
          />
        </th>
        <th style="position: relative;">
          <MainButton :save="true" text="Save" style="right:-84px;" />
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
        <td>{{ item.amount }}</td>
        <td>{{ item.type_40 }}</td>
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
        <td>{{ item.description }}</td>
        <td>{{ item.created_at }}</td>
        <td>{{ item.updated_at }}</td>
        <td scope="col"><OptionsButton @option="action($event, item.id)" /></td>
      </tr>
    </tbody>
  </b-table-simple>
</template>

<script>
// import IconFilter from "../Icons/Filter.vue";
import OptionsButton from "../common/OptionsButton.vue";
import Selectable from "../common/Selectable.vue";
import MixedInput from "../common/MixedInput.vue";
import MainButton from "../common/MainButton.vue";

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
    thead: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  components: { OptionsButton, Selectable, MixedInput, MainButton },
  data: () => ({
    selection: [],
    selected: [],
    selectedRate: [],
    allSelected: false,
    allRatesSelected: false,
    indeterminate: false,
    price_types: ["Percent Markup", "Fixed Markup"],
    directions: ["Export", "Import", "Both"],
    restrictions: ["Freight", "Surcharge", "Inland"],
    currencies: ["USD", "AUSD", "BS"],
    new_rate: {
      price_20: "0",
      price_40: "0",
    },
  }),
  methods: {
    toggleAll(checked) {
      this.selected = checked ? this.prices.slice() : [];

      if (this.dynamic === true) {
        this.selectedRate = checked ? this.rates.slice() : [];
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
        this.$store.dispatch("duplicatePriceLevel", { id: id });
      }
      if (option == "delete") {
        this.$store.dispatch("deletePriceLevel", { id: id });
      }
      if (option == "deleteSelected") {
        this.$store.dispatch("deleteSelectedPriceLevel", {
          body: {
            ids: this.selected,
          },
        });
      }
    },
  },
  watch: {
    selected(newValue, oldValue) {
      if (newValue.length === 0) {
        this.indeterminate = false;
        this.allSelected = false;
      } else if (newValue.length === this.prices.length) {
        this.indeterminate = false;
        this.allSelected = true;
      } else {
        this.indeterminate = true;
        this.allSelected = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>