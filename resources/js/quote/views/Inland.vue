<template>
    <div style="padding: 0px 25px">
        <div v-if="freights.length == 0">
            <h4 style="margin: 10px">
                Nothing to display. Start by adding a new freight at the Ocean
                Freight tab
            </h4>
        </div>

        <b-card v-else class="q-card">
            <div class="row justify-content-between">
                <!-- Origen -> Destino -->
                <div class="col-12 col-lg-8 d-sm-flex align-items-center d-none">
                    <h5 class="mb-0"><b>Inland at:</b></h5>

                    <multiselect
                        v-model="currentPort"
                        :options="port_options"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="false"
                        :allow-empty="false"
                        label="name"
                        track-by="name"
                        placeholder="Select Template"
                        class="q-select ml-3"
                    >
                    </multiselect>

                    <img
                        src="https://i.ibb.co/YjfjzkS/delivery-2-1.png"
                        alt="delivery-2-1"
                        border="0"
                        class="mr-4 ml-4"
                        width="25"
                        height="25"
                    />

                    <h5 class="mb-0">
                        <b>From:</b>
                        <img
                            :src="currentPort['flag']"
                            width="20"
                            height="20"
                            style="border-radius: 50%"
                            alt="bandera"
                            class="ml-2 mr-1"
                        />
                    </h5>

                    <multiselect
                        v-model="currentAddress"
                        :options="address_options"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="false"
                        label="address"
                        track-by="address"
                        placeholder="Select an Address"
                        class="q-select ml-3"
                    > </multiselect>
                </div>
                <!-- End Origen -> Destino -->
                <!-- Origen -> Destino RESPONSIVO -->
                <div class="col-12 col-lg-8 resposive-origin-destination">
                    <div class="mb-5 d-flex flex-column align-items-center justify-content-start origin-destination-inland-res">
                        <h5 class="mb-2"><b>Inland at:</b></h5>

                        <multiselect
                            v-model="currentPort"
                            :options="port_options"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            :allow-empty="false"
                            label="name"
                            track-by="name"
                            placeholder="Select Template"
                            class="q-select ml-3"
                        >
                        </multiselect>
                    </div>

                    <div class="mb-5 d-flex flex-column align-items-center justify-content-start origin-destination-inland-res">
                        <h5 class="mb-2"><b>From:</b></h5>

                        <multiselect
                            v-model="currentAddress"
                            :options="address_options"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            label="address"
                            track-by="address"
                            placeholder="Select an Address"
                            class="q-select ml-3"
                        ></multiselect>
                    </div>
                </div>
                <!-- End Origen -> Destino -->

                <div
                    class="col-12 col-lg-4 d-flex justify-content-end align-items-center"
                >                 
                    <a 
                        v-if="
                        loaded &&
                        currentAddress != undefined 
                        "
                        href="#" 
                        class="btn btn-link btn-delete" 
                        id="show-btn" 
                        @click="deleteInland()"
                        >Delete Inland</a
                    >
                    <a
                        href="#"
                        class="btn btn-primary btn-bg"
                        id="show-btn"
                        @click="showModal"
                        >+ Add Inland</a
                    >
                </div>

                <div
                    v-if="
                        currentAddress == undefined ||
                        currentAddress.length == 0
                    "
                    class="warning-no-address"
                >
                    <h5 class="d-flex align-items-center">
                        <img
                            src="/images/advertir.svg"
                            alt="advertir"
                            width="25"
                            height="25"
                            style="margin-right: 10px"
                        />
                        No address registered for this port. Add a new Inland to
                        start.
                    </h5>
                </div>

                <div v-else class="col-12 mt-5">
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
                        :actions="inlandActions"
                        :quoteEquip="quoteEquip"
                        :autoAdd="false"
                        :limitEquipment="true"
                        :totalActions="inlandActions"
                        :paginated="false"
                        :autoupdateDataTable="true"
                        :multiList="true"
                        :multiId="currentPort.id"
                        :portType="currentPort['type']"
                        :portAddress="currentAddress"
                        :massiveactions="['delete']"
                        :singleActions="['delete']"
                        @onFormFieldUpdated="formFieldUpdated"
                    ></DynamicalDataTable>
                    <!-- End DataTable -->
                </div>

                <!-- Checkbox Group Action -->
                <div 
                    class="col-12 responsive-group-action"
                    v-if="
                        currentAddress != undefined
                    "
                >
                    <b-form-checkbox v-model="groupInlands" class="mr-4" @input="updatePdfOptions('checkbox')"
                        ><span>Group as:</span>
                    </b-form-checkbox>

                    <multiselect
                        v-model="groupedAs"
                        :options="currentPortLocalCharges"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="false"
                        label="charge"
                        track-by="charge"
                        placeholder="Local Charge"
                        @input="updatePdfOptions('select')"
                    ></multiselect>
                </div>
                <!-- End Checkbox Group Action -->
            </div>
        </b-card>

        <!--  Modal  -->
        <b-modal
            ref="addInland"
            size="xl"
            centered
            hide-footer
            title="Inland Charges"
            @close="unsetModal(modalOpen=false)"
            @hidden="unsetModal(modalOpen=false)"
        >
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-3">
                        <label> PORT </label>
                        <multiselect
                            v-model="currentPort"
                            :options="port_options"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            label="name"
                            track-by="name"
                        >
                        </multiselect>
                    </div>

                    <div class="col-lg-3" v-if="modalAddressBar">
                        <label> ADDRESS </label>
                        <gmap-autocomplete
                            v-if="!modalDistance"
                            types = ""
                            @place_changed="setPlace"
                            @input="clearAutocomplete"
                            :value="autocompleteValue"
                            class="form-input form-control"
                            placeholder="Start typing an address"
                        >
                        </gmap-autocomplete>

                        <multiselect
                            v-else
                            v-model="modalAddress"
                            :options="distance_options"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            label="display_name"
                            track-by="display_name"
                            placeholder="Select Address"
                        ></multiselect>
                    </div>

                    <div class="col-lg-6 d-flex mt-3 justify-content-end">
                        <button
                            class="btn btn-link mr-2"
                            @click="validateModalAddress('manual')"
                        >
                            + Add Manually
                        </button>
                        <button 
                            v-if="currentQuoteData['type']=='FCL'"
                            class="btn btn-primary btn-bg"
                            @click="validateModalAddress('search')"
                        >
                            Search
                        </button>
                    </div>
                </div>
                <!-- DataTable -->
                <div class="row">
                    <div id="modal-inlandcharges-table" class="col-12 mt-5" >
                        <b-table-simple
                            v-if="inlandAddRequested"
                            hover
                            small
                            responsive="sm"
                            borderless
                        >
                            <!-- Header table -->
                            <b-thead class="q-thead">
                                <b-tr>
                                    <b-th v-if="inlandFound">
                                    </b-th>

                                    <b-th>
                                        <span class="label-text">Charge</span>
                                    </b-th>

                                    <b-th>
                                        <span class="label-text">Provider</span>
                                    </b-th>

                                    <b-th
                                        v-for="(item, key) in quoteEquip"
                                        :key="key"
                                    >
                                        <span class="label-text"
                                            >{{ item }} + Profit</span
                                        >
                                    </b-th>

                                    <b-th
                                        v-if="currentQuoteData['type']=='LCL'"
                                        ><span class="label-text">Rate</span>
                                    </b-th>

                                    <b-th>
                                        <span class="label-text">Currency</span>
                                    </b-th>

                                    <b-th></b-th>
                                </b-tr>
                            </b-thead>

                            <!-- Loader gif -->
                            <b-tbody v-if="isBusy">
                                <b-tr class="b-table-busy-slot">
                                    <b-td :colspan="fields.length" role="cell" class="">
                                        <div class="text-center text-primary my-2">
                                            <b-spinner class="align-middle"></b-spinner>
                                            <strong>Loading...</strong>
                                        </div>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                            <!-- Loader gif -->

                            <b-tbody v-else>
                                <b-tr
                                    class="q-tr"
                                    v-for="(inlandAdd, key) in this.inlandAdds"
                                    :key="key"
                                    
                                >
                                    <b-td v-if="inlandFound">
                                        <b-form-checkbox
                                            v-model="inlandAdd.selected"
                                            @input="totalizeModalInlands"
                                        ></b-form-checkbox>
                                    </b-td>
                                    <b-td>
                                        <b-form-input
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            v-model="inlandAdd.charge"
                                            placeholder="Choose a charge"
                                            class="data-surcharge"
                                        ></b-form-input>
                                    </b-td>

                                    <b-td>
                                        <multiselect
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            v-model="inlandAdd.provider_id"
                                            :options="datalists['carrier_providers']"
                                            :show-labels="false"
                                            :close-on-select="true"
                                            :preserve-search="true"
                                            placeholder="Provider"
                                            label="name"
                                            track-by="name"
                                            class="data-provider"
                                        ></multiselect>
                                    </b-td>

                                    <b-td
                                        v-if="currentQuoteData['type']=='LCL'"
                                        >
                                        
                                        <div style="display: flex; width: 100%">
                                                <b-form-input
                                                    v-model="inlandAdd.total"
                                                    placeholder="Insert rate"
                                                    class="data-profit"
                                                    @blur="totalizeModalInlands"
                                                ></b-form-input>
                                                <b-form-input
                                                    v-model="inlandAdd.profit"
                                                    placeholder="Insert profit"
                                                    class="data-profit"
                                                    @blur="totalizeModalInlands"
                                                ></b-form-input>
                                        </div>
                                        

                                    </b-td>

                                    <b-td
                                        v-for="(item, key) in quoteEquip"
                                        :key="key"
                                        :id="item"
                                    >
                                        <div style="display: flex; width: 100%">
                                            <b-form-input
                                                v-if="
                                                    inlandAdd.port ==
                                                    currentPort['id']
                                                "
                                                
                                                v-model="
                                                    inlandAdd.price['c' + item]
                                                "
                                                type="number"
                                                class="q-input data-profit"
                                                @blur="totalizeModalInlands"
                                            ></b-form-input>
                                            <b-form-input
                                                v-if="
                                                    inlandAdd.port ==
                                                    currentPort['id']
                                                "
                                                
                                                v-model="
                                                    inlandAdd.markup['m' + item]
                                                "
                                                type="number"
                                                class="q-input data-profit"
                                                @blur="totalizeModalInlands"
                                            ></b-form-input>
                                        </div>
                                    </b-td>

                                    <b-td>
                                        <multiselect
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            v-model="inlandAdd.currency_id"
                                            :options="datalists['currency']"
                                            :multiple="false"
                                            :show-labels="false"
                                            :close-on-select="true"
                                            :preserve-search="true"
                                            placeholder="Choose a currency"
                                            label="alphacode"
                                            track-by="alphacode"
                                            class="data-currency"
                                            @input="totalizeModalInlands"
                                        ></multiselect>
                                    </b-td>

                                    <b-td>
                                        <button
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            type="button"
                                            class="btn-delete"
                                            @click="
                                                deleteModalInland(inlandAdd.id)
                                            "
                                        >
                                            <i
                                                class="fa fa-times"
                                                aria-hidden="true"
                                            ></i>
                                        </button>
                                    </b-td>
                                </b-tr>

                                <b-tr class="q-total">
                                    <b-td></b-td>

                                    <b-td v-if="inlandFound"></b-td>

                                    <b-td>
                                        <span>
                                            <b>Total</b>
                                        </span>
                                    </b-td>

                                    <b-td
                                        v-if="currentQuoteData['type'] == 'LCL'"
                                        ><span>
                                            <b>{{inlandModalTotalLcl}}</b>
                                        </span>
                                    </b-td>

                                    <b-td
                                        v-for="(item, key) in quoteEquip"
                                        :key="key"
                                    >
                                        <span>
                                            <b>{{
                                                inlandModalTotals["c" + item]
                                            }}</b>
                                        </span>
                                    </b-td>

                                    <b-td>
                                        <span>
                                            <b>{{
                                                client_currency.alphacode
                                            }}</b>
                                        </span>
                                    </b-td>

                                    <b-td></b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                        <!-- End DataTable -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div
                            v-if="modalWarning != ''"
                            class="alert alert-danger"
                            role="alert"
                        >
                            {{ modalWarning + " cannot be empty" }}
                        </div>

                        <div
                            v-if="modalSelectWarning"
                            class="alert alert-warning"
                            role="alert"
                        >
                            Select an Inland to add
                        </div>

                        <div
                            v-if="modalSearchWarning"
                            class="alert alert-danger"
                            role="alert"
                        >
                            No results for this particular port
                        </div>

                        <div
                            v-if="modalSuccess"
                            class="alert alert-success"
                            role="alert"
                        >
                            Inlands added successfully!
                        </div>

                        <div
                            v-if="searchAdded"
                            class="alert alert-warning"
                            role="alert"
                        >
                            All Inlands for this port added
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 d-flex justify-content-end mb-5 mt-3">
                        <button
                            class="btn btn-primary btn-bg"
                            @click="addInland"
                        >
                            Add Inland
                        </button>
                    </div>
                </div>
            </div>
        </b-modal>
        <!--  End Modal  -->
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import DynamicalDataTable from "../../components/views/DynamicalDataTable";
import * as VueGoogleMaps from "vue2-google-maps";
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
        localCharges: Array,
    },
    watch: {
        currentPort: function (newVal, oldVal) {
            this.setAddresses();
            this.setGroupingOptions();
        },

        currentAddress: function (newVal, oldVal) {
            this.updateTable();
        },
    },
    data() {
        return {
            openModal: false,
            vdata: {},
            value: "",
            ids: [],
            groupedAs: {},
            groupInlands: false,
            currentPortLocalCharges: [],
            imageFolder: "/images/flags/1x1/",
            loaded: false,
            isBusy: false,
            port_options: [],
            currentPort: "",
            address_options: [],
            currentAddress: {},
            distance_options: [],
            modalAddress: "",
            modalSuccess: false,
            modalSelected: false,
            inlandFound: false,
            inlandAddRequested: false,
            inlandAdds: [],
            inlandActions: {},
            autocompleteValue: null,
            modalWarning: "",
            modalSearchWarning: false,
            modalSelectWarning: false,
            modalDistance: false,
            modalOpen: false,
            modalAddressBar: true,
            inlandModalTotals: {},
            inlandModalTotalLcl: 0,
            searchAdded: false,
            client_currency: this.currentQuoteData.client_currency,
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
                    disabled: true,
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
                    placeholder: "Enter charge",
                },
                provider: {
                    label: "CHARGE",
                    type: "text",
                    rules: "required",
                    placeholder: "Enter provider",
                },
                provider_id: {
                    label: "PROVIDER",
                    type: "select",
                    searchable: true,
                    trackby: "name",
                    placeholder: "Select Provider",
                    options: "carrier_providers",
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
        };
    },
    created() {
        this.setLclFields();

        this.setPorts();

        this.setTotalsFields();

    },
    methods: {
        showModal() {
            let component = this;

            component.modalDistance = false;

            component.$refs["addInland"].show();
            component.modalOpen = true;
            component.changeModalAddressBar();
        },

        setPorts() {
            let component = this;
            
            component.inlandActions
                .harbors(component.$route)
                .then((response) => {
                    response.data.forEach(function(port){
                        var portMatch = false;

                        port.flag = component.imageFolder
                                .concat(port.code.slice(0, 2).toLowerCase())
                                .concat(".svg");
                        
                        component.port_options.forEach(function (opt){
                            if(opt.id == port.id){
                                portMatch = true;
                            }
                        });
                        
                        if(!portMatch){
                            component.port_options.push(port)
                        }
                    })
                    if (component.currentPort == "") {
                        component.currentPort = component.port_options[0];
                    }

                    component.currentAddress = [];

                    component.loaded = true;
                })
                .catch((data) => {
                    component.$refs.observer.setErrors(data.data.errors);
            });
        },

        setAddresses(newAddress = null) {
            let component = this;
            
            component.inlandActions
                .retrieveAddresses(
                    component.currentPort["id"],
                    component.$route
                )
                .then((response) => {
                    component.address_options = response.data.data;
                    if (newAddress == null) {
                        component.currentAddress = component.address_options[0];
                    } else {
                        component.address_options.forEach(function (address) {
                            if (address["address"] == newAddress) {
                                component.currentAddress = address;
                            }
                        });
                    }
                    if(component.modalOpen){
                        component.changeModalAddressBar();
                    }
                    component.getPdfOptions();
                })
                .catch((data) => {
                    component.$refs.observer.setErrors(data.data.errors);
                });

            if (component.inlandAdds != []) {
                let valid = false;

                component.inlandAdds.forEach(function (inlandAdd) {
                    if (inlandAdd["port"] == component.currentPort["id"]) {
                        valid = true;
                    }
                });

                if (!valid) {
                    component.inlandAddRequested = false;
                }
            }

        },

        setTotalsFields() {
            let component = this;

            if(component.currentQuoteData['type']=='FCL'){
                component.quoteEquip.forEach(function (eq) {
                    component.totalsFields["Profits"]["profits_".concat(eq)] = {
                        type: "text",
                        disabled: true,
                    };
                    component.totalsFields["Totals"]["totals_".concat(eq)] = {
                        type: "text",
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
            }else if(component.currentQuoteData['type']=='LCL'){
                component.totalsFields["Profits"]["profit"] = {
                        type: "text",
                        disabled: true,
                    };
                component.totalsFields["Totals"]["lcl_totals"] = {
                        type: "span",
                    };
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
            }
        },

        formFieldUpdated(containers_fields) {
            if(this.currentQuoteData['type']=='FCL'){
                let component = this;
    
                component.containers_fields = containers_fields;
                component.form_fields = {
                    ...this.vform_fields,
                    ...containers_fields,
                };
            }
        },

        updateTable() {
            let component = this;

            component.loaded = false;
            setTimeout(function () {
                component.loaded = true;
            }, 100);
        },

        validateModalAddress(type) {
            let component = this;

            if(!["",null].includes(component.modalAddress)) {
                component.autocompleteValue = component.modalAddress;
                if(type == "manual"){
                    component.setModalTable();
                }else if(type == "search"){
                    component.searchInlands();
                }
            } else {
                component.modalWarning = "Address";
                setTimeout(() => {
                    component.modalWarning = "";
                }, 1500);
            }
        },

        setModalTable(inlandSearch = null) {
            let highest = Number;
            let addId = Number;
            let newInlandAdd = {};
            let component = this;

            component.inlandAddRequested = true;
            if (inlandSearch != null) {
                inlandSearch.forEach(function (search) {
                    var alreadyAdded = false;
                    component.inlandAdds.forEach(function(add){
                        if(search["providerName"] == add["charge"]){
                            alreadyAdded = true;
                        }
                    });

                    if(!alreadyAdded || component.inlandAdds.length == 0){
                        if (component.ids.length != 0) {
                        highest = component.ids.sort(function (a, b) {
                            return b - a;
                        });
                        component.ids.push(highest[0] + 1);
                        addId = highest[0] + 1;
                    } else {
                        component.ids.push(0);
                        addId = 0;
                    }

                    newInlandAdd = {
                        id: component.ids[addId],
                        port: component.currentPort["id"],
                        charge: "",
                        address: "",
                        type: "",
                        provider_id: {},
                        currency_id: {},
                        price: {},
                        markup: {},
                        selected: false,
                        distance: 0,
                    };

                    component.quoteEquip.forEach(function (equip) {
                        newInlandAdd.price["c" + equip] = "";
                        newInlandAdd.markup["m" + equip] = "";
                        newInlandAdd["rates_" + equip] = "";
                    });

                    newInlandAdd["charge"] = search["providerName"];
                    component.quoteEquip.forEach(function (equip) {
                        if(search["inlandDetails"][equip]!=null){
                            newInlandAdd.price["c" + equip] =
                                search["inlandDetails"][equip]["montoInlandT"];
                            newInlandAdd.markup["m" + equip] =
                                search["inlandDetails"][equip]["markup"];
                            newInlandAdd["rates_" + equip] =
                                search["inlandDetails"][equip]["montoInlandT"];
                            newInlandAdd.markup["markups_" + equip] =
                                search["inlandDetails"][equip]["markup"];
                        }else{
                            newInlandAdd.price["c" + equip] = 0;
                            newInlandAdd.markup["m" + equip] = 0;
                            newInlandAdd["rates_" + equip] = 0;
                            newInlandAdd["markups_" + equip] = 0;
                        }
                    });
                    component.datalists.currency.forEach(function (curr) {
                        if (curr.alphacode == search["type_currency"]) {
                            newInlandAdd.currency_id = curr;
                        }
                    });
                    //Solo se está considerando proveedores de tipo "providers". A la fecha no existe una logica correcta para registrar carriers como providers. 
                    component.datalists.providers.forEach(function (prov) {
                        if (prov.id == search["provider_id"]) {
                            newInlandAdd.provider_id = prov;
                        }
                    });

                    component.inlandAdds.push(newInlandAdd);

                    component.totalizeModalInlands();
                    }else if(alreadyAdded){
                        component.searchAdded = true;
                        setTimeout(() => {
                            component.searchAdded = false;
                        }, 2000);
                    }
                });
            } else {
                if (component.ids.length != 0) {
                    highest = component.ids.sort(function (a, b) {
                        return b - a;
                    });
                    component.ids.push(highest[0] + 1);
                    addId = highest[0] + 1;
                } else {
                    component.ids.push(0);
                    addId = 0;
                }

                newInlandAdd = {
                    id: component.ids[addId],
                    port: component.currentPort["id"],
                    charge: "",
                    address: "",
                    type: "",
                    total: "",
                    profit: "",
                    provider_id: {},
                    currency_id: {},
                    price: {},
                    markup: {},
                    selected: true,
                    distance: 0,
                };

                component.quoteEquip.forEach(function (equip) {
                    newInlandAdd.price["c" + equip] = "";
                    newInlandAdd.markup["m" + equip] = "";
                    newInlandAdd["rates_" + equip] = "";
                    newInlandAdd["markups_" + equip] = "";
                });

                component.inlandAdds.push(newInlandAdd);
            }
        },

        deleteModalInland(id) {
            const index = this.inlandAdds.indexOf(this.inlandAdds[id]);

            this.inlandAdds.splice(index, 1);

            this.inlandModalTotals = {};
            this.inlandModalTotalLcl = 0;

            this.totalizeModalInlands();
        },

        totalizeModalInlands() {
            let component = this;

            this.inlandModalTotals = {};
            this.inlandModalTotalLcl = 0;

            component.inlandAdds.forEach(function (inlandAdd) {
                let modalInlandCurrency = inlandAdd.currency_id;

                if (modalInlandCurrency["alphacode"] == undefined) {
                    return;
                } else if (inlandAdd.selected){
                    let inlandAddCurrency = modalInlandCurrency["alphacode"];
                    let inlandAddConversion = modalInlandCurrency["rates"];
                    let clientCurrency =
                        component.currentQuoteData.client_currency["alphacode"];
                    let clientConversion =
                        component.currentQuoteData.client_currency["rates"];

                    if(component.currentQuoteData['type']=='FCL'){
                        component.quoteEquip.forEach(function (equip) {
                            let rates_num = Number(inlandAdd.price["c" + equip]);
                            let markup_num = Number(inlandAdd.markup["m" + equip]);
                            let totals = Number;
    
                            inlandAdd["rates_" + equip] = rates_num;
                            inlandAdd["markups_" + equip] = markup_num;
    
                            if (inlandAddCurrency != clientCurrency) {
                                let totals_usd = Number;
    
                                totals_usd = (rates_num / inlandAddConversion) + (markup_num /inlandAddConversion);
    
                                totals = totals_usd * clientConversion;
                            } else {
                                totals = rates_num + markup_num;
                            }

                            if(component.inlandModalTotals["c" + equip] == undefined ){
                              component.inlandModalTotals[
                                "c" + equip
                            ] = totals;  
                            }else{
                                component.inlandModalTotals[
                                    "c" + equip
                                ] += totals;
                            }
                        });
                    }else if(component.currentQuoteData['type']=='LCL'){
                        let rates_num = Number(inlandAdd.total);
                        let profit_num = Number(inlandAdd.profit);
                        let totals = Number;

                        if (inlandAddCurrency == clientCurrency) {
                                totals = rates_num + profit_num;
                        } else {
                            let price_usd = Number;

                            price_usd = (rates_num + profit_num) / inlandAddConversion;

                            totals = price_usd * clientConversion;
                        }
                        component.inlandModalTotalLcl += totals;
                    }
                }
            });
            component.setDecimals();
        },

        addInland() {
            let component = this;
            let noSelection = true;

            component.autocompleteValue = component.modalAddress;

            component.inlandAdds.forEach(function (inlandAdd) {
                if(inlandAdd.selected){
                    noSelection = false;
                }
            });

            component.inlandAdds.forEach(function (inlandAdd) {
                if (Object.keys(inlandAdd.currency_id).length == 0) {
                    component.modalWarning = "Currency";
                    setTimeout(() => {
                        component.modalWarning = "";
                    }, 1500);
                } else if (inlandAdd.selected == false){
                    if(noSelection){
                        component.modalSelectWarning = true;
                        setTimeout(() => {
                            component.modalSelectWarning = false;
                        }, 1500);
                    }
                } else {
                    inlandAdd["type"] = component.currentPort["type"];
                    if (component.modalDistance) {
                        inlandAdd["address"] =
                            component.modalAddress.display_name;
                        inlandAdd["distance"] =
                            component.modalAddress.distance;
                    } else {
                        inlandAdd["address"] = component.modalAddress;
                    }

                    component.isBusy = true;

                    setTimeout(function (){
                        component.inlandActions
                            .create(
                                component.currentPort["id"],
                                inlandAdd,
                                component.$route
                            )
                            .then((response) => {
                                component.inlandAddRequested = false;
                                component.inlandAdds.splice(
                                    component.inlandAdds.indexOf(inlandAdd)
                                );
                                component.totalizeModalInlands();
                                component.modalSuccess = true;
                                component.updateTable();
                                component.isBusy = false;
                                setTimeout(function () {
                                    component.$refs["addInland"].hide();
                                    component.inlandAddRequested = false;
                                    component.modalSuccess = false;
                                    if(component.modalDistance){
                                        component.setAddresses(component.modalAddress['display_name']);
                                    }else{
                                        component.setAddresses(component.autocompleteValue);
                                    }
                                }, 1500);
                            })
                            .catch((data) => {
                                component.$refs.observer.setErrors(
                                    data.data.errors
                                );
                            });
                    }, (component.inlandAdds.indexOf(inlandAdd) + 1) * 1000);
                }
            });

            setTimeout(function () {
                component.isBusy = false;
            }, 2000);
        },

        setPlace(place) {
            this.modalAddress = place.formatted_address;
        },

        searchInlands() {
            let data = {};
            let inlandSearch = {};
            let component = this;

            data["address"] = component.modalAddress;
            if (component.modalDistance) {
                data["distance"] = component.modalAddress.distance;
            } else {
                data["distance"] = 0;
            }

            component.inlandActions
                .search(component.currentPort["id"], data, component.$route)
                .then((response) => {
                    inlandSearch = response.data;
                    if (inlandSearch.length == 0) {
                        component.modalSearchWarning = true;
                        setTimeout(() => {
                            component.modalSearchWarning = false;
                        }, 1500);
                        component.inlandFound = false;
                    } else {
                        component.setModalTable(inlandSearch);
                        component.inlandFound = true;
                    }
                })
                .catch((data) => {
                    component.$refs.observer.setErrors(data.data.errors);
                });
            
        },

        unsetModal() {
            this.modalOpen = false;
            this.inlandAdds = [];
            this.inlandAddRequested = false;
            this.inlandModalTotals = {};
            this.inlandFound = false;
            this.modalAddress = "";
            this.inlandModalTotalLcl = 0;
        },

        clearAutocomplete() {
            this.modalAddress = "";
        },

        setLclFields(){
            if(this.currentQuoteData['type']=='FCL'){
                this.inlandActions = this.actions.automaticinlands;
            }else if(this.currentQuoteData['type']=='LCL'){
                this.inlandActions = this.actions.automaticinlandslcl;
                this.vform_fields = {
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
                        placeholder: "Enter provider",
                    },
                    provider_id: {
                        label: "PROVIDER",
                        type: "select",
                        searchable: true,
                        trackby: "name",
                        placeholder: "Select Provider",
                        options: "providers",
                    },
                    total:{
                        label: "RATE",
                        type: "text",
                        rules: "required"
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
                };
                this.fields = [
                    {
                        key: "charge",
                        label: "CHARGE",
                        type: "text",
                    },
                    {
                        key: "provider",
                        label: "PROVIDER",
                        type: "text",
                        disabled: true,
                    },
                    {
                        key: "total",
                        label: "RATE",
                        type: "text",
                    },
                    {
                        key: "currency_id",
                        label: "CURRENCY",
                        type: "select",
                        trackby: "alphacode",
                        options: "currency",
                    },
                ];
            }
        },

        setDecimals(){
            let component = this;

            component.inlandAdds.forEach(function (inlandAdd) {
                if(component.currentQuoteData['type']=='FCL'){
                    component.quoteEquip.forEach(function (equip) {
                        if(inlandAdd.price["c" + equip]){
                            if(component.currentQuoteData['decimals'] == 1){
                                inlandAdd.price["c" + equip] = Number(inlandAdd.price["c" + equip]).toFixed(2);
                            }else if(component.currentQuoteData['decimals']==0){
                                inlandAdd.price["c" + equip] = Math.trunc(Number(inlandAdd.price["c" + equip]));
                            }
                        }
                        if(inlandAdd.markup["m" + equip]){
                            if(component.currentQuoteData['decimals'] == 1){
                                inlandAdd.markup["m" + equip] = Number(inlandAdd.markup["m" + equip]).toFixed(2);
                            }else if(component.currentQuoteData['decimals']==0){
                                inlandAdd.markup["m" + equip] = Math.trunc(Number(inlandAdd.markup["m" + equip]));
                            }
                        }
                    });                    
                }else if(component.currentQuoteData['type']=='LCL'){
                    if (inlandAdd.total) {
                        if(component.currentQuoteData['decimals'] == 1){
                            inlandAdd.total = Number(inlandAdd.total).toFixed(2);
                        }else if(component.currentQuoteData['decimals']==0){
                            inlandAdd.total = Math.trunc(Number(inlandAdd.total));
                        }
                    }
                }
            });
            component.quoteEquip.forEach(function (equip) {
                if(Object.keys(component.inlandModalTotals).length != 0){
                    if(component.currentQuoteData['decimals'] == 1){
                        component.inlandModalTotals["c" + equip] = Number(component.inlandModalTotals["c" + equip]).toFixed(2);
                    }else if(component.currentQuoteData['decimals']==0){
                        component.inlandModalTotals["c" + equip] = Math.trunc(Number(component.inlandModalTotals["c" + equip]));
                    }
                }
            });
            if(component.inlandModalTotalLcl!=0){
                if(component.currentQuoteData['decimals'] == 1){
                    component.inlandModalTotalLcl = component.inlandModalTotalLcl.toFixed(2);
                }else if(component.currentQuoteData['decimals'] == 0){
                    component.inlandModalTotalLcl = Math.trunc(component.inlandModalTotalLcl);
                }
            }
        },

        changeModalAddressBar(){
            let component = this;
            
            component.modalAddressBar = false;
            component.modalDistance = false;

            component.inlandActions
                .getHarborAddresses(component.currentPort.id)
                .then((response) => {
                    if(response.data.data.length > 0){
                        component.modalDistance = true;
                        component.distance_options = response.data.data;
                        component.distance_options.forEach(function (distance){
                            if(component.currentAddress != undefined){
                                if(distance["display_name"] == component.currentAddress["address"]){
                                    component.modalAddress = distance;
                                }
                            }
                        });
                    }
                    
                    if(!component.modalDistance){
                        if(component.currentAddress != undefined &&
                        Object.keys(component.currentAddress).length != 0){
                            component.autocompleteValue = component.currentAddress["address"]
                            component.modalAddress = component.currentAddress["address"];
                        }else{
                            component.autocompleteValue = null;
                            component.modalAddress = null;
                        }            
                    }
                
                    setTimeout(() => {
                        component.modalAddressBar = true;                
                    }, 100);
                })
                .catch((error) => {
                    component.$refs.observer.setErrors(error.data.errors);
                });
        },

        deleteInland(){
            let component = this;

            if (
                (component.currentAddress != undefined &&
                    Object.keys(this.currentAddress).length != 0) 
            ) {
                var portAddressCombo = [
                    component.currentAddress["address"] +
                    ";" +
                    component.currentPort["id"]
                ];
            
            component.inlandActions
                .deleteFull(portAddressCombo, component.$route)
                .then((response) => {
                    component.setAddresses();                        
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
            }
        },

        setGroupingOptions(){
            let component = this;

            component.currentPortLocalCharges = [];

            component.localCharges.forEach(function (charge){
                if(charge.port_id == component.currentPort.id){
                    component.currentPortLocalCharges.push(charge);
                }
            });
        },

        getPdfOptions(){
            let component = this;

            if(component.currentAddress != undefined || Object.keys(component.currentAddress).length != 0){
                let portAddressCombo = [
                    component.currentPort.id + ";" + component.currentAddress.id,
                ];
    
                component.inlandActions
                    .retrieveTotals(portAddressCombo, component.$route)
                    .then((response) => {
                        component.groupInlands = response.data.data.pdf_options.grouped;
                        component.currentPortLocalCharges.forEach(function(chargeOption){
                            if(response.data.data.pdf_options.groupId == chargeOption.id){
                                component.groupedAs = chargeOption;
                            }
                        })
                    })
                    .catch((data) => {
                        component.$refs.observer.setErrors(data.data.errors);
                    });
            }
        },

        updatePdfOptions(source){
            if(!this.groupInlands){
                if(source == "checkbox"){
                    this.groupedAs = {};
                }else if(source == "select"){
                    this.groupInlands = true;
                }
            }else if(this.groupInlands && Object.keys(this.groupedAs).length == 0){
                if(Object.keys(this.currentPortLocalCharges).length == 0){
                    this.groupInlands = false;
                }else{
                    this.groupedAs = this.currentPortLocalCharges[0];
                }
            }
            
            let pdfOptions = {
                pdf_options: {
                    grouped: this.groupInlands,
                    groupId: this.groupedAs.id ? this.groupedAs.id : this.groupedAs,
                },
            };

            this.inlandActions
                .updatePdfOptions(this.currentPort.id, pdfOptions, this.$route)
                .then((response) => {
                    console.log("Done!")
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
    },
};
</script>