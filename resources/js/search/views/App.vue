<template>
    <div>

        <Search
            @initialDataLoaded="setDatalists"
            @searchRequested="setSearchStatus"
            @searchSuccess="setSearchData"
            @clearResults="clearDisplay"
            ref="searchComponent"
        ></Search>

        <Recent 
            v-if="foundRates.length == 0 && !searching && requestData.requested == undefined"
            @recentSearch="quickSearch"
        ></Recent>

        <Result 
            v-if="foundRates.length != 0"
            :rates="foundRates"
            :request="searchRequest"
            :datalists="datalists"
        ></Result>

        <APIResults
            v-if="searchRequest.length != 0"
            :request="searchRequest"
            ref="resultsAPI"
        ></APIResults>

    </div>
</template>

<script>
import Search from './Search'; 
import Recent from './Recent';
import Result from './Result'; 
import APIResults from './APIResults'; 

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
        }
    },
    created() {
        this.requestData = this.$route.query;
    },
    methods :
    {
        setDatalists(initialData){
            this.datalists = initialData;
        },

        setSearchStatus(searchRequest){
            this.searching = true;
            this.searchRequest = searchRequest;
            this.$nextTick (()=>{
                this.$refs.resultsAPI.callMaerskAPI();
            })
        },

        setSearchData(searchData){
            //console.log(this.searchData);
            this.searching = false;
            this.foundRates = searchData;
        },

        clearDisplay(){
            this.foundRates = [];
        },

        quickSearch(){
            this.$refs.searchComponent.getQuery();
        },
    },
}
</script>