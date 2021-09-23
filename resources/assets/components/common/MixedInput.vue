<template>
  <form class="d-flex rateselect">
    <CustomInput :mixed="true" :showLabel="false" />
    <Selectable
      background_color="#006bfa"
      border_color="#006bfa"
      font_color="white"
      :icon="false"
      :selected="'$'"
    />
  </form>
</template>

<script>
import CustomInput from "./CustomInput.vue";
import Selectable from "./Selectable.vue";
export default {
  components: { Selectable, CustomInput },
};
</script>

<style>
.rateselect {
  width: 106px;
}
</style>
