<template>
    <div class="pr-5 pl-5">

        <h2 class="mt-5 mb-3 t-recent">results found: <b>10</b></h2>

        <div class="row">

            <div class="col-2">carrier</div>
            <div class="col-10">204040</div>

        </div>

        <div class="row">

            <div class="col-12 mb-4">
                <div class="result-search">

                   <div class="row">

                       <div class="col-12 col-sm-2">logo</div>

                       <div class="row col-12 col-sm-8">

                           <div class="col-12">
                               <h6 class="mt-2 mb-2">contact reference title</h6>
                           </div>

                           <div class="row col-12">

                               <div class="origin">

                                   <span>origin</span>
                                   <p>Lisboa, Lis</p>

                               </div>
                               <div class="via d-flex flex-column justify-content-center align-items-center">

                                   <div class="direction-form">

                                       <img src="" alt="">

                                       <div class="line-route-direct">
                                           <div class="circle"></div>
                                           <div class="line"></div>
                                           <div class="circle fill"></div>
                                       </div>

                                   </div>
                                   <div class="direction-desc">

                                       <b>direct</b>
                                       <p><b>TT:</b> 45 Days</p>

                                   </div>

                               </div>
                               <div class="destination">

                                   <span>destination</span>
                                   <p>Buenos Aires, Arg</p>

                               </div>

                           </div>

                           <div class="col-12 result-action d-flex justify-content-between">

                                <div class="d-flex">
                                    <p class="mr-2"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>
                                </div>

                                <div>
                                    <b-button v-b-toggle.remarks class="rs-btn">remarks</b-button>
                                    <b-button v-b-toggle.detailed class="rs-btn">detailed cost</b-button>
                                </div>

                            </div>

                        </div>

                       <div class="col-12 col-sm-2">

                            <b-form-checkbox v-model="checked1" name="check-button" button>
                                <b>add to quote</b>
                            </b-form-checkbox>

                       </div>

                   </div>

                </div>
                
                <b-collapse id="detailed">
                    <p class="card-text">detailed</p>
                </b-collapse>
                <b-collapse id="remarks">
                    <p class="card-text">remarks</p>
                </b-collapse>

            </div>

        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            checked1: false,
        }
    }
}
</script>