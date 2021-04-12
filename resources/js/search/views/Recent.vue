<template>
    <div class="container-cards">
        <h2 class="mb-5 t-recent" style="margin-top: 80px"><b-icon icon="clock-history" scale="2" variant="secondary" class="mr-3"></b-icon> recent searches</h2>

        <div class="row">

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="recent-search">

                    <img src="/images/logo-ship-blue.svg" alt="bote">

                    <p class="mt-4 mb-0">Lisboa, Lis</p>

                    <div class="direction-spot mt-2 mb-2">
                        <div class="circle"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle"></div>
                    </div>

                    <p class="mb-0">Buenos Aires</p>

                    <b class="mb-4">12-05-2020 / 12-05-2020</b>

                    <a href="#">search again</a>

                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="recent-search">

                    <img src="/images/logo-ship-blue.svg" alt="bote">

                    <p class="mt-4 mb-0">Lisboa, Lis</p>

                    <div class="direction-spot mt-2 mb-2">
                        <div class="circle"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle"></div>
                    </div>

                    <p class="mb-0">Buenos Aires</p>

                    <b class="mb-4">12-05-2020 / 12-05-2020</b>

                    <a href="#">search again</a>

                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="recent-search">

                    <img src="/images/logo-ship-blue.svg" alt="bote">

                    <p class="mt-4 mb-0">Lisboa, Lis</p>

                    <div class="direction-spot mt-2 mb-2">
                        <div class="circle"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle"></div>
                    </div>

                    <p class="mb-0">Buenos Aires</p>

                    <b class="mb-4">12-05-2020 / 12-05-2020</b>

                    <a href="#">search again</a>

                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="recent-search">

                    <img src="/images/logo-ship-blue.svg" alt="bote">

                    <p class="mt-4 mb-0">Lisboa, Lis</p>

                    <div class="direction-spot mt-2 mb-2">
                        <div class="circle"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle"></div>
                    </div>

                    <p class="mb-0">Buenos Aires</p>

                    <b class="mb-4">12-05-2020 / 12-05-2020</b>

                    <a href="#">search again</a>

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
            actions: actions,
            searches: [],
            loaded: false,
        }
    },
    created() {
        actions.search
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
    methods:
    {
        recentSearch(id){
            this.$router.push({ path: `search`, query: { requested: 0, model_id: id} })
        }
    }
}

</script>