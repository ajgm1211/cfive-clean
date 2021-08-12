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
                    <div class="col-lg-12">
                        <!-- Show Totals Checkbox-->
                        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap mt-5 mb-5">
                            <ul class="exchange-rates" style="margin-bottom: 0px !important">
                                <li class="mr-3">
                                    <b class="mr-3">Select PDF:</b>
                                    <multiselect
                                        v-model="selectPDF"
                                        :multiple="false"
                                        :options="selectPDFOptions"
                                        :searchable="false"
                                        :close-on-select="true"
                                        :clear-on-select="false"
                                        :show-labels="false"
                                        :hide-selected="true"
                                        :allow-empty="false"
                                        label="name"
                                        track-by="name"
                                        style="width: 180px"
                                        @input="updatePdfOptions('typePDF')"
                                    >
                                    </multiselect>
                                </li>
                            </ul>
                            
                            <!-- <b-form-checkbox
                                v-model="showTotals"
                                style="width: 120px; top: -4px"
                                @input="updatePdfOptions('totalsCheck')"
                            > -->
                            
                            <span v-show="showTotals" ><b class="mr-3">Show totals in:</b></span>
                            <multiselect
                                v-show="showTotals" 
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
                                @input="updatePdfOptions('showAs')"
                                style="width: 90px"
                                class="change-currency-totals"
                            >
                            </multiselect>

                            <ul v-show="showTotals" class="exchange-rates" style="margin-bottom: 0px !important">
                                <li 
                                    v-for="(currency, key) in exchangeRates"
                                    v-show="totalsCurrency.alphacode != currency.alphacode"
                                    :key="key"
                                    class="mr-3"
                                >
                                    <b class="mr-3">{{ currency.alphacode }} to {{totalsCurrency.alphacode}}:</b>
                                    <b-form-input
                                        v-if="
                                            totalsCurrency.alphacode ==
                                            'USD'
                                        "
                                        v-model="currency.exchangeUSD"
                                        type="number"
                                        @blur="updatePdfOptions('exchangeRates')"
                                        style="width: 90px"
                                    ></b-form-input>
                                    <b-form-input
                                        v-else-if="
                                            totalsCurrency.alphacode ==
                                            'EUR'
                                        "
                                        v-model="currency.exchangeEUR"
                                        type="number"
                                        @blur="updatePdfOptions('exchangeRates')"
                                        style="width: 90px"
                                    ></b-form-input>
                                </li>                               
                            </ul>
                        </div>                       
                        <!-- Show Totals Checkbox End-->
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
            currentQuoteData: {},
            selectPDF: {},
            selectPDFOptions: [
                {id:1, name:"PDF totals only"},
                {id:2, name:"PDF totals + detailed costs"},
                {id:3, name:"PDF detailed costs only"}]
            };
    },
    created() {
        let id = this.$route.params.id;
    
        this.$emit('totalsLoaded', id);
    },
    methods: {
        setInitialOptions(quoteData){
            this.currentQuoteData = quoteData;

            if (typeof this.currentQuoteData.pdf_options == "string") {
                this.pdfOptions = JSON.parse(this.currentQuoteData.pdf_options);
            } else {
                this.pdfOptions = this.currentQuoteData.pdf_options;
            }

            this.showTotals = this.pdfOptions["showTotals"];

            this.totalsCurrency = this.pdfOptions["totalsCurrency"];

            this.exchangeRates = this.pdfOptions["exchangeRates"];
        
            this.selectPDF = this.pdfOptions["selectPDF"];
            
            this.loaded = true;
        },

        updatePdfOptions(updateType) {
            let component = this;
            let newExchangeRates = [];

            if(updateType == 'exchangeRates'){
                component.pdfOptions['exchangeRates'].forEach(function (exRate){
                    exRate['custom'] = true;
                    newExchangeRates.push(exRate);
                });     
                component.exchangeRates = newExchangeRates
            }

            if(updateType == 'typePDF'){
                component.showTotals=false;
                if(component.selectPDF['id']==1 || component.selectPDF['id']==2){
                    component.showTotals=true;
                }
            }

            let pdfOptions = {
                pdf_options: {
                    allIn: component.pdfOptions["allIn"],
                    showCarrier: component.pdfOptions["showCarrier"],
                    showTotals: component.showTotals,
                    totalsCurrency: component.totalsCurrency,
                    exchangeRates: component.exchangeRates,
                    selectPDF: component.selectPDF
                },
            };

            component.actions.quotes
                .update(component.currentQuoteData["id"], pdfOptions)
                .then((response) => {
                    let id = component.$route.params.id;

                    component.$emit("freightAdded", id);
                })
                .catch((data) => {
                    component.$refs.observer.setErrors(data.data.errors);
                });
        },

    },
};
</script>
