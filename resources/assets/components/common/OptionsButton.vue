<template>
  <div style="position:relative;">
    <div class="options-button" @click="showOptions = !showOptions">
      <DotsMenu />
    </div>

    <div class="crud-options" v-if="showOptions">
      <div class="triangle"></div>

      <p
        v-for="option in options"
        :key="option"
        @click="select(option)"
        :class="option == 'delete' ? 'error-msj' : ''"
      >
        {{ option }}
      </p>
    </div>
  </div>
</template>

<script>
import DotsMenu from "../Icons/DotsMenu.vue";
export default {
  props: {
    standar: {
      type: Boolean,
      default: true,
    },
  },
  components: {
    DotsMenu,
  },
  data: () => ({
    showOptions: false,
    options: ["edit", "duplicate", "delete"],
  }),
  created() {
    if (this.standar === false) {
      this.options = this.options.filter((e) => e !== "edit" &&   e !==  "duplicate");
    }
    window.addEventListener("click", (e) => {
      if (!this.$el.contains(e.target)) {
        this.showOptions = false;
      }
    });
  },
  methods:{
    select(option){
      console.log('option', option)
      this.showOptions = false;
    }
  }
};
</script>

<style lang="scss" scoped>
.options-button {
  width: 20px;
  height: 20px;
  background: #e0e0e0;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.crud-options {
  position: absolute;
  background: #fff;
  z-index: 4999999;
  right: 0;
  top: calc(100% + 23px);
  min-width: 100px;
  border-radius: 4px;
  padding: 8px 0;
  box-shadow: 0px 1px 5px rgba(188, 184, 184, 0.53);

  & > p {
    padding: 2px 14px;
    width: 100%;
    margin: 0;
    cursor: pointer;
    margin-bottom: 3px;
    text-transform: capitalize;

    &:hover {
      background: #ececec;
    }

    &:last-of-type {
      margin-bottom: 0;
    }
  }

  & > .error-msj {
    color: #ff4c61 !important;
  }

  & > .triangle {
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    background: #fff;
    width: 13px;
    height: 9px;
    position: absolute;
    bottom: calc(100% - 1px);
    right: 4px;
  }
}
</style>
