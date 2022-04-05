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
          <b-dropdown id="dropdown-left" text="Import">
            <b-dropdown-item href="#" @click="createMasive(true)">Upload Companies</b-dropdown-item>
            <b-dropdown-item href="#" @click="exportCompaniesModalShow()">Donwload File</b-dropdown-item>
            <b-dropdown-item href="/companies/v2/failed">Failed compañias</b-dropdown-item>
            <b-dropdown-item v-if="user.whitelabel == 1" href="#" :disabled="toggleTWL" @click="AddToWhiteLevelModal()" ref="tranferTWL">Transfer to WL</b-dropdown-item>
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
          :toggleAddToWhiteLevel="toggleTWL"
          @toggleButtonWL="toggleButtonWhiteLevel"
          @selectedData="selectedData"
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
          :user="user"
        />
        <ToWLModal
          v-if="modalWhiteLabel"
          :title="'To WhiteLevel'"
          :action="'Add'"
          :selectedCompanies="selectForTransfer"
          @cancel="modalWhiteLabel = false"
          @transferTWL="transferTWL"
        />
        <ExportModal
          v-if="exportCompaniesModal"
          :title="'Companies'"
          :action="'Export'"
          :selectedCompanies="selectForTransfer"
          @cancel="exportCompaniesModal = false"
        />
        
        
    </div>
</template>

<script>

import actions from '../../store/modules/company/actions'
import MainButton from "../../components/common/MainButton"
import DataTable from '../../components/common/DataTable'
import CreateModal from './partials/CreateModal'
import ToWLModal from './partials/ToWhiteLevelModal'
import ExportModal from './partials/ExportModal'
import { mapState } from 'vuex'

export default {
  components: {DataTable, MainButton, CreateModal, ToWLModal, ExportModal},
  data() {
    return {
      actions: actions,
      totalResults:true,
      create: false,
      modalWhiteLabel: false,
      isMassiveCreation:false,
      AddToWhiteLevel:true,
      exportCompaniesModal:false,
      selectForTransfer:[],
      fields: [
        { key: "id", label: "ID", filterIsOpen:true },
        { key: "business_name", label: "Business Name", filterIsOpen:false },
        { key: "phone", label: "Phone", filterIsOpen:false },
        { key: "email", label: "Email", filterIsOpen:false },
        { key: "address", label: "Address", filterIsOpen:false },
        { key: "tax_number", label: "Tax Number", filterIsOpen:false },
        { key: "created_at", label: "Created at", filterIsOpen:false },
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
    },
    toggleTWL: function (){
      return this.AddToWhiteLevel
    },
    ...mapState('auth', ['user'])
  },
  methods: {
    onEdit(data) {
      window.location = `/companies/v2/${data.id}/edit`
    },
    createMasive(state){
      this.create = true
      this.isMassiveCreation = state
    },
    toggleButtonWhiteLevel(status){
      this.AddToWhiteLevel = !status
    },
    AddToWhiteLevelModal(){
      this.modalWhiteLabel = true
    },
    async transferTWL(){
      await this.actions.transferCompanies(this.selectForTransfer)
    },
    selectedData(selectedCompanies){
      this.selectForTransfer = selectedCompanies
    },
    exportCompaniesModalShow(){
      this.exportCompaniesModal = true
    }
  },
  created(){
  }
}
</script>