<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-12">
        <b-card>
          <div class="row">
            <div class="col-6">
              <b-card-title>Inland FCL</b-card-title>
            </div>
            <div class="col-6">
              <div class="float-right">
                <button class="btn btn-primary btn-bg" v-b-modal.add-inland>+ Add Inland</button>
              </div>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-4">
              <b-form inline>
                <i class="fa fa-search" aria-hidden="true"></i>
                <b-input
                  id="inline-form-input-name"
                  type="search"
                  class="mb-2 mr-sm-2 mb-sm-0"
                  placeholder="Search"
                  v-model="search"
                ></b-input>
              </b-form>
            </div>
          </div>

          <!-- Table -->
          <b-form-checkbox-group>
            <b-form-checkbox
              class="select-all"
              v-model="allSelected"
              :indeterminate="indeterminate"
              @change="toggleAll"
            ></b-form-checkbox>
          </b-form-checkbox-group>
          <!--  <p>
{{ selected }}
          </p>-->

          <b-button id="popover-all" class="action-app all-action-app" href="#" tabindex="0">
            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
          </b-button>
          <b-popover
            target="popover-all"
            class="btns-action"
            variant
            triggers="focus"
            placement="bottomleft"
          >
            <button class="btn-action">Edit</button>
            <button class="btn-action">Duplicate</button>
            <button class="btn-action">Delete</button>
          </b-popover>

          <b-table
            borderless
            hover
            ref="selectableTable"
            :fields="fields"
            :items="data"
            :busy="isBusy"
          >
            <!--<div slot="table-busy" class="text-center text-primary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Loading...</strong>
            </div>-->
            <template v-slot:cell(checkbox)="data">
              <b-form-checkbox-group>
                <b-form-checkbox
                  v-bind:value="data.item"
                  v-bind:id="'check'+data.item.id"
                  v-model="selected"
                ></b-form-checkbox>
              </b-form-checkbox-group>
            </template>

            <template v-slot:cell(status)="data">
              <span v-html="data.value"></span>
            </template>

            <template v-slot:cell(direction)="data">
              <span v-html="data.value"></span>
            </template>

            <template v-slot:cell(actions)="data">
              <b-button v-bind:id="'popover'+data.item.id" class="action-app" href="#" tabindex="0">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
              </b-button>
              <b-popover
                v-bind:target="'popover'+data.item.id"
                class="btns-action"
                variant
                triggers="focus"
                placement="bottomleft"
              >
                <button class="btn-action">Edit</button>
                <button class="btn-action">Duplicate</button>
                <button class="btn-action">Delete</button>
              </b-popover>
            </template>
          </b-table>
          <!-- Table end -->
          <!-- Pagination -->
          <paginate
            :page-count="pageCount"
            :click-handler="clickCallback"
            :prev-text="'Prev'"
            :next-text="'Next'"
            :page-class="'page-item'"
            :page-link-class="'page-link'"
            :container-class="'pagination justify-content-end'"
            :prev-class="'page-item'"
            :prev-link-class="'page-link'"
            :next-class="'page-item'"
            :next-link-class="'page-link'"
            :initialPage="initialPage"
          ></paginate>
          <!-- Pagination end -->
        </b-card>

        <b-modal
          ref="addFCL"
          id="add-inland"
          cancel-title="Cancel"
          ok-title="Add Contract"
          hide-header-close
          title="Add Inlands"
          hide-footer
        >
          <b-form ref="form" @submit.stop.prevent="onSubmit" class="modal-input">
            <b-form-group
              id="Provider"
              label="Provider"
              label-for="provider"
              invalid-feedback="Provider is required."
              valid-feedback="Provider is done!"
            >
              <b-form-input v-model="provider" placeholder="Provider"></b-form-input>
            </b-form-group>

            <div class="row">
              <div class="col-12 col-sm-6">
                <b-form-group
                  id="status"
                  label="status"
                  label-for="status"
                  invalid-feedback="Status is required."
                  valid-feedback="Status is done!"
                >
                  <multiselect
                    v-model="status"
                    :options="options"
                    :searchable="false"
                    :close-on-select="true"
                    track-by="id"
                    label="name"
                    :show-labels="false"
                    placeholder="Select Type"
                  ></multiselect>

                </b-form-group>
              </div>
              <div class="col-12 col-sm-6">
                <b-form-group
                  id="validity"
                  label="Validity"
                  label-for="validity"
                  invalid-feedback="Validity is required."
                  valid-feedback="Reference is done!"
                >
                  <date-range-picker
                    ref="picker"
                    :opens="'center'"
                    :locale-data="{ firstDay: 1, format: 'MMM DD, YYYY' }"
                    :singleDatePicker="false"
                    :autoApply="true"
                    :timePicker="false"
                    v-model="selectedDates"
                    :linkedCalendars="true"
                  ></date-range-picker>
                </b-form-group>
              </div>

              <div class="col-12 col-sm-6">
                <b-form-group
                  id="equipment"
                  label="Equipment"
                  label-for="equipment"
                  invalid-feedback="Equipment is required."
                  valid-feedback="Reference is done!"
                >
                  <multiselect
                    v-model="equipment"
                    :options="equipments"
                    :searchable="false"
                    :close-on-select="true"
                    track-by="id"
                    label="name"
                    :show-labels="false"
                    placeholder="Select Equipment"
                  ></multiselect>
                </b-form-group>
              </div>
              <div class="col-12 col-sm-6">
                <b-form-group
                  id="direction"
                  label="Direction"
                  label-for="direction"
                  invalid-feedback="Direction is required."
                  valid-feedback="Reference is done!"
                >
                  <multiselect
                    v-model="direction"
                    :options="directions"
                    :searchable="false"
                    :close-on-select="true"
                    track-by="id"
                    label="name"
                    :show-labels="false"
                    placeholder="Select Direction"
                  ></multiselect>
                </b-form-group>
              </div>
            </div>

            <div class="btns-form-modal">
              <button class="btn" @click="modalClose" type="button">Cancel</button>
              <button class="btn btn-primary btn-bg">Add Inland</button>
            </div>
          </b-form>
        </b-modal>
      </div>
    </div>
  </div>
