<template>
    <div v-if="loaded" style="padding: 0px 25px">

        <div style="width: 100%" class="mb-3 d-flex justify-content-end">
            <a href="#" id="show-btn" @click="showModal('addFreight')" class="btn btn-link">+ Add Freight</a>
        </div>

        <div v-if="freights.length == 0">
            <h2>Nothing to display. Start by <a href="#" id="show-btn" @click="showModal('addFreight')">adding a new freight</a></h2>
        </div>

        <!-- Freight Card -->
        <b-card v-for="freight in freights" :key="freight.id" class="q-card q-freight-card">
            
            <div class="row">
                
                <!-- Logo(compañia), origen, destino y add freight -->
                <div class="col-12 quote-card">
                   
                    <!-- Logo, origen, destino -->
                    <div>
                        <img
                            :src="freight.carrierLogo"
                            alt="logo"
                            width="100" 
                            height="50"
                            class="mr-4"
                        />

                        <div>
                            <span class="mr-4 ml-4">
                            <img :src="freight.originFlag" 
                            alt="bandera"
                            width="20" 
                            height="20"
                            style="border-radius: 50%;" />
                            {{freight.originPortName}}
                            </span>

                            <i
                                class="fa fa-long-arrow-right"
                                aria-hidden="true"
                                style="font-size: 18px"
                            ></i>

                            <span class="mr-4 ml-4">
                                <img :src="freight.destFlag" 
                                alt="bandera"
                                width="20" 
                                height="20"
                                style="border-radius: 50%;" />
                                {{freight.destPortName}}
                            </span>
                        </div>
                    </div>
                    <!-- End Logo, origen, destino -->

                    <!-- Add Freight -->
                    <div class="d-flex align-items-center">
                                                <!-- Inputs Freight -->
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-link btn-delete" id="show-btn" @click="deleteFreight(freight.id)">Delete Freight</a>
                            <a href="#" id="show-btn2" @click="setTableInsert(freight.id);" class="btn btn-primary btn-bg">+ Add Charge</a>
                        </div>
                        <!-- End Inputs Freight -->
                        <button type="button" class="btn" v-b-toggle="String(freight.id)" @click="setCollapseState(freight)">
                            <i class="fa fa-angle-down" aria-hidden="true" style="font-size: 35px"></i>
                        </button>
                    </div>
                </div>
                <!-- End Logo(compañia), origen, destino y add freight -->
            </div>

            <b-collapse :id="String(freight.id)" class="row" v-model="freight.initialCollapse">
                <div v-if="freight.loaded" class="mt-3 mb-3 mr-3 ml-3">
                    <!-- Header TT,via,date,contract-->
                    <div class="d-flex justify-content-between align-items-center">
                        <FormInlineView
                            v-if="rateLoaded"
                            :data ="freight.rateData"
                            :fields="header_fields"
                            :multi="true"
                            :datalists="datalists"
                            :actions="actions.automaticrates"
                            :update="true"
                        ></FormInlineView>
                    </div>


                    <DynamicalDataTable
                        v-if="loaded"
                        :initialFields="fields"
                        :fixedFormFields="eform_fields"
                        :initialFormFields="vform_fields"
                        :searchBar="false"
                        :extraRow="true"
                        :withTotals="true"
                        :autoAdd="false"
                        :changeAddMode="true"
                        :totalsFields="totalsFields"
                        :datalists="datalists"
                        :equipment="equipment"
                        :actions="actions.charges"
                        :totalActions="actions.automaticrates"
                        :autoupdateDataTable="true"
                        :quoteEquip="quoteEquip"
                        :limitEquipment="true"
                        :multiList="true"
                        :multiId="freight.id"
                        :paginated="false"
                        :massiveactions="['delete']"
                        :singleActions="['edit','delete']"
                        :massiveSelect="false"
                        @onFormFieldUpdated="formFieldUpdated"
                        @onOpenModalContainer="openModalContainer"
                        @onEditSuccess="onEdit"
                        :ref="freight.id"
                    ></DynamicalDataTable>

                    <!-- Checkbox Freight-->
                    <div class="col-12 d-flex mt-5 mb-3">
                        <b-form-checkbox v-if="freights.length < 2" v-model="allIn" @input="updatePdfOptions()">
                            <span>Freight All-In</span>
                        </b-form-checkbox>
                    </div>
                    <!-- End Checkbox Freight-->
                </div>

            </b-collapse>
        </b-card>
        <!-- End Freight Card -->

        <!-- Show Carrier checkbox -->
        <b-form-checkbox class="mr-4" v-if="freights.length!=0" v-model="showCarrier" @input="updatePdfOptions()">
            <span>Show Carrier</span>
        </b-form-checkbox>
        <!-- End show Carrier checkbox -->

        <!-- Global Remarks -->
        <b-card class="mt-5">
            <h5 class="q-title">{{'Remarks ' + quoteLanguage.toLowerCase()}}</h5>
            <FormInlineView
                v-if="rateLoaded"
                :data="currentQuoteData"
                :fields="remarks_fields"
                :multi="true"
                :actions="actions.quotes"
                :datalists="datalists"
                :update="true"
            ></FormInlineView>
        </b-card>
        <!-- End global remarks -->

        <!--  Add Freight Modal  -->
        <b-modal id="addFreight" hide-footer title="Add Freight">
            <div class="d-flex flex-column align-items-center justify-content-center mb-5">
                <FormView
                    :data="{}"
                    :fields="addFreightModal_fields"
                    :vdatalists="datalists"
                    btnTxt="Add Freight"
                    @exit="closeModal('addFreight','cancel')"
                    @success="closeModal('addFreight','addFreight')"
                    :actions="actions.automaticrates"
                ></FormView>
            </div>
        </b-modal>
        <!--  End Add Freight Modal  -->

        <!--  Edit Charge Modal  -->
        <b-modal
            id="editCharge"
            size="lg"
            cancel-title="Cancel"
            hide-header-close
            title="Edit Charge"
            hide-footer
        >
            <FormView
                :data="currentChargeData"
                :massivedata="ids_selected"
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Update Charge"
                @exit="closeModal('editCharge','cancel')"
                @success="closeModal('editCharge','edit')"
                :actions="actions.charges"
                :update="true"
            ></FormView>
        </b-modal>
        <!--  Edit Charge Modal end -->
        
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import actions from "../../actions";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import "vue-multiselect/dist/vue-multiselect.min.css";
import DynamicalDataTable from "../../components/views/DynamicalDataTable";
import FormView from "../../components/views/FormView";
import FormInlineView from "../../components/views/FormInlineView";

