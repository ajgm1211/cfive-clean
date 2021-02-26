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
                            <b>{{currentData.type}} Quote | {{currentData.quote_id}}</b>
                        </h4>
                        <div>
                            <a :href="'/api/quote/pdf/' + this.quote_id" target="_blank" class="btn btn-primary btn-bg">+ PDF</a>
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
                                                @success="setTermsField"
                                            ></FormInlineView>
                                        </div>
                                    </div>
                                </b-card>
                                <!-- End Quote inputs -->

                                <!-- LCL Fields -->

                                <b-card class="mt-5" v-if="equip == '[]'">
                                    <FormInlineView
                                        v-if="loaded"
                                        :data="currentData"
                                        :fields="LCL_fields"
                                        :actions="actions.quotes"
                                        :update="true"
                                        @success="setChargeable()"
                                    ></FormInlineView>
                                </b-card>

                                <!-- LCL Fields End -->

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
                            :quoteLanguage="this.currentData.language_id['name']"
                            @freightAdded="setInitialData"
                            ref="oceanTab"
                            ></ocean>
                        </b-tab>

                        <b-tab title="Local Charges" @click="changeView('locals')">
                            <Local
                                v-if="locals"
                                :equipment="equip"
                                :quoteEquip="quoteEquip"
                                :datalists="datalists"
                                :currentQuoteData="currentData"
                                @chargesUpdated="setInitialData"
                            ></Local>
                        </b-tab>

                        <b-tab title="Inland" @click="changeView('inlands')">
                            <Inland v-if="inlands"
                            :currentQuoteData="currentData"
                            :equipment="equip"
                            :quoteEquip="quoteEquip"
                            :actions="actions"
                            :datalists="datalists"
                            :freights="freights"
                            :localCharges="currentData.local_charges"
                            ></Inland>
                        </b-tab>

                        <b-tab title="Totals" @click="changeView('totals')">
                            <Total v-if="totals"
                            :currentQuoteData="currentData"
                            :freights="freights"
                            :datalists="datalists"
                            :actions="actions"
                            @freightAdded="setInitialData"
                            ></Total>
                        </b-tab>

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
import Total from "./Total";
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
        Total,
    },
    data() {
        return {
            actions: actions,
            loaded: false,
            ocean: false,
            locals: false,
            currentLocalCharges: [],
            inlands: false,
            totals: false,
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
                custom_quote_id: {
                    label: "CUSTOM QUOTE ID",
                    type: "text",                    
                    disabled: false,
                    placeholder: "Custom Quote ID",
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
                    label: "VALID UNTIL",
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
                    colClass: "col-lg-1",
                },
                custom_incoterm: {
                    label: "CUSTOM INCOTERM",
                    type: "text",                    
                    disabled: false,
                    placeholder: "Custom incoterm",
                    colClass: "col-lg-2",
                },
                language_id: {
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
            term_fields: {},
            LCL_fields: {
                cargo_type_id: {
                    label: "CARGO TYPE",
                    type: "text",
                    disabled: true,
                    trackby: "name",
                    colClass: "col-lg-2",
                },
                total_quantity: {
                    label: "TOTAL QUANTITY",
                    type: "text",
                    colClass: "col-lg-2",
                },
                total_weight: {
                    label: "TOTAL WEIGHT",
                    type: "text",
                    colClass: "col-lg-2",
                },
                weight_units: {
                    type: "span",
                    colClass: "col-lg",
                },
                total_volume: {
                    label: "TOTAL VOLUME",
                    type: "text",
                    colClass: "col-lg-2",
                },
                volume_units: {
                    type: "span",
                    colClass: "col-lg",
                },
                chargeable_weight: {
                    label: "CHARGEABLE WEIGHT",
                    type: "text",
                    disabled: true,
                    colClass: "col-lg-2",
                },
                chargeable_units: {
                    type: "span",
                    colClass: "col-lg",
                },
            },
            currentData: {},
            vdata: {},
            datalists: {},
            equip: {},
            freights: {},
            quoteEquip: [],
            quote_id: this.$route.params.id,
        };
    },
    watch: {
        currentData: function() {this.setChargeable();},
    },
    created() {
        let id = this.$route.params.id;

        api.getData({}, "/api/quote/data", (err, data) => {
            this.setDropdownLists(err, data.data);
            this.loaded = true;
        });

        this.setInitialData(id);
    },
    beforeUpdate(){
        this.setTermsField();
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
            if (component.equip == '[]'){
                component.equip = {};
            }
            component.freights = data.rates;
            component.quoteEquip = data.equipment.split(",");
            component.quoteEquip.splice(-1, 1);

            component.currentData['volume_units'] = 'm' + '3'.sup();
            component.currentData['weight_units'] = 'Kg'; 
            component.currentData['chargeable_units'] = 'm' + '3'.sup();

            if(component.ocean){
                component.ocean=false;
                setTimeout(function() {
                    component.ocean=true
                },100);
            }
            if(component.inlands){
                component.inlands=false;
                setTimeout(function() {
                    component.inlands=true
                },100);
            }
            if(component.totals){
                component.totals=false;
                setTimeout(function() {
                    component.totals=true
                },100);
            }            
        },

        changeView(val){
            let component = this;

            if(val == 'freight'){
                component.ocean = true;
                component.locals = false;
                component.totals = false;
                component.inlands = false;
            } else if(val == 'locals'){
                component.locals = true;
                component.ocean = false;
                component.totals = false;
                component.inlands = false;
            } else if(val == 'inlands'){
                component.inlands = true;
                component.locals = false;
                component.totals = false;
                component.ocean = false;
            } else if(val == 'totals'){
                component.totals = true;
                component.locals = false;
                component.ocean = false;
                component.inlands = false;
            }
        },

        setInitialData(id){
            let component=this;

            actions.quotes
                .retrieve(id)
                .then((response) => {
                    component.currentData = response.data.data;
                    component.onSuccess(component.currentData);
                })
                .catch((data) => {
                    component.$refs.observer.setErrors(data.data.errors);
                });
        },
        
        setTermsField(){
            if(this.currentData.language_id['name']=="Spanish"){
                this.term_fields = { 
                    terms_and_conditions: {
                    type: "ckeditor",
                    colClass: "col-lg-12",
                    }
                }                 
            }else if(this.currentData.language_id['name']=="Portuguese"){
                this.term_fields = { 
                    terms_portuguese: {
                    type: "ckeditor",
                    colClass: "col-lg-12",
                    }
                }
            }else if(this.currentData.language_id['name']=="English"){
                this.term_fields = { 
                    terms_english: {
                    type: "ckeditor",
                    colClass: "col-lg-12",
                    }
                }
            }
        },

        setChargeable(){
            let calc_volume = parseFloat(this.currentData['total_volume']);
            let calc_weight = parseFloat(this.currentData['total_weight'])/1000;
            
            if (calc_volume > calc_weight) {
                this.currentData['chargeable_weight'] = calc_volume;
            } else {
                this.currentData['chargeable_weight'] = calc_weight;
            }
        },
    },
    
};
</script>
