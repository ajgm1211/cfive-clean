<template>
    <div class="col-12">
      <div class="head">
        <div class="head-title">
          <h2>Contacts</h2>
        </div>
      <div class="head-btns">
        <MainButton @click="createMasive(false)" text="Add Companies" :add="true" />
        <DropdownHeadboard 
          :items="items" 
          :whitelabel="user.whitelabel"
          :toggleWl="toggleTWL"
          @toggleButtonWL="toggleAddToButtonWL"
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
        :toggleAddToWhiteLabel="toggleTWL"
        @toggleButtonWL="toggleButtonWhiteLabel"
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
      <ToWLModal
        v-if="modalWhiteLabel"
        :title="'To WhiteLevel'"
        :action="'Add'"
        :selected="selectForTransfer"
        @cancel="modalWhiteLabel = false"
        @transferTWL="transferTWL"
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
import actions from "../../store/modules/contact/actions";
import MainButton from "../../components/common/MainButton";
import DataTable from "../../components/common/DataTable";
import DropdownHeadboard from "../../components/common/DropdownHeadboard";
import CreateModal from "../../components/common/Modals/CreateModal";
import ToWLModal from "../../components/common/Modals/ToWhiteLevelModal";
import ExportModal from "../../components/common/Modals/ExportModal";
import { mapState } from "vuex";
//import toastr from "toastr"

export default {
  components: { DataTable, MainButton, ToWLModal, ExportModal, CreateModal, DropdownHeadboard },
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
          disabled: false,
          click: () => this.createMasive(true)
        },
        {
          link: "#",
          label: "Donwload File",
          ref: "donwloadFile",
          disabled: false,
          click: () => this.exportEntityModalShow()
        },
        {
          link: "/contacts/v2/failed",
          label: "Failed contacts",
          ref: "failedContacts",
          disabled: false,
          click: () => this.defaultEvent()
        },
        {
          link: "#",
          label: "Transfer to WL",
          ref: "tranferTWL",
          disabled: () => this.toggleTWL,
          click: () => this.AddToWhiteLabelModal()
        },
        {
          link: "/contacts/v2/template",
          label: "Download template",
          ref: "downloadTemplate",
          disabled: false,
          click: () => this.defaultEvent()
        }
      ],
    }
  },
  computed: {
    isMassive: function() {
      return this.isMassiveCreation
    },
    toggleTWL: function() {
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
    toggleButtonWhiteLabel(status) {
      this.AddToWhiteLabel = !status
    },
    AddToWhiteLabelModal() {
      this.modalWhiteLabel = true
    },
    async transferTWL() {
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
    },
    toggleAddToButtonWL(status){
      //console.log(this.$refs.tranferTWL)
      this.$refs.tranferTWL[0]._props.disabled = status
    }
  },
};
</script>

<style lang="scss" scoped></style>