export default {
    components: {
        Multiselect,
        DateRangePicker,
        DynamicalDataTable,
        FormView,
        FormInlineView,
    },
    props: {
        equipment: Object,
        datalists: Object,
        freights: Array,
        quoteEquip: Array,
        currentQuoteData: Object,
        quoteLanguage: String,
    },
    data() {
        return {
            openFreight: false,
            openModal: false,
            imageFolder: "/images/flags/1x1/",
            vdata: {},
            value: "",
            actions: actions,
            header_fields: {
                transit_time: {
                    label: "TRANSIT TIME",
                    type: "text",
                    placeholder: "Days",
                    colClass: "col-lg-2",
                },
                schedule_type: {
                    label: "SERVICE",
                    type: "select",
                    trackby: "name",
                    placeholder: "Select service",
                    colClass: "col-lg-2",
                    options: "schedule_types",
                },
                via: {
                    label: "VIA",
                    searchable: true,
                    type: "text",
                    placeholder: "Transfer",
                    colClass: "col-lg-2",
                },
                exp_date: {
                    label: "VALID UNTIL",
                    type: "datepicker",
                    colClass: "col-lg-3",
                },
                contract: {
                    label: "REFERENCE",
                    type: "text",
                    placeholder: "Contract name",
                    colClass: "col-lg-2",
                },
            },
            remarks_fields: {},
            global_remarks_fields: {},
            addFreightModal_fields: {
                POL: {
                    label: "POL",
                    type: "select",
                    trackby: "display_name",
                    placeholder: "Select POL",
                    colClass: "col-lg-8",
                    options: "harbors",
                },
                POD: {
                    label: "POD",
                    type: "select",
                    trackby: "display_name",
                    placeholder: "Select POD",
                    colClass: "col-lg-8",
                    options: "harbors",
                },
                carrier: {
                    label: "Carrier",
                    type: "select",
                    trackby: "name",
                    placeholder: "Select carrier",
                    colClass: "col-lg-8",
                    options: "carriers",
                },
            },
            editChargeModal_fields:{},
            loaded: true,
            rateLoaded: false,
            form_fields: {},
            extra_fields: {},
            containers_fields: {},
            currentChargeData: {},
            modalFreight: {},
            ids_selected: [],
            showCarrier: true,
            allIn: true,

            /* Table headers */
            fields: [
                {
                    key: "surcharge_id",
                    label: "CHARGE",
                    type: "select",
                    trackby: "name",
                    options: "surcharges",
                },
                {
                    key: "calculation_type_id",
                    label: "DETAIL",
                    type: "select",
                    trackby: "name",
                    options: "calculationtypes",
                },
                {
                    key: "currency_id",
                    label: "CURRENCY",
                    type: "select",
                    trackby: "alphacode",
                    options: "currency",
                },
            ],

            /* Table input inline fields */
            vform_fields: {
                surcharge_id: {
                    label: "CHARGE",
                    type: "select",
                    searchable: true,
                    rules: "required",
                    placeholder: "Select charge type",
                    trackby: "name",
                    options: "surcharges",
                },
                calculation_type_id: {
                    label: "DETAIL",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "name",
                    placeholder: "Select Provider",
                    options: "calculationtypes",
                },
                currency_id: {
                    label: "CURRENCY",
                    searchable: true,
                    type: "select",
                    rules: "required",
                    trackby: "alphacode",
                    placeholder: "Select Currency",
                    options: "currency",
                },
            },
            eform_fields: {
                fixed_surcharge: {
                    type: "extraText",
                    disabled: true,
                    placeholder: "Ocean Freight",
                },
                fixed_calculation_type: {
                    type: "extraText",
                    disabled: true,
                },
                fixed_currency: {
                    searchable: true,
                    type: "extraSelect",
                    rules: "required",
                    trackby: "alphacode",
                    placeholder: "Select Currency",
                    options: "currency",
                },
            },
            totalsFields: {
                Profits: {},
                Totals: {},
            },
            //Datepicker Options
            locale: "en-US",
            dateFormat: { year: "numeric", month: "long", day: "numeric" },
        };
    },
    created() {
        if(typeof this.currentQuoteData.pdf_options == "string"){
            var pdfOptions = JSON.parse(this.currentQuoteData.pdf_options);
        }else{
            var pdfOptions = this.currentQuoteData.pdf_options;
        }

        this.setTotalsFields();

        this.setRemarksField(this.quoteLanguage);
        
        this.setFreightData();

        this.allIn = pdfOptions['allIn'];

        this.showCarrier = pdfOptions['showCarrier'];
    },
    watch: {
        quoteLanguage: function(newVal,oldVal) {this.setRemarksField(newVal);},

        freights: function() {this.setFreightData();},
    },
    methods: {
        showModal(modal) {
            this.$bvModal.show(modal);
        },

        onEdit(data,id){
            this.currentChargeData = data;
            this.$bvModal.show("editCharge");
            this.modalFreight = id;
            
        },

        /* Single Actions */
        formFieldUpdated(containers_fields) {
            let component = this;

            component.containers_fields = containers_fields;
            component.form_fields = {
                ...this.vform_fields,
                ...containers_fields,
            };
            component.extra_fields = {
                ...this.eform_fields,
                ...containers_fields,
            };
        },

        openModalContainer(ids) {
            this.ids_selected = ids;
            this.$bvModal.show("editCharge");
        },

        closeModal(modal,action) {
            let component = this;

            component.$bvModal.hide(modal);
            component.ids_selected = [];
            if(modal=="editCharge" && action=='edit'){
                component.$emit("chargeUpdated")

                component.$refs[component.modalFreight][0].refreshTable()

            }else if(modal=="addFreight" && action=='addFreight'){
                let id = this.$route.params.id;

                component.$emit("freightAdded",id)

            }
        },

        setTotalsFields() {
            let component = this;

            component.quoteEquip.forEach(function (eq) {
                component.totalsFields["Profits"]["profits_".concat(eq)] = {
                    type: "text",
                    placeholder: eq,
                };
                component.totalsFields["Totals"]["totals_".concat(eq)] = {
                    type: "span",
                };
            });

            component.totalsFields["Profits"]["profits_currency"] = {
                searchable: true,
                type: "select",
                rules: "required",
                trackby: "alphacode",
                placeholder: "Select Currency",
                options: "currency",
                disabled: true,
            };
            component.totalsFields["Totals"]["totals_currency"] = {
                type: "span",
            };
        },

        setCollapseState(freight) {
            let component = this;

            if(!freight.loaded){
                actions.automaticrates
                    .retrieve(freight.id,component.$route)
                    .then((response) => {
                        freight.rateData = response.data.data;
                        component.rateLoaded = true;
                        })
                    .catch((data) => {
                        this.$refs.observer.setErrors(data.data.errors);
                        });
                        
                freight.loaded = true;
            }

            setTimeout(function() {
                component.$nextTick(() => {
                    component.$forceUpdate()
                });
            },100);
        },

        setFreightData(){
            let component = this;
            let firstOpen = false;

            component.freights.forEach(function (freight) {
                freight.loaded = false;
                component.datalists.carriers.forEach(function (carrier){
                    if(freight.carrier_id==carrier.id){
                        freight.carrierLogo = "/imgcarrier/".concat(carrier.image)
                    }
                });

                component.datalists.harbors.forEach(function (harbor) {
                    if (harbor.id == freight.origin_port_id) {
                        freight.originFlag = component.imageFolder.concat(harbor.code.slice(0,2).toLowerCase()).concat(".svg")
                        freight.originPortName = harbor.display_name;
                    } else if (harbor.id == freight.destination_port_id) {
                        freight.destFlag = component.imageFolder.concat(harbor.code.slice(0,2).toLowerCase()).concat(".svg")
                        freight.destPortName = harbor.display_name;
                    }
                });
                
                if(!firstOpen){
                    component.setCollapseState(freight);
                    freight.initialCollapse = true;
                    firstOpen = true;
                }else{
                    freight.initialCollapse = false;
                }
            });
        },

        setRemarksField(language){
            if(language=="Spanish"){
                this.remarks_fields = {
                    remarks_spanish: {
                        type: "ckeditor",
                        colClass: "col-sm-12",
                    },
                }  
            }else if(language=="Portuguese"){
                this.remarks_fields = {
                    remarks_portuguese: {
                        type: "ckeditor",
                        colClass: "col-sm-12",
                    },
                }  
            }else if(language=="English"){
                this.remarks_fields = {
                    remarks_english: {
                        type: "ckeditor",
                        colClass: "col-sm-12",
                    },
                }  
            }

            this.global_remarks_fields = {
                remarks: {
                    type: "ckeditor",
                    colClass: "col-sm-12",
                }
            }
        },

        deleteFreight(id){
            actions.automaticrates
                .delete(id)
                .then( ( response ) => {
                    this.setFreightData();
                })
                    .catch(( data ) => {
                });

            let quote_id = this.$route.params.id;

            this.$emit("freightAdded",quote_id)
        },

        setTableInsert(id){
            let component = this;

            component.modalFreight = id;

            component.$refs[component.modalFreight][0].autoAdd = true;
        },

        updatePdfOptions(){
            let pdfOptions = 
            {pdf_options:
                {
                    'allIn' : this.allIn,
                    'showCarrier' : this.showCarrier,
                }
            };

            this.actions.quotes
                .update(this.currentQuoteData['id'], pdfOptions)
                    .then( ( response ) => {
                        console.log('updated!')
                    })
                    .catch(( data ) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
        },
    },
};
</script>