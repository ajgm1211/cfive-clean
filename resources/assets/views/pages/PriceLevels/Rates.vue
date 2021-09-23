<template>
  <section>
    <div class="back-btn" @click="$router.back()">
      <LeftArrow /> <span>back</span>
    </div>
    <div class="inputs-container">
      <CustomInput
        :disabled="true"
        label="Name"
        name="name"
        ref="name"
        v-model="price.name"
        :rules="{
          required: true,
        }"
      />
      <CustomInput
        :disabled="true"
        label="Display name"
        name="display name"
        ref="display_name"
        v-model="price.display_name"
        :rules="{
          required: true,
        }"
      />
      <Selectable
        :disabled="true"
        @selected="setSelected($event)"
        :selected="selected"
        label="Price Level Type"
        :options="price_types"
        :error="selectable_error"
      />
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
        ></ckeditor>

        <Restrictions
          v-if="active == 'Only Apply To'"
          style="border: none!important;"
          :datalists="datalists"
          :actions="actions.restrictions_lcl"
          :data="currentData"
        />

        <ListPrices 
        v-if="active == 'Detail'"
        :filters="false"
        :thead="thead"
        :dynamic="true"
        :prices="prices"/>
      </div>
    </div>
  </section>
</template>

<script>
import Restrictions from "../../../../js/components/contracts/Restrictions.vue";
import CustomInput from "../../../components/common/CustomInput.vue";
import Selectable from "../../../components/common/Selectable.vue";
import LeftArrow from "../../../components/Icons/LeftArrow.vue";
import actions from "../../../../../resources/js/actions";
import ListPrices from "../../../components/PriceLevel/ListPrices.vue";
// import axios from "axios";
export default {
  components: {
    CustomInput,
    Selectable,
    LeftArrow,
    Restrictions,
    ListPrices,
  },
  data: () => ({
    actions: actions,
    datalists: null,
    currentData: {
      daterange: { startDate: null, endDate: null },
    },
    active: "Detail",
    tabs: ["Detail", "Only Apply To", "Description"],
    price: {
      name: "example",
      display_name: "display example",
      description: "hola description",
    },
    price_types: ["FCL", "LCL"],
    selected: "FCL",
    selectable_error: false,
    thead:['Direction', 'Apply to', '20', '40', 'Currency'],
    prices: [
    ],
  }),
  created() {
    this.getData();
  },
  methods: {
    getData() {
      let url = "/api/v2/contractslcl/data";
      api.getData({}, url, (err, data) => {
        this.setDropdownLists(err, data.data);
      });
    },
    setSelected(option) {
      this.selected = option;
    },
    currentTab(tab) {
      this.active = tab;
    },
    setDropdownLists(err, data) {
      this.datalists = data;

      this.datalists["route_types"] = [
        { id: "port", name: "Port", vselected: "harbors" },
        { id: "country", name: "Country", vselected: "countries" },
      ];
    },
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
  grid-template-columns: 200px 200px 200px;
  column-gap: 20px;
  padding: 0 100px;
  margin-top: 20px;
}

.back-btn {
  cursor: pointer;
  display: flex;
  align-items: center;

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
</style>
