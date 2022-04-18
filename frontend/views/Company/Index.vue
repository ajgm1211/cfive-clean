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
          <DropdownButton 
            :items="items"
            :btnText="'Import'"
            :whitelabel="user.whitelabel"
            :toggleAddToWhiteLabel="toggleToWhiteLabel"
            @toggleButtonWhiteLabel="toggleButtonWhiteLabel"
          />
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
          :toggleAddToWhiteLabel="toggleToWhiteLabel"
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
          @transferToWhiteLabel="transferToWhiteLabel"
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

import { mapState } from 'vuex'
import DataTable from '../../components/common/DataTable'
import actions from '../../store/modules/company/actions'
import MainButton from "../../components/common/MainButton"
import CreateModal from '../../components/common/Modals/CreateModal'
import ExportModal from '../../components/common//Modals/ExportModal'
import DropdownButton from "../../components/common/DropdownButton"
import ToWhiteLabelModal from '../../components/common/Modals/ToWhiteLabelModal'

export default {
  components: {DataTable, MainButton, CreateModal, ToWhiteLabelModal, ExportModal, DropdownButton},
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
      ],
      items: [
        {
          link: "#",
          label: "upload companies",
          ref: "uploadCompanies",
          disabled: () => false,
          click: () => this.createMasive(true)
        },
        {
          link: "#",
          label: "Donwload File",
          ref: "donwloadFile",
          disabled: () => false,
          click: () => this.exportEntityModalShow()
        },
        {
          link: "/companies/v2/failed",
          label: "Failed company",
          ref: "failedCompanies",
          disabled: () => false,
          click: () => this.defaultEvent()
        },
        {
          link: "#",
          label: "Transfer to WL",
          ref: "tranferToWhiteLabel",
          disabled: () => this.toggleToWhiteLabel,
          click: () => this.addToWhiteLabelModal()
        },
        {
          link: "/companies/v2/template",
          label: "Download template",
          ref: "downloadTemplate",
          disabled: () => false,
          click: () => this.defaultEvent()
        }
      ],
    }
  },
  computed:{
    isMassive: function () {
        return this.isMassiveCreation 
    },
    toggleToWhiteLabel: function (){
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
    toggleButtonWhiteLabel(){
      this.AddToWhiteLabel = this.selectForTransfer.length > 0 ? false : true
    },
    addToWhiteLabelModal(){
      this.modalWhiteLabel = true
    },
    async transferToWhiteLabel(){
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