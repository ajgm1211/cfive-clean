<template>
    <div>

        <Search
            @initialDataLoaded="setDatalists"
            @searchRequested="setSearchStatus"
            @searchSuccess="setSearchData"
            @clearResults="clearDisplay"
            ref="searchComponent"
        ></Search>

        <!-- RESULTS HEADER -->
        <div v-if="resultsTotal > 0" class="container-cards">
        <!-- FILTERS -->
        <div class="row mb-3" style="margin-top: 80px">
        <div class="col-12 col-sm-6 d-flex align-items-center result-and-filter">
            <h2 class="mr-5 t-recent">
            results found: <b>{{ resultsTotal }}</b>
            </h2>
            <!--<div class="d-flex filter-search">
            <b style="color: #80888b !important; letter-spacing: 2px !important"
                >filter by:</b
            >&nbsp;
            <div
                style="
                width: 200px !important;
                height: 33.5px;
                position: relative;
                top: -8px;
                "
            >
                <multiselect
                v-model="filterBy"
                :multiple="false"
                :close-on-select="true"
                :clear-on-select="false"
                :hide-selected="true"
                :show-labels="false"
                :options="filterOptions"
                placeholder="Carrier"
                class="s-input no-select-style"
                
                >
                </multiselect>
                <button
                v-if="filterBy != '' && filterBy != null"
                type="button"
                class="close custom_close_filter"
                aria-label="Close"
                @click="(filterBy = ''), filterCarriers()"
                >
                <span aria-hidden="true">&times;</span>
                </button>
                <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type"></b-icon>
            </div>
            </div>-->
        </div>

        <div class="col-12 col-sm-6 addcontract-createquote">
            <!--<b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>-->

            <b-button
            v-if="!creatingQuote"
            b-button
            variant="primary"
            @click="createQuote"
            >
            {{ requestData.requested == 0 ? "Create Quote" : "Duplicate Quote" }}
            </b-button>

            <b-button v-else b-button variant="primary">
            <div class="spinner-border text-light" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            </b-button>
        </div>
        </div>
        <!-- FIN FILTERS -->

        <div v-if="noRatesAdded" class="alert alert-warning" role="alert">
        Please select at least one Rate to add
        </div>

        <!-- HEADER FCL -->
        <div class="row mt-4 mb-4 result-header">
        <div
            class="col-12 col-sm-2 d-flex justify-content-center align-items-center"
        >
            <b>carrier</b>
        </div>
        <div class="row col-12 col-sm-4"></div>
        <div
            class="row col-12 col-sm-4 d-flex align-items-center justify-content-end"
        >
            <div
            class="col-12 col-sm-2 d-flex justify-content-end"
            v-for="(container, requestKey) in searchRequest.containers"
            :key="requestKey"
            >
            <b>
                {{ container.code }}
            </b>
            </div>
        </div>
        </div>
        <!-- FIN HEADER FCL -->

        <!-- HEADER LCL -->
        <div class="row mt-4 mb-4 result-header" v-if="false">
        <div
            class="col-12 col-sm-2 d-flex justify-content-center align-items-center"
        >
            <b>carrier</b>
        </div>
        <div
            class="row col-12 col-sm-8 d-flex align-items-center justify-content-between"
        >
            <div class="col-12 col-sm-3"><b>ORIGEN</b></div>
            <div class="col-12 col-sm-3 d-flex justify-content-end">
            <b>DESTINO</b>
            </div>
            <div class="col-12 col-sm-6 d-flex justify-content-center">
            <b>PRICE</b>
            </div>
        </div>
        </div>
        <!-- FIN HEADER LCL -->
        </div>
        <!-- FIN RESULTS HEADER -->

        <Recent 
            v-if="resultsTotal == 0"
            @recentSearch="quickSearch"
        ></Recent>

        <Result 
            v-if="foundRates.length != 0"
            :rates="foundRates"
            :request="searchRequest"
            :datalists="datalists"
            @createQuote="createQuote"
            @addedToQuote="setRatesForQuote"
        ></Result>

        <APIResults
            v-if="searchRequest.length != 0"
            :request="searchRequest"
            :datalists="datalists"
            @apiSearchStarted="clearDisplay"
            @apiSearchDone="addApiResults"
            ref="resultsAPI"
        ></APIResults>

    </div>
</template>

<script>
import Search from './Search'; 
import Recent from './Recent';
import Result from './Result'; 
import APIResults from './APIResults'; 
import actions from "../../actions";

export default {
    components: {
        Search,
        Recent,
        Result,
        APIResults
    },
    data() {
        return {
            searching: false,
            searchRequested: false,
            foundRates: [],
            foundCharges: {},            
            searchRequest: [],
            datalists: {},
            requestData: {},
            resultsTotal: 0,
            creatingQuote: false,
            noRatesAdded: false,
            ratesForQuote: [],
            actions: actions,
        }
    },
    created() {
        this.requestData = this.$route.query;
    },
    methods :
    {
        createQuote() {
            let component = this;
            
            component.creatingQuote = true;
            
            if (component.ratesForQuote.length == 0) {
                component.noRatesAdded = true;
                component.creatingQuote = false;
                setTimeout(function () {
                    component.noRatesAdded = false;
                }, 2000);
            } else {
                if (component.requestData.requested == 0) {
                component.actions.quotes
                    .create(component.ratesForQuote, component.$route)
                    .then((response) => {
                    window.location.href =
                        "/api/quote/" + response.data.data.id + "/edit";
                    component.creatingQuote = false;
                    })
                    .catch((error) => {
                    if (error.status === 422) {
                        component.responseErrors = error.data.errors;
                        component.creatingQuote = false;
                    }
                    });
                } else if (component.requestData.requested == 1) {
                component.actions.quotes
                    .specialduplicate(component.ratesForQuote)
                    .then((response) => {
                    window.location.href =
                        "/api/quote/" + response.data.data.id + "/edit";
                    component.creatingQuote = false;
                    })
                    .catch((error) => {
                    if (error.status === 422) {
                        component.responseErrors = error.data.errors;
                        component.creatingQuote = false;
                    }
                    });
                }
            }
        },

        setDatalists(initialData){
            this.datalists = initialData;
        },

        setSearchStatus(searchRequest){
            this.searching = true;
            this.searchRequest = searchRequest;
            this.$nextTick (()=>{
                this.$refs.resultsAPI.callAPIs();
            })
        },

        setSearchData(searchData){
            //console.log(this.searchData);
            this.searching = false;
            this.foundRates = searchData;
            this.resultsTotal += this.foundRates.length;
        },

        clearDisplay(){
            this.foundRates = [];
            this.ratesForQuote = [];
            this.resultsTotal = 0;
        },

        quickSearch(){
            this.$refs.searchComponent.getQuery();
        },

        addApiResults(resultsFound){
            this.resultsTotal += resultsFound;
            if(resultsFound == 0){
                this.$refs.searchComponent.foundApiRates = false;
            }else{
                this.$refs.searchComponent.foundApiRates = true;
            }
        },

        setRatesForQuote(rates){
            this.ratesForQuote = rates;
        },
    },
}
</script>