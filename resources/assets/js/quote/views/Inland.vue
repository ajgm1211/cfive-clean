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

                    <h5>
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
                    >
                        ></multiselect
                    >
                </div>
                <!-- End Origen -> Destino -->

                <div
                    class="col-12 col-lg-4 d-flex justify-content-end align-items-center"
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
                <div class="col-12 d-flex">
                    <b-form-checkbox value="carrier" class="mr-4"
                        ><span>Group as:</span>
                    </b-form-checkbox>

                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Forfait"
                        style="width: 8%"
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
            @close="unsetModal"
            @hidden="unsetModal"
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

                    <div class="col-lg-3">
                        <label> ADDRESS </label>
                        <gmap-autocomplete
                            v-if="!modalDistance"
                            @place_changed="setPlace"
                            @input="clearAutocomplete"
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
                            @click="setNewAddress"
                        >
                            + Add Manually
                        </button>
                        <button 
                            v-if="currentQuoteData['type']=='FCL'"
                            class="btn btn-primary btn-bg"
                            @click="searchInlands"
                        >
                            Search
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-5">
                        <!-- DataTable -->
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
                                    <b-th></b-th>

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
                                    <b-td>
                                        <b-form-input
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            v-model="inlandAdd.charge"
                                            placeholder="Choose a charge"
                                        ></b-form-input>
                                    </b-td>

                                    <b-td>
                                        <multiselect
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            v-model="inlandAdd.provider_id"
                                            :options="datalists['providers']"
                                            :show-labels="false"
                                            :close-on-select="true"
                                            :preserve-search="true"
                                            placeholder="Choose a provider"
                                            label="name"
                                            track-by="name"
                                        ></multiselect>
                                    </b-td>

                                    <b-td
                                        v-if="currentQuoteData['type']=='LCL'"
                                        ><b-form-input
                                            v-model="inlandAdd.total"
                                            placeholder="Insert rate"
                                            @blur="totalizeModalInlands"
                                        ></b-form-input>
                                    </b-td>

                                    <b-td
                                        v-for="(item, key) in quoteEquip"
                                        :key="key"
                                    >
                                        <b-form-input
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            :placeholder="item"
                                            v-model="
                                                inlandAdd.price['c' + item]
                                            "
                                            type="number"
                                            class="q-input"
                                            @input="totalizeModalInlands"
                                        ></b-form-input>
                                        <b-form-input
                                            v-if="
                                                inlandAdd.port ==
                                                currentPort['id']
                                            "
                                            :placeholder="item"
                                            v-model="
                                                inlandAdd.markup['m' + item]
                                            "
                                            type="number"
                                            class="q-input"
                                            @input="totalizeModalInlands"
                                        ></b-form-input>
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

                                    <b-td></b-td>

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
    },
    watch: {
        currentPort: function (newVal, oldVal) {
            this.updateTable();
            this.setAddresses();
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
            imageFolder: "/images/flags/1x1/",
            loaded: false,
            isBusy: false,
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
            modalWarning: "",
            modalSearchWarning: false,
            modalDistance: false,
            inlandModalTotals: {},
            inlandModalTotalLcl: 0,
            client_currency: this.currentQuoteData.client_currency,
            /* Table headers */
            fields: [
                {
                    key: "charge",
                    label: "CHARGE",
                    type: "text",
                },
                {
                    key: "provider_id",
                    label: "PROVIDER",
                    type: "select",
                    trackby: "name",
                    options: "providers",
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
                provider_id: {
                    label: "PROVIDER",
                    type: "select",
                    searchable: true,
                    trackby: "name",
                    placeholder: "Select Provider",
                    options: "providers",
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
    mounted() {
        this.createInlandTotals(this.currentAddress["address"]);
    },
    methods: {
        showModal() {
            let component = this;

            component.modalDistance = false;

            component.$refs["addInland"].show();
            component.datalists.distances.forEach(function (distance) {
                if (component.currentPort.id == distance.harbor_id) {
                    component.modalDistance = true;
                    component.distance_options.push(distance);
                }
            });
        },

        setPorts() {
            let component = this;

            component.freights.forEach(function (freight) {
                component.datalists.harbors.forEach(function (harbor) {
                    let portMatch = false;

                    if (freight.origin_port_id == harbor.id) {
                        var harbor_opt = {
                            name: harbor.display_name,
                            id: harbor.id,
                            type: "Origin",
                            flag: component.imageFolder
                                .concat(harbor.code.slice(0, 2).toLowerCase())
                                .concat(".svg"),
                        };
                        portMatch = true;
                    }
                    if (freight.destination_port_id == harbor.id) {
                        var harbor_opt = {
                            name: harbor.display_name,
                            id: harbor.id,
                            type: "Destination",
                            flag: component.imageFolder
                                .concat(harbor.code.slice(0, 2).toLowerCase())
                                .concat(".svg"),
                        };
                        portMatch = true;
                    }

                    if (portMatch) {
                        let inOptions = false;

                        component.port_options.forEach(function (opt) {
                            if (harbor_opt["name"] == opt.name) {
                                inOptions = true;
                            }
                        });

                        if (!inOptions) {
                            component.port_options.push(harbor_opt);
                        }
                    }
                });
            });

            if (component.currentPort == "") {
                component.currentPort = component.port_options[0];
            }

            component.currentAddress = [];

            component.loaded = true;
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
                            if (address["address"] == newAddress && !component.inlandFound) {
                                component.currentAddress = address;
                                component.setModalTable();
                            }
                        });
                    }
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
            }else if(component.currentQuoteData['type']=='LCL'){
                component.totalsFields["Profits"]["profit"] = {
                        type: "text",
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

        setNewAddress() {
            let component = this;
            let addressMatch = false;

            if (component.modalAddress != "") {
                component.address_options.forEach(function (address) {
                    if (component.modalAddress == address["address"]) {
                        component.currentAddress = address;
                    } else {
                        addressMatch = true;
                    }
                });

                if (addressMatch || component.address_options.length == 0) {
                    component.createInlandTotals(component.modalAddress);
                    addressMatch = false;
                } else if (!component.inlandFound) {
                    component.setModalTable();
                }
            } else {
                component.modalWarning = "Address";
                setTimeout(() => {
                    component.modalWarning = "";
                }, 3000);
            }
        },

        createInlandTotals(totalAddress) {
            let component = this;

            if (
                (component.currentAddress != undefined &&
                    Object.keys(this.currentAddress).length != 0) ||
                totalAddress == component.modalAddress
            ) {
                if (this.modalDistance) {
                    var portAddressCombo = [
                        totalAddress["display_name"] +
                        ";" +
                        component.currentPort["type"] +
                        ";" +
                        component.currentPort["id"],
                    ];
                } else {
                    var portAddressCombo = [
                        totalAddress +
                        ";" +
                        component.currentPort["type"] +
                        ";" +
                        component.currentPort["id"],
                    ];
                }

                component.inlandActions
                    .createTotals(portAddressCombo, component.$route)
                    .then((response) => {
                        if (component.modalAddress != "") {
                            if (this.modalDistance) {
                                component.setAddresses(
                                    component.modalAddress["display_name"]
                                );
                            } else {
                                component.setAddresses(component.modalAddress);
                            }
                        } else {
                            component.updateTable();
                        }
                    })
                    .catch((data) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
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
                    component.datalists.providers.forEach(function (prov) {
                        if (prov.id == search["prov_id"]) {
                            newInlandAdd.provider_id = prov;
                        }
                    });

                    component.inlandAdds.push(newInlandAdd);

                    component.totalizeModalInlands();
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
                    newInlandAdd["markups_" + equip] = "";
                });

                component.inlandAdds.push(newInlandAdd);
            }
        },

        deleteModalInland(id) {
            const index = this.inlandAdds.indexOf(this.inlandAdds[id]);

            this.inlandAdds.splice(index, 1);
        },

        totalizeModalInlands() {
            let component = this;

            component.inlandAdds.forEach(function (inlandAdd) {
                let modalInlandCurrency = inlandAdd.currency_id;

                if (modalInlandCurrency["alphacode"] == undefined) {
                    return;
                } else {
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
                            }
    
                            component.inlandModalTotals[
                                "c" + equip
                            ] = totals.toFixed(2);
                        });
                    }else if(component.currentQuoteData['type']=='LCL'){
                        let rates_num = Number(inlandAdd.total);
                        let totals = Number;

                        if (inlandAddCurrency == clientCurrency) {
                                totals = rates_num;
                        } else {
                            let price_usd = Number;

                            price_usd = rates_num / inlandAddConversion;

                            totals = price_usd * clientConversion;
                        }
                        component.inlandModalTotalLcl = totals.toFixed(2);
                    }
                }
            });
        },

        addInland() {
            let component = this;

            component.inlandAdds.forEach(function (inlandAdd) {
                if (Object.keys(inlandAdd.currency_id).length == 0) {
                    component.modalWarning = "Currency";
                    setTimeout(() => {
                        component.modalWarning = "";
                    }, 3000);
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
                            }, 3000);
                        })
                        .catch((data) => {
                            component.$refs.observer.setErrors(
                                data.data.errors
                            );
                        });
                }
            });

            setTimeout(function () {
                component.isBusy = false;
            }, 3000);
        },

        setPlace(place) {
            this.modalAddress = place.formatted_address;
        },

        searchInlands() {
            if (this.modalAddress != "") {
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
                            }, 3000);
                            component.inlandFound = false;
                        } else {
                            component.setModalTable(inlandSearch);
                            component.createInlandTotals(component.modalAddress);
                            component.inlandFound = true;
                        }
                    })
                    .catch((data) => {
                        component.$refs.observer.setErrors(data.data.errors);
                    });
            } else {
                this.modalWarning = "Address";
                setTimeout(() => {
                    this.modalWarning = "";
                }, 3000);
            }
        },

        unsetModal() {
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
                        key: "provider_id",
                        label: "PROVIDER",
                        type: "select",
                        trackby: "name",
                        options: "providers",
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
        }
    },
};
</script>