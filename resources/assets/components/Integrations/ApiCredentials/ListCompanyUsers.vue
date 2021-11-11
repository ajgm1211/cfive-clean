<template>
  <b-table-simple checkable class="table table-striped table-responsive">
    <thead>
      <tr>
        <th>
          ID
        </th>
        <th>
          Name
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in companyUsers" :key="index">
        <td>{{ item.id }}</td>
        <td>{{ item.name }}</td>
        <td><button class="btn btn-primary" v-on:click="redirect(item.id)">See Integration</button></td>
      </tr>
    </tbody>
    
  </b-table-simple>
</template>

<script>
import OptionsButton from "../../common/OptionsButton.vue";
import Selectable from "../../common/Selectable.vue";
import MainButton from "../../common/MainButton.vue";
import CustomInput from "../../common/CustomInput.vue";
import { mapGetters } from "vuex";
import SorteableDropdown from "../../common/SorteableDropdown.vue";

export default {
  props: {
    companyUsers: {
      type: Array,
      default() {
        return [];
      },
    },
    currentPage: {
      default: 0,
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
  }),
  mounted() {
    
  },
  methods: {
    redirect(id) {
      this.$router.push({
       name: "api-credentials-by-company-user",
       params: { id: id },
      });
    },
  },
  computed: {
     //...mapGetters(["GET_PRICE_LEVEL_DATA"]),
  },
};
</script>

<style lang="scss" scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>
