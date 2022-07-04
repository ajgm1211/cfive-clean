<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div v-if="create" class="modal-content-create">
        <div class="p-3">
            <p>{{content}}</p>
        </div>
        <div>
          <div class="modal-footer-create-container">
            <div class="modal-footer-content-wl input-box" >
              <div id="checkbox-create">
                  <div>
                    <b-form-checkbox
                      v-model="addContact"
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
                @click="confirmTransfer()"
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

import actions from '../../../store/modules/company/actions'
import CustomInput from "../../../components/common/CustomInput"
import MainButton from "../../../components/common/MainButton"
import toastr from "toastr"

export default {
    components: {MainButton},
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
        company:{
            type: Number,
            default(){
                return {};
            }
        }
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            actions:actions,
            input_error: false,
            dataLoaded: false,
            textAdd:"Also contacts to",
            content:"Are you sure that you want to transfer to whitelabel?",
            addContact: false
        }
    },
    methods:{
        async confirmTransfer(){
            try {
                await this.actions.transfer([this.company], this.addContact)
                toastr.success("Transfer to whitelabel successfully")
                this.$emit('cancel')
            } catch (error) {
                toastr.error("Not Transfer to whitelabel successfully")
            }
                
            
        }
    }
}
</script>

<style lang="scss" scoped>
</style>