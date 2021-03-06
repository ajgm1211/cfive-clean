<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div v-if="dataLoaded" class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <form action="" class="create-form" autocomplete="off">
        <div v-for="(field, fieldKey) in fields" :key="fieldKey">
          <CustomInput
            v-if="field.type == 'input'"
            :custom_error="field.error"
            :label="field.label"
            :name="field.name"
            :ref="field.name"
            v-model="model[field.name]"
            :placeholder="field.placeholder"
            :rules="field.rules"
            :type="field.input_type ? field.input_type : 'text'"
          />

          <SorteableDropdown
            :class="[
              showCurrency == false && field.name == 'currency' ? 'hidden' : '',
            ]"
            v-else-if="field.type == 'dropdown'"
            @reset="model[field.name] = ''"
            :error="selectable_error"
            :label="field.label"
            @selected="setSelected($event, field.name)"
            :itemList="field.items"
            :show_by="field.show_by"
            :preselected="model[field.name]"
          />
        </div>
      </form>
      <div class="controls-container">
        <p @click="$emit('cancel')">Cancel</p>
        <MainButton
          @click="postData()"
          :text="action + ' ' + title"
          :add="true"
        />
      </div>
    </div>
  </section>
</template>

<script>
import MainButton from "../common/MainButton.vue";
import CustomInput from "../common/CustomInput.vue";
import SorteableDropdown from "../common/SorteableDropdown.vue";

export default {
  components: { MainButton, CustomInput, SorteableDropdown },
  props: {
    fields: {
      type: Array,
      default() {
        return [];
      },
    },
    model: {
      type: Object,
      default() {
        return {};
      },
    },
    title: {
      type: String,
    },
    action: {
      type: String,
    },
    dispatch: {
      type: String,
    },
  },
  data: () => ({
    selectable_error: false,
    input_error: false,
    dataLoaded: false,
    showCurrency: true,
  }),
  mounted() {
    this.setInitialData();

    if (
      this.model.type_20_t == "Percent Markup" &&
      this.model.type_40_t == "Percent Markup"
    ) {
      this.showCurrency = false;
    } else if (this.model.type_lcl_t == "Percent Markup") {
      this.showCurrency = false;
    } else {
      this.showCurrency = true;
    }
  },
  methods: {
    postData() {
      if (!this.validate()) return;

      let dispatchBody = this.setBody();
      if (this.dispatch == "editPriceLevel") {
        let body;

        if (
          (dispatchBody.type_20_t == "Percent Markup" &&
            dispatchBody.type_40_t == "Percent Markup") ||
          dispatchBody.type_lcl_t == "Percent Markup"
        ) {
          dispatchBody.currency = null;
        }

        if ("type_lcl" in this.model) {
          body = {
            currency: dispatchBody.currency,
            direction: dispatchBody.direction,
            price_level_apply: dispatchBody.price_level_apply,
            amount: {
              type_lcl: {
                amount: dispatchBody.type_lcl,
                markup: dispatchBody.type_lcl_t,
              },
            },
            showCurrency: this.showCurrency,
          };
        } else {
          body = {
            currency: dispatchBody.currency,
            direction: dispatchBody.direction,
            price_level_apply: dispatchBody.price_level_apply,
            amount: {
              type_20: {
                amount: dispatchBody.type_20,
                markup: dispatchBody.type_20_t,
              },
              type_40: {
                amount: dispatchBody.type_40,
                markup: dispatchBody.type_40_t,
              },
            },
            showCurrency: this.showCurrency,
          };
        }

        this.$store.dispatch(this.dispatch, {
          body: body,
          id: this.model.id,
          currentId: this.$route.params.id,
          page: 1,
        });
      } else {
        this.$store.dispatch(this.dispatch, {
          body: dispatchBody,
        });
      }
    },
    validate() {
      let component = this;

      let bool;

      this.fields.forEach(function(field) {
        if (field.type == "input") {
          if (!component.model[field.name] && field.rules.required) {
            field.error = true;
            bool = false;
          } else {
            field.error = false;
            bool = true;
          }
        } else if (field.type == "dropdown") {
          if (!component.model[field.name] && field.rules.required) {
            component.selectable_error = true;
            bool = false;
          } else {
            bool = true;
          }
        }
      });

      if (bool == false) {
        return false;
      } else {
        return true;
      }
    },
    setSelected(option, field_name) {
      this.model[field_name] = option;
      if (
        this.model.type_20_t == "Percent Markup" &&
        this.model.type_40_t == "Percent Markup"
      ) {
        this.showCurrency = false;
      } else if (this.model.type_lcl_t == "Percent Markup") {
        this.showCurrency = false;
      } else {
        this.showCurrency = true;
      }
    },
    setInitialData() {
      let component = this;
      var dataIndex = 0;

      this.fields.forEach(function(field) {
        field.id = dataIndex;
        if (!component.model[field.name]) {
          component.model[field.name] = "";
        }
        field.placeholder = "Insert " + field.label;
        dataIndex += 1;
      });

      this.dataLoaded = true;
    },
    setBody() {
      var body = {};
      let component = this;

      this.fields.forEach(function(field) {
        body[field.name] = component.model[field.name];
      });

      return body;
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
  position: fixed;
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
  z-index: 50010;
}

.create-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 20px;
  row-gap: 30px;
  padding: 40px 40px 20px 40px;
  grid-template-rows: repeat(3, 1fr);
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

.hidden {
  display: none;
}
</style>
