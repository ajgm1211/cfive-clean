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
        <th scope="col" style="width:60px"><IconFilter /> ID</th>
        <th scope="col" style="width:93"><IconFilter /> Price Type</th>
        <th scope="col"><IconFilter /> Name</th>
        <th scope="col"><IconFilter /> Display name</th>
        <th scope="col" style="width: 300px;"><IconFilter /> Description</th>
        <th scope="col"><IconFilter /> Created at</th>
        <th scope="col"><IconFilter /> Updated at</th>
        <th scope="col" style="width:40px">
          <OptionsButton :standar="false" />
        </th>
      </tr>
      <tr v-if="dynamic">
        <th>
          <b-form-checkbox
            v-model="allSelected"
            aria-describedby="prices"
            aria-controls="prices"
            @change="toggleAll"
          >
          </b-form-checkbox>
        </th>

        <th v-for="item in thead" :key="item">{{ item }}</th>

        <th scope="col" style="width:40px">
          <OptionsButton :standar="false" />
        </th>
      </tr>
    </thead>
    <thead  v-if="dynamic">
      <tr>
        <th></th>
        <th><selectable></selectable></th>
        <th><selectable></selectable></th>
        <th> <MixedInput/> </th>
        <th> <MixedInput/> </th>
        <th><selectable></selectable></th>
        <th><MainButton text="Save"/></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in prices" :key="index">
        <td scope="row">
          <b-form-checkbox-group>
            <b-form-checkbox
              v-bind:value="item"
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
        <td scope="col"><OptionsButton /></td>
      </tr>
    </tbody>
  </b-table-simple>
</template>

<script>
import IconFilter from "../Icons/Filter.vue";
import OptionsButton from "../common/OptionsButton.vue";
import Selectable from "../common/Selectable.vue";
import MixedInput from '../common/MixedInput.vue';
import MainButton from '../common/MainButton.vue';
export default {
  props: {
    prices: {
      type: Array,
      default: [],
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
      default: [],
    },
  },
  components: { IconFilter, OptionsButton, Selectable, MixedInput, MainButton },
  data: () => ({
    selected: [],
    allSelected: false,
    indeterminate: false,
  }),
  methods: {
    toggleAll(checked) {
      this.selected = checked ? this.prices.slice() : [];
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
