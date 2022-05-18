
<template>
  <div class="col-12">
    <div class="back-btn" @click="$router.push('/companies/v2');">
        <LeftArrow /> <span>back</span>
    </div>
    <div class="head">
        <div class="head-title">
            <h2>Failed Companies</h2>
        </div>
        <div class="head-btns">
        </div>
    </div>
    <div>
      <DataTable
        :fields="fields"
        :actions="actions"
        :filter="true"
        :simpleSelect="false"
        :massiveSelect="false"
        :massiveactions="[]"
        :singleActions="['Fix']"
        :totalResults="totalResults"
        :classTable="classTable"
        :customAction="'FIX'"
        @customAction="customAction"
        ref="datatable_failed"
      >
      </DataTable>
    </div>
    <div>
      <ModalFrom
        v-if="showForm"
        :title="'Failed Contact'"
        :action="'Edit'"
        @cancel="showForm = false"
        :fields="modal_fields"
        :model="model"
        @submitForm="submitForm"
      />
    </div>
  </div>
</template>

<script>
import LeftArrow from '../../components/icons/LeftArrow'
import DataTable from '../../components/common/DataTable'
import actions from '../../store/modules/company/actionFailed'
import ModalFrom from '../../components/common/Modals/ModalFrom'

export default {
  components: {DataTable, LeftArrow, ModalFrom},
  data() {
    return {
        totalResults:true,
        classTable:"table table-striped table-responsive",
        actions:actions,
        fields: [
          { key: "id", label: "ID", filterIsOpen:false },
          { key: "business_name", label: "Business Name", filterIsOpen:false },
          { key: "phone", label: "Phone", filterIsOpen:false },
          { key: "email", label: "Email", filterIsOpen:false },
          { key: "address", label: "Address", filterIsOpen:false },
          { key: "tax_number", label: "Tax Number", filterIsOpen:false },
          { key: "created_at", label: "Created at", filterIsOpen:false },
        ],
        showForm:false,
        modal_fields: [
          {
            type: "input",
            label: "Business Name",
            name: "business_name",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Phone",
            name: "phone",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Email",
            name: "email",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Address",
            name: "address",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Tax Number",
            name: "tax_number",
            error:false,
            rules: {
              required: true,
            },
          }
        ],
    }
  },
  computed:{
    model(){
      return this.dataEstructure
    }
  },
  methods:{
    customAction(values){
      this.dataEstructure = values
      this.showForm= true
    },
    async submitForm(values){
      try {
        await this.actions.update(values.id, values)
        this.$refs.datatable_failed.refreshData()
        toastr.success("successful Modification")
      } catch (error) {
        this.$refs.datatable_failed.refreshData()
        toastr.error("unsuccessful Modification")
      }
    }
  }
}
</script>

<style lang="scss" scoped>
</style>