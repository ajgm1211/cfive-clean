<template>
    <div class="col-12">
      <div class="head">
        <div class="head-title">
          <h2>Companies</h2>
        </div>
        <div class="head-btns">
          <MainButton
          @click="createMasive(false)"
          text="Add Companies"
          :add="true"
          />
          <b-dropdown id="dropdown-left" text="Importacion">
            <b-dropdown-item href="#" @click="createMasive(true)">Upload Companies</b-dropdown-item>
            <b-dropdown-item href="#">Donwload File</b-dropdown-item>
            <b-dropdown-item href="/companies/v2/failed">Failed compañias</b-dropdown-item>
            <b-dropdown-item href="#">Transfer to WL</b-dropdown-item>
            <b-dropdown-item href="/companies/v2/template">Download template</b-dropdown-item>
          </b-dropdown>
        </div>
      </div>
      <div>
        <DataTable
          :fields="fields"
          :actions="actions"
          :filter="true"
          :singleActions="['edit', 'duplicate', 'delete']"
          @onEdit="onEdit"
          :totalResults="totalResults"
          :classTable="classTable"
        >
        </DataTable>
      </div>
        <CreateModal
          :create="isMassive"
          v-if="create"
          :title="'Companies'"
          :action="'Add'"
          @cancel="create = false"
          :fields="modal_fields"
        />
        
        
    </div>
</template>

<script>

import actions from '../../store/modules/company/actions'
import MainButton from "../../components/common/MainButton"
import DataTable from '../../components/common/DataTable'
import CreateModal from './partials/CreateModal'

export default {
  components: {DataTable, MainButton, CreateModal},
  data() {
    return {
      actions: actions,
      totalResults:true,
      create: false,
      isMassiveCreation:false,
      fields: [
        { key: "id", label: "ID", filterIsOpen:true },
        { key: "business_name", label: "Business Name", filterIsOpen:false },
        { key: "phone", label: "Phone", filterIsOpen:false },
        { key: "email", label: "Email", filterIsOpen:false },
        { key: "address", label: "Address", filterIsOpen:false },
        { key: "tax_number", label: "Tax Number", filterIsOpen:false },
        { key: "created_at", formatter: (value) => { return value.date; }, label: "Created at", filterIsOpen:false },
      ],
      classTable:"table table-striped table-responsive",
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
      ]
    }
  },
  computed:{
    isMassive: function () {
        return this.isMassiveCreation 
    }
  },
  methods: {
    onEdit(data) {
      window.location = `/companies/v2/${data.id}/edit`
    },
    createMasive(state){
      this.create = true
      this.isMassiveCreation = state
    }
  }
}
</script>