</template>
<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import paginate from "../paginate";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
export default {
  props: {
    label: String,
    value: Array | String | Object,
    name: String,
    startDate: String,
    endDate: String,
    rules: String,
    placeholder: {
      type: String,
      required: false,
      default: "Select Dates Range"
    }
  },
  components: {
    DateRangePicker,
    Multiselect,
    paginate
  },
  data() {
    return {
      prueba: true,
      isBusy: true, // Loader
      data: null,
      nameState: true,
      search: null,
      fields: [
        {
          key: "checkbox",
          label: "",
          tdClass: "checkbox-add-fcl",
          isHtml: true
        },
        { key: "provider", label: "Provider", sortable: false },
        {
          key: "status",
          label: "Status",
          sortable: false,
          isHtml: true,
          formatter: value => {
            return '<span class="status-st ' + value + '"></span>';
          }
        },

        {
          key: "gp_container",
          label: "Equipment",
          sortable: false,
          formatter: value => {
            return value.name;
          }
        },
        { key: "validity", label: "Valid From", sortable: false },
        { key: "expire", label: "Valid Until", sortable: false },
        {
          key: "direction",
          label: "Direction",
          formatter: value => {
            return this.badgetypes(value);
          }
        },
        { key: "actions", label: "", tdClass: "actions-add-inland" }
      ],
      // Models Data
      options: [
        { name: "Publish", value: "publish" },
        { name: "Expired", value: "expired" }
      ],
      reference: "",
      types: [],
      status: [],
      equipment: "",
      direction: "",
      status: "",
      type:"" ,
      selectMode: "multi",
      selected: [],
      allSelected: false,
      indeterminate: false,
      selectedDates: {},
      //List Data
      
      directions: [],
      equipments: [],
      //Pagination
      pageCount: 0,
      initialPage: 1
    };
  },
  created() {
    /* Return the Contracts lists data*/
    api.getData({}, "/api/v2/inlands", (err, data) => {
      this.setData(err, data);
    });

    /* Return the lists data for dropdowns */
    api.getData({}, "/api/v2/inlands/data", (err, data) => {
      this.setDropdownLists(err, data.data);
    });

    this.$emit("input", { startDate: null, endDate: null });
    this.setDates();
  },
  methods: {
    // invalid empty return form
    invalidFeedback(data) {
      if (data.type == "") {
        $("#type .invalid-feedback").css({ display: "block" });
      }
      if (data.provider == "") {
        $("#reference .invalid-feedback").css({ display: "block" });
      }
      if (data.gp_container == null) {
        $("#equipment .invalid-feedback").css({ display: "block" });
      }
      if (data.direction == null) {
        $("#direction .invalid-feedback").css({ display: "block" });
      }
    },
    toggleAll(checked) {
      this.selected = checked ? this.data.slice() : []; //Selected all the checkbox
    },
    modalClose() {
      this.$bvModal.hide("add-inland"); //Close modal
    },
    setDates() {
      if (this.startDate && this.endDate) {
        this.selectedDates = {
          startDate: moment(this.startDate, "YYYY-MM-DD").format(
            "MMM DD, YYYY"
          ),
          endDate: moment(this.endDate, "YYYY-MM-DD").format("MMM DD, YYYY")
        };
      }
    },
    /* Set the Dropdown lists to use in form */
    setDropdownLists(err, data) {
      //this.types = data.types;
      this.equipments = data.equipments;
      this.directions = data.directions;
    },
    /* Set the data response in table */

    setData(err, { data: records, links, meta }) {
      this.isBusy = false;
      if (err) {
        this.error = err.toString();
      } else {
        this.data = records;
        this.pageCount = Math.ceil(meta.total / meta.per_page);
      }
    },
    /* Prepare the data to create a new Contract */
    prepareData() {
      let types = [];
      
      return {
        provider: this.provider,
        direction: this.direction.id,
        validity: "2020-04-01",
        expire: "2020-05-20", //this.dateRange.endDate,
        status:this.status.value,
        gp_container: this.equipment.id,
       // type: type
      };
    },
    /* Handle the submit of Create Form and 
              send the data to store a new contract */
    onSubmit() {
      const data = this.prepareData();
      this.invalidFeedback(data);
      api
        .call("post", "/api/v2/inlands/store", data)
        .then(response => {
         //console.log(response);
          window.location =
            "http://cargofive/api/inlands/";
        })
        .catch(data => {
          this.$refs.observer.setErrors(data.data.errors);
        });
    },
    /* Pagination Callback */
    clickCallback(pageNum) {
      this.isBusy = true;
      let qs = {
        page: pageNum
      };
      if (this.$route.query.sort) qs.sort = this.$route.query.sort;
      if (this.$route.query.q) qs.q = this.$route.query.q;
      this.routerPush(qs);
    },
    /* Update url and execute api call */
    routerPush(qs) {
      this.$router.push({ query: qs });
      api.getData(qs, "/api/v2/inlands", (err, data) => {
        this.setData(err, data);
      });
    },
    badgetypes(value) {
      let variation = "";
      if (value == 1) {
        variation += "<span class='badge badge-primary'>Export</span> ";
        return variation;
      } else if (value == 2) {
        variation += "<span class='badge badge-primary'>Import</span> ";
        return variation;
      } else {
        variation += "<span class='badge badge-primary'>Both</span> ";
        return variation;
      }
    }
  },
  watch: {
    selected() {
      this.$emit("input", this.selected);
    },
    selectedDates: {
      handler: function(val, oldVal) {
        this.$emit("input", val);
        this.model = "example";
      },
      deep: true
    },
    search: {
      handler: function(val, oldVal) {
        let qs = { q: val };
        this.routerPush(qs);
      }
    }
  }
};
</script>