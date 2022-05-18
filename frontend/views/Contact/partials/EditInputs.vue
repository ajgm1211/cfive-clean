
<template>
    <div class="col-12">
        <div class="back-btn" @click="$router.push('/contacts/v2');">
            <LeftArrow /> <span>back</span>
        </div>
        <b-row>
          <b-col>
            <CustomInput
                  label="First Name"
                  name="first_name"
                  ref="first_name"
                  v-model="contactData.first_name"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Last Name"
                  name="last_name"
                  ref="last_name"
                  v-model="contactData.last_name"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Phone"
                  name="phone"
                  ref="phone"
                  v-model="contactData.phone"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Email"
                  name="email"
                  ref="email"
                  v-model="contactData.email"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Position"
                  name="position"
                  ref="position"
                  v-model="contactData.position"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <label for="select-lenguage" class="labelv2">Company</label>
            <b-form-select
              name="select-lenguage"
              class="input-v2" 
              v-model="contactData.company_id" 
              :options="optionsCompanies" 
              @change="update()"
            >
            </b-form-select>
          </b-col>
        </b-row>
    </div>
</template>

<script>

import actions from '../../../store/modules/contact/actions'
import LeftArrow from "../../../components/icons/LeftArrow"
import CustomInput from "../../../components/common/CustomInput"
import toastr from "toastr"

export default {
  components: {CustomInput, LeftArrow},
  props: {
    contact: {
      type: Object,
      default:{}
    },
    user:{
      type: Object,
      default(){
        return {};
      }
    }
  },
  data() {
    return {
      actions:actions,
      optionsCompanies:[]

    }
  },
  computed:{
    contactData:{
      get: function(){
        var vm = this;
        return vm.contact
      },
      set: function(newContactData){
        return newContactData
      }
    }
  },
  async created(){
    var dataOptions = await this.actions.getCompanies()
    this.optionsCompanies = dataOptions.data.data.companies.map(item =>{
      return {
        text:item.business_name,
        value:item.id
      }
    })
  },
  methods:{
    async update(){
      try {
        const {newContact} = await this.actions.update(this.$route.params.id, this.contactData)  
        this.contactData = newContact
        toastr.success("successful update");
      } catch (error) {
        toastr.error("unsuccessful update.");
      }
    }
  }
}
</script>