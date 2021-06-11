<template>
    <div>
        <b-card class="no-scroll">
            <div class="row">
                <div class="col-6">
                    <b-card-title>Ocean Freight</b-card-title>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <button
                            class="btn btn-primary btn-bg"
                            v-on:click="link"
                        >
                            + Import Contract
                        </button>
                    </div>
                </div>
            </div>

            <DynamicalDataTable
                v-if="loaded"
                :initialFields="fields"
                :initialFormFields="vform_fields"
                :datalists="datalists"
                :equipment="equipment"
                :actions="actions"
                :massiveactions="['openmodalcontainer','openmodalharbororigin','openmodalharbordestination', 'delete']"
                @onEditSuccess="onEdit"
                @onFormFieldUpdated="formFieldUpdated"
                @onOpenModalContainer="openModalContainer"
                @onOpenModalHarborOrig="openModalHarborOrig"
                @onOpenModalHarborDest="openModalHarborDest"
                :view="'oceanfreight'"
                :classTable="classTable"
            ></DynamicalDataTable>
            
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
                :fields="input_form_fields"
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

        <!-- Edit Form -->
        <b-modal
            id="editContainers"
            size="lg"
            cancel-title="Cancel"
            hide-header-close
            title="Edit Containers"
            hide-footer
        >
            <FormView
                :data="{}"
                :massivedata="ids_selected"
                :fields="containers_fields"
                :vdatalists="datalists"
                btnTxt="Update Containers"
                @exit="closeModal('editContainers')"
                @success="closeModal('editContainers')"
                :actions="actions"
                :update="true"
                :massivechange="true"
            >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->
    </div>
</template>

<script>
import DynamicalDataTable from "../views/DynamicalDataTable";
import FormView from "../views/FormView";

export default {
    components: {
        DynamicalDataTable,
        FormView,
    },
    props: {
        equipment: Object,
        datalists: Object,
        actions: Object,
        contractData: Object,
        classTable: String
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
                    options: "carriers"
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
            this.form_fields = { ...this.vform_fields, ...containers_fields };
            this.input_form_fields = { ...this.input_fields, ...containers_fields };
        },

        openModalContainer(ids) {
            console.log("test modal");
            this.ids_selected = ids;
            this.$bvModal.show("editContainers");
        },

      openModalHarborOrig(ids) {
            
            this.ids_selected = ids;
            this.$bvModal.show("editHarborsOrig");
        },
        openModalHarborDest(ids) {
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
            window.location = "/RequestFcl/NewRqFcl";
        },
    },
};
</script>