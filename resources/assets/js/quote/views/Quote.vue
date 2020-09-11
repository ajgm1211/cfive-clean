<template>
    <div class="quote-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" style="padding: 0px 50px">
                    <a href="/api/quotes/" class="p-light quote-link">
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
                                    <FormInlineView
                                        v-if="loaded"
                                        :data="currentData"
                                        :fields="term_fields"
                                        :actions="actions.quotes"
                                        :update="true"
                                    ></FormInlineView>
                                    <!--textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea-->
                                </b-card>
                                <!-- End Terms and Condition -->
                            </div>
                        </b-tab>

                        <b-tab title="Ocean Freight" @click="changeView('freight')">
                            <ocean v-if="ocean"
                            :equipment="equip"
                            :currentQuoteData="currentData"
                            :quoteEquip="quoteEquip"
                            :datalists="datalists"
                            :freights="freights"
                            ></ocean>
                        </b-tab>

                        <b-tab title="Local Charges">
                            <Local
                                v-if="loaded"
                                :equipment="equip"
                                :quoteEquip="quoteEquip"
                                :datalists="datalists"
                            ></Local>
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
            ocean: false,
            tabs_loaded: false,
            form_fields: {
                quote_id: {
                    label: "QUOTE ID",
                    type: "text",
                    rules: "required",
                    disabled: true,
                    placeholder: "Quote ID",
                    colClass: "col-lg-3",
                },
                delivery_type: {
                    label: "SERVICE",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "delivery_types",
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
                    isLocker: true,
                },
                commodity: {
                    label: "COMMODITY",
                    type: "text",
                    rules: "required",
                    placeholder: "Commodity",
                    colClass: "col-lg-3",
                },
                status: {
                    label: "STATUS",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    options: "status_options",
                    colClass: "col-lg-3",
                },
                type: {
                    label: "TYPE",
                    type: "text",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Quote Type",
                    disabled: true,
                    colClass: "col-lg-3",
                },
                contact_id: {
                    label: "CONTACT",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    disabled: false,
                    options: [],
                    all_options: "contacts",
                    colClass: "col-lg-3",
                    selectLock: true,
                    lock_tracker: "company_id",
                    locking: "company_id",
                },
                kind_of_cargo: {
                    label: "KIND OF CARGO",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Kind of cargo",
                    options: "kind_of_cargo",
                    colClass: "col-lg-3",
                },
                validity_start: {
                    label: "DATE ISSUED",
                    type: "datepicker",
                    rules: "required",
                    colClass: "col-lg-3",
                },
                equipment: {
                    label: "EQUIPMENT",
                    type: "text",
                    disabled: true,
                    rules: "required",
                    placeholder: "Equipment",
                    colClass: "col-lg-3",
                },
                user_id: {
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
                validity_end: {
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
                language: {
                    label: "LANGUAGE",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select options",
                    colClass: "col-lg-3",
                    options:"languages",
                },
            },
            term_fields: {
                terms_and_conditions: {
                    type: "ckeditor",
                    rules: "required",
                    placeholder: "Insert terms",
                    colClass: "col-sm-12",
                },
            },
            currentData: {},
            vdata: {},
            datalists: {},
            equip: {},
            freights: {},
            quoteEquip: [],
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
                this.onSuccess(this.currentData);
            })
            .catch((data) => {
                this.$refs.observer.setErrors(data.data.errors);
            });
    },

    methods: {
        //Set dropdowns
        setDropdownLists(err, data) {
            this.datalists = data;
            //console.log(this.datalists);
        },
        onSuccess(data) {
            let component = this;

            component.equip = data.gp_container;
            component.freights = data.rates;
            component.quoteEquip = data.equipment.split(",");
            component.quoteEquip.splice(-1, 1);
        },

        changeView(val){

            if(val == 'freight'){
                this.ocean = true;
            }
        }
    },

    
};
</script>
