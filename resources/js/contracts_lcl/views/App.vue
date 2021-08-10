<template>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12">
        <b-card>
          <div class="row">
            <div class="col-6">
              <b-card-title>LCL Contracts</b-card-title>
            </div>
            <div class="col-6">
              <div class="float-right">
                <!--<button class="btn btn-link" v-b-modal.exportExcel>
                  <i class="fa fa-download"></i> Export Excel
                </button>-->
                <button class="btn btn-link" v-b-modal.addContract>
                  <i class="fa fa-plus"></i> Add Contract
                </button>
                <a
                  v-if="datalists['rol'] != 'subuser'"
                  href="/RequestsLcl/Requestimporlcl"
                  class="btn btn-primary btn-bg"
                  ><i class="fa fa-upload"></i> Import Contract</a
                >
              </div>
            </div>
          </div>

          <DataTable
            :fields="fields"
            :actions="actions.contracts_lcl"
            :singleActions="[
              'edit',
              'duplicate',
              'delete',
              //'seeProgressDetails',
            ]"
            :filter="true"
            @onEdit="onEdit"
            @onOpenModalProgressDetails="showProgressDetailsModal"
            :totalResults="totalResults"
          ></DataTable>
        </b-card>
      </div>
    </div>

    <!-- Create Form -->
    <b-modal
      id="addContract"
      size="md"
      hide-header-close
      title="Add Contract"
      hide-footer
    >
      <FormView
        :data="fdata"
        :fields="form_fields"
        :vdatalists="datalists"
        btnTxt="Add Contract"
        @exit="closeModal('addContract')"
        @success="success"
        :actions="actions.contracts_lcl"
      >
      </FormView>
    </b-modal>
    <!-- End Create Form -->

    <!-- Create Form -->
    <b-modal
      id="exportExcel"
      size="md"
      hide-header-close
      title="Export Contract"
      hide-footer
    >
      <FormView
        :data="fdata2"
        :fields="form_fields_excel"
        :vdatalists="datalists"
        :download="true"
        btnTxt="Export Excel"
        @exit="closeModal('exportExcel')"
        @success="closeModal('exportExcel', true)"
        :actions="actions.excel"
      >
      </FormView>

      <div class="row">
        <div class="col-lg-12">
          <div v-if="modalSuccess" class="alert alert-success" role="alert">
            Your file is being processed. It will be sent to your email address
          </div>
        </div>
      </div>
    </b-modal>
    <!-- End Create Form -->

    <!-- Create Form -->
    <b-modal
      id="seeProgressDetails"
      size="md"
      hide-header-close
      title="Processing progress"
      hide-footer
    >
      <div style="padding: 20px; margin-bottom: 25px">
        <b-progress :max="max" height="2rem">
          <b-progress-bar :value="value">
            <span
              ><b>{{ value }}%</b></span
            >
          </b-progress-bar>
        </b-progress>
      </div>
    </b-modal>
    <!-- End Create Form -->
  </div>
</template>


<script>
import DataTable from "../../components/DataTable";
import actions from "../../actions";
import FormView from "../../components/views/FormView.vue";

export default {
  components: {
    DataTable,
    FormView,
  },
  data() {
    return {
      totalResults: true,
      value: 45,
      max: 100,
      isBusy: true, // Loader
      modalSuccess: false,
      actions: actions,
      fdata: { validity: { startDate: null, endDate: null } },
      fdata2: { validity: { startDate: null, endDate: null } },

      // Dropdown Lists
      datalists: {
        carriers: [],
        equipments: [],
        directions: [],
        containers: [],
      },

      /* Table headers */
      fields: [
        {
          key: "name",
          label: "Reference",
          formatter: (value) => {
            return `<p class="truncate-contract" title="${value}">${value}</p>`;
          },
          filterIsOpen: true,
        },
        {
          key: "carriers",
          label: "Carrier",
          formatter: (value) => {
            return this.badgecarriers(value);
          },
          filterIsOpen: true,
          filterTrackBy: "name",
          trackLabel: "name",
        },
        {
          key: "status",
          label: "Status",
          formatter: (value) => {
            return `<span class="status-st ${value}"></span>`;
          },
          filterIsOpen: true,
        },
        { key: "validity", label: "Valid From", filterIsOpen: true },
        { key: "expire", label: "Valid Until", filterIsOpen: true },
        {
          key: "direction",
          label: "Direction",
          formatter: (value) => {
            return value.name;
          },
          filterIsOpen: true,
          filterTrackBy: "name",
          trackLabel: "name",
        },
        { key: "user_name", label: "Owner", filterIsOpen: true },
        { key: "created_at", label: "Created At", filterIsOpen: true },
      ],

      /* Form Modal Fields */
      form_fields: {
        name: {
          label: "Reference",
          type: "text",
          rules: "required",
          placeholder: "Reference",
          colClass: "col-sm-12",
        },
        validity: {
          label: "Validity",
          rules: "required",
          type: "daterange",
          sdName: "validity",
          edName: "expire",
        },
        carriers: {
          label: "Carriers",
          searchable: true,
          type: "multiselect",
          rules: "required",
          trackby: "name",
          placeholder: "Select options",
          options: "carriers",
        },
        direction: {
          label: "Direction",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "name",
          placeholder: "Select option",
          options: "directions",
        },
      },
      form_fields_excel: {
        validity: {
          label: "Validity",
          rules: "required",
          type: "daterange",
          sdName: "validity",
          edName: "expire",
        },

        direction: {
          label: "Direction",
          searchable: true,
          type: "select",
          rules: "required",
          trackby: "name",
          placeholder: "Select option",
          options: "directions",
        },
      },
    };
  },
  created() {
    /* Return the lists data for dropdowns */
    api.getData({}, "/api/v2/contractslcl/data", (err, data) => {
      this.setDropdownLists(err, data.data);
    });
  },
  methods: {
    /* Set the Dropdown lists to use in form */
    setDropdownLists(err, data) {
      this.datalists = data;
    },

    link() {
      window.location = "/RequestFcl/NewRqFcl";
    },

    showProgressDetailsModal(id) {
      actions.contracts_lcl
        .getRequestStatus(id)
        .then((response) => {
          this.value = response.data.progress;
          this.$bvModal.show("seeProgressDetails");
        })
        .catch((error) => {
          //
        });
    },

    closeModal(modal, exporting = false) {
      let component = this;

      if (exporting == false) {
        component.$bvModal.hide(modal);
      } else {
        component.modalSuccess = true;
        setTimeout(function () {
          component.modalSuccess = false;
          component.$bvModal.hide(modal);
        }, 3000);
      }
    },

    success(id) {
      window.location = `/api/contractslcl/${id}/edit`;
    },

    /* Single Actions */
    onEdit(data) {
      window.location = `/api/contractslcl/${data.id}/edit`;
    },

    badgecarriers(value) {
      let carriers = "";

      if (value) {
        value.forEach(function (val) {
          carriers +=
            "<span class='badge badge-primary'>" + val.name + "</span> ";
        });

        return carriers;
      } else {
        return "-";
      }
    },
  },
};
</script>
