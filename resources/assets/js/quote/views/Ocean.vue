<template>
    <div style="padding: 0px 25px">

        <div style="width: 100%" class="mb-3 d-flex justify-content-end">
            <a href="#" id="show-btn" @click="showModal" class="btn btn-link">+ Add Freight</a>
        </div>

        <!-- Freight Card -->
        <b-card v-for="freight in freights" :key="freight.id" class="q-card q-freight-card" :id="freight.id">

            <div class="row">

                <!-- Logo(compañia), origen, destino y add freight -->
                <div class="col-12 d-flex justify-content-between">
                    
                    <!-- Logo, origen, destino -->
                    <div>

                        <img src="https://i.ibb.co/BNf0WNM/download-logo-HLAG-1.png" alt="logo" class="mr-4">
                    
                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/ZTq7994/spain.png" alt="bandera">
                            {{freight.originPortName}}
                        </span>

                        <i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px"></i>

                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/7WffMF5/china-1-1.png" alt="bandera">
                            {{freight.destPortName}}
                        </span>

                    </div>
                    <!-- End Logo, origen, destino -->

                    <!-- Add Freight -->       
                    <button type="button" class="btn" v-b-toggle="'collapse-1'">
                        <i class="fa fa-angle-down" aria-hidden="true" style="font-size: 35px"></i>
                    </button>

                </div>
                <!-- End Logo(compañia), origen, destino y add freight -->

            </div>

            <b-collapse id="collapse-1" class="row">
                
                <div class="mt-3 mb-3 mr-3 ml-3">
                    <!-- Header TT,via,date,contract-->
                    <FormInlineView
                        v-if="loaded"
                        :data="currentRateData[freight.id]"
                        :fields="header_fields"
                        :datalists="datalists"
                        :actions="actions.automaticrates"
                        :update="true"
                    ></FormInlineView>
                
                    <!-- Inputs Freight -->
                    <!-- End Inputs Freight -->

                    <DynamicalDataTable 
                        v-if="loaded"
                        :initialFields="fields"
                        :extraInitialFormFields="eform_fields"
                        :initialFormFields="vform_fields"
                        :searchBar="false"
                        :fixedData="currentChargeData[freight.id]"
                        :extraRow="true"
                        :datalists="datalists"
                        :equipment="equipment"
                        :actions="actions.charges"
                        :quoteEquip="quoteEquip"
                        :limitEquipment="true"
                        :multiList="true"
                        :multiId="freight.id"
                        :paginated="false"
                        :massiveactions="['openmodalcontainer', 'delete']"
                        @onFormFieldUpdated="formFieldUpdated"
                        @onOpenModalContainer="openModalContainer"
                    ></DynamicalDataTable>

                    <hr>

                    <div class="row">    
                        <div class="col-lg-2"></div>

                        <div class="col-lg-2" style ="text-align:right;">
                            <span><b>Profit</b></span>
                        </div>

                        <!-- Profits field -->
                        <div class="col-lg-8">
                            <FormInlineView
                                v-if="loaded"
                                :data="currentChargeData[freight.id]"
                                :fields="profit_fields"
                                :datalists="datalists"
                                :actions="actions.charges"
                                :update="true"
                            ></FormInlineView>
                        </div>
                    </div>

                </div>

                <!-- Checkbox Freight-->
                <div class="col-12 d-flex mt-5 mb-3">

                    <b-form-checkbox value="carrier" class="mr-4">
                        <span>Show Carrier</span>
                    </b-form-checkbox>

                    <b-form-checkbox value="freight">
                        <span>Freight All-In</span>
                    </b-form-checkbox>

                </div>
                <!-- End Checkbox Freight-->

            </b-collapse>

        </b-card>
        <!-- End Freight Card -->

        <!-- Freight Card -->
        <b-card class="q-card q-freight-card">

            <div class="row">

                <!-- Logo(compañia), origen, destino y add freight -->
                <div class="col-12 d-flex justify-content-between">
                    
                    <!-- Logo, origen, destino -->
                    <div>

                        <img src="https://i.ibb.co/BNf0WNM/download-logo-HLAG-1.png" alt="logo" class="mr-4">
                    
                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/ZTq7994/spain.png" alt="bandera">
                            Barcelona, ESBCN
                        </span>

                        <i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px"></i>

                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/7WffMF5/china-1-1.png" alt="bandera">
                            Shanghai, CNSHA
                        </span>

                    </div>
                    <!-- End Logo, origen, destino -->

                    <!-- Add Freight -->      
                    <button type="button" class="btn" v-b-toggle="'collapse-2'">
                        <i class="fa fa-angle-down" aria-hidden="true" style="font-size: 35px"></i>
                    </button>

                </div>
                <!-- End Logo(compañia), origen, destino y add freight -->

            </div>

            <b-collapse id="collapse-2" class="row">

                <!-- Inputs Freight -->
                <div class="col-12 d-flex mt-3">

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">transit time</span>
                        <b-form-input placeholder="33" class="q-input"></b-form-input>

                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">services</span>
                        <b-form-input placeholder="Directo" class="q-input"></b-form-input>

                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">valid until</span>
                        <b-form-datepicker 
                            id="valid-datepicker" 
                            v-model="value" 
                            class="mb-2"
                            :locale="locale"
                            :date-format-options="dateFormat">
                        </b-form-datepicker>
                    
                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">reference</span>
                        <b-form-input placeholder="QT123456798" class="q-input"></b-form-input>
                    
                    </label>

                </div>
                <!-- End Inputs Freight -->

                <!-- Tabla Freight -->
                <div class="col-12">

                    <!-- DataTable -->
                    <b-table-simple small responsive borderless>

                        <!-- Header table -->
                        <b-thead class="q-thead">

                            <b-tr>

                                <b-th>
                                    <span class="label-text">charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">provider</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">20 dv</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">40 dv</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">40 hc</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">currency</span>
                                </b-th>

                            </b-tr>

                        </b-thead>
                        <!-- End Header table -->

                        <!-- Body table -->
                        <b-tbody >

                            <!-- Data -->
                            <b-tr>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Per Container" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1000" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>
                            <!-- End Data -->

                            <!-- Data -->
                            <b-tr>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Per Container" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1000" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>
                            <!-- End Data -->

                            <!-- Profit -->
                            <b-tr>

                                <b-td></b-td>

                                <b-td>
                                    <span><b>Profit</b></span>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="0" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="0" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>
                                
                                <b-td></b-td>

                            </b-tr>
                            <!-- End Profit -->

                            <!-- Total -->
                            <b-tr class="q-total">

                                <b-td></b-td>

                                <b-td><span><b>Total</b></span></b-td>

                                <b-td><span><b>1600</b></span></b-td>

                                <b-td><span><b>500</b></span></b-td>
                                
                                <b-td><span><b>150</b></span></b-td>

                                <b-td><span><b>EUR</b></span></b-td>

                                <b-td></b-td>

                            </b-tr>
                            <!-- End Total -->

                        </b-tbody>
                        <!-- Body table -->

                    </b-table-simple>
                    <!-- End DataTable -->

                </div>
                <!-- End Tabla Freight -->

                <!-- Checkbox Freight-->
                <div class="col-12 d-flex mt-3 mb-3">

                    <b-form-checkbox value="carrier" class="mr-4">
                        <span>Show Carrier</span>
                    </b-form-checkbox>

                    <b-form-checkbox value="freight">
                        <span>Freight All-In</span>
                    </b-form-checkbox>

                </div>
                <!-- End Checkbox Freight-->

            </b-collapse>

        </b-card>
        <!-- End Freight Card -->

        <!-- Remarks -->
        <b-card class="mt-5">

            <h5 class="q-title">Remarks</h5>
            <textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea>

        </b-card>
        <!-- End Remarks -->

        <!--  Modal  -->
        <b-modal ref="my-modal" hide-footer title="Add Freight">
            <div class="d-flex flex-column align-items-center justify-content-center mb-5" >
                <label style="width:50%">
                    POL
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <label style="width:50%">
                    POD
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <label style="width:50%">
                    Carrier
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <a href="#" class="btn btn-primary btn-bg mt-2">Add Freight</a>
            </div>
        </b-modal>
        <!--  End Modal  -->

        <b-modal id="editContainers" size="lg" cancel-title="Cancel" hide-header-close title="Edit Containers" hide-footer>
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

    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import actions from "../../actions";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import "vue-multiselect/dist/vue-multiselect.min.css";
