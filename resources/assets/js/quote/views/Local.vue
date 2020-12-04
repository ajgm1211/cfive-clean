<template>
    <div style="padding: 0px 25px">
        <b-card class="q-card">
            <div class="row justify-content-between">
                <!-- Titulo y Pais -->
                <div class="col-12 col-lg-6 d-flex align-items-center">
                    <h5 style="line-height: 2">
                        <b>Local Charges at:</b>
                    </h5>

                    <multiselect
                        v-model="value"
                        :options="harbors"
                        :multiple="false"
                        :show-labels="false"
                        :close-on-select="true"
                        :preserve-search="true"
                        placeholder="Choose a Port"
                        label="display_name"
                        @input="getValues()"
                        track-by="display_name"
                        class="q-select ml-3"
                    ></multiselect>
                </div>
                <!-- End Titulo y Pais -->

                <!-- Agregar Charges -->
                <div
                    class="col-12 col-lg-6 d-flex justify-content-end align-items-center"
                >
                    <multiselect
                        v-model="template"
                        :options="saleterms"
                        :multiple="false"
                        :show-labels="false"
                        :close-on-select="true"
                        :preserve-search="true"
                        placeholder="Select Sale Template"
                        label="name"
                        track-by="name"
                        @input="getCharges()"
                        class="q-select mr-3"
                        style="position: relative; top: 4px"
                    ></multiselect>

                    <a
                        href="/api/sale_terms"
                        target="_blank"
                        class="btn btn-link mr-4"
                        id="show-btn"
                    >
                        + Add Sale Template
                    </a>
                    <button
                        class="btn btn-primary btn-bg"
                        id="show-btn"
                        @click="showModal"
                    >
                        + Add Charges
                    </button>
                </div>
                <!-- End Agregar Charges -->

                <div class="col-12 mt-5">
                    <!-- DataTable -->
                    <b-table-simple
                        hover
                        small
                        responsive
                        borderless
                        :striped="false"
                    >
                        <!-- Header table -->
                        <b-thead class="q-thead">
                            <b-tr>
                                <b-th>
                                    <span class="label-text">Charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <span class="label-text">{{ item }}</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Currency</span>
                                </b-th>
                            </b-tr>
                        </b-thead>

                        <!-- Body table -->
                        <b-tbody>
                            <b-tr
                                class="q-tr"
                                v-for="(charge, key) in this.charges"
                                :key="key"
                            >
                                <b-td>
                                    <b-form-input
                                        v-model="charge.charge"
                                        class="q-input"
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.charge,
                                                'charge',
                                                1
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>
                                <b-td>
                                    <multiselect
                                        v-model="charge.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a calculation type"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.calculation_type.id,
                                                'calculation_type_id',
                                                1
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <b-form-input
                                        v-model="charge.total['c' + item]"
                                        class="q-input"
                                        @keypress="isNumber($event)"
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.total['c' + item],
                                                'total->c' + item,
                                                1
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>
                                <b-td>
                                    <multiselect
                                        v-model="charge.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.currency.id,
                                                'currency_id',
                                                1
                                            )
                                        "
                                    ></multiselect>
                                </b-td>
                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onDelete(charge.id, 1)"
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

                                <b-td>
                                    <span>
                                        <b>Total</b>
                                    </span>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <span v-if="totals.total">
                                        <b>{{ totals.total["c" + item] }}</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span v-if="loaded">
                                        <multiselect
                                            v-model="totals.currency"
                                            :options="
                                                datalists['filtered_currencies']
                                            "
                                            :multiple="false"
                                            :show-labels="false"
                                            :close-on-select="true"
                                            :preserve-search="true"
                                            placeholder="Select a currency"
                                            label="alphacode"
                                            track-by="alphacode"
                                            @input="
                                                onUpdate(
                                                    totals.id,
                                                    totals.currency.id,
                                                    'currency_id',
                                                    5
                                                )
                                            "
                                        ></multiselect>
                                    </span>
                                </b-td>

                                <b-td></b-td>
                            </b-tr>
                        </b-tbody>
                        <!-- End Body table -->
                    </b-table-simple>
                    <!-- End DataTable -->
                </div>
            </div>
        </b-card>

        <!-- Remarks -->
        <b-card class="mt-5">
            <h5 class="q-title">Remarks</h5>
            <br />
            <ckeditor
                id="inline-form-input-name"
                type="classic"
                v-model="remarks"
                v-on:blur="updateRemarks(remarks)"
            ></ckeditor>
        </b-card>

        <!--  Modal  -->
        <b-modal
            ref="my-modal"
            id="localcharges"
            size="xl"
            centered
            hide-footer
            title="Add Charges"
        >
            <div class="row">
                <div class="col-12 col-lg-6 d-flex alig-items-center">
                    <h5>
                        <b>
                            <span v-if="this.value.type == 1">Origin</span>
                            <span v-if="this.value.type == 2">Destination</span>
                            Costs at:
                        </b>
                    </h5>

                    <span class="ml-3" v-if="this.port != ''">
                        <img
                            v-bind:src="
                                '/images/flags/1x1/' + this.code_port + '.svg'
                            "
                            alt="bandera"
                            width="20"
                            height="20"
                            style="border-radius: 50%"
                        />&nbsp;
                        <b>{{ this.port }}</b>
                    </span>
                </div>

                <div class="col-12 mt-5" style="overflow-y: auto">
                    <!-- DataTable -->
                    <b-table-simple hover small responsive="sm" borderless>
                        <!-- Header table -->
                        <b-thead class="q-thead">
                            <b-tr>
                                <b-th></b-th>

                                <b-th>
                                    <span class="label-text">Charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Show As</span>
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

                                <b-th>
                                    <span class="label-text">Currency</span>
                                </b-th>

                                <b-th></b-th>
                            </b-tr>
                        </b-thead>

                        <b-tbody>
                            <b-tr
                                class="q-tr"
                                v-for="(localcharge, key) in this.localcharges"
                                :key="key"
                            >
                                <b-td>
                                    <b-form-checkbox
                                        v-model="selectedCharges"
                                        :id="'id_' + localcharge.id"
                                        :value="localcharge"
                                    ></b-form-checkbox>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="localcharge.surcharge"
                                        :options="datalists['surcharges']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a surcharge"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.surcharge.id,
                                                'surcharge_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="localcharge.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a calculation type"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.calculation_type.id,
                                                'calculation_type_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="localcharge.sale_codes"
                                        :options="datalists['sale_codes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a sale code"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="
                                            localcharge.automatic_rate.carrier
                                        "
                                        :options="carriers"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        :disabled="true"
                                        placeholder="Choose a provider"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.automatic_rate
                                                    .carrier.id,
                                                'carrier',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <b-form-input
                                        placeholder
                                        v-model="localcharge.price['c' + item]"
                                        class="q-input"
                                        @keypress="isNumber($event)"
                                        v-on:change="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.price['c' + item],
                                                'c' + item,
                                                3
                                            )
                                        "
                                    ></b-form-input>
                                    <b-form-input
                                        placeholder
                                        v-model="localcharge.markup['m' + item]"
                                        class="q-input"
                                        @keypress="isNumber($event)"
                                        v-on:change="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.markup['m' + item],
                                                'm' + item,
                                                4
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="localcharge.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.currency.id,
                                                'currency_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onDelete(localcharge.id, 2)"
                                    >
                                        <i
                                            class="fa fa-times"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>

                            <b-tr
                                class="q-tr"
                                v-for="(input, counter) in inputs"
                                :key="counter"
                            >
                                <b-td></b-td>
                                <b-td>
                                    <multiselect
                                        v-model="input.surcharge"
                                        :options="datalists['surcharges']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a surcharge"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="input.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a calculation type"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="input.sale_codes"
                                        :options="datalists['sale_codes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a sale code"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="input.carrier"
                                        :options="carriers"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a provider"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <b-form-input
                                        placeholder
                                        v-model="input.price['c' + item]"
                                        @keypress="isNumber($event)"
                                        class="q-input"
                                    ></b-form-input>
                                    <b-form-input
                                        placeholder
                                        v-model="input.markup['m' + item]"
                                        @keypress="isNumber($event)"
                                        class="q-input"
                                    ></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="input.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Choose a currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-save"
                                        v-on:click="onSubmitCharge(counter)"
                                    >
                                        <i
                                            class="fa fa-check"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onRemove(counter)"
                                    >
                                        <i
                                            class="fa fa-close"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                    <!-- End DataTable -->
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
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end mb-5 mt-3">
                    <button class="btn btn-link mr-2" @click="add()">
                        + Add New
                    </button>
                    <button
                        class="btn btn-primary btn-bg"
                        @click="onSubmit"
                        @success="closeModal('localcharges')"
                    >
                        + Add Charges
                    </button>
                </div>
            </div>
        </b-modal>
        <!--  End Modal  -->
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import actions from "../../actions";
import FormInlineView from "../../components/views/FormInlineView.vue";
export default {
    components: {
        Multiselect,
        FormInlineView,
    },
    created() {
        let id = this.$route.params.id;

        this.getHarbors(id);

        this.getRemarks(id);
    },
    props: {
        equipment: Object,
        datalists: Object,
        quoteEquip: Array,
        currentQuoteData: Object,
    },
    data() {
        return {
            actions: actions.localcharges,
            currencies: this.datalists.currency,
            openModal: false,
            ids: [],
            sale_codes: [],
            options: [],
            saleterms: [],
            charges: [],
            localcharges: [],
            harbors: [],
            port: [],
            totals: [],
            inputs: [],
            selectedCharges: [],
            carriers: [],
            value: "",
            template: "",
            code_port: "",
            rate_id: "",
            sale_code: "",
            modalWarning: "",
            remarks: null,
            errors: null,
            loaded: false,
            remark_field: {
                localcharge_remarks: {
                    type: "ckeditor",
                    rules: "required",
                    placeholder: "Insert remarks",
                    colClass: "col-sm-12",
                },
            },
        };
    },
    methods: {
        add() {
            if (this.value != "") {
                this.inputs.push({
                    surcharge: "",
                    calculation_type: "",
                    sale_codes: "",
                    price: {},
                    markup: {},
                    currency: "",
                });
            } else {
                this.alert(
                    "You must select a port before create a new charge",
                    "error"
                );
            }
        },
        showModal() {
            this.$refs["my-modal"].show();
        },
        closeModal() {
            this.$refs["my-modal"].hide();
        },
        addSaleCode(value) {
            this.sale_codes.push(value);
        },
        getHarbors(id) {
            actions.localcharges
                .harbors(id)
                .then((response) => {
                    this.harbors = response.data;
                    this.value = this.harbors[0];
                    this.getValues();
                })
                .catch((data) => {
                    //
                });
        },
        getValues() {
            this.getSaleTerms();
            this.getLocalCharges();
            this.getStoredCharges();
            this.getTotal();
            this.getCarriers();
        },
        getSaleTerms() {
            this.saleterms = [];
            this.charges = [];
            this.template = null;
            let data = {
                equipment: this.equipment.id,
                port_id: this.value.id,
                type: this.value.type,
            };
            actions.localcharges
                .saleterms(data)
                .then((response) => {
                    this.saleterms = response.data;
                })
                .catch((data) => {
                    //
                });
        },
        getCharges() {
            this.charges = [];
            this.totals = [];
            let data = {
                id: this.template.id,
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
            };
            actions.localcharges
                .charges(data)
                .then((response) => {
                    this.charges = response.data;
                    this.getTotal();
                })
                .catch((data) => {
                    //
                });
        },
        getStoredCharges() {
            this.charges = [];
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
            };
            actions.localcharges
                .storedCharges(data)
                .then((response) => {
                    this.charges = response.data;
                })
                .catch((data) => {
                    //
                });
        },
        getTotal() {
            this.totals = [];
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
            };
            actions.localcharges
                .total(data)
                .then((response) => {
                    this.totals = response.data;
                    this.loaded = true;
                })
                .catch((data) => {
                    //
                });
        },
        getCarriers() {
            let self = this;
            let quote = this.$route.params.id;
            actions.localcharges
                .carriers(quote)
                .then((response) => {
                    self.carriers = response.data;
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        getLocalCharges() {
            this.localcharges = [];
            this.port = [];
            let self = this;
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type: this.value.type,
            };
            actions.localcharges
                .localcharges(data)
                .then((response) => {
                    self.localcharges = response.data.charges;
                    self.port = response.data.port.display_name;
                    self.code_port = response.data.port.country.code.toLowerCase();
                    self.rate_id = response.data.automatic_rate.id;
                })
                .catch((data) => {
                    //
                });
        },
        getRemarks(id) {
            let self = this;
            actions.localcharges
                .remarks(id)
                .then((response) => {
                    self.remarks = response.data.localcharge_remarks;
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        onDelete(id, type) {
            actions.localcharges
                .delete(id, type)
                .then((response) => {
                    this.alert("Record deleted successfully", "success");
                    this.getTotal();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });

            this.charges = this.charges.filter(function (item) {
                return id != item.id;
            });
            this.localcharges = this.localcharges.filter(function (item) {
                return id != item.id;
            });
        },
        onSubmit() {
            if (this.selectedCharges.length > 0) {
                this.charges = [];
                this.totals = [];
                let data = {
                    selectedCharges: this.selectedCharges,
                    sale_codes: this.sale_codes,
                    quote_id: this.$route.params.id,
                    port_id: this.value.id,
                    type_id: this.value.type,
                };
                actions.localcharges
                    .create(data)
                    .then((response) => {
                        this.charges = response.data;
                        this.getStoredCharges();
                        this.getTotal();
                        this.alert("Record saved successfully", "success");
                        this.closeModal();
                        this.selectedCharges = [];
                    })
                    .catch((data) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
            } else {
                this.alert("You must select a charge at least", "error");
            }
        },
        onSubmitCharge(counter) {
            let data = {
                charges: this.inputs[counter],
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
            };

            actions.localcharges
                .createCharge(data)
                .then((response) => {
                    this.getLocalCharges();
                    this.onRemove(counter);
                    this.getStoredCharges();
                    this.getTotal();
                    this.closeModal();
                    this.alert("Record saved successfully", "success");
                })
                .catch((e) => {
                    let errors_key = Object.keys(e.data.errors);
                    let component = this;
                    errors_key.forEach(function (key) {
                        component.alert(e.data.errors[key][0], "error");
                    });
                });
        },
        onRemove(index) {
            this.inputs.splice(index, 1);
        },
        onUpdate(id, data, index, type) {
            this.totals = [];
            let self = this;
            actions.localcharges
                .update(id, data, index, type)
                .then((response) => {
                    this.getTotal();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        updateRemarks(remarks) {
            let quote_id = this.$route.params.id;

            actions.localcharges
                .updateRemarks(remarks, quote_id)
                .then((response) => {
                    //
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        alert(msg, type) {
            this.$toast.open({
                message: msg,
                type: type,
                duration: 5000,
                dismissible: true,
            });
        },
        isNumber: function (evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (
                charCode > 31 &&
                (charCode < 48 || charCode > 57) &&
                charCode !== 46
            ) {
                evt.preventDefault();
            } else {
                return true;
            }
        },
    },
};
</script>