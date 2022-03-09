<template>
    <div class="col-12">
        <DataTable
          :fields="fields"
          :actions="actions"
          :filter="true"
          :singleActions="['edit', 'duplicate', 'delete']"
          @onEdit="onEdit"
          :totalResults="totalResults"
          :classTable="classTable"
        ></DataTable>
    </div>
</template>

<script>  

import actions from '../../../store/modules/contact/actions'
import MainButton from "../../../components/common/MainButton"
import DataTable from '../../../components/common/DataTable'
//import toastr from "toastr"

export default {
  components: {DataTable, MainButton},
  data() {
    return {
      actions: actions,
      totalResults:true,
      fields: [
        { key: "id", label: "ID", filterIsOpen:true },
        { key: "first_name", label: "First Name", filterIsOpen:false },
        { key: "last_name", label: "Last Name", filterIsOpen:false },
        { key: "email", label: "Email", filterIsOpen:false },
        { key: "phone", label: "Phone", filterIsOpen:false },
        { key: "position", label: "Position", filterIsOpen:false },
        { key: "created_at", label: "Created at", filterIsOpen:false },
      ],
      classTable:"table table-striped table-responsive"
    }
  },
  async created(){
  },
  methods: {
    onEdit(data) {
      window.location = `/contacts/v2/${data.id}/edit`;
    }
  }
}
</script>

<style lang="scss" scoped>
</style>