import DynamicalDataTable from '../../components/views/DynamicalDataTable';
import FormView from '../../components/views/FormView';
import FormInlineView from '../../components/views/FormInlineView';

export default {
  components: {
    Multiselect,
    DateRangePicker,
    DynamicalDataTable,
    FormView,
    FormInlineView
  },
  props: {
        equipment: Object,
        datalists: Object,
        freights: Array,
        quoteEquip: Array
  },
  data() {
    return {
      openFreight: false,
      openModal: false,
      vdata: {},
      value: "",
      actions: actions,
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
        "touched"
      ],
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
            hiding: "via",
            showCondition:"Transfer",
            isHiding:true
        },
        via: {
            label: "VIA",
            searchable: true,
            hidden: true,
            type: "text",
            placeholder: "Transfer",
            colClass: "col-lg-2",
        },
        exp_date: {
            label: "VALID UNTIL",
            type: "datepicker",
            colClass: "col-lg-3"
        },
        contract: {
            label: "REFERENCE",
            type: "text",
            placeholder: "Contract name",
            colClass: "col-lg-2",
        },
      },
      profit_fields: {
      },
      loaded: true,
      currentRateData: {},
      currentChargeData: {},
      form_fields: {},
      extra_fields: {},
      containers_fields: {},
      ids_selected: [],

        /* Table headers */
        fields: [ 
            { key: 'surcharge_id', label: 'CHARGE',formatter: (value)=> { return value.name }}, 
            { key: 'calculation_type_id', label: 'PROVIDER',formatter: (value)=> { return value.name }},
            { key: 'currency_id', label: 'CURRENCY', formatter: (value)=> { return value.alphacode }}
        ],

        /* Table input inline fields */
        vform_fields: {
            surcharge_id: { 
                label: 'CHARGE',  
                type: 'select',
                searchable: true, 
                rules: 'required', 
                placeholder: 'Select charge type',
                trackby: 'name', 
                options: 'surcharges'
            },
            calculation_type_id: { 
                label: 'PROVIDER', 
                searchable: true,
                type: 'select', 
                rules: 'required',
                trackby: 'name',
                placeholder: 'Select Provider', 
                options: 'calculationtypes'
            },
            currency_id: { 
                label: 'CURRENCY',
                searchable: true, 
                type: 'select', 
                rules: 'required', 
                trackby: 'alphacode', 
                placeholder: 'Select Currency', 
                options: 'currency' 
            },
        },
        eform_fields: {
            fixed_surcharge: { 
                type: 'extraText',
                disabled: true,
                placeholder: 'Ocean Freight'
            },
            fixed_calculation_type: { 
                type: 'extraText', 
                disabled: true
            },
            fixed_currency: { 
                searchable: true, 
                type: 'extraSelect', 
                rules: 'required', 
                trackby: 'alphacode', 
                placeholder: 'Select Currency', 
                options: 'currency'
            }
        },
       //Datepicker Options
      locale: 'en-US',
      dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'}
    };
  },
  created(){
    let component = this;

    component.setProfitFields();

    component.freights.forEach(function(freight){
        actions.automaticrates
            .retrieve(freight.id,component.$route)
            .then((response) => {
                Vue.set(component.currentRateData,freight.id,response.data.data);
                })
            .catch((data) => {
                component.$refs.observer.setErrors(data.data.errors);
            });
        actions.charges
            .retrieve(freight.id)
            .then((response) => {
                Vue.set(component.currentChargeData,freight.id,response.data.data);
                })
            .catch((data) => {
                component.$refs.observer.setErrors(data.data.errors);
            });
    });
  },
  methods: {
    showModal() {
        this.$refs['my-modal'].show();
        },

    /* Single Actions */
    formFieldUpdated(containers_fields){
        let component = this;

        component.containers_fields = containers_fields;
        component.form_fields = {...this.vform_fields, ...containers_fields};
        component.extra_fields = {...this.eform_fields, ...containers_fields};

        component.freights.forEach(function(freight){
            component.datalists.harbors.forEach(function(harbor){
                if(harbor.id == freight.origin_port_id){
                    freight.originPortName = harbor.display_name
                } else if(harbor.id == freight.destination_port_id) {
                    freight.destPortName = harbor.display_name
                    }
            });
        });
    },

    openModalContainer(ids){
        console.log('test modal');
        this.ids_selected = ids;
            this.$bvModal.show('editContainers');
    },

    setProfitFields(){
        let component = this;

        component.quoteEquip.forEach(function(eq){
            component.profit_fields['markups_'.concat(eq)] = { type: 'text', placeholder: eq, colClass:'col-lg-3'}           
        });

        component.profit_fields['profit_currency'] = {searchable: true, 
                                                    type: 'select', 
                                                    rules: 'required', 
                                                    trackby: 'alphacode', 
                                                    placeholder: 'Select Currency', 
                                                    options: 'currency',
                                                    colClass: 'col-lg-3'
                                                    }

    },
  }
     
};
</script>