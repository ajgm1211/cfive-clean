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
            :label="field.label"
            :name="field.name"
            :ref="field.name"
            v-model="model[field.name]"
            :rules="field.rules"
          />

          <SorteableDropdown
            v-else-if="field.type == 'dropdown'"
            @reset="selected = ''"
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
    dataLoaded: false,
  }),
  mounted() {
    this.setInitialData();

    console.log(this.model);
    console.log(this.dispatch);
  },
  methods: {
    postData() {
      if (!this.validate()) return;

      let dispatchBody = this.setBody();

      if (this.dispatch == "editPriceLevel") {
        
              console.log("dispatchBody", dispatchBody);
        console.log("hello");
        let body = {
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
        };

        console.log("body", body);
        this.$store.dispatch(this.dispatch, {
          body: body,
          id: this.model.id,
          currentId: this.$route.params.id,
          page: 1
        });
        
      } else {
        this.$store.dispatch(this.dispatch, {
          body: dispatchBody,
        });
      }
    },
    validate() {
      let component = this;
      let index = 0;

      this.fields.forEach(function(field) {
        if (field.type == "input") {
          if (component.$refs[field.name][index].validate()) {
            return false;
          }
        } else if (field.type == "dropdown") {
          if (!component.model[field.name] && field.rules.required) {
            component.selectable_error = true;
            return false;
          }
        }
      });

      return true;
    },
    setSelected(option, field_name) {
      this.model[field_name] = option;
    },
    setInitialData() {
      let component = this;
      var dataIndex = 0;

      this.fields.forEach(function(field) {
        field.id = dataIndex;
        if (!component.model[field.name]) {
          component.model[field.name] = "";
        }
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
