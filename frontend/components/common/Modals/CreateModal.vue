<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div v-if="!create" class="modal-content-create">
        <div v-if="dataLoaded">
          <form on="" class="create-form" autocomplete="off">
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
          <div class="modal-footer-create-container">
            <div class="modal-footer-content-wl input-box" >
              <div id="checkbox-create" v-if="user.settings_whitelabel">
                  <div>
                    <b-form-checkbox
                      v-model="whitelabel"
                      id="create-whitelabel"
                      name="checkbox-create"
                      true-value="true"
                      false-value="false"
                    >
                      <label for="create-whitelabel">
                        {{textAdd}}
                      </label> 
                    </b-form-checkbox>
                  </div>
              </div>
            </div>
            <div class="modal-footer-create-container-btns">
              <p @click="$emit('cancel')">Cancel</p>
              <MainButton
                @click="createCompany()"
                :text="action + ' ' + title"
                :add="true"
              />
            </div>
          </div>
        </div>
      </div>
      <div v-else class="modal-content-create">
        <b-form @submit.prevent="onSubmitValidate">
          <b-container>
              <b-row>
                  <b-col cols="12" md="12" class="mb-2">
                      <b-form-group description="Warning: Only excel files are allowed">
                          <b-form-file
                              placeholder="Insert the file here" 
                              drop-placeholder="Drag file here..."
                              ref="file"
                              v-model="file"
                              id="file"
                              :state="Boolean(file)"
                              accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                              required
                          ></b-form-file>
                      </b-form-group>
                  </b-col>
              </b-row>
              <b-row class="modal-footer-create">
                  <b-col cols="12" md="12">
                      <div>
                        <b-alert v-model="showDismissibleAlertSuccess" variant="success" dismissible>{{messageFile}}</b-alert>
                        <b-alert v-model="showDismissibleAlert" variant="danger" dismissible>{{messageFile}}</b-alert>
                      </div>
                      <div class="modal-footer-create-container">
                        <div class="modal-footer-content-wl input-box">
                          <div id="checkbox-create" v-if="user.settings_whitelabel" >
                            <div>
                              <b-form-checkbox
                                id="create-whitelabel"
                                v-model="whitelabel"
                                name="checkbox-create"
                                true-value="true"
                                false-value="false"
                              >
                                <label for="create-whitelabel">
                                  {{textAdd}}
                                </label> 
                              </b-form-checkbox>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer-create-container-btns">
                          <p @click="$emit('cancel')">Cancel</p>
                          <b-button type="submit" class="btn-form" variant="primary">{{action}} {{title}}</b-button>
                        </div>
                      </div>
                  </b-col>
              </b-row>
          </b-container>
        </b-form>
      </div>
    </div>
  </section>
</template>

<script>

import actions from '../../../store/modules/company/actions'
import CustomInput from "../../../components/common/CustomInput"
import MainButton from "../../../components/common/MainButton"
import toastr from "toastr"

export default {
  components: {CustomInput, MainButton},
  props: {
    title: {
      type: String,
    },
    action: {
      type: String,
    },
    create:{
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
    },
    user:{
      type: Object,
      default(){
        return {};
      }
    }
  },
  data() {
    return{
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      actions:actions,
      showCurrency: true,
      file:null,
      showDismissibleAlert: false,
      showDismissibleAlertSuccess: false,
      messageFile:'',
      input_error: false,
      dataLoaded: false,
      textAdd:"Add To WhiteLabel",
      validformats:[
                    ".csv",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/vnd.ms-excel"
      ],
      whitelabel:false
    }
  },
  methods: {
    async createCompany(){
      try {
        if (!this.validate()) return;
        this.setBody()
        const {newCompany} = await this.actions.create(this.model, this.whitelabel)  
        //this.company  = newCompany
        toastr.success("Created successfully")
        this.$root.$emit('submitData')
        //this.$emit('cancel')
      } catch (error) {
        toastr.success("Not created successfully")
      }
        
      
    },
    async onSubmitValidate() {
        
        let formData = new FormData()
        formData.append('_token', this.csrf)
        
        let validation = this.validateFormat(this.$refs.file.files[0].type)
        
        if(validation == true){

            this.messageFile = "The File is valid!"
            if($(this.$refs.file.files[0]) !== undefined){
                formData.append('file', this.$refs.file.files[0])
                formData.append('whitelabel', this.whitelabel)
            }
            this.show = !this.show
            await this.actions.createMassive(formData)
            .then((response)=>{
                this.messageFile = response.data
                this.showDismissibleAlert=false
                this.showDismissibleAlertSuccess= true
                this.show = true
            }).catch(error => {
                this.showDismissibleAlert=true
                this.showDismissibleAlertSuccess= false
                this.show = !this.show
                this.messageFile = 'We are sorry, it seems that a communication error occurred or it may be that the excel format does not comply with the requested standard'
            });

        }else{
            this.messageFile = "The file is invalid, please enter a valid file format: .csv, .xlsx, .xls"
        }
    },
    validateFormat(type){
        if(this.validformats.indexOf(type) == -1){
            this.showDismissibleAlert=true
            this.showDismissibleAlertSuccess= false
            return false
        } else{
            this.showDismissibleAlert=false
            this.showDismissibleAlertSuccess= true
            return true
        }
    },
    toogleAddToWhiteLabel(){
      this.whitelabel= !this.whitelabel
      if (this.whitelabel == true) {
        this.textAdd = "Selected for add to WhiteLabel"  
      }else{
        this.textAdd = "Add To WhiteLabel"
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
      let component = this
      this.fields.forEach(function(field) {
        body[field.name] = component.model[field.name];
      });

      return body;
    }
  },
  mounted() {
    if(!this.create){
      this.setInitialData();
    }
  },
};
</script>

