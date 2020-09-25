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
                        placeholder="Select Template"
                        label="name"
                        track-by="name"
                        @input="getCharges()"
                        class="q-select mr-3"
                        style="position: relative; top: 4px"
                    ></multiselect>

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
                                                'charge'
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
                                                'calculation_type_id'
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
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.total['c' + item],
                                                'total.c' + item
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
                                                'currency_id'
                                            )
                                        "
                                    ></multiselect>
                                </b-td>
                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onDelete(charge.id)"
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
                                    <span>
                                        <b>EUR</b>
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
                            style="width: 15px; border-radius: 2px"
                        />&nbsp;
                        <b>{{ this.port }}</b>
                    </span>
                </div>

                <div class="col-12 mt-5">
                    <!-- DataTable -->
                    <b-table-simple hover small responsive borderless>
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
                                        v-model="ids"
                                        :id="'id_' + localcharge.id"
                                        :value="localcharge.id"
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
                                    ></multiselect>
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
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="
                                            localcharge.automatic_rate.carrier
                                        "
                                        :options="datalists['carriers']"
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
                                        v-model="localcharge.price['c' + item]"
                                        class="q-input"
                                    ></b-form-input>
                                    <b-form-input
                                        placeholder
                                        v-model="localcharge.markup['m' + item]"
                                        class="q-input"
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
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i
                                            class="fa fa-times"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>

                            <!--<b-tr class="q-total">
                                <b-td></b-td>

                                <b-td></b-td>

                                <b-td></b-td>

                                <b-td></b-td>

                                <b-td>
                                    <span>
                                        <b>Total</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span>
                                        <b>1600</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span>
                                        <b>500</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span>
                                        <b>150</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span>
                                        <b>EUR</b>
                                    </span>
                                </b-td>

                                <b-td></b-td>
                            </b-tr>-->
                        </b-tbody>
                    </b-table-simple>
                    <!-- End DataTable -->
                </div>

                <div class="col-12 d-flex justify-content-end mb-5 mt-3">
                    <button class="btn btn-link mr-2">+ Add New</button>
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
        /* Return the lists data for dropdowns */
        api.getData({}, "/api/quote/local/data/" + id, (err, data) => {
            this.harbors = data;
        });

        this.getRemarks(id);

        actions.quotes
            .retrieve(id)
            .then((response) => {
                this.currentData = response.data.data;
            })
            .catch((data) => {
                this.$refs.observer.setErrors(data.data.errors);
            });
    },
    props: {
        equipment: Object,
        datalists: Object,
        quoteEquip: Array,
    },
    data() {
        return {
            actions: actions.localcharges,
            currencies: this.datalists.currency,
            openModal: false,
            ids: [],
            options: [],
            saleterms: [],
            charges: [],
            localcharges: [],
            harbors: [],
            port: [],
            totals: [],
            value: "",
            template: "",
            code_port: "",
            rate_id: "",
            remark_field: {
                localcharge_remarks: {
                    type: "ckeditor",
                    rules: "required",
                    placeholder: "Insert remarks",
                    colClass: "col-sm-12",
                },
            },
            remarks: "",
            currentData: {},
            datalists: {},
        };
    },
    methods: {
        showModal() {
            this.$refs["my-modal"].show();
        },
        getValues() {
            this.getSaleTerms();
            this.getLocalCharges();
            this.getStoredCharges();
            this.getTotal();
            //this.getRemarks();
        },
        closeModal(modal) {
            this.$bvModal.hide(modal);
        },
        getSaleTerms() {
            this.saleterms = [];
            this.charges = [];
            api.getData(
                {
                    equipment: this.equipment.id,
                    port_id: this.value.id,
                    type: this.value.type,
                },
                "/api/quote/localcharge/saleterm",
                (err, data) => {
                    this.saleterms = data;
                }
            );
        },
        getCharges() {
            this.charges = [];
            api.postData(
                {
                    id: this.template.id,
                    quote_id: this.$route.params.id,
                    port_id: this.value.id,
                    type_id: this.value.type,
                },
                "/api/quote/localcharge/store/salecharge",
                (err, data) => {
                    this.charges = data;
                }
            );
            this.getTotal();
        },
        getStoredCharges() {
            this.charges = [];
            api.getData(
                {
                    quote_id: this.$route.params.id,
                    port_id: this.value.id,
                    type_id: this.value.type,
                },
                "/api/quote/get/localcharge",
                (err, data) => {
                    this.charges = data;
                }
            );
        },
        getTotal() {
            this.totals = [];
            api.getData(
                {
                    quote_id: this.value.quote_id,
                    port_id: this.value.id,
                },
                "/api/quote/localcharge/total",
                (err, data) => {
                    this.totals = data;
                }
            );
        },
        getLocalCharges() {
            this.localcharges = [];
            this.port = [];
            api.getData(
                {
                    quote_id: this.$route.params.id,
                    port_id: this.value.id,
                    type: this.value.type,
                },
                "/api/quote/localcharge",
                (err, data) => {
                    this.localcharges = data.charges;
                    this.port = data.port.display_name;
                    this.code_port = data.port.country.code;
                    this.rate_id = data.automatic_rate_id;
                }
            );
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
        onDelete(id) {
            actions.localcharges
                .delete(id)
                .then((response) => {
                    this.getTotal();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });

            this.charges = this.charges.filter(function (item) {
                return id != item.id;
            });
        },
        onSubmit() {
            this.charges = [];
            this.totals = [];
            let data = {
                ids: this.ids,
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
            };

            actions.localcharges
                .create(data)
                .then((response) => {
                    this.charges = response.data;
                    this.getTotal();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        onUpdate(id, data, index) {
            let self = this;
            actions.localcharges
                .update(id, data, index)
                .then((response) => {
                    //
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
    },
};
</script>