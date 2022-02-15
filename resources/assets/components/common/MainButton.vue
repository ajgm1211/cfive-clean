<template>
  <button class="main-btn" @click="$emit('click')">
    <Plus v-if="add" class="mr-2"/> <Check v-if="save" class="mr-2"/>   {{ text }}</button>
</template>

<script>
import Plus from "../Icons/Plus.vue";
import Check from "../Icons/Check.vue";
export default {
  props: {
    text: {
      type: String,
      default: "button text",
    },
    add: {
      type: Boolean,
      default: false,
    },
    save: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    Plus,
    Check
  },
};
</script>

<style lang="scss">
.main-btn {
  background-color: #006bfa;
  border: none;
  outline: none;
  color: white;
  display: flex;
  flex-direction: row;
  justify-content: center;
  height: fit-content;
  align-items: center;
  padding: 5px 25px;
  text-transform: capitalize;
  box-shadow: 0px 4px 8px rgba(54, 123, 245, 0.24);
  border-radius: 40px;

  &:hover {
    background-color: #0051bb;
    box-shadow: 0px 4px 8px rgba(28, 111, 255, 0.295);
  }
}
</style>
