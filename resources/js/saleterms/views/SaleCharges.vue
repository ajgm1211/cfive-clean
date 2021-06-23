<template>
  <div>
    <b-card>
      <div class="row">
        <div class="col-6">
          <b-card-title></b-card-title>
        </div>
        <div class="col-6">
          <div class="float-right">
            <!--<button class="btn btn-link" v-b-modal.addSurcharge>+ Add Surcharge</button>-->
            <button class="btn btn-primary btn-bg btn-adds" v-b-modal.addCharge>
              + Add Charge
            </button>
          </div>
        </div>
      </div>

      <DynamicalDataTable
        v-if="loaded"
        :initialFields="fields"
        :initialFormFields="input_fields"
        :datalists="fdatalists"
        :equipment="equipment"
        :actions="actions"
        @onEditSuccess="onEdit"
        @onFormFieldUpdated="formFieldUpdated"
        :firstEmpty="false"
        :classTable="classTable"
      ></DynamicalDataTable>

      
    </b-card>

    <!-- Edit Form -->
    <b-modal
      id="editCharge"
      size="lg"
      cancel-title="Cancel"
      ok-title="Add Charge"
      hide-header-close
      title="Update Charge"
      hide-footer
    >
      <FormView
        :data="currentData"
        :fields="input_fields"
        :vdatalists="fdatalists"
        btnTxt="Update Charge"
        @exit="closeModal('editCharge')"
        @success="closeModal('editCharge')"
        :actions="actions"
        :update="true"
      ></FormView>
    </b-modal>
    <!-- End Edit Form -->

    <!-- Create Form -->
    <b-modal
      id="addCharge"
      size="lg"
      hide-header-close
      title="Add Charge"
      hide-footer
    >
      <FormView
        :fields="input_fields"
        :vdatalists="fdatalists"
        btnTxt="Add Charge"
        @exit="closeModal('addCharge')"
        @success="closeModal('addCharge')"
        :actions="actions"
      ></FormView>
    </b-modal>
    <!-- End Create Form -->

  </div>
</template>


<script>
import DataTable from "../../components/DataTable";
import FormView from "../../components/views/FormView";
import DynamicalDataTable from "../../components/views/DynamicalDataTable";

export default {
  components: {
    DynamicalDataTable,
    DataTable,
    FormView,
  },
  props: {
    datalists: Object,
    equipment: Object,
    actions: Object,
    classTable: String,
  },
  data() {
    return {
      loaded: false,
      isBusy: true, // Loader
      data: null,
      currentData: {},
      containers_fields: {},
      

      /* Table headers */
      fields: [
        {
          key: "sale_term_code",
          label: "Sale code",
          formatter: (value) => {
            return value.name;
          },
        },
        {
          key: "calculation_type",
          label: "Calculation Type",
          formatter: (value) => {
            return value.name;
          },
        },

        {
          key: "currency",
          label: "Currency",
          formatter: (value) => {
            return value.alphacode;
          },
        },
      ],

      /* Table input inline fields */
      input_fields: {
        check: { label: "", type: "checkbox" },
        sale_term_code: {
          label: "Sale code",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "name",
          placeholder: "Select option",
          options: "sale_term_codes",
          colClass: "col-lg-12",
        },
        calculation_type: {
          label: "Calculation type",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "name",
          placeholder: "Select option",
          options: "calculation_types",
          colClass: "col-lg-6",
        },

        currency: {
          label: "Currency",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "alphacode",
          placeholder: "Select option",
          options: "currencies",
          colClass: "col-lg-6",
        },
      },
    };
  },
  created() {
    let id = this.$route.params.id;
    this.fdatalists = JSON.parse(JSON.stringify(this.datalists));

    /* Return the lists data for dropdowns */
    api.getData({}, "/api/v2/sale_terms/data", (err, data) => {
      this.fdatalists = { ...this.fdatalists, ...data.data };
      this.loaded = true;
    });

    
  },
  methods: {
    /* Single Actions */
    onEdit(data) {
      this.currentData = data;
      this.$bvModal.show("editCharge");
    },

    /* Single Actions */
    formFieldUpdated(containers_fields) {
      this.containers_fields = containers_fields;
      this.form_fields = { ...this.input_fields, ...containers_fields };
      this.input_fields = { ...this.input_fields, ...containers_fields };
    },

    /* Dispatched event */
    closeModal(modal) {
      this.$bvModal.hide(modal);

      let component = this;

      component.loaded = false;
      setTimeout(function () {
        component.loaded = true;
      }, 100);
    },

    badges(value, color = "primary") {
      let carriers = "";

      if (value) {
        if (Array.isArray(value)) {
          value.forEach(function (val) {
            carriers += `<span class='badge badge-${color}'>${val.display_name}</span>`;
          });
        } else {
          let fields_keys = Object.keys(value);

          fields_keys.forEach(function (key) {
            const item = value[key];
            carriers += `<span class='badge badge-${color}'>${item.display_name}</span>`;
          });
        }

        return carriers;
      } else {
        return "-";
      }
    },
    badgescarriers(value, color = "primary") {
      let carriers = "";

      if (value) {
        value.forEach(function (val) {
          carriers += `<span class='badge badge-${color}'>${val.name}</span>`;
        });

        return carriers;
      } else {
        return "-";
      }
    },
  },
};
</script>
