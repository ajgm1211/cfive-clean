<template>
    <div class="col-12">
      <div class="head">
        <div class="head-title">
          <h2>Contacts</h2>
        </div>
      <div class="head-btns">
        <MainButton @click="createMasive(false)" text="Add Companies" :add="true" />
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
      ></DataTable>
    </div>
    <div>
      <CreateModal
        :create="isMassive"
        v-if="create"
        :title="'Contacts'"
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
        :title="'Contacts'"
        :action="'Export'"
        :exportLink="'contacts/v2/export-contacts'"
        @cancel="exportEntityModal = false"
      />
    </div>
  </div>
</template>

<script>

import { mapState } from "vuex"
import DataTable from "../../components/common/DataTable"
import actions from "../../store/modules/contact/actions"
import MainButton from "../../components/common/MainButton"
import CreateModal from "../../components/common/Modals/CreateModal"
import ExportModal from "../../components/common/Modals/ExportModal"
import DropdownButton from "../../components/common/DropdownButton"
import ToWhiteLabelModal from "../../components/common/Modals/ToWhiteLabelModal"

export default {
  components: { DataTable, MainButton, ToWhiteLabelModal, ExportModal, CreateModal, DropdownButton },
  data() {
    return {
      actions: actions,
      totalResults: true,
      fields: [
        { key: "id", label: "ID", filterIsOpen: true },
        { key: "first_name", label: "First Name", filterIsOpen: false },
        { key: "last_name", label: "Last Name", filterIsOpen: false },
        { key: "email", label: "Email", filterIsOpen: false },
        { key: "phone", label: "Phone", filterIsOpen: false },
        { key: "position", label: "Position", filterIsOpen: false },
        { key: "created_at", label: "Created at", filterIsOpen: false },
      ],
      classTable: "table table-striped table-responsive",
      create: false,
      modalWhiteLabel: false,
      selectForTransfer: [],
      exportEntityModal: false,
      isMassiveCreation: false,
      AddToWhiteLabel: true,
      modal_fields: [],
      items: [
        {
          link: "#",
          label: "upload contacts",
          ref: "uploadContacts",
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
          link: "/contacts/v2/failed",
          label: "Failed contacts",
          ref: "failedContacts",
          disabled: () => false,
          click: () => this.defaultEvent()
        },
        {
          link: "#",
          label: "Transfer to WL",
          ref: "tranferTWhiteLabel",
          disabled: () => this.toggleToWhiteLabel,
          click: () => this.addToWhiteLabelModal()
        },
        {
          link: "/contacts/v2/template",
          label: "Download template",
          ref: "downloadTemplate",
          disabled: () => false,
          click: () => this.defaultEvent()
        }
      ],
    }
  },
  computed: {
    isMassive: function() {
      return this.isMassiveCreation
    },
    toggleToWhiteLabel: function() {
      return this.AddToWhiteLabel
    },
    ...mapState("auth", ["user"]),
  },
  methods: {
    onEdit(data) {
      window.location = `/contacts/v2/${data.id}/edit`
    },
    createMasive(state) {
      this.create = true;
      this.isMassiveCreation = state
    },
    toggleButtonWhiteLabel(){
      this.AddToWhiteLabel = this.selectForTransfer.length > 0 ? false : true
    },
    addToWhiteLabelModal() {
      this.modalWhiteLabel = true
    },
    async transferToWhiteLabel() {
      await this.actions.transfer(this.selectForTransfer)
    },
    selectedData(selected) {
      this.selectForTransfer = selected
    },
    exportEntityModalShow() {
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

<style lang="scss" scoped></style>
