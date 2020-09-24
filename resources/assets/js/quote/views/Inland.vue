<template>
    <div style="padding: 0px 25px">

        <div v-if="freights.length == 0">
            <h2 style="margin: 10px;">Nothing to display. Start by adding a new freight at the Ocean Freight tab</h2>
        </div>

        <b-card v-else class="q-card">

            <div class="row justify-content-between">

                <!-- Origen -> Destino -->
                <div class="col-12 col-lg-8 d-flex align-items-center">

                    <h5><b>Inland at:</b></h5>

                    <multiselect
                        v-model="currentPort"
                        :options="port_options"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="false"
                        label="name"
                        track-by="name"
                        placeholder="Select Template"
                        class="q-select ml-3">
                    </multiselect>

                    <img src="https://i.ibb.co/YjfjzkS/delivery-2-1.png" alt="delivery-2-1" border="0" class="mr-4 ml-4">
                        
                    <h5>
                        <b>From:</b>
                        <img :src="currentPort['flag']"
                            width="20" 
                            height="20"
                            style="border-radius: 50%;"
                            alt="bandera" 
                            class="ml-2 mr-1">
                        <span v-if="currentPortType=='Origin'" style="font-size: 14px">{{currentQuoteData.origin_address}}</span>
                        <span v-else style="font-size: 14px">{{currentQuoteData.destination_address}}</span>
                    </h5>

                </div>
                <!-- End Origen -> Destino -->

                <div class="col-12 col-lg-4 d-flex justify-content-end align-items-center">

                    <a href="#" class="btn btn-primary btn-bg" id="show-btn" @click="showModal">+ Add Inland</a>

                </div>

                <div class="col-12 mt-5">

                    <!-- DataTable -->
                    <DynamicalDataTable
                        v-if="loaded"
                        :initialFields="fields"
                        :initialFormFields="vform_fields"
                        :searchBar="false"
                        :withTotals="true"
                        :totalsFields="totalsFields"
                        :datalists="datalists"
                        :equipment="equipment"
                        :actions="actions.automaticinlands"
                        :quoteEquip="quoteEquip"
                        :limitEquipment="true"
                        :totalActions="actions.automaticinlands"
                        :paginated="false"
                        :autoupdateDataTable="true"
                        :multiList="true"
                        :multiId="currentPort.id"
                        :portType="currentPortType"
                        :massiveactions="['delete']"
                        :singleActions="['delete']"
                        @onFormFieldUpdated="formFieldUpdated"
                    ></DynamicalDataTable>
                    <!-- End DataTable -->
                
                </div>

                <!-- Checkbox Group Action -->
                <div class="col-12 d-flex">

                    <b-form-checkbox value="carrier" class="mr-4"><span>Group as:</span> </b-form-checkbox>

                    <multiselect 
                        v-model="value" 
                        :options="options" 
                        :searchable="true" 
                        :close-on-select="false" 
                        :show-labels="false" 
                        placeholder="Forfait"
                        style="width:8%">
                    </multiselect>

                </div>
                <!-- End Checkbox Group Action -->

            </div>

        </b-card>

        <!--  Modal  -->
        <b-modal ref="my-modal" size="xl" centered hide-footer title="Inland Charges">
            
            <div class="row">

                <div class="col-12 d-flex align-items-center justify-content-between">

                    <div>
                        <label class="mr-5">
                            PORT
                            <multiselect 
                                v-model="currentPort" 
                                :options="port_options" 
                                :searchable="true" 
                                :close-on-select="false" 
                                :show-labels="false"
                                label="name"
                                track-by="name"
                                placeholder="Select a port">
                            </multiselect>
                        </label>

                        <label>
                            ORIGIN ADDRESS
                            <multiselect 
                                v-model="currentAddress" 
                                :options="options" 
                                :searchable="true" 
                                :close-on-select="false" 
                                :show-labels="false" 
                                placeholder="Select a country">
                            </multiselect>
                        </label>
                    </div>

                    <div>
                        <button class="btn btn-link mr-2">+ Add Manual</button>
                        <button class="btn btn-primary btn-bg">Search</button>
                    </div>

                </div>

                <div class="col-12 mt-5">

                    <!-- DataTable -->
                    <b-table-simple hover small responsive borderless>

                        <!-- Header table -->
                        <b-thead class="q-thead">

                            <b-tr>

                                <b-th></b-th>

                                <b-th>
                                    <span class="label-text">provider</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">20 DV + Profit</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">40 DV + Profit</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">40 HC + Profit</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">currency</span>
                                </b-th>

                                <b-th></b-th>

                            </b-tr>

                        </b-thead>

                        <b-tbody>
                        
                            <b-tr class="q-tr">
                                
                                <b-td>
                                    <b-form-checkbox value="carrier"></b-form-checkbox>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="MSC" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>

                            <b-tr class="q-tr">
                                
                                <b-td>
                                    <b-form-checkbox value="carrier"></b-form-checkbox>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="MSC" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                    <b-form-input placeholder="100" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>
                                
                            </b-tr>

                        </b-tbody>
                        
                    </b-table-simple>
                    <!-- End DataTable -->

                </div>

                <div class="col-12 d-flex justify-content-end mb-5 mt-3">

                    <button class="btn btn-primary btn-bg">Add Inland</button>

                </div>

            </div>

        </b-modal>
        <!--  End Modal  -->

    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import 'vue-multiselect/dist/vue-multiselect.min.css';
    import DynamicalDataTable from "../../components/views/DynamicalDataTable";
    export default {
        components: {
            Multiselect,
            DynamicalDataTable,
        },
        props: {
            datalists: Object,
            freights: Array,
            currentQuoteData: Object,
            equipment: Object,
            quoteEquip: Array,
            actions: Object,
        },
        watch: {
            currentPort: function(newVal,oldVal){this.updateTable(newVal);}
        },
        data() {
            return {
                openModal: false,
                vdata: {},
                value: '',
                imageFolder: "/images/flags/1x1/",
                loaded: false,
                options: ['Select option', 'options', 'selected', 'mulitple', 'label', 'searchable', 'clearOnSelect', 'hideSelected', 'maxHeight', 'allowEmpty', 'showLabels', 'onChange', 'touched'],
                currentPort: '',
                currentPortType: '',
                currentAddress: '',
                port_options: [],
                  /* Table headers */
                fields: [
                    {
                        key: "charge",
                        label: "CHARGE",
                        type: "text",
                    },
                    {
                        key: "provider",
                        label: "PROVIDER",
                        type: "text",
                    },
                    {
                        key: "currency_id",
                        label: "CURRENCY",
                        type: "select",
                        trackby: "alphacode",
                        options: "currency",
                    },
                ],
                /* Table inputs */
                vform_fields: {
                    charge: {
                        label: "CHARGE",
                        type: "text",
                        rules: "required",
                        placeholder: "Select charge",
                    },
                    provider: {
                        label: "PROVIDER",
                        type: "text",
                        rules: "required",
                        placeholder: "Select Provider",
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
                totalsFields: {
                    Profits: {},
                    Totals: {},
                },
            }
        },
        created() {
            this.setPorts();

            this.setTotalsFields();
        },
        methods: {
            showModal() {
                this.$refs['my-modal'].show();
            },
            
            setPorts() {
                let component = this;

                component.freights.forEach(function(freight){
                    component.datalists.harbors.forEach(function (harbor) {
                        if(freight.origin_port_id == harbor.id && !component.port_options.includes(harbor.display_name)){
                            component.port_options.push({"name":harbor.display_name,
                                                        "id":harbor.id,
                                                        "type":"Origin",
                                                        "flag":component.imageFolder.concat(harbor.code.slice(0,2).toLowerCase()).concat(".svg")});
                        }
                        if(freight.destination_port_id == harbor.id && !component.port_options.includes(harbor.display_name)){
                            component.port_options.push({"name":harbor.display_name,
                                                        "id":harbor.id,
                                                        "type":"Destination",
                                                        "flag":component.imageFolder.concat(harbor.code.slice(0,2).toLowerCase()).concat(".svg")});
                        }
                    });
                });

                if(component.currentPort == ''){
                    component.currentPort = component.port_options[0];
                }

                component.loaded = true
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

                component.totalsFields["Profits"]["currency_id"] = {
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
                    label: "alphacode",
                };
            },

            /* Single Actions */
            formFieldUpdated(containers_fields) {
                let component = this;

                component.containers_fields = containers_fields;
                component.form_fields = {
                    ...this.vform_fields,
                    ...containers_fields,
                };
            },

            updateTable(port) {
                let component = this;

                component.loaded = false;
                setTimeout(function() {
                    component.loaded = true;
                },100);

                this.currentPortType = port.type;
                if(port.type == 'Origin'){
                    this.currentAddress = this.currentQuoteData.origin_address
                }else{
                    this.currentAddress = this.currentQuoteData.destination_address
                }
            },
        },
    }
</script>