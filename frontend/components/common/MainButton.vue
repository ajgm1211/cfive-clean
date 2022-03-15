<template>
  <button class="main-btn" @click="$emit('click')">
    <Plus v-if="add" class="mr-2"/> <Check v-if="save" class="mr-2"/>   {{ text }}</button>
</template>

<script>
import Plus from "../icons/Plus";
import Check from "../icons/Check";
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
