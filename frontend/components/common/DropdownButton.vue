<template>
  <div>
    <b-dropdown id="dropdown-left" :text="btnText">
      <b-dropdown-item v-for="(item, i) in items" :key="i"
        :href="item.link" 
        :ref="item.ref" 
        :disabled="item.disabled()"
        @click="item.click" 
      >
        {{ item.label }}
      </b-dropdown-item>
    </b-dropdown>
  </div>
</template>
<script>
export default {
  props: {
    items: {
      type: Array,
      default: [],
    },
    btnText:{
      type:String,
      required: false,
      default: 'button text',
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
