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
            <b-dropdown-item href="#" @click="exportEntityModalShow()">Donwload File</b-dropdown-item>
            <b-dropdown-item href="/companies/v2/failed">Failed compañias</b-dropdown-item>
            <b-dropdown-item v-if="user.whitelabel == 1" href="#" :disabled="toggleTWhiteLabel" @click="AddToWhiteLabelModal()" ref="tranferTWhiteLabel">Transfer to WhiteLabel</b-dropdown-item>
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
          :toggleAddToWhiteLabel="toggleTWhiteLabel"
          @toggleButtonWhiteLabel="toggleButtonWhiteLabel"
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
        <ToWhiteLabelModal
          v-if="modalWhiteLabel"
          :title="'To WhiteLabel'"
          :action="'Add'"
          :selected="selectForTransfer"
          @cancel="modalWhiteLabel = false"
          @transferTWhiteLabel="transferTWhiteLabel"
        />
        <ExportModal
          v-if="exportEntityModal"
          :title="'Companies'"
          :action="'Export'"
          :exportLink="'companies/v2/export-companies'"
          @cancel="exportEntityModal = false"
        />
        
        
    </div>
</template>

<script>

import actions from '../../store/modules/company/actions'
import MainButton from "../../components/common/MainButton"
import DataTable from '../../components/common/DataTable'
import CreateModal from '../../components/common/Modals/CreateModal'
import ToWhiteLabelModal from '../../components/common/Modals/ToWhiteLabelModal'
import ExportModal from '../../components/common//Modals/ExportModal'
import { mapState } from 'vuex'

export default {
  components: {DataTable, MainButton, CreateModal, ToWhiteLabelModal, ExportModal},
  data() {
    return {
      actions: actions,
      totalResults:true,
      create: false,
      modalWhiteLabel: false,
      isMassiveCreation:false,
      AddToWhiteLabel:true,
      exportEntityModal:false,
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
    toggleTWhiteLabel: function (){
      return this.AddToWhiteLabel
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
    toggleButtonWhiteLabel(status){
      this.AddToWhiteLabel = !status
    },
    AddToWhiteLabelModal(){
      this.modalWhiteLabel = true
    },
    async transferTWhiteLabel(){
      await this.actions.transfer(this.selectForTransfer)
    },
    selectedData(selected){
      this.selectForTransfer = selected
    },
    exportEntityModalShow(){
      this.exportEntityModal = true
    }
  }
}
</script>