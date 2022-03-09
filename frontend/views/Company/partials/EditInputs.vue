
<template>
    <div class="col-12">
        <div class="back-btn" @click="$router.push('/companies/v2');">
            <LeftArrow /> <span>back</span>
        </div>
        <b-row>
          <b-col>
            <CustomInput
                  label="Business Name"
                  name="business_name"
                  ref="business_name"
                  v-model="companyData.business_name"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Phone"
                  name="phone"
                  ref="phone"
                  v-model="companyData.phone"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Email"
                  name="email"
                  ref="email"
                  v-model="companyData.email"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Pdf Language"
                  name="pdf_language"
                  ref="pdf_language"
                  v-model="companyData.pdf_language"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  label="Address"
                  name="address"
                  ref="address"
                  v-model="companyData.address"
                  @blur="update()"
            />
          </b-col>
          <b-col>
            <CustomInput
                  type= "checkbox"
                  label="WhiteLabel"
                  name="whitelabel"
                  ref="whitelabel"
                  v-model="companyData.whitelabel"
                  @blur="update()"
            />
          </b-col>
        </b-row>
    </div>
</template>

<script>
import actions from '../../../store/modules/company/actions'
import LeftArrow from "../../../components/icons/LeftArrow"
import CustomInput from "../../../components/common/CustomInput"
import toastr from "toastr"

export default {
  components: {CustomInput, LeftArrow},
  props: {
    company: {
      type: Object,
      default:{}
    }
  },
  data() {
    return {
      actions:actions,
      
    }
  },
  computed:{
    companyData:{
      get: function(){
        var vm = this;
        return vm.company
      },
      set: function(newCompanyData){
        return newCompanyData
      }

    }
  },
  methods:{
    async update(){
      try {
        const {newCompany} = await this.actions.update(this.$route.params.id, this.companyData)  
        this.companyData  = newCompany
        toastr.success("successful update");
      } catch (error) {
        toastr.error("unsuccessful update.");
      }
    }
  }
}
</script>

<style lang="scss" scoped>
</style>