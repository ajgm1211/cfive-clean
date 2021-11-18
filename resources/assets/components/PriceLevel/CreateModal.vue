<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head"><h3>add price level</h3></div>
      <form action="" class="create-form" autocomplete="off">
        <CustomInput
          label="Name"
          name="name"
          ref="name"
          v-model="price.name"
          :rules="{
            required: true,
          }"
        />
        <CustomInput
          label="Display name"
          name="display name"
          ref="display_name"
          v-model="price.display_name"
          :rules="{
            required: true,
          }"
        />

        <SorteableDropdown @reset="selected = ''" :error="selectable_error" label="Price Level Type" @selected="setSelected($event)" :itemList="price_types" />
      </form>
      <div class="controls-container">
        <p @click="$emit('cancel')">Cancel</p>
        <MainButton @click="postData()" text="Add Price Levels" :add="true" />
      </div>
    </div>
  </section>
</template>

<script>
import MainButton from "../common/MainButton.vue";
import CustomInput from "../common/CustomInput.vue";
import SorteableDropdown from '../common/SorteableDropdown.vue';

export default {
  components: { MainButton, CustomInput, SorteableDropdown },
  data: () => ({
    price: {
      name: "",
      display_name: "",
    },
    price_types: ["FCL", "LCL"],
    selected: "",
    selectable_error: false,
  }),
  methods: {
    postData() {
      if (!this.validate()) return;

      this.$store.dispatch("createPriceLevel", {
        body: {
          name: this.price.name,
          display_name: this.price.display_name,
          price_level_type: this.selected,
        },
      });
    },
    validate() {
      if (this.$refs.name.validate()) {
        return false;
      }
      if (this.$refs.display_name.validate()) {
        return false;
      }
      if (!this.selected) {
        this.selectable_error = true;
        return false;
      }

      return true;
    },
    setSelected(option) {
      this.selected = option;
    },
  },
};
</script>

<style lang="scss" scoped>
section {
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: transparent;
}

.layer {
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.397);
  z-index: 5000;
  position: absolute;
  top: 0;
  left: 0;
}

.create-modal {
  background: #f9f9f9;
  border-radius: 15px;
  width: 600px;
  //   height: 350px;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  z-index: 5001;
}

.create-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 20px;
  row-gap: 30px;
  padding: 40px 40px 20px 40px;
}

.controls-container {
  display: flex;
  justify-content: flex-end;
  padding: 20px;
  align-items: center;

  & > p {
    margin: 0;
    margin-right: 20px;
    color: #ff4c61;
    cursor: pointer;
  }
}

.modal-head {
  background-color: white;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  padding: 10px 20px;

  & > h3 {
    margin: 0;
    text-transform: capitalize;
    font-size: 17px;
    color: #071c4b;
    letter-spacing: 0.05em;
    font-weight: 500;
  }
}
</style>
