<template>
    <div class="pr-5 pl-5">

<div 
                class="col-12 mb-4"
            >

                <div class="result-search">

                    <div class="banda-top"><span>CMA CGM PRICES</span></div>

                    <!-- CONTENT MAIN CARD -->
                    <div class="row">

                       <!-- CARRIER -->
                        <div class="col-12 col-lg-2 carrier-img d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                            <img 
                                src="/images/cma.png"  
                                alt="logo" 
                                width="115px">
                        </div>

                        <!-- INFO CARD -->
                        <div class="row col-12 col-lg-8 margin-res">

                            <!-- CONTRACT NAME -->
                            <div class="col-12">
                                <h6 class="mt-4 mb-5 contract-title">CMA CARD</h6>
                            </div>

                            <!-- INFO AND PRICE -->
                            <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                                <!-- INFO -->
                                <div class="col-12 col-lg-6 d-flex transi-time-res">

                                    <!-- ORIGIN -->
                                    <div class="origin mr-4">

                                        <span>origin</span>
                                        <p>España</p>

                                    </div>

                                    <!-- TT -->
                                    <div class="via d-flex flex-column justify-content-center align-items-center">

                                        <div class="direction-form route-indirect tt">

                                            <img src="/images/logo-ship-blue.svg" alt="bote" style="top: -30px">

                                            <div class="line-route-direct">
                                            <div class="circle mr-2"></div>
                                            <div class="line"></div>
                                            <div class="circle fill-circle-gray mr-2 ml-2"></div>
                                            <div class="line line-blue"></div>
                                            <div class="circle fill-circle ml-2"></div>
                                            </div>

                                        </div>

                                    
                                        <div class="direction-desc mt-2">

                                            <p style="margin-bottom: 0px"><b>Transit Time:</b> 45 Days</p>
                                            <p><b>Vessel</b> Gordito</p>

                                        </div>

                                    </div>

                                    <!-- DESTINATION -->
                                    <div class="destination ml-4">

                                        <span>destination</span>
                                        <p>Argentina</p>

                                    </div>

                                </div>

                                <!-- PRICES -->
                                <!-- <div class="col-12 col-lg-6 ">
                                    <div class="row card-amount">
                                        <div 
                                            class="col-12 col-lg-2 pl-0 pr-0 prices-card-res"
                                            v-for="(container,contKey) in request.containers"
                                            :key="contKey"
                                        >
                                            <p><b style="font-size:16px">{{ rate.totals_with_markups ? rate.totals_with_markups['C'+container.code].toFixed(2) : rate.totals['C'+container.code] }} <span style="font-size: 10px">{{rate.client_currency.alphacode}}</span></b></p>
                                        </div>
                                    </div>
                                </div> -->

                            </div>

                            <!-- OPTIONS -->
                            <div class="col-12 mt-3 mb-3 result-action">

                                <div class="d-flex align-items-center">
                                    
                                    <a href="#" style="color: #006BFA;" ><b-icon icon="check-circle-fill"></b-icon> CMA CGM My PRICES</a>
                                    <p class="ml-4 mb-0"><b>Validity:</b> 2020-08-02 / 2020-05-09</p>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button 
                                        class="rs-btn"
                                        v-b-toggle.schedules
                                    ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button 
                                        class="rs-btn"
                                        v-b-toggle.detailed
                                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                </div>

                            </div>

                        </div>

                        <!-- ADD QUOTE BTN -->
                        <div class="col-12 col-lg-2 d-flex justify-content-center align-items-center btn-quote-res" style="border-left: 1px solid #f3f3f3">
                                <b-form-checkbox class="btn-add-quote" name="check-button" button>
                                    <b>add to quote</b>
                                </b-form-checkbox>
                        </div>

                   </div>

                   <div class="row">
                   
                        <!-- <b-collapse id="detailed" class="pt-5 pb-5 pl-5 pr-5 col-12" v-model="">
                            <div 
                                v-for="(chargeArray,chargeType) in rate.charges"
                                :key="chargeType"
                            >
                                <h5><b>{{ chargeType }}</b></h5>

                                <b-table-simple hover small responsive class="sc-table">

                                    <b-thead>
                                        <b-tr>
                                            <b-th>Charge</b-th>
                                            <b-th>Detail</b-th>
                                            <b-th></b-th>
                                            <b-th></b-th>
                                            <b-th
                                                v-for="(container,contKey) in request.containers"
                                                :key="contKey"
                                                style="padding: 0.75rem !important"
                                            >

                                            {{container.code}}
                                            </b-th>
                                        </b-tr>
                                    </b-thead>

                                    <b-tbody>
                                        <b-tr 
                                            v-for="(charge,chargeKey) in chargeArray"
                                            :key="chargeKey"
                                        >
                                            <b-td><b>{{ charge.surcharge.name }}</b></b-td>
                                            <b-td>{{ charge.calculationtype.name }}</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td
                                                v-for="(container,contKey) in request.containers"
                                                :key="contKey"
                                            >
                                            <p v-if="charge.container_markups != undefined">{{ charge.joint_as=='client_currency' ? charge.containers_client_currency['C'+container.code] : charge.containers['C'+container.code] }}</p>
                                            <span v-if="charge.container_markups != undefined && charge.container_markups['C'+container.code] != undefined" class="profit">+{{charge.joint_as=='client_currency' ? charge.totals_markups['C'+container.code] : charge.container_markups['C'+container.code]}}</span>
                                            <b>{{ charge.joint_as=='client_currency' && chargeType != 'Freight' ? rate.client_currency.alphacode : charge.currency.alphacode}}</b> 
                                            <b v-if="charge.container_markups != undefined">{{ charge.joint_as=='client_currency' ? charge.totals_with_markups['C'+container.code] : charge.containers_with_markups['C'+container.code] }}</b>
                                            <b v-else >{{ charge.joint_as=='client_currency' ? charge.containers_client_currency['C'+container.code] : charge.containers['C'+container.code] }}</b>
                                            </b-td>
                                        </b-tr>
                
                                        <b-tr>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td><b>Total {{ chargeType }}</b></b-td>
                                            <b-td 
                                                v-for="(container,contKey) in request.containers"
                                                :key="contKey"
                                            ><b>{{ chargeType == 'Freight' ? rate.currency.alphacode : rate.client_currency.alphacode }} {{ rate.charge_totals_by_type[chargeType]['C'+container.code].toFixed(2) }}</b></b-td>
                                        </b-tr>
                                    </b-tbody>
                                
                                </b-table-simple>
                            </div>
                        </b-collapse> -->
                        <b-collapse id="schedules" class="pt-5 pb-5 pl-5 pr-5 col-12 schedule-tt">

                                <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>
                                
                                <div class="row">
                                     
                                    <div class="col-lg-6 col-12 info-schedule" style="border-right: 1px solid #eee">

                                        <div class="row schedule">

                                            <div class="col-12 col-lg-6">


                                                <h5 class="title-schedule"><b-icon icon="hdd-rack"></b-icon> Vessel Information</h5>

                                                <div class="row mt-4">
                                                    <div class="col-12 col-lg-6">

                                                        <h5 class="sub-title-schedule">vessel/Voyage</h5>
                                                        <p class="text-schedule"><b>MSC DITTE 038E</b></p>

                                                    </div>
                                                    <div class="col-12 col-lg-6">

                                                        <h5 class="sub-title-schedule">IMO</h5>
                                                        <p class="text-schedule"><b>MSC DITTE 038E</b></p>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 col-lg-6">

                                                <h5 class="title-schedule"><b-icon icon="stopwatch"></b-icon> Deadlines</h5>
                                        
                                                <div class="row mt-4">
                                                    <div class="col-12 col-lg-6">

                                                        <h5 class="sub-title-schedule">CY</h5>
                                                        <p class="text-schedule"><b>18 Sep, 2020 08:00</b></p>

                                                    </div>
                                                    <div class="col-12 col-lg-6">

                                                        <h5 class="sub-title-schedule">VGM</h5>
                                                        <p class="text-schedule"><b>18 Sep, 2020 08:00</b></p>

                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        
                                        



                                    </div>

                                    <div class="col-12 col-lg-6 d-flex transi-time">

                                        <!-- ORIGIN -->
                                        <div class="origin mr-4">

                                            <span>origin</span>
                                            <p>España</p>

                                        </div>

                                        <!-- TT -->
                                        <div class="via d-flex flex-column justify-content-center align-items-center">

                                            <div class="direction-form route-indirect tt">

                                                <img src="/images/logo-ship-blue.svg" alt="bote" style="top: -30px">

                                                <div class="line-route-direct">
                                                <div class="circle mr-2"></div>
                                                <div class="line"></div>
                                                <div class="circle fill-circle-gray mr-2 ml-2"></div>
                                                <div class="line line-blue"></div>
                                                <div class="circle fill-circle ml-2"></div>
                                                </div>

                                            </div>

                                        
                                            <div class="direction-desc">

                                                <p><b>TT:</b> 45 Days</p>

                                            </div>

                                        </div>

                                        <!-- DESTINATION -->
                                        <div class="destination ml-4">

                                            <span>destination</span>
                                            <p>Argentina</p>

                                        </div>

                                    </div>

                                </div>
                            
                        </b-collapse>
                   
                   </div>

                </div>
            </div>
        

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
