<template>
    <div>

        <Search
            @initialDataLoaded="setDatalists"
            @searchRequest="setSearchStatus"
            @searchSuccess="setSearchData"
        ></Search>

        <Recent 
            v-if="(Object.keys(foundRates).length == 0 || foundRates.length == 0) && !searching"
        ></Recent>

        <Result 
            v-if="Object.keys(foundRates).length != 0"
            :rates="foundRates"
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
    created() {
        console.log()
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
            console.log(this.searchData);
            this.searching = false;
            this.foundRates = searchData;
            this.searchRequest = searchRequest;
        },
    },
}
</script>