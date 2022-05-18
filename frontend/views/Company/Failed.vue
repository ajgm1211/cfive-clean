
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
        :singleActions="[]"
        :totalResults="totalResults"
        :classTable="classTable"
      >
      </DataTable>
    </div>
  </div>
</template>

<script>
import LeftArrow from '../../components/icons/LeftArrow'
import DataTable from '../../components/common/DataTable'
import actions from '../../store/modules/company/actionFailed'


export default {
  components: {DataTable, LeftArrow},
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
        { key: "associated_quotes", label: "Associated Quotes", filterIsOpen:false },
        { key: "owner", label: "Owner", filterIsOpen:false },
        { key: "created_at", label: "Created at", filterIsOpen:false },
      ],
    }
  }
}
</script>

<style lang="scss" scoped>
</style>