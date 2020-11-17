<template>
    <div v-if="loaded" style="padding: 0px 25px">

        <div v-if="freights.length == 0">
            <h4>Nothing to totalize. Start by adding a new freight at the Ocean Freight Tab</h4>
        </div>
            <div class="row">
                <!-- Show Totals Checkbox-->
                <div class="col-12 d-flex mt-5 mb-3">
                    <b-form-checkbox v-model="showTotals">
                        <span>Show Totals</span>
                    </b-form-checkbox>
                </div>
                <!-- Show Totals Checkbox End-->

                <!-- Currency Multiselect-->
                <div class="col-12 d-flex mt-5 mb-3">
                    <span>Show Totals in:</span>
                    <multiselect 
                        v-model="totalsCurrency" 
                        :multiple="false"
                        :options="datalists['currency']"
                        :searchable="true"
                        :close-on-select="true"
                        :clear-on-select="false"
                        :show-labels="false"
                        label="alphacode"
                        track-by="alphacode"
                        placeholder='Select Currency'>
                    </multiselect>
                </div>
                <!-- Currency Multiselect End-->
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
    },
    components: {
        Multiselect,
    },
    data() {
        return{
            showTotals: false,
            totalsCurrency: {},
            loaded: false,
        }
    },
    created() {
        this.totalsCurrency = this.currentQuoteData['client_currency'];
        this.loaded = true;
    },
    methods: {},
};
</script>
