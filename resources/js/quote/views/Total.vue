<template>
    <div v-if="loaded" style="padding: 0px 25px">
        <div v-if="freights.length == 0">
            <h4>
                Nothing to totalize. Start by adding a new freight at the Ocean
                Freight Tab
            </h4>
        </div>
        <div v-else>
            <div class="card" style="width: 100%">
                <div class="card-body row" style="overflow: inherit">
                    <!-- Show Totals Checkbox-->
                    <div class="col-12 col-lg-2 col-sm-2 d-flex mt-5 mb-3">
                        <b-form-checkbox
                            v-model="showTotals"
                            @input="updatePdfOptions('totalsCheck')"
                        >
                            <span>Show Totals</span>
                        </b-form-checkbox>
                    </div>
                    <!-- Show Totals Checkbox End-->

                    <div class="col">
                        <!-- Currency Multiselect-->
                        <div class="col-12 col-lg-2 col-sm-4 d-flex mt-5 mb-3">
                            <span>Show in:</span>
                            <multiselect
                                v-model="totalsCurrency"
                                :multiple="false"
                                :options="datalists['filtered_currencies']"
                                :searchable="true"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :hide-selected="true"
                                :allow-empty="false"
                                label="alphacode"
                                track-by="alphacode"
                                placeholder="Select Currency"
                                @input="updatePdfOptions('showAs')"
                            >
                            </multiselect>
                        </div>
                        <!-- Currency Multiselect End-->
                        <!-- Exchange rate table-->
                        <div class="col-12 col-lg-2 col-sm-4 d-flex mt-5 mb-3">
                            <span>Exchange rates:</span>

                            <b-table-simple
                                hover
                                small
                                responsive="sm"
                                borderless
                            >
                                <!-- Header table -->
                                <b-thead class="q-thead">
                                    <b-tr>
                                        <b-th>
                                            <span class="label-text">Currency</span>
                                        </b-th>

                                        <b-th>
                                            <span class="label-text">Exchange Rate {{totalsCurrency.alphacode}}</span>
                                        </b-th>

                                    </b-tr>
                                </b-thead>

                                <b-tbody>
                                    <b-tr
                                        v-for="(currency,key) in exchangeRates"
                                        :key = key
                                        class="q-tr"
                                    >
                                        <b-td>
                                            <span>
                                                <b>{{
                                                    currency.alphacode
                                                }}</b>
                                            </span>
                                        </b-td>
                                        <b-td>
                                            <b-form-input v-if="totalsCurrency.alphacode=='USD'"
                                                v-model="currency.exchangeUSD"
                                                @blur="updatePdfOptions"
                                            ></b-form-input>
                                            <b-form-input v-else-if="totalsCurrency.alphacode=='EUR'"
                                                v-model="currency.exchangeEUR"
                                                @blur="updatePdfOptions"
                                            ></b-form-input>
                                        </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <!-- Exchange rate table End-->
                    </div>                    
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
    props: {
        freights: Array,
        datalists: {},
        currentQuoteData: Object,
        actions: Object,
    },
    components: {
        Multiselect,
    },
    data() {
        return {
            showTotals: false,
            totalsCurrency: {},
            exchangeRates: null,
            loaded: false,
            pdfOptions: {},
        };
    },
    created() {
        if (typeof this.currentQuoteData.pdf_options == "string") {
            this.pdfOptions = JSON.parse(this.currentQuoteData.pdf_options);
        } else {
            this.pdfOptions = this.currentQuoteData.pdf_options;
        }

        this.showTotals = this.pdfOptions["showTotals"];

        this.totalsCurrency = this.pdfOptions["totalsCurrency"];

        this.exchangeRates = this.pdfOptions["exchangeRates"];

        this.loaded = true;
    },
    methods: {

        updatePdfOptions(updateType) {
            let pdfOptions = {
                pdf_options: {
                    allIn: this.pdfOptions["allIn"],
                    showCarrier: this.pdfOptions["showCarrier"],
                    showTotals: this.showTotals,
                    totalsCurrency: this.totalsCurrency,
                    exchangeRates: this.exchangeRates,
                },
            };

            this.actions.quotes
                .update(this.currentQuoteData["id"], pdfOptions)
                .then((response) => {
                    let id = this.$route.params.id;

                    this.$emit("freightAdded", id);
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },

    },
};
</script>
