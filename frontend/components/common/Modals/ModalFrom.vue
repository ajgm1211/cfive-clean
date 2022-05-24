<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="form-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div v-if="!showForm" class="form-modal-content">
        <div v-if="dataLoaded">
          <form on="" class="from-body" autocomplete="off">
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
          <div class="form-modal-footer-container">
            <div class="form-modal-footer-content-lf input-box">

            </div>
            <div class="form-modal-footer-content-rf">
              <p @click="$emit('cancel')">Cancel</p>
              <MainButton
                @click="submitForm()"
                :text="action + ' ' + title"
                :add="true"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import CustomInput from "../../../components/common/CustomInput"
import MainButton from "../../../components/common/MainButton"

export default {
  components: {CustomInput, MainButton},
  props: {
    action:{
        type:String
    },
    title: {
        type: String
    },
    showForm:{
        type:Boolean,
        default() {
            return false;
        }
    },
    fields:{
        type:Array,
        default(){
            return [];
        }
    },
    model: {
        type: Object,
        default() {
            return {};
        }
    }
  },
  data() {
    return{
        dataLoaded:false
    }
  },
  methods: {
    async submitForm(){
        if (!this.validate()) return;
            this.setBody()
            await this.$emit('submitForm',this.model)
            this.$emit('cancel')
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
      })
      this.dataLoaded = true;
    },
    setBody() {
      var body = {};
      let component = this;

      this.fields.forEach(function(field) {
        body[field.name] = component.model[field.name];
      });

      return body;
    }
  },
  mounted() {
    if(!this.showForm){
      this.setInitialData();
    }
  },
};
</script>

