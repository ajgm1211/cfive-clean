<template>
    <div>

        <Search
            @initialDataLoaded="setDatalists"
            @searchRequested="setSearchStatus"
            @searchSuccess="setSearchData"
            @clearResults="clearDisplay"
        ></Search>

        <Recent 
            v-if="foundRates.length == 0 && !searching && requestData.requested == undefined"
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

        setSearchStatus(){
            this.searching = true;
        },

        setSearchData(searchData,searchRequest){
            //console.log(this.searchData);
            this.searching = false;
            this.foundRates = searchData;
            this.searchRequest = searchRequest;
            this.$nextTick (()=>{
                this.$refs.resultsAPI.callMaerskAPI();
            })
        },

        clearDisplay(){
            this.foundRates = [];
        },
    },
}
</script>