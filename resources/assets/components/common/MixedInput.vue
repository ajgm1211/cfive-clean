<template>
  <form class="d-flex rateselect">
    <CustomInput v-model="v_model" :mixed="true" :showLabel="false" />
    <Selectable
      @selected="setSelected($event)"
      :options="options"
      background_color="#006bfa"
      border_color="#006bfa"
      font_color="white"
      :icon="false"
      :mixed="true"
      :selected="selected"
    />
  </form>
</template>

<script>
import CustomInput from "./CustomInput.vue";
import Selectable from "./Selectable.vue";
export default {
  components: { Selectable, CustomInput },
  props: {
    options: {
      type: Array,
    },
    v_model:{
      required: true,
    }
  },
  data: () => ({
    selected: "Fixed Markup",
  }),
  methods: {
    // setSelected(option) {
    //   this.selected = option;
    // },
  },
};
</script>

<style>
.rateselect {
  width: 95px;
}
</style>
