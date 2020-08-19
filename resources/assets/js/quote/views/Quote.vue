<template>
    <div class="quote-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" style="padding: 0px 50px">
                    <a href="#" class="p-light quote-link">
                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back
                    </a>

                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mt-3">
                            <b>FCL Quote</b>
                        </h4>
                        <div>
                            <a href="#" class="btn btn-primary btn-bg">+ PDF</a>
                        </div>
                    </div>
                </div>

                <!-- Tabs Section -->
                <b-card no-body class="card-tabs" style="width: 100%">
                    <b-tabs card class="quote-content-tab">
                        <b-tab title="Quote Info" active>
                            <div style="padding: 0px 25px">
                                <!-- Quote inputs -->
                                <b-card class="q-card">
                                    <div class="row">
                                        <div class="col-12">
                                            <FormInlineView
                                                v-if="loaded"
                                                :data="currentData"
                                                :fields="form_fields"
                                                :datalists="datalists"
                                                :actions="actions.quotes"
                                                :update="true"
                                            ></FormInlineView>
                                        </div>
                                    </div>
                                </b-card>
                                <!-- End Quote inputs -->

                                <!-- Terms and Condition -->
                                <b-card class="mt-5">
                                    <h5 class="q-title">Terms and Conditions</h5>

                                    <textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea>
                                </b-card>
                                <!-- End Terms and Condition -->
                            </div>
                        </b-tab>

                        <b-tab title="Ocean Freight">
                            <Ocean></Ocean>
                        </b-tab>

                        <b-tab title="Local Charges">
                            <Local></Local>
                        </b-tab>

                        <b-tab title="Inland">
                            <Inland></Inland>
                        </b-tab>

                        <b-tab title="Totals">Totales</b-tab>
                    </b-tabs>
                </b-card>
                <!-- End Tabs Section -->
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import "vue-multiselect/dist/vue-multiselect.min.css";
import actions from "../../actions";
import Quote from "./Quote";
import Inland from "./Inland";
import Ocean from "./Ocean";
import Local from "./Local";
import FormInlineView from "../../components/views/FormInlineView.vue";

export default {
    components: {
        Multiselect,
        DateRangePicker,
        Quote,
        Ocean,
        Inland,
        Local,
        FormInlineView,
    },
    data() {
        return {
            actions: actions,
            loaded: false,
            form_fields: {
                quote_id: {
                    label: "QUOTE ID",
                    type: "text",
                    rules: "required",
                    placeholder: "Quote ID",
                    colClass: "col-lg-3",
                },
                delivery_type: {
                    label: "SERVICE",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "display_name",
                    placeholder: "Select options",
                    options: "harbors",
                    colClass: "col-lg-3",
                },
                company_id: {
                    label: "CUSTOMER",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "business_name",
                    placeholder: "Select options",
                    options: "companies",
                    colClass: "col-lg-3",
                },
                commodity: {
                    label: "COMMODITY",
                    type: "text",
                    rules: "required",
                    placeholder: "Commodity",
                    colClass: "col-lg-3",
                },
                status_id: {
                    label: "STATUS",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "companies",
                    colClass: "col-lg-3",
                },
                type_id: {
                    label: "TYPE",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "companies",
                    colClass: "col-lg-3",
                },
                contact_id: {
                    label: "CONTACT",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "first_name",
                    placeholder: "Select options",
                    options: "contacts",
                    colClass: "col-lg-3",
                },
                kind_of_cargo: {
                    label: "KIND OF CARGO",
                    type: "text",
                    rules: "required",
                    placeholder: "Kind of cargo",
                    colClass: "col-lg-3",
                },
                issued: {
                    label: "DATE ISSUED",
                    type: "datepicker",
                    rules: "required",
                    colClass: "col-lg-3",
                },
                equipment: {
                    label: "EQUIPMENT",
                    type: "text",
                    rules: "required",
                    placeholder: "Equipment",
                    colClass: "col-lg-3",
                },
                owner: {
                    label: "OWNER",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "users",
                    colClass: "col-lg-3",
                },
                payment_conditions: {
                    label: "PAYMENT CONDITIONS",
                    type: "text",
                    rules: "required",
                    placeholder: "Payment conditions",
                    colClass: "col-lg-3",
                },
                validity: {
                    label: "VALIDITY",
                    type: "datepicker",
                    rules: "required",
                    colClass: "col-lg-3",
                },
                incoterm_id: {
                    label: "INCOTERM",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "incoterms",
                    colClass: "col-lg-3",
                },
                language_id: {
                    label: "LANGUAGE",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "incoterms",
                    colClass: "col-lg-3",
                },
            },
            currentData: {},
            vdata: {},
            datalists: {},
            companyLocked: true,
            service_options: [
                "Port to Port",
                "Port to Door",
                "Door to Port",
                "Door to Door",
            ],
            cargo_options: [
                "General",
                "Perishable",
                "Dangerous",
                "Valuable Cargo",
                "All Live Animals",
                "Human Remains",
                "Pharma",
            ],
            status_options: ["Lost", "Draft", "Won"],
            company_options: [],
            owners: [],
            incoterms: [],
            all_payments: [],
            sel_payments: [],
            all_contacts: [],
            sel_contacts: [],
            all_langs: [],
            sel_langs: [],
            options: [
                "Select option",
                "options",
                "selected",
                "mulitple",
                "label",
                "searchable",
                "clearOnSelect",
                "hideSelected",
                "maxHeight",
                "allowEmpty",
                "showLabels",
                "onChange",
                "touched",
            ],
        };
    },
    created() {
        let id = this.$route.params.id;

        api.getData({}, "/api/quote/data", (err, data) => {
            this.setDropdownLists(err, data.data);
            this.loaded = true;
        });

        actions.quotes
            .retrieve(id)
            .then((response) => {
                this.currentData = response.data.data;
            })
            .catch((data) => {
                this.$refs.observer.setErrors(data.data.errors);
            });
    },
    methods: {
        setCompanyDrops(value, id) {
            this.companyLocked = "true";
            this.contact = "";
            if (typeof this.all_contacts[value] == "string") {
                this.sel_contacts = [this.all_contacts[value]];
            } else {
                this.sel_contacts = this.all_contacts[value];
            }
            this.payment = "";
            if (typeof this.all_payments[value] == "string") {
                this.sel_payments = [this.all_payments[value]];
            } else {
                this.sel_payments = this.all_payments[value];
            }
            this.language = "";
            if (typeof this.all_langs[value] == "string") {
                this.sel_langs = [this.all_langs[value]];
            } else {
                this.sel_langs = this.all_langs[value];
            }
        },
        //Set dropdowns
        setDropdownLists(err, data) {
            this.datalists = data;
            console.log(this.datalists);
        },
    },
};
</script>
