
<template>
  <div>
    <b-card>
      <div class="row">
        <div class="col-6">
          <b-card-title>Per Location</b-card-title>
        </div>
        <div class="col-6">
          <div class="float-right">             
            <button class="btn btn-primary btn-bg" v-b-modal.addRange>Import File</button>
          </div> 
        </div>
      </div>
      <DynamicalDataTable
        v-if="loaded"
        :initialFields="fields"
        :initialFormFields="input_fields"
        :datalists="datalists"
        :equipment="equipment"
        :actions="actions"
        :groupContainer="True"
        :massiveactions="['changecontainersview', 'delete']"
        @onEditSuccess="onEdit"
        @onFormFieldUpdated="formFieldUpdated"
        :classTable="classTable"
      ></DynamicalDataTable>
    </b-card>
  </div>
</template>

<script>
import DynamicalDataTable from "../../components/views/DynamicalDataTable";
import FormView from "../../components/views/FormView.vue";

export default {
  components: {
    DynamicalDataTable,
    FormView,
  },
  props: {
    equipment: Object,
    datalists: Object,
    actions: Object,
    classTable: String,
  },
  data() {
    return {
      loaded: true,
      currentData: {},
      form_fields: {},
      containers_fields: {},

      /* Table headers */
      fields: [
        { key: "port", label: "Ports" },
        { key: "location", label: "Address" },
        { key: "Service", label: "Service" },
        { key: "currency", label: "Currency" },
      ],

      /* Table input inline fields */
      input_fields: {
        port: {
          label: "Port",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "display_name",
          placeholder: "Select option",
          options: "harbors",
        },
        location: {
          label: "Address",
          type: "select",
          rules: "required",
          trackby: "name",
          placeholder: "Select an Address",
          options: "location",
        },
        Service: {
          label: "Service",
          type: "text",
          // rules: "required",
          placeholder: "Select a Service",
        },
        currency: {
          label: "Currency",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "alphacode",
          placeholder: "Select option",
          options: "currencies",
        },
      },
    };
  },
  methods: {
    /* Single Actions */
    onEdit(data) {
      this.currentData = data;
      this.$bvModal.show("editRange");
    },

    /* Single Actions */
    formFieldUpdated(containers_fields) {
      this.containers_fields = containers_fields;
      this.form_fields = { ...this.input_fields, ...containers_fields };
      this.input_fields = { ...this.input_fields, ...containers_fields };
    },

    /* Close modal form by modal name */
    closeModal(modal) {
      this.$bvModal.hide(modal);

      let component = this;

      component.loaded = false;
      setTimeout(function () {
        component.loaded = true;
      }, 100);
    },

    link() {
      window.location = "/RequestFcl/NewRqFcl";
    },
  },
};
</script>
