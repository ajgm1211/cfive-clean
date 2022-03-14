<template>
    <div class="pr-5 pl-5">

        <h2 class="mb-5 t-recent" style="margin-top: 80px"><b-icon icon="clock-history" scale="2" variant="secondary" class="mr-3"></b-icon> recent searches</h2>

        <div
            v-if="loaded"
            class="row"
        >

            <div 
                v-for="(search,searchKey) in searches"
                :key="searchKey"
                class="col-12 col-md-6 col-xl-3"
            >
                <div class="recent-search mb-3">

                    <img src="/images/logo-ship-blue.svg" alt="bote">

                    <p class="mt-4 mb-0" style="padding: 0px 65px">{{ search.origin_address.length ? search.origin_address[0].location : search.origin_ports[0].display_name }}</p>

                    <div class="direction-spot mt-2 mb-2">
                        <div class="circle"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle"></div>
                    </div>

                    <p class="mb-0" style="padding: 0px 10px">{{ search.destination_address.length ? search.destination_address[0].location : search.destination_ports[0].display_name }}</p>

                    <b class="mb-4">{{ search.pick_up_date }}</b>

                    <a 
                        @click="recentSearch(search.id)"
                        style="color:white"
                    >
                    search again
                    </a>

                </div>
            </div>
        </div>

    </div>
</template>

<script>
import actions from "../../actions";

export default {
    data() {
        return {
            searchActions: {},
            searches: [],
            actions: actions,
            loaded: false,
            searchType: "FCL",
        }
    },
    created() {
        this.setActions();
    },
    methods:
    {
        setActions() {
            if(this.searchType == "FCL"){
                this.searchActions = this.actions.search;
            }else if(this.searchType == "LCL"){
                this.searchActions = this.actions.searchlcl;
            }

            this.setRecentList();
        },

        setRecentList(){
            let component = this;
        
            component.searchActions
                .list({})
                .then((response) => {
                    this.searches = response.data.data;
                    this.loaded = true;
                })
                .catch(error => {
                    if(error.status === 403) {
                        console.log(error.data.errors);
                    }
                })    
        },

        recentSearch(id){
            this.$router.push({ path: `search`, query: { requested: 0, model_id: id} });
            this.$emit("recentSearch");
        }
    }
}

</script>