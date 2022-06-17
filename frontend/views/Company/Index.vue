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
          :moduleTitle="'Companies'"
          :title="'To WhiteLabel'"
          :action="'Add'"
          :selected="selectedForModal"
          @cancel="modalWhiteLabel = false"
          @transferToWhiteLabel="transferToWhiteLabel"
        >
          <template v-slot:entity_whitelabel="slotProps">
            <p v-if="slotProps.entity.whitelabel == 1" class="item-label color-sucess" title="This company is on Whitelabel" > On Whitelabel</p>
            <p v-else class="item-label color-warning" title="This company is not on Whitelabel" > Ready for send</p>
          </template>
            
          <template v-slot:action_whitelabel>
            <div id="checkbox-create" class="main-btn" @click="toogleAddContact()" >
              {{textAddContact}}
            </div>
          </template>
        </ToWhiteLabelModal>
        <ExportModal
          v-if="exportEntityModal"
          :title="'Companies'"
          :action="'Export'"
          :exportLink="'/companies/v2/export-companies'"
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
      textAddContact:"Add Contacts To WhiteLabel",
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
      addContact:false
    }
  },
  computed:{
    isMassive: function () {
        return this.isMassiveCreation 
    },
    toggleToWhiteLabel: function (){
      return this.AddToWhiteLabel
    },
    selectedForModal(){
      return this.selectForTransfer.map(item =>{
        return {
          id :item.id,
          name: item.business_name,
          whitelabel: item.whitelabel
        }
      })
    },
    selectedForWhitelabel(){
      return this.selectForTransfer.map(item =>{
        return item.id
      })
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
    toogleAddContact(){
      this.addContact= !this.addContact
      if (this.addContact == true) {
        this.textAddContact = "Selected for add to WhiteLabel"  
      }else{
        this.textAddContact = "Add Contacts To WhiteLabel"
      }
      
    },
    addToWhiteLabelModal(){
      this.modalWhiteLabel = true
    },
    async transferToWhiteLabel(){
      await this.actions.transfer(this.selectedForWhitelabel,this.addContact)
      await toastr.success("successful data transfer")
      window.location.reload();
      },
    selectedData(selected){
      this.selectForTransfer = selected
    },
    exportEntityModalShow(){
      this.exportEntityModal = true
    },
    defaultEvent(){
      console.log('click')
    }
  },
  created(){
    if (this.user.settings_whitelabel == null) {
      this.items.find(item => {
        if (item.ref === 'tranferToWhiteLabel') {
          this.items.splice(this.items.indexOf(item),1);
          return
        }
      })
    }
  }
}
</script>