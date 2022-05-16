
<template>
  <div class="col-12">
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
            :singleActions="[]"
            :totalResults="totalResults"
            :classTable="classTable"
          >
          </DataTable>
        </div>
  </div>
</template>

<script>

import DataTable from '../../components/common/DataTable'
import actions from '../../store/modules/contact/actionFailed'

export default {
  components: {DataTable},
  data() {
    return {
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
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
