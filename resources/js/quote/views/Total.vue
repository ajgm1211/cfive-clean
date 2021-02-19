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

                    <!-- Currency Multiselect-->
                    <div class="col-12 col-lg-2 col-sm-4 d-flex mt-5 mb-3">
                        <span>Show in:</span>
                        <multiselect
                            v-model="totalsCurrency"
                            :multiple="false"
                            :options="datalists['currency']"
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

                    <!-- Convert from Multiselect-->
                    <div class="col-12">
                        <div class="col-12 col-lg-2 col-sm-4 d-flex mt-5 mb-3">
                            <span>Convert from:</span>
                            <multiselect
                                v-model="convertFrom"
                                :multiple="false"
                                :options="datalists['currency']"
                                :searchable="true"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :hide-selected="true"
                                :allow-empty="false"
                                label="alphacode"
                                track-by="alphacode"
                                placeholder="Select Currency"
                                @input="updatePdfOptions('currency')"
                            >
                            </multiselect>
                        </div>
                        <div
                            v-if="exchangeSet"
                            class="alert alert-warning"
                            role="alert"
                        >
                            There will be conversion errors if the totals of each tab are not set to this currency!
                        </div>
                    </div>
                    <!-- Convert from Multiselect End-->

                    <!-- Exchange rate input-->
                    <div class="col-12 col-lg-2 col-sm-4 d-flex mt-5 mb-3">
                        <span>Exchange rate:</span>
                        <b-form-input
                            placeholder="Insert rate"
                            v-model="exchangeRate"
                            type="number"
                            class="q-input"
                            @blur="updatePdfOptions('exchange')"
                        ></b-form-input>
                    </div>
                    <!-- Exchange rate input End-->
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
            convertFrom: {},
            exchangeRate: null,
            loaded: false,
            exchangeSet: false,
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

        this.convertFrom = this.pdfOptions["convertFrom"];

        this.exchangeRate = this.pdfOptions["exchangeRate"];

        if(this.exchangeRate != null && this.exchangeRate != undefined && this.convertFrom != null && this.convertFrom != undefined){
            this.exchangeSet = true;
        }

        this.loaded = true;
    },
    methods: {
        updatePdfOptions(updateType) {
            if(updateType == "currency"){
                this.exchangeSet = true;
            }

            let pdfOptions = {
                pdf_options: {
                    allIn: this.pdfOptions["allIn"],
                    showCarrier: this.pdfOptions["showCarrier"],
                    showTotals: this.showTotals,
                    totalsCurrency: this.totalsCurrency,
                    convertFrom: this.convertFrom,
                    exchangeRate: this.exchangeRate,
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
