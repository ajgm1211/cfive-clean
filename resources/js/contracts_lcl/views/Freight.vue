<template>
  <div>
    <b-card class="no-scroll">
      <div class="row">
        <div class="col-6">
          <b-card-title>Ocean Freight</b-card-title>
        </div>
        <div class="col-6">
          <div class="float-right">
            <button class="btn btn-primary btn-bg" v-on:click="link">
              + Import Contract
            </button>
          </div>
        </div>
      </div>

      <DataTable
        v-if="loaded"
        :fields="fields"
        :inputFields="vform_fields"
        :vdatalists="datalists"
        :actions="actions"
        @onEdit="onEdit"
        :massiveactions="[
          'openmodalharbororigin',
          'openmodalharbordestination',
          'delete',
        ]"
        @onEditSuccess="onEdit"
        @onFormFieldUpdated="formFieldUpdated"
        @onOpenModalHarborOrigView="openModalHarborOrigView"
        @onOpenModalHarborDestView="openModalHarborDestView"
        :view="'oceanfreight'"
        :classTable="classTable"
      ></DataTable>
    </b-card>

    <!-- Edit Form -->
    <b-modal
      id="editHarborsOrig"
      size="lg"
      cancel-title="Cancel"
      hide-header-close
      title="Edit Harbors"
      hide-footer
    >
      <FormView
        :data="origin_fields"
        :massivedata="ids_selected"
        :fields="origin_fields"
        :vdatalists="datalists"
        btnTxt="Update Harbors"
        @exit="closeModal('editHarborsOrig')"
        @success="closeModal('editHarborsOrig')"
        :actions="actions"
        :update="true"
        :massivechangeHarborOrig="true"
      >
      </FormView>
    </b-modal>

    <b-modal
      id="editHarborsDest"
      size="lg"
      cancel-title="Cancel"
      hide-header-close
      title="Edit Harbors"
      hide-footer
    >
      <FormView
        :data="{}"
        :massivedata="ids_selected"
        :fields="destination_fields"
        :vdatalists="datalists"
        btnTxt="Update Harbors"
        @exit="closeModal('editHarborsDest')"
        @success="closeModal('editHarborsDest')"
        :actions="actions"
        :update="true"
        :massivechangeHarborDest="true"
      >
      </FormView>
    </b-modal>

    <b-modal
      id="editOFreight"
      size="lg"
      cancel-title="Cancel"
      hide-header-close
      title="Add Ocean Freight"
      hide-footer
    >
      <FormView
        :data="currentData"
        :fields="input_fields"
        :vdatalists="datalists"
        btnTxt="Update Ocean Freight"
        @exit="closeModal('editOFreight')"
        @success="closeModal('editOFreight')"
        :actions="actions"
        :update="true"
      >
      </FormView>
    </b-modal>
    <!-- End Edit Form -->

    <!-- Create Form -->
    <b-modal
      id="addOFreight"
      size="lg"
      hide-header-close
      title="Update Ocean Freight"
      hide-footer
    >
      <FormView
        :data="{}"
        :fields="form_fields"
        :vdatalists="datalists"
        btnTxt="Add Ocean Freight"
        @exit="closeModal('addOFreight')"
        @success="closeModal('addOFreight')"
        :actions="actions"
      >
      </FormView>
    </b-modal>
    <!-- End Create Form -->
  </div>
</template>

<script>
import DataTable from "../../components/DataTable";
import FormView from "../../components/views/FormView";

export default {
  components: {
    DataTable,
    FormView,
  },
  props: {
    datalists: Object,
    actions: Object,
    contractData: Object,
    classTable: String,
  },
  data() {
    return {
      loaded: true,
      currentData: {},
      form_fields: {},
      input_form_fields: {},
      containers_fields: {},
      origin_fields: {},
      destination_fields: {},
      ids_selected: [],

      /* Table headers */
      fields: [
        {
          key: "origin",
          label: "Origin Port",
          formatter: (value) => {
            return value.display_name;
          },
        },
        {
          key: "destination",
          label: "Destination Port",
          formatter: (value) => {
            return value.display_name;
          },
        },
        {
          key: "carrier",
          label: "Carrier",
          formatter: (value) => {
            return value.name;
          },
        },
        { key: "uom", label: "W/M" },
        { key: "minimum", label: "Minimum" },
        {
          key: "currency",
          label: "Currency",
          formatter: (value) => {
            return value.alphacode;
          },
        },
      ],

      origin_fields: {
        origin: {
          label: "Origin Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "display_name",
          placeholder: "Select Origin Port",
          options: "harbors",
        },
      },

      destination_fields: {
        destination: {
          label: "Destination Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "display_name",
          placeholder: "Select Destination Port",
          options: "harbors",
        },
      },

      input_fields: {
        origin: {
          label: "Origin Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "display_name",
          placeholder: "Origin Port",
          options: "harbors",
        },
        destination: {
          label: "Destination Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "display_name",
          placeholder: "Destination Port",
          options: "harbors",
        },
        carrier: {
          label: "Carrier",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "name",
          options: "carriers",
        },
        uom: {
          label: "W/M",
          type: "text",
          rules: "required",
          placeholder: "W/M",
        },
        minimum: {
          label: "Reference",
          type: "text",
          rules: "required",
          placeholder: "Minimum",
        },
        currency: {
          label: "Origin Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "alphacode",
          placeholder: "Currency",
          options: "currencies",
        },
      },
      /* Table input inline fields */
      vform_fields: {
        origin: {
          label: "Origin Port",
          searchable: true,
          type: "multiselect",
          rules: "required",
          trackby: "display_name",
          placeholder: "Origin Port",
          options: "harbors",
        },
        destination: {
          label: "Destination Port",
          searchable: true,
          type: "multiselect",
          rules: "required",
          trackby: "display_name",
          placeholder: "Destination Port",
          options: "harbors",
        },
        carrier: {
          label: "Carrier",
          searchable: true,
          type: "multiselect_data",
          rules: "required",
          trackby: "name",
          placeholder: "Carrier Port",
          options: "carriers",
          values: this.contractData.carriers,
        },
        uom: {
          label: "W/M",
          type: "text",
          rules: "required",
          placeholder: "W/M",
        },
        minimum: {
          label: "Minimum",
          type: "text",
          rules: "required",
          placeholder: "Minimum",
        },
        currency: {
          label: "Origin Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "alphacode",
          placeholder: "Currency",
          options: "currencies",
        },
      },
    };
  },
  methods: {
    /* Single Actions */
    onEdit(data) {
      let component = this;

      component.currentData = data;

      this.$bvModal.show("editOFreight");
    },

    /* Single Actions */
    formFieldUpdated(containers_fields) {
      this.containers_fields = containers_fields;
      this.form_fields = this.vform_fields;
      this.input_form_fields = this.input_fields;
    },

    openModalContainer(ids) {
      console.log("test modal");
      this.ids_selected = ids;
      this.$bvModal.show("editContainers");
    },

    openModalHarborOrigView(ids) {
      this.ids_selected = ids;
      this.$bvModal.show("editHarborsOrig");
    },
    openModalHarborDestView(ids) {
      console.log("test modal");
      this.ids_selected = ids;
      this.$bvModal.show("editHarborsDest");
    },

    /* Close modal form by modal name */
    closeModal(modal) {
      this.$bvModal.hide(modal);
      this.ids_selected = [];

      let component = this;

      component.loaded = false;
      setTimeout(function () {
        component.loaded = true;
      }, 100);
    },

    link() {
      window.location = "/RequestsLcl/Requestimporlcl";
    },
  },
};
</script>