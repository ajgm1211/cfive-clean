<template>
    <div v-if="loaded" style="padding: 0px 25px">

        <div v-if="freights.length == 0">
            <h4>Nothing to totalize. Start by adding a new freight at the Ocean Freight Tab</h4>
        </div>
            <div v-else >
                <!-- Show Totals Checkbox-->
                <div class="card" style="width:100%">
                    <div class="card-body row">
                    <div class="col-12 col-sm-2 d-flex mt-5 mb-3">
                    <b-form-checkbox v-model="showTotals" @input="updatePdfOptions()">
                        <span>Show Totals</span>
                    </b-form-checkbox>
                </div>
                <!-- Show Totals Checkbox End-->

                <!-- Currency Multiselect-->
                <div class="col-12 col-sm-4 d-flex mt-5 mb-3">
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
                        placeholder='Select Currency'
                        @input="updatePdfOptions()">
                    </multiselect>
                </div>
                </div>
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
        actions: Object,
    },
    components: {
        Multiselect,
    },
    data() {
        return{
            showTotals: false,
            totalsCurrency: {},
            loaded: false,
            pdfOptions: {},
        }
    },
    created() {

        if(typeof this.currentQuoteData.pdf_options == "string"){
            this.pdfOptions = JSON.parse(this.currentQuoteData.pdf_options);
        }else{
            this.pdfOptions = this.currentQuoteData.pdf_options;
        }

        this.showTotals = this.pdfOptions['showTotals'];

        this.totalsCurrency = this.pdfOptions['totalsCurrency'];

        this.loaded = true;
    },
    methods: {

        updatePdfOptions(){
            let pdfOptions = {
                pdf_options:
                    {
                    "allIn" : this.pdfOptions['allIn'],
                    "showCarrier": this.pdfOptions['showCarrier'],
                    "showTotals" : this.showTotals,
                    "totalsCurrency" : this.totalsCurrency,
                    }
            };
            
            this.actions.quotes
                .update(this.currentQuoteData['id'], pdfOptions)
                    .then( ( response ) => {
                        let id = this.$route.params.id;

                        this.$emit("freightAdded",id)
                    })
                    .catch(( data ) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
        },

    },
};
</script>
