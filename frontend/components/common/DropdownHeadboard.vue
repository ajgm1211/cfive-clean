<template>
  <div>
    <b-dropdown id="dropdown-left" text="Import">
      <b-dropdown-item v-for="(item, i) in items" :key="i"
        :href="item.link" 
        @click="item.click" 
        :ref="item.ref" 
        :disabled="item.disabled()"
      >
        {{ item.label }}
      </b-dropdown-item>
    </b-dropdown>
  </div>
</template>
<script>
export default {
  props: {
    whitelabel: {
      type: Number,
      default: 0,
    },
    items: {
      type: Array,
      default: [],
    },
    toggleAddToWhiteLabel:{
      type:Boolean,
      required: false,
      default: true,
    },
  },
  watch:{
    toggleWhiteLabel(){
      this.$emit("toggleButtonWhiteLabel", this.toggleAddToWhiteLabel)
    }
  }
};
</script>
