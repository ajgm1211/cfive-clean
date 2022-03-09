<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div v-if="!create" class="modal-content-create">
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
          <div class="modal-footer-content-wl">
            <input type="checkbox" id="checkbox" v-model="model.whitelabel">
            <label for="checkbox">Add to whitelabel</label>
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
                        <div class="modal-footer-content-wl">
                          <input type="checkbox" id="checkbox" v-model="whitelabel">
                          <label for="checkbox">Add to whitelabel</label>
                        </div>
                        <div class="modal-footer-create-container-btns">
                          <p @click="$emit('cancel')">Cancel</p>
                          <b-button type="submit" class="btn-form" variant="primary">Add Companies</b-button>
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
      },
    }
  },
  data() {
    return{
      actions:actions,
      showCurrency: true,
      file:null,
      showDismissibleAlert: false,
      showDismissibleAlertSuccess: false,
      messageFile:'',
      validformats:[
                    ".csv",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/vnd.ms-excel"
      ],
      whiteLabel:false
    }
  },
  methods: {
    async createCompany(){
      try {
        const {newCompany} = await this.actions.create(this.model)  
        //this.company  = newCompany
        toastr.success("successful create")
        this.$root.$emit('submitData')
        this.$emit('cancel')
      } catch (error) {
        toastr.error("unsuccessful create.")
      }
    },
    async onSubmitValidate() {
        var url = 'api/contracts'
        
        let formData = new FormData()
        formData.append('_token', this.csrf)
        
        let validation = this.validateFormat(this.$refs.file.files[0].type)
        
        if(validation == true){

            this.messageFile = "The File is valid!"
            if($(this.$refs.file.files[0]) !== undefined){
                formData.append('file', this.$refs.file.files[0])
                formData.append('whiteLabel', this.whiteLabel)
            }
            this.show = !this.show
            await this.actions.update(url , formData)
            .then((response)=>{
                this.messageFile = 'We have registered the information correctly!'
                this.showDismissibleAlert=false
                this.showDismissibleAlertSuccess= true
                this.items = response.data.data
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
    addToWhiteLabel(){
      this.whiteLabel = !this.whiteLabel
    }
  },
};
</script>

