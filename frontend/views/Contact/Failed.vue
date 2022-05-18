
<template>
  <div class="col-12">
    <div class="back-btn" @click="$router.push('/contacts/v2');">
        <LeftArrow /> <span>back</span>
    </div>
    <div class="head">
        <div class="head-title">
            <h2>Failed Contacts</h2>
        </div>
        <div class="head-btns">
        </div>
    </div>
    <div>
      <DataTable
        :fields="fields"
        :actions="actions"
        :filter="true"
        :massiveSelect="false"
        :simpleSelect="false"
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

import toastr from "toastr"
import DataTable from '../../components/common/DataTable'
import LeftArrow from "../../components/icons/LeftArrow"
import actions from '../../store/modules/contact/actionFailed'
import ModalFrom from '../../components/common/Modals/ModalFrom'
export default {
  components: {DataTable, ModalFrom, LeftArrow},
  data() {
    return {
        dataEstructure:{},
        totalResults:true,
        classTable:"table table-striped table-responsive",
        actions:actions,
        fields: [
          { key: "id", label: "ID", filterIsOpen:false },
          { key: "first_name", label: "First Name", filterIsOpen: false },
          { key: "last_name", label: "Last Name", filterIsOpen: false },
          { key: "email", label: "Email", filterIsOpen: false },
          { key: "phone", label: "Phone", filterIsOpen: false },
          { key: "position", label: "Position", filterIsOpen: false },
          { key: "created_at", label: "Created at", filterIsOpen:false },
        ],
        showForm:false,
        modal_fields: [
          {
            type: "input",
            label: "First Name",
            name: "first_name",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Last Name",
            name: "last_name",
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
            label: "Phone",
            name: "phone",
            error:false,
            rules: {
              required: true,
            },
          },
          {
            type: "input",
            label: "Position",
            name: "position",
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