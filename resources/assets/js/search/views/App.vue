<template>
    <div>

        <Search
            @initialDataLoaded="setDatalists"
            @searchRequest="setSearchStatus"
            @searchSuccess="setSearchData"
        ></Search>

        <Recent 
            v-if="Object.keys(foundRates).length == 0 && !searching"
        ></Recent>

        <Result 
            v-if="Object.keys(foundRates).length != 0"
            :rates="foundRates"
            :charges="foundCharges"
            :request="searchRequest"
            :datalists="datalists"
        ></Result>

    </div>
</template>

<script>
import Search from './Search'; 
import Recent from './Recent';
import Result from './Result'; 

export default {
    components: {
        Search,
        Recent,
        Result
    },
    data() {
        return {
            searching: false,
            foundRates: {},
            foundCharges: {},            
            searchRequest: {},
            datalists: {},
        }
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
            this.searching = false;
            this.foundCharges = searchData.charges;
            this.foundRates = searchData.rates;
            this.searchRequest = searchRequest;
        },
    },
}
</script>