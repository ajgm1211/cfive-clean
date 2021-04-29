<template>
    <div class="container-cards">

        <!-- TARJETA CMA -->
        <div 
            class="mb-4" 
            v-for="(cmaResult, cmaResultKey) in results.cmacgm"
            :key="cmaResultKey+'cma'"
        >
        <div class="result-search">
            <div class="banda-top cma"><span>CMA CGM PRICES</span></div>

            <!-- INFORMACION DE TARIFA -->
            <div class="row">
            <!-- CARRIER -->
            <div
                class="col-12 col-lg-2 carrier-img d-flex justify-content-center align-items-center"
                style="border-right: 1px solid #f3f3f3"
            >
                <img src="/images/cma.png" alt="logo" width="115px" />
            </div>
            <!-- FIN CARRIER -->

            <!-- INFORMACION PRINCIPAL -->
            <div class="row col-12 col-lg-8 margin-res">
                <!-- CONTRACT NAME -->
                <div class="col-12">
                <h6 class="mt-4 mb-5 contract-title">{{ cmaResult.quoteLine }}</h6>
                </div>
                <!-- FIN CONTRACT NAME -->

                <!-- RUTA Y PRECIOS -->
                <div
                class="row col-12 mr-0 ml-0"
                style="border-bottom: 1px solid #f3f3f3"
                >
                <!-- RUTA -->
                <div
                    class="col-12 col-lg-6 d-none d-lg-flex"
                    style="border-bottom: 1px solid #eeeeee"
                >
                    <!-- ORGIEN -->
                    <div class="origin mr-4">
                    <span>origin</span>
                    <p class="mb-0">{{ cmaResult.routingDetails[0].departureName }}</p>
                    <p>{{ cmaResult.departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- LINEA DE RUTA -->
                    <div
                    class="d-flex flex-column justify-content-center align-items-center"
                    >
                    <div class="direction-form">
                        <img src="/images/logo-ship-blue.svg" alt="bote" />

                        <div class="route-indirect d-flex align-items-center">
                        <div class="circle mr-2"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle-gray mr-2 ml-2"></div>
                        <div class="line line-blue"></div>
                        <div class="circle fill-circle ml-2"></div>
                        </div>
                    </div>

                    <div class="direction-desc mt-2">
                        <p class="mb-1"><b>Transit Time: </b> {{ cmaResult.transitTime + ' days' }}</p>
                        <p><b>Vessel: </b> {{ cmaResult.vehiculeName }}</p>
                    </div>
                    </div>
                    <!-- FIN LINEA DE RUTA -->

                    <!-- DESTINO -->
                    <div class="destination ml-4">
                    <span>destination</span>
                    <p class="mb-0">{{ cmaResult.routingDetails[0].arrivalName }}</p>
                    <p>{{ cmaResult.arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINO -->
                </div>
                <!-- FIN RUTA -->

                <!-- RUTA RESPONSIVA -->
                <div
                    class="row col-lg-6 d-lg-none mr-0 ml-0"
                    style="border-bottom: 1px solid #eeeeee"
                >
                    <!-- DESTINOS -->
                    <div class="col-sm-6">
                    <!-- ORGIEN -->
                    <div class="origin mb-3">
                        <span>origin</span>
                        <p class="mb-1">{{ cmaResult.routingDetails[0].departureName }}</p>
                        <p>{{ cmaResult.departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- DESTINO -->
                    <div class="destination align-items-start mb-3">
                        <span>destination</span>
                        <p class="mb-1">{{ cmaResult.routingDetails[0].arrivalName }}</p>
                        <p>{{ cmaResult.arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINO -->
                    </div>
                    <!-- FIN DESTINOS -->

                    <!-- TRANSIT TIME -->
                    <div class="col-sm-6">
                    <!-- LINEA DE RUTA -->
                    <div class="via">
                        <ul class="pl-0" style="list-style: none">
                        <li>
                            <p class="mb-1"><b>Transit Time: </b>{{ cmaResult.transitTime + ' days' }}</p>
                        </li>
                        <li>
                            <p><b>Vessel: </b>{{ cmaResult.vehiculeName }}</p>
                        </li>
                        </ul>
                    </div>
                    <!-- FIN LINEA DE RUTA -->
                    </div>
                    <!-- FIN TRANSIT TIME -->
                </div>
                <!-- FIN RUTA RESPONSIVA -->

                <!-- PRECIO -->
                <div class="col-12 col-lg-6">
                    <!-- PRECIO RESPONSIVE -->
                    <div class="row card-amount card-amount-header__res">
                    <div 
                        class="col-2 pl-0 pr-0 prices-card-res"
                        v-for="(cont,contCode) in request.containers"
                        :key="contCode"
                    >
                        <p><b>{{ cont.code }}</b></p>
                    </div>
                    </div>
                    <!-- FIN PRECIO RESPONSIVE -->

                    <!-- PRECIO -->
                    <div class="row card-amount card-amount__res">
                    <div 
                        class="col-2 pl-0 pr-0 prices-card-res"
                        :class="countContainersClass()"
                        v-for="(cmaGlobalTotal, cmaTotalKey) in cmaResult.pricingDetails.totalRatePerContainer"
                        :key="cmaTotalKey"
                    >
                        <p>
                        <b style="font-size: 16px"
                            > {{ cmaGlobalTotal.total }} <span style="font-size: 10px">{{ cmaGlobalTotal.currencyCode }}</span></b
                        >
                        </p>
                    </div>
                    </div>
                    <!-- FIN PRECIO -->
                </div>
                <!-- FIN PRECIO -->
                </div>
                <!-- RUTA Y PRECIOS -->

                <!-- OPCIONES E INFORMACION EXTRA -->
                <div class="col-12 mt-3 mb-3 result-action">
                <div class="d-flex align-items-center">
                    <span style="color: #006bfa; text-transform: capitalize;"
                    ><b-icon icon="check-circle-fill"></b-icon> CMA CGM My
                    PRICES</span
                    >
                    <p class="ml-4 mb-0">
                    <b>Validity:</b> {{ cmaResult.validityFrom.substring(0,10) + ' / ' + cmaResult.validityTo.substring(0,10) }}
                    </p>
                </div>

                <div class="d-flex justify-content-end align-items-center">
                    <b-button 
                        class="rs-btn"
                        :class="cmaResult.scheduleCollapse ? null : 'collapsed'"
                        :aria-expanded="cmaResult.scheduleCollapse ? 'true' : 'false'"
                        :aria-controls="'schedules_' + String(cmaResult.routingDetails[0].voyageNumber)"
                        @click="cmaResult.scheduleCollapse = !cmaResult.scheduleCollapse;
                                cmaResult.detailCollapse ? cmaResult.detailCollapse=false : cmaResult.detailCollapse = cmaResult.detailCollapse" 
                    ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                    ></b-button>
                    <b-button 
                        class="rs-btn" 
                        :class="cmaResult.detailCollapse ? null : 'collapsed'"
                        :aria-expanded="cmaResult.detailCollapse ? 'true' : 'false'"
                        :aria-controls="'details_' + String(cmaResult.routingDetails[0].voyageNumber)"
                        @click="cmaResult.detailCollapse = !cmaResult.detailCollapse;
                                cmaResult.scheduleCollapse ? cmaResult.scheduleCollapse=false : cmaResult.scheduleCollapse = cmaResult.scheduleCollapse"
                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                    ></b-button>
                </div>
                </div>
                <!-- FIN OPCIONES E INFORMACION EXTRA -->
            </div>
            <!-- FIN INFORMACION PRINCIPAL -->

            <!-- ADD QUOTE BTN -->
            <div
                class="col-12 col-lg-2 d-flex justify-content-center align-items-center btn-quote-res"
                style="border-left: 1px solid #f3f3f3"
            >
                <b-form-checkbox class="btn-add-quote" name="check-button" button>
                <b>add to quote</b>
                </b-form-checkbox>
            </div>
            </div>
            <!-- FIN INFORMACION DE TARIFA -->

            <!-- INFORMACION DESPLEGADA -->
            <div class="row mr-0 ml-0">
            <!-- DETALLES DE TARIFA -->
            <b-collapse 
                :id="'details_' + String(cmaResult.routingDetails[0].voyageNumber)" 
                v-model = cmaResult.detailCollapse 
                class="pt-5 pb-5 pl-5 pr-5 col-12"
            >
                <div
                    v-for="(cmaSurchargeType, cmaSurchargeKey) in cmaResult.pricingDetails.surcharges"
                    :key="cmaSurchargeKey"
                >
                <h5><b>{{ cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).charAt(0).toUpperCase() + cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).slice(1)}}</b></h5>

                <b-table-simple hover small responsive class="sc-table">
                    <b-thead>
                    <b-tr>
                        <b-th style="width:300px">Charge</b-th>
                        <b-th style="width:325px">Detail</b-th>
                        <!-- <b-th></b-th>
                        <b-th></b-th> -->
                        <b-th 
                            style="
                            padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;
                            "
                            v-for="(requestContainer, rContainerKey) in request.containers"
                            :key="rContainerKey"
                        >{{ requestContainer.code }}
                        </b-th>
                    </b-tr>
                    </b-thead>

                    <b-tbody>
                    <b-tr 
                        v-for="(cmaSurchargeName, cmaNameKey) in cmaSurchargeType"
                        :key="cmaNameKey">
                        <b-td><b>{{ cmaSurchargeName.chargeName != null ? cmaSurchargeName.chargeCode + ' - ' + cmaSurchargeName.chargeName : cmaSurchargeName.chargeCode }}</b></b-td>
                        <b-td>{{ cmaSurchargeName.calculationType }}</b-td>
                        <!-- <b-td></b-td>
                        <b-td></b-td> -->
                        <b-td 
                            v-for="(cmaSurchargeContainer, cmaContainerKey) in cmaSurchargeName.containers"
                            :key="cmaContainerKey"
                        ><p>{{ cmaSurchargeContainer.amount }} <b>{{ cmaSurchargeContainer.currencyCode }}</b></p></b-td
                        >
                    </b-tr>

                    <b-tr>
                        <!-- <b-td></b-td>
                        <b-td></b-td>
                        <b-td></b-td> -->
                        <b-td colspan="2" style="text-align: right"><b>Total {{ cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).charAt(0).toUpperCase() + cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).slice(1)}}</b></b-td>
                        <b-td
                            v-for="(cmaTypeTotal, cmaTypeTotalKey) in cmaResult.pricingDetails.totalRatePerType['totalRate'+cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).charAt(0).toUpperCase() + cmaSurchargeKey.substring(0, cmaSurchargeKey.length - 10).slice(1)]"
                            :key="cmaTypeTotalKey"
                        >
                        <b>{{ cmaTypeTotal.currencyCode }} {{ cmaTypeTotal.total }} </b></b-td>
                    </b-tr>
                    </b-tbody>
                </b-table-simple>
                </div>
            </b-collapse>
            <!-- FIN DETALLES DE TARIFA-->

            <!-- SCHEDULES -->
            <b-collapse
                :id="'schedules_' + String(cmaResult.routingDetails[0].voyageNumber)" 
                v-model = cmaResult.scheduleCollapse
                class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
            >
                <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

                <div class="row">
                <!-- INFOMACION DE LA API -->
                <div
                    class="col-lg-6 info-schedule"
                    style="border-right: 1px solid #eee"
                >
                    <div class="row schedule">
                    <!-- INFORMACION DEL BARCO -->
                    <div class="col-lg-6">
                        <h5 class="title-schedule">
                        <b-icon icon="hdd-rack"></b-icon> Vessel Information
                        </h5>

                        <div class="row mt-4">
                        <div class="col-lg-6">
                            <h5 class="sub-title-schedule">Vessel/Voyage</h5>
                            <p class="text-schedule"><b>{{ cmaResult.routingDetails[0].vehiculeName + ' / ' + cmaResult.routingDetails[0].voyageNumber }}</b></p>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="sub-title-schedule" v-if="cmaResult.routingDetails[0].imoNumber != null">IMO</h5>
                            <p class="text-schedule"><b>{{ cmaResult.routingDetails[0].imoNumber }}</b></p>
                        </div>
                        </div>
                    </div>
                    <!-- FIN INFORMACION DEL BARCO -->

                    <!-- DEADLINE -->
                    <div class="col-lg-6">
                        <h5 class="title-schedule">
                        <b-icon icon="stopwatch"></b-icon> Deadlines
                        </h5>

                        <div class="row mt-4">
                        <div 
                            class="col-12 col-sm-6" 
                            v-for="(cmaDeadline, cmaDeadlineKey) in cmaResult.routingDetails[0].deadlines"
                            :key="cmaDeadlineKey">
                            <h5 class="sub-title-schedule">{{ cmaDeadline.deadlineKey }}</h5>
                            <p class="text-schedule"><b>{{ cmaDeadline.deadline.substring(0,10) + " " + cmaDeadline.deadline.substring(11,cmaDeadline.deadline.length-1) }}</b></p>
                        </div>
                        </div>
                    </div>
                    <!-- FIN DEADLINE -->
                    </div>
                </div>
                <!-- FIN INFOMACION DE LA API -->

                <!-- RUTA -->
                <div
                    class="col-12 col-lg-6 d-none d-lg-flex align-items-center"
                >
                    <!-- ORIGEN -->
                    <div class="origin mr-4">
                    <span>origin</span>
                    <p class="mb-0">{{ cmaResult.routingDetails[0].departureName }}</p>
                    <p>{{ cmaResult.routingDetails[0].departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORIGEN -->

                    <!-- TT -->
                    <div
                    class="d-flex flex-column justify-content-center align-items-center"
                    >
                    <div class="direction-form">
                        <img
                        src="/images/logo-ship-blue.svg"
                        class="img-indirect"
                        alt="bote"
                        />

                        <div class="route-indirect d-flex align-items-center">
                        <div class="circle mr-2"></div>
                        <div class="line"></div>
                        <b-button
                            id="popover-direction"
                            class="pl-0 pr-0 popover-direction circle fill-circle-gray mr-2 ml-2"
                        ></b-button>
                        <b-popover
                            target="popover-direction"
                            triggers="hover"
                            placement="top"
                        >
                            <template #title>Transshipments</template>
                            <ul>
                                <li 
                                    v-for="(trans, transKey) in cmaResult.routingDetails"
                                    :key="transKey">{{ trans.departureName + ' - ' + trans.arrivalName + ' : ' + trans.arrivalDateGmt.substring(0,10) }}
                                </li>
                            </ul>
                        </b-popover>
                        <div class="line line-blue"></div>
                        <div class="circle fill-circle ml-2"></div>
                        </div>
                    </div>

                    <div class="direction-desc">
                        <p class="mb-0"><b>TT: </b> {{ cmaResult.transitTime + ' days'}}</p>
                        <p><b>Service: </b> {{ cmaResult.routingDetails.length > 1 ? "Transshipment" : "Direct" }}</p>
                    </div>
                    </div>

                    <!-- DESTINATION -->
                    <div class="destination ml-4">
                    <span>destination</span>
                    <p class="mb-0">{{ cmaResult.routingDetails[0].arrivalName }}</p>
                    <p>{{ cmaResult.routingDetails[0].arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINATION -->
                </div>
                <!-- FIN RUTA -->

                <!-- RUTA RESPONSIVA -->
                <div class="col-12 d-lg-none">
                    <h6>Transshipments</h6>
                    <ul>
                    <li 
                        v-for="(trans, transKey) in cmaResult.routingDetails"
                        :key="transKey">{{ trans.departureName + ' - ' + trans.arrivalName + ' : ' + trans.arrivalDateGmt.substring(0,10) }}
                    </li>
                    </ul>
                </div>
                <!-- FIN RUTA RESPONSIVA -->
                </div>
            </b-collapse>
            <!-- FIN SCHEDULES -->
            </div>
            <!-- FIN INFORMACION DESPLEGADA -->
        </div>
        </div>
        <!-- FIN TARJETA CMA -->

        <!-- TARJETA MAERKS -->
        <div 
            class="mb-4" 
            v-for="(result, key) in results.maersk"
            :key="key+'maersk'">
        <div class="result-search">
            <div class="banda-top maerks"><span>{{ result.company }}</span></div>

            <!-- INFORMACION DE TARIFA -->
            <div class="row">
            <!-- CARRIER -->
            <div
                class="col-12 col-lg-2 carrier-img d-flex justify-content-center align-items-center"
                style="border-right: 1px solid #f3f3f3"
            >
                <img :src="'/images/' + result.companyCode + '.png'" alt="logo" width="115px" />
            </div>
            <!-- FIN CARRIER -->

            <!-- INFORMACION PRINCIPAL -->
            <div class="row col-12 col-lg-8 margin-res">
                <!-- CONTRACT NAME -->
                <div class="col-12">
                <h6 class="mt-4 mb-5 contract-title">{{ result.quoteLine }}</h6>
                </div>
                <!-- FIN CONTRACT NAME -->

                <!-- RUTA Y PRECIOS -->
                <div
                class="row col-12 mr-0 ml-0"
                style="border-bottom: 1px solid #f3f3f3"
                >
                <!-- RUTA -->
                <div
                    class="col-12 col-lg-6 d-none d-lg-flex align-items-center"
                    style="border-bottom: 1px solid #eeeeee"
                >
                    <!-- ORGIEN -->
                    <div class="origin mr-4">
                    <span>origin</span>
                    <p class="mb-0">{{ result.departureTerminal }}</p>
                    <p>{{ result.departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- LINEA DE RUTA -->
                    <div
                    class="d-flex flex-column justify-content-center align-items-center"
                    >
                    <div class="direction-form">
                        <img src="/images/logo-ship-blue.svg" alt="bote" />

                        <div class="route-direct d-flex align-items-center">
                        <div class="circle mr-2"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle ml-2"></div>
                        </div>
                    </div>

                    <div class="direction-desc mt-2">
                        <p class="mb-1"><b>Transit Time: </b>{{ result.transitTime + ' days' }}</p>
                        <p><b>{{ result.vehiculeType }}:</b> {{ result.vehiculeName }}</p>
                    </div>
                    </div>
                    <!-- FIN LINEA DE RUTA -->

                    <!-- DESTINO -->
                    <div class="destination ml-4">
                    <span>destination</span>
                    <p class="mb-0">{{ result.arrivalTerminal }}</p>
                    <p>{{ result.arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINO -->
                </div>
                <!-- FIN RUTA -->

                <!-- RUTA RESPONSIVA -->
                <div
                    class="row col-lg-6 d-lg-none mr-0 ml-0"
                    style="border-bottom: 1px solid #eeeeee"

                >
                    <!-- DESTINOS -->
                    <div class="col-sm-6">
                    <!-- ORGIEN -->
                    <div class="origin mb-3">
                        <span>origin</span>
                        <p class="mb-0">{{ result.departureTerminal }}</p>
                        <p>{{ result.departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- DESTINO -->
                    <div class="destination align-items-start mb-3">
                        <span>destination</span>
                        <p class="mb-0">{{ result.arrivalTerminal }}</p>
                        <p>{{ result.arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINO -->
                    </div>
                    <!-- FIN DESTINOS -->

                    <!-- TRANSIT TIME -->
                    <div class="col-sm-6">
                    <!-- LINEA DE RUTA -->
                    <div class="via">
                        <ul class="pl-0" style="list-style: none">
                        <li>
                            <p class="mb-1"><b>Transit Time: </b>{{ result.transitTime + ' days' }}</p>
                        </li>
                        <li>
                            <p><b>{{ result.vehiculeType }}:</b> {{ result.vehiculeName }}</p>
                        </li>
                        </ul>
                    </div>
                    <!-- FIN LINEA DE RUTA -->
                    </div>
                    <!-- FIN TRANSIT TIME -->
                </div>
                <!-- FIN RUTA RESPONSIVA -->

                <!-- PRECIO -->
                <div class="col-12 col-lg-6">
                    <!-- PRECIO RESPONSIVE -->
                    <div class="row card-amount card-amount-header__res">
                    <div 
                        class="col-2 pl-0 pr-0 prices-card-res"
                        v-for="(cont,contCode) in request.containers"
                        :key="contCode"    
                    >
                        <p><b>{{ cont.code }}</b></p>
                    </div>
                    </div>
                    <!-- FIN PRECIO RESPONSIVE -->

                    <!-- PRECIO -->
                    <div class="row card-amount card-amount__res">
                    <div class="col-2 pl-0 pr-0 prices-card-res"
                        :class="countContainersClass()"
                        v-for="(globalTotal, totalKey) in result.pricingDetails.totalRatePerContainer"
                        :key="totalKey">
                        <p>
                        <b style="font-size: 16px"
                            >{{ globalTotal.total }} <span style="font-size: 10px">{{ globalTotal.currencyCode }}</span></b
                        >
                        </p>
                    </div>
                    </div>
                    <!-- FIN PRECIO -->
                </div>
                <!-- FIN PRECIO -->
                </div>
                <!-- RUTA Y PRECIOS -->

                <!-- OPCIONES E INFORMACION EXTRA -->
                <div class="col-12 mt-3 mb-3 result-action">
                <div class="result-action">
                    <span style="color: #006bfa;text-transform: capitalize;" class="mr-3"
                    ><b-icon icon="check-circle-fill"></b-icon> guaranteed Price
                    & loading</span
                    >
                    <span style="color: #006bfa;text-transform: capitalize;" class="mr-3"
                    ><b-icon icon="check-circle-fill"></b-icon> two-way
                    commitment</span
                    >
                    <a v-if="result.company == 'Maersk Spot'" href="https://terms.maersk.com/terms-spot-booking" style="color: #071c4b" target="_blank"> T&C applicable</a>
                    <a v-else href="https://terms.sealandmaersk.com/europe/terms-spot-booking" style="color: #071c4b" target="_blank"> T&C applicable</a>
                </div>

                <div class="d-flex justify-content-end align-items-center">
                    <b-button 
                        class="rs-btn"
                        :class="result.detentionCollapse ? null : 'collapsed'"
                        :aria-expanded="result.detentionCollapse ? 'true' : 'false'"
                        :aria-controls="'detention_' + String(result.routingDetails[0].voyageNumber)"
                        @click="result.detentionCollapse = !result.detentionCollapse;
                                result.scheduleCollapse ? result.scheduleCollapse=false : result.scheduleCollapse = result.scheduleCollapse;
                                result.detailCollapse ? result.detailCollapse=false : result.detailCollapse = result.detailCollapse" 
                    ><b>D&D</b><b-icon icon="caret-down-fill"></b-icon
                    ></b-button>
                    <b-button 
                        class="rs-btn"
                        :class="result.scheduleCollapse ? null : 'collapsed'"
                        :aria-expanded="result.scheduleCollapse ? 'true' : 'false'"
                        :aria-controls="'schedules_' + String(result.routingDetails[0].voyageNumber)"
                        @click="result.scheduleCollapse = !result.scheduleCollapse;
                                result.detentionCollapse ? result.detentionCollapse=false : result.detentionCollapse = result.detentionCollapse;
                                result.detailCollapse ? result.detailCollapse=false : result.detailCollapse = result.detailCollapse" 
                    ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                    ></b-button>
                    <b-button 
                        class="rs-btn" 
                        :class="result.detailCollapse ? null : 'collapsed'"
                        :aria-expanded="result.detailCollapse ? 'true' : 'false'"
                        :aria-controls="'details_' + String(result.routingDetails[0].voyageNumber)"
                        @click="result.detailCollapse = !result.detailCollapse;
                                result.scheduleCollapse ? result.scheduleCollapse=false : result.scheduleCollapse = result.scheduleCollapse;
                                result.detentionCollapse ? result.detentionCollapse=false : result.detentionCollapse = result.detentionCollapse"
                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                    ></b-button>
                </div>
                </div>
                <!-- FIN OPCIONES E INFORMACION EXTRA -->
            </div>
            <!-- FIN INFORMACION PRINCIPAL -->

            <!-- ADD QUOTE BTN -->
            <div
                class="col-12 col-lg-2 d-flex flex-column justify-content-center align-items-center btn-quote-res"
                style="border-left: 1px solid #f3f3f3"
            >
                <b-form-checkbox class="btn-add-quote" name="check-button" button>
                <b>add to quote</b>
                </b-form-checkbox>
                <a v-b-modal.qty-modal class="btn-add-quote btn-book"
                ><strong>BOOK</strong></a
                >
            </div>
            </div>
            <!-- FIN INFORMACION DE TARIFA -->

            <!-- INFORMACION DESPLEGADA -->
            <div class="row mr-0 ml-0">
            <!-- DETALLES DE TARIFA -->
            <b-collapse 
                class="pt-5 pb-5 pl-5 pr-5 col-12"
                :id="'details_' + String(result.routingDetails[0].voyageNumber)" 
                v-model = result.detailCollapse
            >
                <div 
                    v-for="(surchargeType, surchargeKey) in result.pricingDetails.surcharges"
                    :key="surchargeKey"
                >
                <h5><b>{{ surchargeKey.substring(0, surchargeKey.length - 10).charAt(0).toUpperCase() + surchargeKey.substring(0, surchargeKey.length - 10).slice(1)}}</b></h5>

                <b-table-simple hover small responsive class="sc-table">
                    <b-thead>
                    <b-tr>
                        <b-th style="width:300px">Charge</b-th>
                        <b-th style="width:325px">Detail</b-th>
                        <!-- <b-th></b-th>
                        <b-th></b-th> -->
                        <b-th
                        style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;"
                        v-for="(requestContainer, rContainerKey) in request.containers"
                        :key="rContainerKey"
                        >
                        {{ requestContainer.code }}
                        </b-th>
                    </b-tr>
                    </b-thead>

                    <b-tbody>
                    <b-tr
                        v-for="(surchargeName, nameKey) in surchargeType"
                        :key="nameKey">
                        <b-td><b>{{ surchargeName.chargeName != null ? surchargeName.chargeCode + ' - ' + surchargeName.chargeName : surchargeName.chargeCode }}</b></b-td>
                        <b-td>{{ surchargeName.calculationType }}</b-td>
                        <!-- <b-td></b-td>
                        <b-td></b-td> -->
                        <b-td 
                            v-for="(surchargeContainer, containerKey) in surchargeName.containers"
                            :key="containerKey"
                        ><p>{{ surchargeContainer.amount }} <b>{{ surchargeContainer.currencyCode }}</b></p></b-td
                        >
                    </b-tr>

                    <b-tr>
                        <!-- <b-td></b-td>
                        <b-td></b-td>
                        <b-td></b-td> -->
                        <b-td colspan="2" style="text-align: right"><b>Total {{ surchargeKey.substring(0, surchargeKey.length - 10).charAt(0).toUpperCase() + surchargeKey.substring(0, surchargeKey.length - 10).slice(1)}}</b></b-td>
                        <b-td
                            v-for="(typeTotal, typeTotalKey) in result.pricingDetails.totalRatePerType['totalRate'+surchargeKey.substring(0, surchargeKey.length - 10).charAt(0).toUpperCase() + surchargeKey.substring(0, surchargeKey.length - 10).slice(1)]"
                            :key="typeTotalKey"
                        >
                        <b>{{ typeTotal.currencyCode }} {{ typeTotal.total }} </b></b-td>
                    </b-tr>
                    </b-tbody>
                </b-table-simple>
                </div>

                <div>
                    <h5><b>{{ result.company }} Fees</b></h5>

                    <b-table-simple hover small responsive class="sc-table">
                        <b-thead>
                            <b-tr>
                                <b-th style="width:300px">Fee</b-th>
                                <b-th style="width:325px"></b-th>
                                <b-th
                                style="
                                    padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;
                                "
                                v-for="(requestContainer, rContainerKey) in request.containers"
                                :key="rContainerKey"
                                >{{ requestContainer.code }}</b-th
                                >
                            </b-tr>
                        </b-thead>

                        <b-tbody>
                            <b-tr
                                v-for="(fee, feeKey) in result.formattedPenalties"
                                :key="feeKey">
                                <b-td><b>{{ fee.name }}</b></b-td>
                                <b-td style="width:325px"></b-td>
                                <b-td 
                                    v-for="(maerskContainer, maerskContainerKey) in containerCodesMaerskPenalties"
                                    :key="maerskContainerKey"
                                ><p>{{ fee[maerskContainer] }} <b>{{ fee[maerskContainer+'currency'] }}</b></p></b-td
                                >
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>
            </b-collapse>
            <!-- FIN DETALLES DE TARIFA-->

            <!-- SCHEDULES -->
            <b-collapse
                :id="'schedules_' + String(result.routingDetails[0].voyageNumber)"
                v-model = result.scheduleCollapse
                class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
                style="background: #fbfbfb"
            >
                <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

                <div class="row">
                <!-- INFOMACION DE LA API -->
                <div
                    class="col-lg-6 info-schedule"
                    style="border-right: 1px solid #eee"
                >
                    <div class="row schedule">
                    <!-- INFORMACION DEL BARCO -->
                    <div class="col-lg-6">
                        <h5 class="title-schedule">
                        <b-icon icon="hdd-rack"></b-icon> Vessel Information
                        </h5>

                        <div class="row mt-4">
                        <div class="col-lg-6">
                            <h5 class="sub-title-schedule">Vessel/Voyage</h5>
                            <p class="text-schedule"><b>{{ result.routingDetails[0].vehiculeName }} / {{ result.routingDetails[0].voyageNumber }}</b></p>
                        </div>
                        <div v-if="result.routingDetails[0].imoNumber != null" class="col-lg-6">
                            <h5 class="sub-title-schedule">IMO</h5>
                            <p class="text-schedule"><b>{{ result.routingDetails[0].imoNumber }}</b></p>
                        </div>
                        </div>
                    </div>
                    <!-- FIN INFORMACION DEL BARCO -->

                    <!-- DEADLINE -->
                    <div class="col-lg-6">
                        <h5 class="title-schedule">
                        <b-icon icon="stopwatch"></b-icon> Deadlines
                        </h5>

                        <div class="row mt-4">
                        <div class="col-12 col-sm-6" 
                            v-for="(deadline, deadKey) in result.routingDetails[0].deadlines"
                            :key="deadKey"
                        >
                            <h5 class="sub-title-schedule"> {{ deadline.deadlineKey }} </h5>
                            <p class="text-schedule"><b>{{ deadline.deadline }}</b></p>
                        </div>
                        </div>
                    </div>
                    <!-- FIN DEADLINE -->
                    </div>
                </div>
                <!-- FIN INFOMACION DE LA API -->

                <!-- RUTA -->
                <div
                    class="col-12 col-lg-6 d-none d-lg-flex align-items-center"
                >
                    <!-- ORIGEN -->
                    <div class="origin mr-4">
                    <span>origin</span>
                    <p class="mb-0">{{ result.departureTerminal }}</p>
                    <p>{{ result.departureDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN ORIGEN -->

                    <!-- TT -->
                    <div
                    class="d-flex flex-column justify-content-center align-items-center"
                    >
                    <div class="direction-form">
                        <img
                        src="/images/logo-ship-blue.svg"
                        class="img-direct"
                        alt="bote"
                        />

                        <div 
                            class="route-indirect d-flex align-items-center"
                            v-if="result.routingDetails.length > 1"
                        >
                            <div class="circle mr-2"></div>
                            <div class="line"></div>
                            <b-button
                                id="popover-direction"
                                class="pl-0 pr-0 popover-direction circle fill-circle-gray mr-2 ml-2"
                            ></b-button>
                            <b-popover
                                target="popover-direction"
                                triggers="hover"
                                placement="top"
                            >
                                <template #title>Transshipments</template>
                                <ul>
                                <li v-for="(trans, transKey) in result.routingDetails"
                                    :key="transKey"
                                >
                                {{ trans.departureName + ' - ' + trans.arrivalName + ' : ' + trans.arrivalDateGmt.substring(0,10) }}
                                </li>
                                </ul>
                            </b-popover>
                            <div class="line line-blue"></div>
                            <div class="circle fill-circle ml-2"></div>
                        </div>

                        <div 
                            class="route-direct d-flex align-items-center"
                            v-else
                        >
                            <div class="circle mr-2"></div>
                            <div class="line"></div>
                            <div class="circle fill-circle ml-2"></div>
                        </div>
                    </div>

                    <div class="direction-desc">
                        <p class="mb-0"><b>TT: </b> {{ result.transitTime + ' days'}}</p>
                        <p><b>Service</b> {{ result.routingDetails.length > 1 ? 'Transshipment' : 'Direct'}}</p>
                    </div>
                    </div>

                    <!-- DESTINATION -->
                    <div class="destination ml-4">
                    <span>destination</span>
                    <p class="mb-0">{{ result.arrivalTerminal }}</p>
                    <p>{{ result.arrivalDateGmt.substring(0,10) }}</p>
                    </div>
                    <!-- FIN DESTINATION -->
                </div>
                <!-- FIN RUTA -->

                <!-- RUTA RESPONSIVA -->
                <div v-if="result.routingDetails.length > 1" class="col-12 d-lg-none">
                    <h6>Transshipments</h6>
                    <ul>
                    <li v-for="(trans, transKey) in result.routingDetails"
                        :key="transKey"
                    >
                    {{ trans.departureName + ' - ' + trans.arrivalName + ' : ' + trans.arrivalDateGmt.substring(0,10) }}
                    </li>
                    </ul>
                </div>
                <!-- FIN RUTA RESPONSIVA -->
                </div>
            </b-collapse>
            <!-- FIN SCHEDULES -->

            <!-- DETENTIONS -->
            <b-collapse
                :id="'detention_' + String(result.routingDetails[0].voyageNumber)"
                v-model = result.detentionCollapse
                class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
            >
                <div>
                    <h5><b>Demurrage & Detention</b></h5>

                    <b-table-simple hover small responsive class="sc-table mb-0">
                        <b-thead>
                            <b-tr>
                                <b-th colspan="3"></b-th>
                                <b-th colspan="3" style="text-align: center">Free time (days)</b-th>
                            </b-tr>
                            <b-tr>
                                <b-th>Type (import)</b-th>
                                <b-th>Start Event</b-th>
                                <b-th></b-th>
                                <b-th
                                style="
                                    padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;
                                "
                                v-for="(requestContainer, rContainerKey) in request.containers"
                                :key="rContainerKey"
                                >{{ requestContainer.code }}</b-th
                                >
                            </b-tr>
                        </b-thead>

                        <b-tbody>
                            <b-tr
                                v-for="(detention, detentionKey) in result.formattedDetentions"
                                :key="detentionKey"
                            >
                                <b-td><b>{{ detention.name }}</b></b-td>
                                <b-td><b>{{ detention.event }}</b></b-td>
                                <b-td></b-td>
                                <b-td 
                                    v-for="(maerskContainer, maerskContainerKey) in containerCodesMaerskDetentions"
                                    :key="maerskContainerKey"
                                ><p>{{ detention[maerskContainer] }}</p></b-td
                                >
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>

            </b-collapse>
            <!-- FIN DETENTIONS -->
            </div>
            <!-- FIN INFORMACION DESPLEGADA -->
        </div>

        <!--  Book Qty Modal  -->
        <b-modal
        ref="qty-modal"
        id="qty-modal"
        size="md"
        centered
        hide-footer
        title="Choose quantity"
        >
        <div class="row">
            <div class="col-12">
            <!-- Containers -->
            <div v-for="(item, key) in request.containers" :key="key">
                <div class="containers-numbers">
                <p>{{ item.code }}</p>
                <vue-numeric-input
                    v-model="container_qty[item.alternate_name]"
                    :min="0"
                    :max="100"
                ></vue-numeric-input>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-end mb-4 mt-4">
            <div class="footer-add-contract-modal pl-4 pr-4">
                <button type="button" class="btn btn-primary" @click="completeBook(result.additionalData.deeplink)">
                Confirm Book
                </button>
            </div>
            </div>
        </div>
        </b-modal>
        <!--  End Modal  -->
        </div>
        <!-- FIN TARJETA MAERKS -->
        
    </div>
</template>

<script>

export default {
  props: {
    request: Object,
    datalists: Object,
  },
  data() {
    return {
        //Containers count
        container_qty: {
            "20DRY": 0,
            "40DRY": 0,
            "40HDRY": 0,
            "45HDRY": 0,
            "40NOR": 0,
            "20RF": 0,
            "40RF": 0,
            "40HCRF": 0,
            "20OT": 0,
            "40OT": 0,
            "20FR": 0,
            "40FR": 0,
        },
        book_qty: {},
        results: {
            maersk: [],
            cmacgm: [],
        },
        containerCodesMaerskPenalties: [],
        containerCodesMaerskDetentions: [],
    };
  },
  methods: {
    countContainersClass() {
        if(this.request.containers.length == 5 || this.request.containers.length == 4) {
            return 'col-2';
        }
        
        if(this.request.containers.length == 3) {
            return 'col-3';
        }
        if(this.request.containers.length == 2) {
            return 'col-4';
        }
    },

    callAPIs(){
        let component = this;
        let apiOriginPorts = [];
        let apiDestinationPorts = [];
        let apiDate = new Date().toISOString().substring(0,10);
        let apiContainers = "";
        let apiCarrierCodes = "";

        component.$emit('apiSearchStarted');

        component.request.originPorts.forEach(function (originPort){
            if(!apiOriginPorts.includes(originPort.code)){
                apiOriginPorts.push(originPort.code);
            }
        });

        component.request.destinationPorts.forEach(function (destinationPort){
            if(!apiDestinationPorts.includes(destinationPort.code)){
                apiDestinationPorts.push(destinationPort.code);
            }
        });

        component.results.maersk = [];
        component.results.cmacgm = [];

        apiContainers = component.setApiContainers();
        if(this.request.carriersApi.length > 0){

            component.request.carriersApi.forEach(function (carrier){
                apiCarrierCodes += carrier.code;
                if(component.request.carriersApi[component.request.carriersApi.indexOf(carrier) + 1] != undefined){
                    apiCarrierCodes += ',';
                }
            });

            apiOriginPorts.forEach(function (origin){
                apiDestinationPorts.forEach(function (destination){
                    axios
                        .get('https://mighty-castle-09151.herokuapp.com/https://carriers.cargofive.com/api/pricing',
                            {
                            params: {
                                originPort: origin,
                                destinationPort: destination,
                                equipmentSizeType: apiContainers,
                                departureDate: apiDate,
                                uemail: 'dcabanales@gmail.com',
                                brands: apiCarrierCodes
                                },
                            headers:{
                                'Authorization': 'Bpu7Ijd4iau5zphybdbDUbfiKhPNlSXkmRBkrky0QJPQ1Aj2Ha',
                                'Accept': 'application/json',
                                'Content-type': 'application/json'
                                } 
                            },
                        )
                        .then((response) => {
                            response.data.forEach(function (respData){
                                if(respData.company == "Maersk Spot" || respData.company == "Sealand Spot"){
                                    component.results["maersk"].push(respData);
                                    component.setPenalties(respData);
                                    component.setDetention(respData);
                                }else if(respData.company == "CMA CGM"){
                                    component.results["cmacgm"].push(respData);
                                }
                                
                            });

                            component.$emit('apiSearchDone',response.data.length);
                        })
                        .catch((error) => {
                            console.log(error);
                            component.$emit('apiSearchDone',0);
                        })
                });
            });
            
        }else{
            component.$emit('apiSearchDone',0);
        }
    },
    
    setPenalties(responseData){
        let finalPenalties = [];
        let penaltyCodes = [];
        let component = this;
        
        responseData.additionalData.penaltyFees.forEach(function(penaltyPerContainer){
            penaltyPerContainer.charges.forEach(function (penaltyCont){
                if(!penaltyCodes.includes(penaltyCont.penaltyType)){
                    penaltyCodes.push(penaltyCont.penaltyType);
                    finalPenalties.push({
                        name: penaltyCont.displayName
                    });
                }

                if(!component.containerCodesMaerskPenalties.includes(penaltyPerContainer.containerSizeType)){
                    component.containerCodesMaerskPenalties.push(penaltyPerContainer.containerSizeType);
                }
            });
        });

        responseData.additionalData.penaltyFees.forEach(function(penaltyPerContainer){
            penaltyPerContainer.charges.forEach(function (penaltyCont){
                finalPenalties.forEach(function (final){
                    if(penaltyCont.displayName == final.name){
                        final[penaltyPerContainer.containerSizeType] = penaltyCont.chargeFee;
                        final[penaltyPerContainer.containerSizeType + "currency"] = penaltyPerContainer.currency;
                    }
                });
            });
        });

        responseData.formattedPenalties = finalPenalties;
    },

    setDetention(responseData){
        let component = this;
        let finalDetentions = [];
        let detentionCodes = [];

        responseData.additionalData.importDnDConditions.forEach(function(detention){
            if(!detentionCodes.includes(detention.chargeType)){
                detentionCodes.push(detention.chargeType);
                finalDetentions.push({
                    name: detention.chargeType,
                    event: detention.freetimeStartEvent
                });
            }

            if(!component.containerCodesMaerskDetentions.includes(detention.containerSizeType)){
                component.containerCodesMaerskDetentions.push(detention.containerSizeType);
            }
        });

        responseData.additionalData.importDnDConditions.forEach(function(detention){
            finalDetentions.forEach(function (final){
                if(detention.chargeType == final.name){
                    final[detention.containerSizeType] = detention.freetimeGrantInDays;
                }
            });
        });

        responseData.formattedDetentions = finalDetentions;
    },

    setApiContainers(){
        let component = this;
        let finalContainerString = "";

        component.request.containers.forEach(function(container){
            let containerString = "1x" + container.code.substring(0,2);

            if(["NOR","HCRF","OT","FR"].includes(container.code)){
                return;
            }
            if(container.code.includes("HC")){
                containerString += "HC";
            }
            if(container.code.includes("DV")){
                containerString += "DRY";
            }
            if(container.code.includes("RF")){
                containerString += "RF";
            }
            
            containerString += "x2";

            finalContainerString += containerString;

            if(component.request.containers[component.request.containers.indexOf(container) + 1] != undefined){
                finalContainerString += ",";
            }
        });

        return finalContainerString;
    },

    alert(msg, type) {
      this.$toast.open({
        message: msg,
        type: type,
        duration: 5000,
        dismissible: true,
      });
    },

    completeBook(link) {
      let component = this;
      let string = "";
      let qty = "";
      let link_str = "";

      $.each(component.container_qty, function (key, value) {
        if (value > 0) {
          string += "containers=" + value + "x" + key + "x2&";
        }
      });

      if (string == "") {
        component.alert("You must indicate the number of containers", "error");
        return false;
      }

      //Formating URI
      link_str = link.split("?");
      qty = string.slice(0, -1);

      //Redirecting to Maersk site
      window.open(link_str[0] + "?" + qty, "_blank");
      component.alert("Redirecting to the Maersk site", "success");
      component.$root.$emit("bv::hide::modal", "qty-modal");
    },
  },
};
</script>