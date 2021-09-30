<template>
  <div class="container-cards">
    <!-- TARJETA CMA -->
    <div
      class="mb-4"
      v-for="(cmaResult, cmaResultKey) in orderedCmaRates"
      :key="cmaResultKey + 'cma'"
    >
      <div class="result-search">
        <div class="banda-top cma"><span>CMA CGM PRICES</span></div>

        <!-- INFORMACION DE TARIFA -->
        <div class="row">
          <!-- CARRIER -->
          <div
            class="
              col-12 col-lg-2
              carrier-img
              d-flex
              justify-content-center
              align-items-center
            "
            style="border-right: 1px solid #f3f3f3"
          >
            <img
              :src="
                'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
                  cmaResult.image
              "
              alt="logo"
              width="115px"
            />
          </div>
          <!-- FIN CARRIER -->

          <!-- INFORMACION PRINCIPAL -->
          <div class="row col-12 col-lg-8 margin-res">
            <!-- CONTRACT NAME -->
            <div class="col-lg-10 col-12">
              <h6 class="mt-4 mb-5 contract-title">
                {{ cmaResult.contractReference }}
                <b-button
                  :id="'popover-name-' + cmaResultKey"
                  v-if="cmaResult.additionalData.namedAccounts.length > 0"
                  class="pophover-name-account"
                  style="border: none !important"
                >
                  <b-icon
                    v-if="cmaResult.additionalData.namedAccounts.length == 1"
                    icon="person"
                  ></b-icon>
                  <b-icon v-else icon="people"></b-icon>
                </b-button>
                <b-popover
                  :target="'popover-name-' + cmaResultKey"
                  triggers="hover"
                  placement="top"
                >
                  <ul class="pl-2 ml-2">
                    <li v-for="data,namedKey in cmaResult.additionalData.namedAccounts"
                      :key="namedKey"
                    >
                      {{ data.name }}
                    </li>
                  </ul>
                </b-popover>
              </h6>
            </div>
            <!-- FIN CONTRACT NAME -->
            <div class="col-lg-2 col-12 text-center"></div>
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
                  <p class="mb-0">
                    {{ cmaResult.departureName }}
                  </p>
                  <p v-if="cmaResult.departureDateGmt">
                    {{ cmaResult.departureDateGmt.substring(0, 10) }}
                  </p>
                </div>
                <!-- FIN ORGIEN -->

                <!-- LINEA DE RUTA -->
                <div
                  class="
                    d-flex
                    flex-column
                    justify-content-center
                    align-items-center
                  "
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
                    <p class="mb-1">
                      <b>Transit Time: </b>
                      {{ cmaResult.transitTime + " days" }}
                    </p>
                    <p><b>Vessel: </b> {{ cmaResult.vehiculeName }}</p>
                  </div>
                </div>
                <!-- FIN LINEA DE RUTA -->

                <!-- DESTINO -->
                <div class="destination ml-4">
                  <span>destination</span>
                  <p class="mb-0">
                    {{ cmaResult.arrivalName }}
                  </p>
                  <p v-if="cmaResult.arrivalDateGmt">
                    {{ cmaResult.arrivalDateGmt.substring(0, 10) }}
                  </p>
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
                    <p class="mb-1">
                      {{ cmaResult.departureName }}
                    </p>
                    <p v-if="cmaResult.departureDateGmt">
                      {{ cmaResult.departureDateGmt.substring(0, 10) }}
                    </p>
                  </div>
                  <!-- FIN ORGIEN -->

                  <!-- DESTINO -->
                  <div class="destination align-items-start mb-3">
                    <span>destination</span>
                    <p class="mb-1">
                      {{ cmaResult.arrivalName }}
                    </p>
                    <p v-if="cmaResult.arrivalDateGmt">
                      {{ cmaResult.arrivalDateGmt.substring(0, 10) }}
                    </p>
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
                        <p class="mb-1">
                          <b>Transit Time: </b
                          >{{ cmaResult.transitTime + " days" }}
                        </p>
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
                    v-for="(cont, contCode) in request.containers"
                    :key="contCode"
                  >
                    <p>
                      <b>{{ cont.code }}</b>
                    </p>
                  </div>
                </div>
                <!-- FIN PRECIO RESPONSIVE -->

                <!-- PRECIO -->
                <div class="row card-amount card-amount__res">
                  <div
                    class="col-2 pl-0 pr-0 prices-card-res"
                    :class="countContainersClass()"
                    v-for="(cmaGlobalTotal, cmaTotalKey) in cmaResult
                      .pricingDetails.totalRatePerContainer"
                    :key="cmaTotalKey"
                  >
                    <p>
                      <b style="font-size: 16px">
                        {{ cmaGlobalTotal.total }}
                        <span style="font-size: 10px">{{
                          cmaGlobalTotal.currencyCode
                        }}</span></b
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
                <span style="color: #006bfa; text-transform: capitalize"
                  ><b-icon icon="check-circle-fill"></b-icon> CMA CGM My
                  PRICES</span
                >
                <p class="ml-4 mb-0">
                  <b style="font-size:11px;">VALIDITY:</b>
                  {{
                    cmaResult.validityFrom.substring(0, 10) +
                      " / " +
                      cmaResult.validityTo.substring(0, 10)
                  }}
                </p>

                <b-button
                  :id="'popover-name-commodity-' + cmaResultKey"
                  v-if="cmaResult.additionalData.commodities.length > 0"
                  class="pophover-name-account ml-3 mb-0 mt-1"
                  style="border:none !important"
                >
                  <b style="font-size:11px; color:#212529;">COMMODITIES</b>
                  &nbsp;<b-icon icon="box"></b-icon>
                </b-button>
                <b-popover
                  :target="'popover-name-commodity-' + cmaResultKey"
                  triggers="hover"
                  placement="top"
                >
                  <ul class="pl-2 ml-2">
                    <li v-for="data,commKey in cmaResult.additionalData.commodities"
                      :key="commKey"
                    >
                      {{ data.name }}
                    </li>
                  </ul>
                </b-popover>
              </div>

              <div class="d-flex justify-content-end align-items-center">
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'schedules_' +
                      String(cmaResult.contractReference) +
                      '_' +
                      String(cmaResult.accordion_id)
                  "
                  ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'details_' +
                      String(cmaResult.contractReference) +
                      '_' +
                      String(cmaResult.accordion_id)
                  "
                  ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
              </div>
            </div>
            <!-- FIN OPCIONES E INFORMACION EXTRA -->
          </div>
          <!-- FIN INFORMACION PRINCIPAL -->

          <!-- ADD QUOTE BTN -->
          <div
            class="
              col-12 col-lg-2
              d-flex
              justify-content-center
              align-items-center
              btn-quote-res
            "
            style="border-left: 1px solid #f3f3f3"
          >
            <b-form-checkbox
              v-model="cmaResult.addToQuote"
              class="btn-add-quote"
              name="check-button"
              button
              @change="addResultToQuote(cmaResult)"
            >
              <b>add to quote</b>
            </b-form-checkbox>
          </div>
        </div>
        <!-- FIN INFORMACION DE TARIFA -->

        <!-- INFORMACION DESPLEGADA -->
        <div
          :id="'my-accordion-' + cmaResult.accordion_id"
          class="row mr-0 ml-0 accordion"
          role="tablist"
        >
          <!-- DETALLES DE TARIFA -->
          <b-collapse
            :id="
              'details_' +
                String(cmaResult.contractReference) +
                '_' +
                String(cmaResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12"
            :accordion="'my-accordion-' + cmaResult.accordion_id"
            role="tabpanel"
            v-model="cmaResult.detailCollapse"
          >
            <div
              v-for="(cmaSurchargeType, cmaSurchargeKey) in cmaResult
                .pricingDetails.surcharges"
              :key="cmaSurchargeKey"
            >
              <h5>
                <b>{{
                  cmaSurchargeKey
                    .substring(0, cmaSurchargeKey.length - 10)
                    .charAt(0)
                    .toUpperCase() +
                    cmaSurchargeKey
                      .substring(0, cmaSurchargeKey.length - 10)
                      .slice(1)
                }}</b>
              </h5>

              <b-table-simple hover small responsive class="sc-table">
                <b-thead>
                  <b-tr>
                    <b-th style="width: 300px">Charge</b-th>
                    <b-th style="width: 325px">Detail</b-th>
                    <!-- <b-th></b-th>
                        <b-th></b-th> -->
                    <b-th
                      style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                      v-for="(requestContainer,
                      rContainerKey) in request.containers"
                      :key="rContainerKey"
                      >{{ requestContainer.code }}
                    </b-th>
                  </b-tr>
                </b-thead>

                <b-tbody>
                  <b-tr
                    v-for="(cmaSurchargeName, cmaNameKey) in cmaSurchargeType"
                    :key="cmaNameKey"
                  >
                    <b-td
                      ><b>{{
                        cmaSurchargeName.chargeName != null
                          ? cmaSurchargeName.chargeCode +
                            " - " +
                            cmaSurchargeName.chargeName
                          : cmaSurchargeName.chargeCode
                      }}</b></b-td
                    >
                    <b-td>{{ cmaSurchargeName.calculationType }}</b-td>
                    <!-- <b-td></b-td>
                        <b-td></b-td> -->
                    <b-td
                      v-for="(cmaSurchargeContainer,
                      cmaContainerKey) in cmaSurchargeName.containers"
                      :key="cmaContainerKey"
                      ><p>
                        <b
                          >{{ cmaSurchargeContainer.currencyCode }}
                          {{ cmaSurchargeContainer.amount }}</b
                        >
                      </p></b-td
                    >
                  </b-tr>

                  <b-tr>
                    <!-- <b-td></b-td>
                        <b-td></b-td>
                        <b-td></b-td> -->
                    <b-td colspan="2" style="text-align: right"
                      ><b
                        >Total
                        {{
                          cmaSurchargeKey
                            .substring(0, cmaSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                            cmaSurchargeKey
                              .substring(0, cmaSurchargeKey.length - 10)
                              .slice(1)
                        }}</b
                      ></b-td
                    >
                    <b-td
                      v-for="(cmaTypeTotal, cmaTypeTotalKey) in cmaResult
                        .pricingDetails.totalRatePerType[
                        'totalRate' +
                          cmaSurchargeKey
                            .substring(0, cmaSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                          cmaSurchargeKey
                            .substring(0, cmaSurchargeKey.length - 10)
                            .slice(1)
                      ]"
                      :key="cmaTypeTotalKey"
                    >
                      <b
                        >{{ cmaTypeTotal.currencyCode }}
                        {{ cmaTypeTotal.total }}
                      </b></b-td
                    >
                  </b-tr>
                </b-tbody>
              </b-table-simple>
            </div>
          </b-collapse>
          <!-- FIN DETALLES DE TARIFA-->

          <!-- SCHEDULES -->
          <b-collapse
            :id="
              'schedules_' +
                String(cmaResult.contractReference) +
                '_' +
                String(cmaResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
            :accordion="'my-accordion-' + cmaResult.accordion_id"
            role="tabpanel"
            v-model="cmaResult.scheduleCollapse"
          >
            <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

            <!-- SCHEDULE INFORMATION -->
            <b-tabs
              pills
              card
              vertical
              class="d-none d-lg-flex"
              v-model="cmaResult.activeTab"
            >
              <b-tab
                v-for="(route, routeKey) in cmaResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <template #title>
                  <div class="margin-res">
                    <!-- NOMBRE -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ cmaResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN NOMBRE -->

                    <!-- RUTA -->
                    <div class="row col-12 mr-0 ml-0">
                      <div
                        class="col-12 d-none d-lg-flex justify-content-between"
                      >
                        <!-- ORGIEN -->
                        <div class="origin mr-4">
                          <span>origin</span>
                          <p class="mb-0">
                            {{ route.details[0].departureName }}
                          </p>
                          <p v-if="route.details[0].departureDateGmt">
                            {{
                              route.details[0].departureDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN ORGIEN -->

                        <!-- LINEA DE RUTA -->
                        <div
                          class="
                            d-flex
                            flex-column
                            justify-content-center
                            align-items-center
                          "
                        >
                          <div class="direction-form">
                            <img
                              src="/images/logo-ship-blue.svg"
                              alt="bote"
                              style="top: -30px"
                            />

                            <div
                              class="route-indirect d-flex align-items-center"
                            >
                              <div class="circle mr-2"></div>
                              <div class="line"></div>
                              <div
                                class="circle fill-circle-gray mr-2 ml-2"
                              ></div>
                              <div class="line line-blue"></div>
                              <div class="circle fill-circle ml-2"></div>
                            </div>
                          </div>

                          <div class="direction-desc mt-2">
                            <p class="mb-1">
                              <b>Transit Time: </b>{{ route.transitTime }} days
                            </p>
                            <p v-if="route.details.length > 1">
                              <b>Via: </b>{{ route.details[0].arrivalName }}
                            </p>
                            <p>
                              <b>Service: </b
                              >{{
                                route.details.length > 1
                                  ? "Transhipment"
                                  : "Direct"
                              }}
                            </p>
                          </div>
                        </div>
                        <!-- FIN LINEA DE RUTA -->

                        <!-- DESTINO -->
                        <div class="destination ml-4">
                          <span>destination</span>
                          <p class="mb-0">
                            {{ route.details.slice(-1)[0].arrivalName }}
                          </p>
                          <p v-if="route.details[0].arrivalDateGmt">
                            {{
                              route.details[0].arrivalDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN DESTINO -->
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </div>
                </template>
                <!-- FIN INFORMACION PRINCIPAL -->

                <!-- INFORMACION DE LA RUTA -->
                <div class="row">
                  <div
                    class="col-12 d-none d-lg-flex align-items-center pl-5"
                    style="border-left: 1px solid #eee"
                  >
                    <div class="row" style="width: 100%">
                      <!-- INFORMACION DEL BARCO -->
                      <div class="col-xl-6 schedule-info">
                        <!-- VESSEL Information -->
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="hdd-rack"></b-icon> Vessel Information
                        </h5>
                        <ul>
                          <li>
                            <h5
                              class="sub-title-schedule"
                              v-if="route.details[0].imoNumber != null"
                            >
                              <b>IMO:</b>
                            </h5>
                            <p class="text-schedule">
                              {{ route.details[0].imoNumber }}
                            </p>
                          </li>
                          <li>
                            <h5 class="sub-title-schedule">
                              <b>Vessel/Voyage:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                route.details[0].vehiculeName +
                                  " / " +
                                  route.details[0].voyageNumber
                              }}
                            </p>
                          </li>
                        </ul>

                        <!-- DEADLINE Information -->
                        <h5
                          class="title-schedule mb-3"
                          style="margin-top: 25px"
                        >
                          <b-icon icon="stopwatch"></b-icon> Deadlines
                        </h5>
                        <ul>
                          <li
                            v-for="(cmaDeadline, cmaDeadlineKey) in route
                              .details[0].deadlines"
                            :key="cmaDeadlineKey"
                          >
                            <h5 class="sub-title-schedule">
                              <b>{{ cmaDeadline.deadlineKey }}:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                cmaDeadline.deadline.substring(0, 10) +
                                  " " +
                                  cmaDeadline.deadline.substring(
                                    11,
                                    cmaDeadline.deadline.length - 4
                                  )
                              }}
                            </p>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN INFORMACION DEL BARCO -->

                      <!-- DIAGRAMA DE LA RUTA -->
                      <div class="col-xl-6 schedule-route-info">
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="calendar2-check"></b-icon> Itinerary
                          details
                        </h5>
                        <ul>
                          <li
                            v-for="(routeDetail, detailKey) in route.details"
                            :key="detailKey"
                          >
                            <div>
                              <p v-if="routeDetail.arrivalDateGmt">
                                {{
                                  routeDetail.arrivalDateGmt.substring(0, 10)
                                }}
                                {{
                                  routeDetail.arrivalDateGmt.substring(12, 16)
                                }}
                              </p>
                            </div>
                            <div class="sri-circle"></div>
                            <div class="d-flex">
                              <img
                                src="/images/port.svg"
                                width="25px"
                                alt="port"
                              />
                              <p>{{ routeDetail.arrivalName }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN DIAGRAMA DE LA RUTA -->
                    </div>
                  </div>
                </div>
                <!-- FIN INFORMACION DE LA RUTA -->
              </b-tab>
            </b-tabs>
            <!-- FIN SCHEDULE INFORMATION -->

            <!-- SCHEDULE INFORMATION RESPONSIVE -->
            <div>
              <div
                class="d-block d-lg-none si-responsive mb-3"
                v-for="(route, routeKey) in cmaResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <b-button
                  v-b-toggle="
                    'responsiveCollapse_' +
                      cmaResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  style="width: 100%"
                >
                  <div class="row margin-res">
                    <!-- CONTRACT NAME -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ cmaResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN CONTRACT NAME -->

                    <!-- INFORMACION DE LA RUTA -->
                    <div class="col-12 mr-0 ml-0 si-route-info">
                      <!-- ORGIEN -->
                      <div class="origin">
                        <span>origin</span>
                        <p class="mb-0">{{ route.details[0].departureName }}</p>
                        <p v-if="route.details[0].departureDateGmt">
                          {{
                            route.details[0].departureDateGmt.substring(0, 10)
                          }}
                        </p>
                      </div>
                      <!-- FIN ORGIEN -->

                      <!-- LINEA DE RUTA -->
                      <div class="direction-desc">
                        <p class="mb-1">
                          <b>Transit Time: </b>{{ route.transitTime }} days
                        </p>
                        <p v-if="route.details.length > 1">
                          <b>Via: </b>{{ route.details[0].arrivalName }}
                        </p>
                        <p>
                          <b>Service: </b
                          >{{
                            route.details.length > 1 ? "Transhipment" : "Direct"
                          }}
                        </p>
                      </div>
                      <!-- FIN LINEA DE RUTA -->

                      <!-- DESTINO -->
                      <div class="destination">
                        <span>destination</span>
                        <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                        <p v-if="route.details[0].arrivalDateGmt">
                          {{ route.details[0].arrivalDateGmt.substring(0, 10) }}
                        </p>
                      </div>
                      <!-- FIN DESTINO -->
                    </div>
                    <!-- FIN INFORMACION DE LA RUTA -->
                  </div>
                </b-button>

                <b-collapse
                  :id="
                    'responsiveCollapse_' +
                      cmaResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  class="mt-2"
                >
                  <b-card>
                    <!-- RUTA -->
                    <div class="row">
                      <div class="col-12 d-flex align-items-center mt-3">
                        <div class="row" style="width: 100%">
                          <!-- INFORMACION DEL BARCO -->
                          <div class="col-sm-6 schedule-info">
                            <!-- Vessel Information -->
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="hdd-rack"></b-icon> Vessel
                              Information
                            </h5>

                            <ul>
                              <li>
                                <h5
                                  class="sub-title-schedule"
                                  v-if="route.details[0].imoNumber != null"
                                >
                                  <b>IMO:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{ route.details[0].imoNumber }}
                                </p>
                              </li>
                              <li>
                                <h5 class="sub-title-schedule">
                                  <b>Vessel/Voyage:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    route.details[0].vehiculeName +
                                      " / " +
                                      route.details[0].voyageNumber
                                  }}
                                </p>
                              </li>
                            </ul>

                            <!-- Vessel Information -->
                            <h5
                              class="title-schedule mb-3"
                              style="margin-top: 25px"
                            >
                              <b-icon icon="stopwatch"></b-icon> Deadlines
                            </h5>

                            <ul>
                              <li
                                v-for="(cmaDeadline, cmaDeadlineKey) in route
                                  .details[0].deadlines"
                                :key="cmaDeadlineKey"
                              >
                                <h5 class="sub-title-schedule">
                                  <b>{{ cmaDeadline.deadlineKey }}:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    cmaDeadline.deadline.substring(0, 10) +
                                      " " +
                                      cmaDeadline.deadline.substring(
                                        11,
                                        cmaDeadline.deadline.length - 4
                                      )
                                  }}
                                </p>
                              </li>
                            </ul>
                          </div>
                          <!-- FIN INFORMACION DEL BARCO -->

                          <div class="col-sm-6 schedule-route-info mt-3">
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="calendar2-check"></b-icon> Itinerary
                              details
                            </h5>
                            <ul>
                              <li
                                v-for="(routeDetail,
                                detailKey) in route.details"
                                :key="detailKey"
                              >
                                <div>
                                  <p v-if="routeDetail.arrivalDateGmt">
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        0,
                                        10
                                      )
                                    }}
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        12,
                                        16
                                      )
                                    }}
                                  </p>
                                </div>
                                <div class="sri-circle"></div>
                                <div class="d-flex">
                                  <img
                                    src="/images/port.svg"
                                    width="25px"
                                    alt="port"
                                  />
                                  <p>{{ routeDetail.arrivalName }}</p>
                                </div>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </b-card>
                </b-collapse>

                <!-- FIN INFORMACION PRINCIPAL -->
              </div>
            </div>
            <!-- FIN SCHEDULE INFORMATION RESPONSIVE -->
          </b-collapse>
          <!-- FIN SCHEDULES -->
        </div>
        <!-- FIN INFORMACION DESPLEGADA -->
      </div>
    </div>
    <!-- FIN TARJETA CMA -->

    <!-- TARJETA MAERSK -->
    <div
      class="mb-4"
      v-for="(result, key) in orderedMaerskRates"
      :key="key + 'maersk'"
    >
      <div class="result-search">
        <div class="banda-top" :class="result.companyCode">
          <span>{{ result.company }}</span>
        </div>

        <!-- INFORMACION DE TARIFA -->
        <div class="row">
          <!-- CARRIER -->
          <div
            class="
              col-12 col-lg-2
              carrier-img
              d-flex
              justify-content-center
              align-items-center
            "
            style="border-right: 1px solid #f3f3f3"
          >
            <img
              :src="
                'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
                  result.image
              "
              alt="logo"
              width="115px"
            />
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
                  <p>{{ result.departureDateGmt.substring(0, 10) }}</p>
                </div>
                <!-- FIN ORGIEN -->

                <!-- LINEA DE RUTA -->
                <div
                  class="
                    d-flex
                    flex-column
                    justify-content-center
                    align-items-center
                  "
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
                    <p class="mb-1">
                      <b>Transit Time: </b>{{ result.transitTime + " days" }}
                    </p>
                    <p>
                      <b>{{ result.vehiculeType }}:</b>
                      {{ result.vehiculeName }}
                    </p>
                  </div>
                </div>
                <!-- FIN LINEA DE RUTA -->

                <!-- DESTINO -->
                <div class="destination ml-4">
                  <span>destination</span>
                  <p class="mb-0">{{ result.arrivalTerminal }}</p>
                  <p>{{ result.arrivalDateGmt.substring(0, 10) }}</p>
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
                    <p>{{ result.departureDateGmt.substring(0, 10) }}</p>
                  </div>
                  <!-- FIN ORGIEN -->

                  <!-- DESTINO -->
                  <div class="destination align-items-start mb-3">
                    <span>destination</span>
                    <p class="mb-0">{{ result.arrivalTerminal }}</p>
                    <p>{{ result.arrivalDateGmt.substring(0, 10) }}</p>
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
                        <p class="mb-1">
                          <b>Transit Time: </b
                          >{{ result.transitTime + " days" }}
                        </p>
                      </li>
                      <li>
                        <p>
                          <b>{{ result.vehiculeType }}:</b>
                          {{ result.vehiculeName }}
                        </p>
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
                    v-for="(cont, contCode) in request.containers"
                    :key="contCode"
                  >
                    <p>
                      <b>{{ cont.code }}</b>
                    </p>
                  </div>
                </div>
                <!-- FIN PRECIO RESPONSIVE -->

                <!-- PRECIO -->
                <div class="row card-amount card-amount__res">
                  <div
                    class="col-2 pl-0 pr-0 prices-card-res"
                    :class="countContainersClass()"
                    v-for="(globalTotal, totalKey) in result.pricingDetails
                      .totalRatePerContainer"
                    :key="totalKey"
                  >
                    <p>
                      <b style="font-size: 16px"
                        >{{ globalTotal.total }}
                        <span style="font-size: 10px">{{
                          globalTotal.currencyCode
                        }}</span></b
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
                <span
                  style="color: #006bfa; text-transform: capitalize"
                  class="mr-3"
                  ><b-icon icon="check-circle-fill"></b-icon> guaranteed Price &
                  loading</span
                >
                <span
                  style="color: #006bfa; text-transform: capitalize"
                  class="mr-3"
                  ><b-icon icon="check-circle-fill"></b-icon> two-way
                  commitment</span
                >
                <a
                  v-if="result.company == 'Maersk Spot'"
                  href="https://terms.maersk.com/terms-spot-booking"
                  style="color: #071c4b"
                  target="_blank"
                >
                  T&C applicable</a
                >
                <a
                  v-else
                  href="https://terms.sealandmaersk.com/europe/terms-spot-booking"
                  style="color: #071c4b"
                  target="_blank"
                >
                  T&C applicable</a
                >
              </div>

              <div class="d-flex justify-content-end align-items-center">
                <!-- INFORMACION DESPLEGADA -->
                <div
                  :id="'my-accordion-' + result.accordion_id"
                  class="row mr-0 ml-0 accordion"
                  role="tablist"
                >
                  <b-button
                    class="rs-btn"
                    v-b-toggle="'details_' + String(result.quoteLine)"
                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>

                  <b-button
                    class="rs-btn"
                    v-b-toggle="'schedules_' + String(result.quoteLine)"
                    ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>

                  <b-button
                    class="rs-btn"
                    v-b-toggle="'detention_' + String(result.quoteLine)"
                    ><b>D&D</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                </div>
                <!-- FIN INFORMACION DESPLEGADA -->
              </div>
            </div>
            <!-- FIN OPCIONES E INFORMACION EXTRA -->
          </div>
          <!-- FIN INFORMACION PRINCIPAL -->

          <!-- ADD QUOTE BTN -->
          <div
            class="
              col-12 col-lg-2
              d-flex
              flex-column
              justify-content-center
              align-items-center
              btn-quote-res
            "
            style="border-left: 1px solid #f3f3f3"
          >
            <b-form-checkbox
              v-model="result.addToQuote"
              class="btn-add-quote"
              name="check-button"
              button
              @change="addResultToQuote(result)"
            >
              <b>add to quote</b>
            </b-form-checkbox>
            <a
              @click="openModal(result.quoteLine)"
              class="btn-add-quote btn-book"
              ><strong>BOOK</strong></a
            >
          </div>
        </div>
        <!-- FIN INFORMACION DE TARIFA -->

        <!-- DETALLES DE TARIFA -->
        <b-collapse
          class="pt-5 pb-5 pl-5 pr-5 col-12"
          :accordion="'my-accordion-' + result.accordion_id"
          role="tabpanel"
          :id="'details_' + String(result.quoteLine)"
          v-model="result.detailCollapse"
        >
          <div
            v-for="(surchargeType, surchargeKey) in result.pricingDetails
              .surcharges"
            :key="surchargeKey"
          >
            <h5>
              <b>{{
                surchargeKey
                  .substring(0, surchargeKey.length - 10)
                  .charAt(0)
                  .toUpperCase() +
                  surchargeKey.substring(0, surchargeKey.length - 10).slice(1)
              }}</b>
            </h5>

            <b-table-simple hover small responsive class="sc-table">
              <b-thead>
                <b-tr>
                  <b-th style="width: 300px">Charge</b-th>
                  <b-th style="width: 325px">Detail</b-th>
                  <!-- <b-th></b-th>
                        <b-th></b-th> -->
                  <b-th
                    style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                    v-for="(requestContainer,
                    rContainerKey) in request.containers"
                    :key="rContainerKey"
                  >
                    {{ requestContainer.code }}
                  </b-th>
                </b-tr>
              </b-thead>

              <b-tbody>
                <b-tr
                  v-for="(surchargeName, nameKey) in surchargeType"
                  :key="nameKey"
                >
                  <b-td
                    ><b>{{
                      surchargeName.chargeName != null
                        ? surchargeName.chargeCode +
                          " - " +
                          surchargeName.chargeName
                        : surchargeName.chargeCode
                    }}</b></b-td
                  >
                  <b-td>{{ surchargeName.calculationType }}</b-td>
                  <!-- <b-td></b-td>
                        <b-td></b-td> -->
                  <b-td
                    v-for="(surchargeContainer,
                    containerKey) in surchargeName.containers"
                    :key="containerKey"
                    ><p>
                      <b
                        >{{ surchargeContainer.currencyCode }}
                        {{ surchargeContainer.amount }}</b
                      >
                    </p></b-td
                  >
                </b-tr>

                <b-tr>
                  <!-- <b-td></b-td>
                        <b-td></b-td>
                        <b-td></b-td> -->
                  <b-td colspan="2" style="text-align: right"
                    ><b
                      >Total
                      {{
                        surchargeKey
                          .substring(0, surchargeKey.length - 10)
                          .charAt(0)
                          .toUpperCase() +
                          surchargeKey
                            .substring(0, surchargeKey.length - 10)
                            .slice(1)
                      }}</b
                    ></b-td
                  >
                  <b-td
                    v-for="(typeTotal, typeTotalKey) in result.pricingDetails
                      .totalRatePerType[
                      'totalRate' +
                        surchargeKey
                          .substring(0, surchargeKey.length - 10)
                          .charAt(0)
                          .toUpperCase() +
                        surchargeKey
                          .substring(0, surchargeKey.length - 10)
                          .slice(1)
                    ]"
                    :key="typeTotalKey"
                  >
                    <b
                      >{{ typeTotal.currencyCode }} {{ typeTotal.total }}
                    </b></b-td
                  >
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </div>

          <div
            v-if="
              result.additionalData.penaltyFees != null &&
                result.additionalData.penaltyFees.length > 0
            "
          >
            <h5>
              <b>{{ result.company }} Fees</b>
            </h5>

            <b-table-simple hover small responsive class="sc-table">
              <b-thead>
                <b-tr>
                  <b-th style="width: 300px">Fee</b-th>
                  <b-th style="width: 325px"></b-th>
                  <b-th
                    style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                    v-for="(requestContainer,
                    rContainerKey) in request.containers"
                    :key="rContainerKey"
                    >{{ requestContainer.code }}</b-th
                  >
                </b-tr>
              </b-thead>

              <b-tbody>
                <b-tr
                  v-for="(fee, feeKey) in result.formattedPenalties"
                  :key="feeKey"
                >
                  <b-td
                    ><b>{{ fee.name }}</b></b-td
                  >
                  <b-td style="width: 325px"></b-td>
                  <b-td
                    v-for="(maerskContainer,
                    maerskContainerKey) in containerCodesMaerskPenalties"
                    :key="maerskContainerKey"
                    ><p>
                      <b
                        >{{ fee[maerskContainer + "currency"] }}
                        {{ fee[maerskContainer] }}</b
                      >
                    </p></b-td
                  >
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </div>
        </b-collapse>
        <!-- FIN DETALLES DE TARIFA-->

        <!-- SCHEDULES -->
        <b-collapse
          :id="'schedules_' + String(result.quoteLine)"
          :accordion="'my-accordion-' + result.accordion_id"
          role="tabpanel"
          v-model="result.scheduleCollapse"
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
                      <p class="text-schedule">
                        <b
                          >{{
                            result.routingDetails[0].details[0].vehiculeName
                          }}
                          /
                          {{
                            result.routingDetails[0].details[0].voyageNumber
                          }}</b
                        >
                      </p>
                    </div>
                    <div
                      v-if="
                        result.routingDetails[0].details[0].imoNumber != null
                      "
                      class="col-lg-6"
                    >
                      <h5 class="sub-title-schedule">IMO</h5>
                      <p class="text-schedule">
                        <b>{{
                          result.routingDetails[0].details[0].imoNumber
                        }}</b>
                      </p>
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
                      v-for="(deadline, deadKey) in result.routingDetails[0]
                        .details[0].deadlines"
                      :key="deadKey"
                    >
                      <h5 class="sub-title-schedule">
                        {{ deadline.deadlineKey }}
                      </h5>
                      <p class="text-schedule">
                        <b>{{
                          deadline.deadline.substring(
                            0,
                            deadline.deadline.length - 3
                          )
                        }}</b>
                      </p>
                    </div>
                  </div>
                </div>
                <!-- FIN DEADLINE -->
              </div>
            </div>
            <!-- FIN INFOMACION DE LA API -->

            <!-- RUTA -->
            <div class="col-12 col-lg-6 d-none d-lg-flex align-items-center">
              <!-- ORIGEN -->
              <div class="origin mr-4">
                <span>origin</span>
                <p class="mb-0">{{ result.departureTerminal }}</p>
                <p>{{ result.departureDateGmt.substring(0, 10) }}</p>
              </div>
              <!-- FIN ORIGEN -->

              <!-- TT -->
              <div
                class="
                  d-flex
                  flex-column
                  justify-content-center
                  align-items-center
                "
              >
                <div class="direction-form">
                  <img
                    src="/images/logo-ship-blue.svg"
                    class="img-direct"
                    alt="bote"
                  />

                  <div
                    class="route-indirect d-flex align-items-center"
                    v-if="result.routingDetails[0].details[0].length > 1"
                  >
                    <div class="circle mr-2"></div>
                    <div class="line"></div>
                    <b-button
                      id="popover-direction"
                      class="
                        pl-0
                        pr-0
                        popover-direction
                        circle
                        fill-circle-gray
                        mr-2
                        ml-2
                      "
                    ></b-button>
                    <b-popover
                      target="popover-direction"
                      triggers="hover"
                      placement="top"
                    >
                      <template #title>Transhipments</template>
                      <ul>
                        <li
                          v-for="(trans, transKey) in result.routingDetails[0]
                            .details[0]"
                          :key="transKey"
                        >
                          {{
                            trans.departureName +
                              " - " +
                              trans.arrivalName +
                              " : " +
                              trans.arrivalDateGmt.substring(0, 10)
                          }}
                        </li>
                      </ul>
                    </b-popover>
                    <div class="line line-blue"></div>
                    <div class="circle fill-circle ml-2"></div>
                  </div>

                  <div class="route-direct d-flex align-items-center" v-else>
                    <div class="circle mr-2"></div>
                    <div class="line"></div>
                    <div class="circle fill-circle ml-2"></div>
                  </div>
                </div>

                <div class="direction-desc">
                  <p class="mb-0">
                    <b>TT: </b> {{ result.transitTime + " days" }}
                  </p>
                  <p>
                    <b>Service:</b>
                    {{
                      result.routingDetails[0].details[0].length > 1
                        ? "Transhipment"
                        : "Direct"
                    }}
                  </p>
                </div>
              </div>

              <!-- DESTINATION -->
              <div class="destination ml-4">
                <span>destination</span>
                <p class="mb-0">{{ result.arrivalTerminal }}</p>
                <p>{{ result.arrivalDateGmt.substring(0, 10) }}</p>
              </div>
              <!-- FIN DESTINATION -->
            </div>
            <!-- FIN RUTA -->

            <!-- RUTA RESPONSIVA -->
            <div
              v-if="result.routingDetails[0].details[0].length > 1"
              class="col-12 d-lg-none"
            >
              <h6>Transshipments</h6>
              <ul>
                <li
                  v-for="(trans, transKey) in result.routingDetails[0]
                    .details[0]"
                  :key="transKey"
                >
                  {{
                    trans.departureName +
                      " - " +
                      trans.arrivalName +
                      " : " +
                      trans.arrivalDateGmt.substring(0, 10)
                  }}
                </li>
              </ul>
            </div>
            <!-- FIN RUTA RESPONSIVA -->
          </div>
        </b-collapse>
        <!-- FIN SCHEDULES -->

        <!-- DETENTIONS -->
        <b-collapse
          :id="'detention_' + String(result.quoteLine)"
          v-model="result.detentionCollapse"
          class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
          :accordion="'my-accordion-' + result.accordion_id"
          role="tabpanel"
        >
          <div>
            <h5><b>Demurrage & Detention</b></h5>

            <b-table-simple hover small responsive class="sc-table mb-0">
              <b-thead>
                <b-tr>
                  <b-th colspan="3"></b-th>
                  <b-th colspan="3" style="text-align: center"
                    >Free time (days)</b-th
                  >
                </b-tr>
                <b-tr>
                  <b-th>Type (import)</b-th>
                  <b-th>Start Event</b-th>
                  <b-th></b-th>
                  <b-th
                    style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                    v-for="(requestContainer,
                    rContainerKey) in request.containers"
                    :key="rContainerKey"
                    >{{ requestContainer.code }}</b-th
                  >
                </b-tr>
              </b-thead>

              <b-tbody>
                <b-tr
                  v-for="(detention,
                  detentionKey) in result.formattedDetentions"
                  :key="detentionKey"
                >
                  <b-td
                    ><b>{{ detention.name }}</b></b-td
                  >
                  <b-td
                    ><b>{{ detention.event }}</b></b-td
                  >
                  <b-td></b-td>
                  <b-td
                    v-for="(maerskContainer,
                    maerskContainerKey) in containerCodesMaerskDetentions"
                    :key="maerskContainerKey"
                    ><p>{{ detention[maerskContainer] }}</p></b-td
                  >
                </b-tr>
              </b-tbody>
            </b-table-simple>

            <a
              v-if="result.companyCode == 'maersk'"
              href="https://assets.maerskline.com/combined-pricing-assets/maeu/dnd/free_time_offer_for_Maersk_SPOT.xlsx"
              >Export Demurrage & Detention T&C</a
            >
            <a
              v-else-if="result.companyCode == 'sealand'"
              href="https://assets.maerskline.com/combined-pricing-assets/seau/dnd/free_time_offer_for_Sealand_SPOT.xlsx"
              >Export Demurrage & Detention T&C</a
            >
          </div>
        </b-collapse>
        <!-- FIN DETENTIONS -->
      </div>

      <!--  Book Qty Modal  -->
      <b-modal
        :ref="'qty-modal_' + result.quoteLine"
        :id="'qty-modal_' + result.quoteLine"
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
              <button
                type="button"
                class="btn btn-primary"
                @click="
                  completeBook(
                    result.additionalData.deeplink,
                    result.departureTerminal,
                    result.arrivalTerminal
                  )
                "
              >
                Confirm Book
              </button>
            </div>
          </div>
        </div>
      </b-modal>
      <!--  End Modal  -->
    </div>
    <!-- FIN TARJETA MAERSK -->

    <!-- TARJETA EVERGREEN -->
    <div
      class="mb-4"
      v-for="(evergreenResult, evergreenResultKey) in orderedEvergreenRates"
      :key="evergreenResultKey + 'evergreen'"
    >
      <div class="result-search">
        <div class="banda-top evergreen"><span>EVERGREEN SPOT</span></div>

        <!-- INFORMACION DE TARIFA -->
        <div class="row">
          <!-- CARRIER -->
          <div
            class="
              col-12 col-lg-2
              carrier-img
              d-flex
              justify-content-center
              align-items-center
            "
            style="border-right: 1px solid #f3f3f3"
          >
            <img
              :src="
                'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
                  evergreenResult.image
              "
              alt="logo"
              width="115px"
            />
          </div>
          <!-- FIN CARRIER -->

          <!-- INFORMACION PRINCIPAL -->
          <div class="row col-12 col-lg-8 margin-res">
            <!-- CONTRACT NAME -->
            <div class="col-12">
              <h6 class="mt-4 mb-5 contract-title">
                {{ evergreenResult.contractReference }}
              </h6>
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
                  <p class="mb-0">
                    {{ evergreenResult.departureTerminal }}
                  </p>
                  <p>{{ evergreenResult.departureDateGmt.substring(0, 10) }}</p>
                </div>
                <!-- FIN ORGIEN -->

                <!-- LINEA DE RUTA -->
                <div
                  class="
                    d-flex
                    flex-column
                    justify-content-center
                    align-items-center
                  "
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
                    <p class="mb-1">
                      <b>Transit Time: </b>
                      {{ evergreenResult.transitTime + " days" }}
                    </p>
                    <p><b>Vessel: </b> {{ evergreenResult.vehiculeName }}</p>
                  </div>
                </div>
                <!-- FIN LINEA DE RUTA -->

                <!-- DESTINO -->
                <div class="destination ml-4">
                  <span>destination</span>
                  <p class="mb-0">
                    {{ evergreenResult.arrivalTerminal }}
                  </p>
                  <p>{{ evergreenResult.arrivalDateGmt.substring(0, 10) }}</p>
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
                    <p class="mb-1">
                      {{ evergreenResult.departureTerminal }}
                    </p>
                    <p>
                      {{ evergreenResult.departureDateGmt.substring(0, 10) }}
                    </p>
                  </div>
                  <!-- FIN ORGIEN -->

                  <!-- DESTINO -->
                  <div class="destination align-items-start mb-3">
                    <span>destination</span>
                    <p class="mb-1">
                      {{ evergreenResult.arrivalTerminal }}
                    </p>
                    <p>{{ evergreenResult.arrivalDateGmt.substring(0, 10) }}</p>
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
                        <p class="mb-1">
                          <b>Transit Time: </b
                          >{{ evergreenResult.transitTime + " days" }}
                        </p>
                      </li>
                      <li>
                        <p><b>Vessel: </b>{{ evergreenResult.vehiculeName }}</p>
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
                    v-for="(cont, contCode) in request.containers"
                    :key="contCode"
                  >
                    <p>
                      <b>{{ cont.code }}</b>
                    </p>
                  </div>
                </div>
                <!-- FIN PRECIO RESPONSIVE -->

                <!-- PRECIO -->
                <div class="row card-amount card-amount__res">
                  <div
                    class="col-2 pl-0 pr-0 prices-card-res"
                    :class="countContainersClass()"
                    v-for="(evergreenGlobalTotal,
                    evergreenTotalKey) in evergreenResult.pricingDetails
                      .totalRatePerContainer"
                    :key="evergreenTotalKey"
                  >
                    <p>
                      <b style="font-size: 16px">
                        {{ evergreenGlobalTotal.total }}
                        <span style="font-size: 10px">{{
                          evergreenGlobalTotal.currencyCode
                        }}</span></b
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
                <span style="color: #006bfa; text-transform: capitalize"
                  ><b-icon icon="check-circle-fill"></b-icon> EVERGREEN
                  SPOT</span
                >
                <p
                  class="ml-4 mb-0"
                  v-if="
                    evergreenResult.validityFrom && evergreenResult.validityTo
                  "
                >
                  <b style="font-size:11px;">VALIDITY:</b>
                  {{
                    evergreenResult.validityFrom.substring(0, 10) +
                      " / " +
                      evergreenResult.validityTo.substring(0, 10)
                  }}
                </p>
              </div>

              <div class="d-flex justify-content-end align-items-center">
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'schedules_' +
                      String(evergreenResult.contractReference) +
                      '_' +
                      String(evergreenResult.accordion_id)
                  "
                  ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'details_' +
                      String(evergreenResult.contractReference) +
                      '_' +
                      String(evergreenResult.accordion_id)
                  "
                  ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
              </div>
            </div>
            <!-- FIN OPCIONES E INFORMACION EXTRA -->
          </div>
          <!-- FIN INFORMACION PRINCIPAL -->

          <!-- ADD QUOTE BTN -->
          <div
            class="
              col-12 col-lg-2
              d-flex
              justify-content-center
              align-items-center
              btn-quote-res
            "
            style="border-left: 1px solid #f3f3f3"
          >
            <b-form-checkbox
              v-model="evergreenResult.addToQuote"
              class="btn-add-quote"
              name="check-button"
              button
              @change="addResultToQuote(evergreenResult)"
            >
              <b>add to quote</b>
            </b-form-checkbox>
          </div>
        </div>
        <!-- FIN INFORMACION DE TARIFA -->

        <!-- INFORMACION DESPLEGADA -->
        <div
          :id="'my-accordion-' + evergreenResult.accordion_id"
          class="row mr-0 ml-0 accordion"
          role="tablist"
        >
          <!-- DETALLES DE TARIFA -->
          <b-collapse
            :id="
              'details_' +
                String(evergreenResult.contractReference) +
                '_' +
                String(evergreenResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12"
            :accordion="'my-accordion-' + evergreenResult.accordion_id"
            role="tabpanel"
            v-model="evergreenResult.detailCollapse"
          >
            <div
              v-for="(evergreenSurchargeType,
              evergreenSurchargeKey) in evergreenResult.pricingDetails
                .surcharges"
              :key="evergreenSurchargeKey"
            >
              <h5>
                <b>{{
                  evergreenSurchargeKey
                    .substring(0, evergreenSurchargeKey.length - 10)
                    .charAt(0)
                    .toUpperCase() +
                    evergreenSurchargeKey
                      .substring(0, evergreenSurchargeKey.length - 10)
                      .slice(1)
                }}</b>
              </h5>

              <b-table-simple hover small responsive class="sc-table">
                <b-thead>
                  <b-tr>
                    <b-th style="width: 300px">Charge</b-th>
                    <b-th style="width: 325px">Detail</b-th>
                    <!-- <b-th></b-th>
                          <b-th></b-th> -->
                    <b-th
                      style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                      v-for="(requestContainer,
                      rContainerKey) in request.containers"
                      :key="rContainerKey"
                      >{{ requestContainer.code }}
                    </b-th>
                  </b-tr>
                </b-thead>

                <b-tbody>
                  <b-tr
                    v-for="(evergreenSurchargeName,
                    evergreenNameKey) in evergreenSurchargeType"
                    :key="evergreenNameKey"
                  >
                    <b-td
                      ><b>{{
                        evergreenSurchargeName.chargeName != null
                          ? evergreenSurchargeName.chargeCode +
                            " - " +
                            evergreenSurchargeName.chargeName
                          : evergreenSurchargeName.chargeCode
                      }}</b></b-td
                    >
                    <b-td>{{ evergreenSurchargeName.calculationType }}</b-td>
                    <!-- <b-td></b-td>
                          <b-td></b-td> -->
                    <b-td
                      v-for="(evergreenSurchargeContainer,
                      evergreenContainerKey) in evergreenSurchargeName.containers"
                      :key="evergreenContainerKey"
                      ><p>
                        <b
                          >{{ evergreenSurchargeContainer.currencyCode }}
                          {{ evergreenSurchargeContainer.amount }}</b
                        >
                      </p></b-td
                    >
                  </b-tr>

                  <b-tr>
                    <!-- <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td> -->
                    <b-td colspan="2" style="text-align: right"
                      ><b
                        >Total
                        {{
                          evergreenSurchargeKey
                            .substring(0, evergreenSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                            evergreenSurchargeKey
                              .substring(0, evergreenSurchargeKey.length - 10)
                              .slice(1)
                        }}</b
                      ></b-td
                    >
                    <b-td
                      v-for="(evergreenTypeTotal,
                      evergreenTypeTotalKey) in evergreenResult.pricingDetails
                        .totalRatePerType[
                        'totalRate' +
                          evergreenSurchargeKey
                            .substring(0, evergreenSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                          evergreenSurchargeKey
                            .substring(0, evergreenSurchargeKey.length - 10)
                            .slice(1)
                      ]"
                      :key="evergreenTypeTotalKey"
                    >
                      <b
                        >{{ evergreenTypeTotal.currencyCode }}
                        {{ evergreenTypeTotal.total }}
                      </b></b-td
                    >
                  </b-tr>
                </b-tbody>
              </b-table-simple>
            </div>
          </b-collapse>
          <!-- FIN DETALLES DE TARIFA-->

          <!-- SCHEDULES -->
          <b-collapse
            :id="
              'schedules_' +
                String(evergreenResult.contractReference) +
                '_' +
                String(evergreenResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
            :accordion="'my-accordion-' + evergreenResult.accordion_id"
            role="tabpanel"
            v-model="evergreenResult.scheduleCollapse"
          >
            <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

            <!-- SCHEDULE INFORMATION -->
            <b-tabs
              pills
              card
              vertical
              class="d-none d-lg-flex"
              v-model="evergreenResult.activeTab"
            >
              <b-tab
                v-for="(route, routeKey) in evergreenResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <template #title>
                  <div class="margin-res">
                    <!-- NOMBRE -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ evergreenResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN NOMBRE -->

                    <!-- RUTA -->
                    <div class="row col-12 mr-0 ml-0">
                      <div
                        class="col-12 d-none d-lg-flex justify-content-between"
                      >
                        <!-- ORGIEN -->
                        <div class="origin mr-4">
                          <span>origin</span>
                          <p class="mb-0">
                            {{ route.details[0].departureName }}
                          </p>
                          <p>
                            {{
                              route.details[0].departureDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN ORGIEN -->

                        <!-- LINEA DE RUTA -->
                        <div
                          class="
                            d-flex
                            flex-column
                            justify-content-center
                            align-items-center
                          "
                        >
                          <div class="direction-form">
                            <img
                              src="/images/logo-ship-blue.svg"
                              alt="bote"
                              style="top: -30px"
                            />

                            <div
                              class="route-indirect d-flex align-items-center"
                            >
                              <div class="circle mr-2"></div>
                              <div class="line"></div>
                              <div
                                class="circle fill-circle-gray mr-2 ml-2"
                              ></div>
                              <div class="line line-blue"></div>
                              <div class="circle fill-circle ml-2"></div>
                            </div>
                          </div>

                          <div class="direction-desc mt-2">
                            <p class="mb-1">
                              <b>Transit Time: </b>{{ route.transitTime }} days
                            </p>
                            <p v-if="route.details.length > 1">
                              <b>Via: </b>{{ route.details[0].arrivalName }}
                            </p>
                            <p>
                              <b>Service: </b
                              >{{
                                route.details.length > 1
                                  ? "Transhipment"
                                  : "Direct"
                              }}
                            </p>
                          </div>
                        </div>
                        <!-- FIN LINEA DE RUTA -->

                        <!-- DESTINO -->
                        <div class="destination ml-4">
                          <span>destination</span>
                          <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                          <p>
                            {{
                              route.details[0].arrivalDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN DESTINO -->
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </div>
                </template>
                <!-- FIN INFORMACION PRINCIPAL -->

                <!-- INFORMACION DE LA RUTA -->
                <div class="row">
                  <div
                    class="col-12 d-none d-lg-flex align-items-center pl-5"
                    style="border-left: 1px solid #eee"
                  >
                    <div class="row" style="width: 100%">
                      <!-- INFORMACION DEL BARCO -->
                      <div class="col-xl-6 schedule-info">
                        <!-- VESSEL Information -->
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="hdd-rack"></b-icon> Vessel Information
                        </h5>
                        <ul>
                          <li>
                            <h5
                              class="sub-title-schedule"
                              v-if="route.details[0].imoNumber != null"
                            >
                              <b>IMO:</b>
                            </h5>
                            <p class="text-schedule">
                              {{ route.details[0].imoNumber }}
                            </p>
                          </li>
                          <li>
                            <h5 class="sub-title-schedule">
                              <b>Vessel/Voyage:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                route.details[0].vehiculeName +
                                  " / " +
                                  route.details[0].voyageNumber
                              }}
                            </p>
                          </li>
                        </ul>

                        <!-- DEADLINE Information -->
                        <h5
                          class="title-schedule mb-3"
                          style="margin-top: 25px"
                        >
                          <b-icon icon="stopwatch"></b-icon> Deadlines
                        </h5>
                        <ul>
                          <li
                            v-for="(evergreenDeadline,
                            evergreenDeadlineKey) in route.details[0].deadlines"
                            :key="evergreenDeadlineKey"
                          >
                            <h5 class="sub-title-schedule">
                              <b>{{ evergreenDeadline.deadlineKey }}:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                evergreenDeadline.deadline.substring(0, 10) +
                                  " " +
                                  evergreenDeadline.deadline.substring(
                                    11,
                                    evergreenDeadline.deadline.length - 4
                                  )
                              }}
                            </p>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN INFORMACION DEL BARCO -->

                      <!-- DIAGRAMA DE LA RUTA -->
                      <div class="col-xl-6 schedule-route-info">
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="calendar2-check"></b-icon> Itinerary
                          details
                        </h5>
                        <ul>
                          <li
                            v-for="(routeDetail, detailKey) in route.details"
                            :key="detailKey"
                          >
                            <div>
                              <p>
                                {{
                                  routeDetail.arrivalDateGmt.substring(0, 10)
                                }}
                                {{
                                  routeDetail.arrivalDateGmt.substring(12, 16)
                                }}
                              </p>
                            </div>
                            <div class="sri-circle"></div>
                            <div class="d-flex">
                              <img
                                src="/images/port.svg"
                                width="25px"
                                alt="port"
                              />
                              <p>{{ routeDetail.arrivalName }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN DIAGRAMA DE LA RUTA -->
                    </div>
                  </div>
                </div>
                <!-- FIN INFORMACION DE LA RUTA -->
              </b-tab>
            </b-tabs>
            <!-- FIN SCHEDULE INFORMATION -->

            <!-- SCHEDULE INFORMATION RESPONSIVE -->
            <div>
              <div
                class="d-block d-lg-none si-responsive mb-3"
                v-for="(route, routeKey) in evergreenResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <b-button
                  v-b-toggle="
                    'responsiveCollapse_' +
                      evergreenResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  style="width: 100%"
                >
                  <div class="row margin-res">
                    <!-- CONTRACT NAME -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ evergreenResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN CONTRACT NAME -->

                    <!-- INFORMACION DE LA RUTA -->
                    <div class="col-12 mr-0 ml-0 si-route-info">
                      <!-- ORGIEN -->
                      <div class="origin">
                        <span>origin</span>
                        <p class="mb-0">{{ route.details[0].departureName }}</p>
                        <p>
                          {{
                            route.details[0].departureDateGmt.substring(0, 10)
                          }}
                        </p>
                      </div>
                      <!-- FIN ORGIEN -->

                      <!-- LINEA DE RUTA -->
                      <div class="direction-desc">
                        <p class="mb-1">
                          <b>Transit Time: </b>{{ route.transitTime }} days
                        </p>
                        <p v-if="route.details.length > 1">
                          <b>Via: </b>{{ route.details[0].arrivalName }}
                        </p>
                        <p>
                          <b>Service: </b
                          >{{
                            route.details.length > 1 ? "Transhipment" : "Direct"
                          }}
                        </p>
                      </div>
                      <!-- FIN LINEA DE RUTA -->

                      <!-- DESTINO -->
                      <div class="destination">
                        <span>destination</span>
                        <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                        <p>
                          {{ route.details[0].arrivalDateGmt.substring(0, 10) }}
                        </p>
                      </div>
                      <!-- FIN DESTINO -->
                    </div>
                    <!-- FIN INFORMACION DE LA RUTA -->
                  </div>
                </b-button>

                <b-collapse
                  :id="
                    'responsiveCollapse_' +
                      evergreenResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  class="mt-2"
                >
                  <b-card>
                    <!-- RUTA -->
                    <div class="row">
                      <div class="col-12 d-flex align-items-center mt-3">
                        <div class="row" style="width: 100%">
                          <!-- INFORMACION DEL BARCO -->
                          <div class="col-sm-6 schedule-info">
                            <!-- Vessel Information -->
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="hdd-rack"></b-icon> Vessel
                              Information
                            </h5>

                            <ul>
                              <li>
                                <h5
                                  class="sub-title-schedule"
                                  v-if="route.details[0].imoNumber != null"
                                >
                                  <b>IMO:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{ route.details[0].imoNumber }}
                                </p>
                              </li>
                              <li>
                                <h5 class="sub-title-schedule">
                                  <b>Vessel/Voyage:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    route.details[0].vehiculeName +
                                      " / " +
                                      route.details[0].voyageNumber
                                  }}
                                </p>
                              </li>
                            </ul>

                            <!-- Vessel Information -->
                            <h5
                              class="title-schedule mb-3"
                              style="margin-top: 25px"
                            >
                              <b-icon icon="stopwatch"></b-icon> Deadlines
                            </h5>

                            <ul>
                              <li
                                v-for="(evergreenDeadline,
                                evergreenDeadlineKey) in route.details[0]
                                  .deadlines"
                                :key="evergreenDeadlineKey"
                              >
                                <h5 class="sub-title-schedule">
                                  <b>{{ evergreenDeadline.deadlineKey }}:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    evergreenDeadline.deadline.substring(
                                      0,
                                      10
                                    ) +
                                      " " +
                                      evergreenDeadline.deadline.substring(
                                        11,
                                        evergreenDeadline.deadline.length - 4
                                      )
                                  }}
                                </p>
                              </li>
                            </ul>
                          </div>
                          <!-- FIN INFORMACION DEL BARCO -->

                          <div class="col-sm-6 schedule-route-info mt-3">
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="calendar2-check"></b-icon> Itinerary
                              details
                            </h5>
                            <ul>
                              <li
                                v-for="(routeDetail,
                                detailKey) in route.details"
                                :key="detailKey"
                              >
                                <div>
                                  <p>
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        0,
                                        10
                                      )
                                    }}
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        12,
                                        16
                                      )
                                    }}
                                  </p>
                                </div>
                                <div class="sri-circle"></div>
                                <div class="d-flex">
                                  <img
                                    src="/images/port.svg"
                                    width="25px"
                                    alt="port"
                                  />
                                  <p>{{ routeDetail.arrivalName }}</p>
                                </div>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </b-card>
                </b-collapse>

                <!-- FIN INFORMACION PRINCIPAL -->
              </div>
            </div>
            <!-- FIN SCHEDULE INFORMATION RESPONSIVE -->
          </b-collapse>
          <!-- FIN SCHEDULES -->
        </div>
        <!-- FIN INFORMACION DESPLEGADA -->
      </div>
    </div>
    <!-- FIN TARJETA EVERGREEN -->

    <!-- TARJETA HAPAG -->
    <div
      class="mb-4"
      v-for="(hapagResult, hapagResultKey) in orderedHapagRates"
      :key="hapagResultKey + 'hapag'"
    >
      <div class="result-search">
        <div class="banda-top hapag"><span>QUICK-QUOTES</span></div>

        <!-- INFORMACION DE TARIFA -->
        <div class="row" style="min-height: 199px !important">
          <!-- CARRIER -->
          <div
            class="
              col-12 col-lg-2
              carrier-img
              d-flex
              justify-content-center
              align-items-center
            "
            style="border-right: 1px solid #f3f3f3"
          >
            <img
              :src="
                'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
                  hapagResult.image
              "
              alt="logo"
              width="115px"
            />
          </div>
          <!-- FIN CARRIER -->

          <!-- INFORMACION PRINCIPAL -->
          <div class="row col-12 col-lg-8 margin-res">
            <!-- CONTRACT NAME -->
            <div class="col-12">
              <h6 class="mt-4 mb-5 contract-title">
                {{ hapagResult.quoteLine }}
                <!-- {{ hapagResult.contractReference }} -->
              </h6>
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
                  <p class="mb-0">
                    {{ hapagResult.departureName }}
                  </p>
                  <p v-if="hapagResult.departureDateGmt">
                    {{ hapagResult.departureDateGmt.substring(0, 10) }}
                  </p>
                </div>
                <!-- FIN ORGIEN -->

                <!-- LINEA DE RUTA -->
                <div
                  class="
                    d-flex
                    flex-column
                    justify-content-center
                    align-items-center
                  "
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
                    <p class="mb-1">
                      <b>Transit Time: </b>
                      {{ hapagResult.transitTime + " days" }}
                    </p>
                    <p v-if="hapagResult.vehiculeName">
                      <b>Vessel: </b> {{ hapagResult.vehiculeName }}
                    </p>
                  </div>
                </div>
                <!-- FIN LINEA DE RUTA -->

                <!-- DESTINO -->
                <div class="destination ml-4">
                  <span>destination</span>
                  <p class="mb-0">
                    {{ hapagResult.arrivalName }}
                  </p>
                  <p v-if="hapagResult.arrivalDateGmt">
                    {{ hapagResult.arrivalDateGmt.substring(0, 10) }}
                  </p>
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
                    <p class="mb-1">
                      {{ hapagResult.departureName }}
                    </p>
                    <p v-if="hapagResult.departureDateGmt">
                      {{ hapagResult.departureDateGmt.substring(0, 10) }}
                    </p>
                  </div>
                  <!-- FIN ORGIEN -->

                  <!-- DESTINO -->
                  <div class="destination align-items-start mb-3">
                    <span>destination</span>
                    <p class="mb-1">
                      {{ hapagResult.arrivalName }}
                    </p>
                    <p v-if="hapagResult.arrivalDateGmt">
                      {{ hapagResult.arrivalDateGmt.substring(0, 10) }}
                    </p>
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
                        <p class="mb-1">
                          <b>Transit Time: </b
                          >{{ hapagResult.transitTime + " days" }}
                        </p>
                      </li>
                      <li>
                        <p><b>Vessel: </b>{{ hapagResult.vehiculeName }}</p>
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
                    v-for="(cont, contCode) in request.containers"
                    :key="contCode"
                  >
                    <p>
                      <b>{{ cont.code }}</b>
                    </p>
                  </div>
                </div>
                <!-- FIN PRECIO RESPONSIVE -->

                <!-- PRECIO -->
                <div class="row card-amount card-amount__res">
                  <div
                    class="col-2 pl-0 pr-0 prices-card-res"
                    :class="countContainersClass()"
                    v-for="(hapagGlobalTotal, hapagTotalKey) in hapagResult
                      .pricingDetails.totalRatePerContainer"
                    :key="hapagTotalKey"
                  >
                    <p>
                      <b style="font-size: 16px">
                        {{ datalists.company_user.decimals === 1 ? hapagGlobalTotal.total : parseFloat(hapagGlobalTotal.total).toFixed(0) }}
                        <span style="font-size: 10px">{{
                          hapagGlobalTotal.currencyCode
                        }}</span></b
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
                <span style="color: #006bfa; text-transform: capitalize"
                  ><b-icon icon="check-circle-fill"></b-icon> HAPAG-LLOYD QUICK
                  QUOTE</span
                >
                <p
                  class="ml-4 mb-0"
                  v-if="hapagResult.validityFrom && hapagResult.validityTo"
                >
                  <b style="font-size:11px;">VALIDITY:</b>
                  {{
                    hapagResult.validityFrom.substring(0, 10) +
                      " / " +
                      hapagResult.validityTo.substring(0, 10)
                  }}
                </p>
              </div>

              <div class="d-flex justify-content-end align-items-center">
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'schedules_' +
                      String(hapagResult.contractReference) +
                      '_' +
                      String(hapagResult.accordion_id)
                  "
                  v-if="!hapagResult.routingDetails.length"
                  ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
                <b-button
                  class="rs-btn"
                  v-b-toggle="
                    'details_' +
                      String(hapagResult.contractReference) +
                      '_' +
                      String(hapagResult.accordion_id)
                  "
                  ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                ></b-button>
              </div>
            </div>
            <!-- FIN OPCIONES E INFORMACION EXTRA -->
          </div>
          <!-- FIN INFORMACION PRINCIPAL -->

          <!-- ADD QUOTE BTN -->
          <div
            class="
              col-12 col-lg-2
              d-flex
              justify-content-center
              align-items-center
              btn-quote-res
            "
            style="border-left: 1px solid #f3f3f3"
          >
            <b-form-checkbox
              v-model="hapagResult.addToQuote"
              class="btn-add-quote"
              name="check-button"
              button
              @change="addResultToQuote(hapagResult)"
            >
              <b>add to quote</b>
            </b-form-checkbox>
          </div>
        </div>
        <!-- FIN INFORMACION DE TARIFA -->

        <!-- INFORMACION DESPLEGADA -->
        <div
          :id="'my-accordion-' + hapagResult.accordion_id"
          class="row mr-0 ml-0 accordion"
          role="tablist"
        >
          <!-- DETALLES DE TARIFA -->
          <b-collapse
            :id="
              'details_' +
                String(hapagResult.contractReference) +
                '_' +
                String(hapagResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12"
            :accordion="'my-accordion-' + hapagResult.accordion_id"
            role="tabpanel"
            v-model="hapagResult.detailCollapse"
          >
            <div
              v-for="(hapagSurchargeType, hapagSurchargeKey) in hapagResult
                .pricingDetails.surcharges"
              :key="hapagSurchargeKey"
            >
              <h5>
                <b>{{
                  hapagSurchargeKey
                    .substring(0, hapagSurchargeKey.length - 10)
                    .charAt(0)
                    .toUpperCase() +
                    hapagSurchargeKey
                      .substring(0, hapagSurchargeKey.length - 10)
                      .slice(1)
                }}</b>
              </h5>

              <b-table-simple hover small responsive class="sc-table">
                <b-thead>
                  <b-tr>
                    <b-th style="width: 300px">Charge</b-th>
                    <b-th style="width: 325px">Detail</b-th>
                    <!-- <b-th></b-th>
                          <b-th></b-th> -->
                    <b-th
                      style="padding: 0.75rem 0.75rem 0.3rem 0.75rem !important"
                      v-for="(requestContainer,
                      rContainerKey) in request.containers"
                      :key="rContainerKey"
                      >{{ requestContainer.code }}
                    </b-th>
                  </b-tr>
                </b-thead>

                <b-tbody>
                  <b-tr
                    v-for="(hapagSurchargeName,
                    hapagNameKey) in hapagSurchargeType"
                    :key="hapagNameKey"
                  >
                    <b-td
                      ><b>{{
                        hapagSurchargeName.chargeName != null
                          ? hapagSurchargeName.chargeCode +
                            " - " +
                            hapagSurchargeName.chargeName
                          : hapagSurchargeName.chargeCode
                      }}</b></b-td
                    >
                    <b-td>{{ hapagSurchargeName.calculationType }}</b-td>
                    <!-- <b-td></b-td>
                          <b-td></b-td> -->
                    <b-td
                      v-for="(hapagSurchargeContainer,
                      hapagContainerKey) in hapagSurchargeName.containers"
                      :key="hapagContainerKey"
                      ><p>
                        <b
                          >{{ hapagSurchargeContainer.currencyCode }}
                          {{ hapagSurchargeContainer.amount }}</b
                        >
                      </p></b-td
                    >
                  </b-tr>

                  <b-tr>
                    <!-- <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td> -->
                    <b-td colspan="2" style="text-align: right"
                      ><b
                        >Total
                        {{
                          hapagSurchargeKey
                            .substring(0, hapagSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                            hapagSurchargeKey
                              .substring(0, hapagSurchargeKey.length - 10)
                              .slice(1)
                        }}</b
                      ></b-td
                    >
                    <b-td
                      v-for="(hapagTypeTotal, hapagTypeTotalKey) in hapagResult
                        .pricingDetails.totalRatePerType[
                        'totalRate' +
                          hapagSurchargeKey
                            .substring(0, hapagSurchargeKey.length - 10)
                            .charAt(0)
                            .toUpperCase() +
                          hapagSurchargeKey
                            .substring(0, hapagSurchargeKey.length - 10)
                            .slice(1)
                      ]"
                      :key="hapagTypeTotalKey"
                    >
                      <b
                        >{{ hapagTypeTotal.currency }}
                        {{ hapagTypeTotal.total }}
                      </b></b-td
                    >
                  </b-tr>
                </b-tbody>
              </b-table-simple>
            </div>
          </b-collapse>
          <!-- FIN DETALLES DE TARIFA-->

          <!-- SCHEDULES -->
          <b-collapse
            :id="
              'schedules_' +
                String(hapagResult.contractReference) +
                '_' +
                String(hapagResult.accordion_id)
            "
            class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
            :accordion="'my-accordion-' + hapagResult.accordion_id"
            role="tabpanel"
            v-model="hapagResult.scheduleCollapse"
          >
            <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

            <!-- SCHEDULE INFORMATION -->
            <b-tabs
              pills
              card
              vertical
              class="d-none d-lg-flex"
              v-model="hapagResult.activeTab"
            >
              <b-tab
                v-for="(route, routeKey) in hapagResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <template #title>
                  <div class="margin-res">
                    <!-- NOMBRE -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ hapagResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN NOMBRE -->

                    <!-- RUTA -->
                    <div class="row col-12 mr-0 ml-0">
                      <div
                        class="col-12 d-none d-lg-flex justify-content-between"
                      >
                        <!-- ORGIEN -->
                        <div class="origin mr-4">
                          <span>origin</span>
                          <p class="mb-0">
                            {{ route.details[0].departureName }}
                          </p>
                          <p v-if="route.details[0].departureDateGmt">
                            {{
                              route.details[0].departureDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN ORGIEN -->

                        <!-- LINEA DE RUTA -->
                        <div
                          class="
                            d-flex
                            flex-column
                            justify-content-center
                            align-items-center
                          "
                        >
                          <div class="direction-form">
                            <img
                              src="/images/logo-ship-blue.svg"
                              alt="bote"
                              style="top: -30px"
                            />

                            <div
                              class="route-indirect d-flex align-items-center"
                            >
                              <div class="circle mr-2"></div>
                              <div class="line"></div>
                              <div
                                class="circle fill-circle-gray mr-2 ml-2"
                              ></div>
                              <div class="line line-blue"></div>
                              <div class="circle fill-circle ml-2"></div>
                            </div>
                          </div>

                          <div class="direction-desc mt-2">
                            <p class="mb-1">
                              <b>Transit Time: </b>{{ route.transitTime }} days
                            </p>
                            <p v-if="route.details.length > 1">
                              <b>Via: </b>{{ route.details[0].arrivalName }}
                            </p>
                            <p>
                              <b>Service: </b
                              >{{
                                route.details.length > 1
                                  ? "Transhipment"
                                  : "Direct"
                              }}
                            </p>
                          </div>
                        </div>
                        <!-- FIN LINEA DE RUTA -->

                        <!-- DESTINO -->
                        <div class="destination ml-4">
                          <span>destination</span>
                          <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                          <p v-if="route.details[0].arrivalDateGmt">
                            {{
                              route.details[0].arrivalDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN DESTINO -->
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </div>
                </template>
                <!-- FIN INFORMACION PRINCIPAL -->

                <!-- INFORMACION DE LA RUTA -->
                <div class="row">
                  <div
                    class="col-12 d-none d-lg-flex align-items-center pl-5"
                    style="border-left: 1px solid #eee"
                  >
                    <div class="row" style="width: 100%">
                      <!-- INFORMACION DEL BARCO -->
                      <div class="col-xl-6 schedule-info">
                        <!-- VESSEL Information -->
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="hdd-rack"></b-icon> Vessel Information
                        </h5>
                        <ul>
                          <li>
                            <h5
                              class="sub-title-schedule"
                              v-if="route.details[0].imoNumber != null"
                            >
                              <b>IMO:</b>
                            </h5>
                            <p class="text-schedule">
                              {{ route.details[0].imoNumber }}
                            </p>
                          </li>
                          <li>
                            <h5 class="sub-title-schedule">
                              <b>Vessel/Voyage:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                route.details[0].vehiculeName +
                                  " / " +
                                  route.details[0].voyageNumber
                              }}
                            </p>
                          </li>
                        </ul>

                        <!-- DEADLINE Information -->
                        <h5
                          class="title-schedule mb-3"
                          style="margin-top: 25px"
                        >
                          <b-icon icon="stopwatch"></b-icon> Deadlines
                        </h5>
                        <ul>
                          <li
                            v-for="(hapagDeadline, hapagDeadlineKey) in route
                              .details[0].deadlines"
                            :key="hapagDeadlineKey"
                          >
                            <h5 class="sub-title-schedule">
                              <b>{{ hapagDeadline.deadlineKey }}:</b>
                            </h5>
                            <p class="text-schedule">
                              {{
                                hapagDeadline.deadline.substring(0, 10) +
                                  " " +
                                  hapagDeadline.deadline.substring(
                                    11,
                                    hapagDeadline.deadline.length - 4
                                  )
                              }}
                            </p>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN INFORMACION DEL BARCO -->

                      <!-- DIAGRAMA DE LA RUTA -->
                      <div class="col-xl-6 schedule-route-info">
                        <h5 class="title-schedule mb-3">
                          <b-icon icon="calendar2-check"></b-icon> Itinerary
                          details
                        </h5>
                        <ul>
                          <li
                            v-for="(routeDetail, detailKey) in route.details"
                            :key="detailKey"
                          >
                            <div>
                              <p v-if="routeDetail.arrivalDateGmt">
                                {{
                                  routeDetail.arrivalDateGmt.substring(0, 10)
                                }}
                                {{
                                  routeDetail.arrivalDateGmt.substring(12, 16)
                                }}
                              </p>
                            </div>
                            <div class="sri-circle"></div>
                            <div class="d-flex">
                              <img
                                src="/images/port.svg"
                                width="25px"
                                alt="port"
                              />
                              <p>{{ routeDetail.arrivalName }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <!-- FIN DIAGRAMA DE LA RUTA -->
                    </div>
                  </div>
                </div>
                <!-- FIN INFORMACION DE LA RUTA -->
              </b-tab>
            </b-tabs>
            <!-- FIN SCHEDULE INFORMATION -->

            <!-- SCHEDULE INFORMATION RESPONSIVE -->
            <div>
              <div
                class="d-block d-lg-none si-responsive mb-3"
                v-for="(route, routeKey) in hapagResult.routingDetails"
                :key="routeKey"
              >
                <!-- INFORMACION PRINCIPAL -->
                <b-button
                  v-b-toggle="
                    'responsiveCollapse_' +
                      hapagResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  style="width: 100%"
                >
                  <div class="row margin-res">
                    <!-- CONTRACT NAME -->
                    <div class="col-12">
                      <h6 class="mt-4 mb-5 contract-title">
                        {{ hapagResult.contractReference }}
                      </h6>
                    </div>
                    <!-- FIN CONTRACT NAME -->

                    <!-- INFORMACION DE LA RUTA -->
                    <div class="col-12 mr-0 ml-0 si-route-info">
                      <!-- ORGIEN -->
                      <div class="origin">
                        <span>origin</span>
                        <p class="mb-0">{{ route.details[0].departureName }}</p>
                        <p v-if="route.details[0].departureDateGmt">
                          {{
                            route.details[0].departureDateGmt.substring(0, 10)
                          }}
                        </p>
                      </div>
                      <!-- FIN ORGIEN -->

                      <!-- LINEA DE RUTA -->
                      <div class="direction-desc">
                        <p class="mb-1">
                          <b>Transit Time: </b>{{ route.transitTime }} days
                        </p>
                        <p v-if="route.details.length > 1">
                          <b>Via: </b>{{ route.details[0].arrivalName }}
                        </p>
                        <p>
                          <b>Service: </b
                          >{{
                            route.details.length > 1 ? "Transhipment" : "Direct"
                          }}
                        </p>
                      </div>
                      <!-- FIN LINEA DE RUTA -->

                      <!-- DESTINO -->
                      <div class="destination">
                        <span>destination</span>
                        <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                        <p v-if="route.details[0].arrivalDateGmt">
                          {{ route.details[0].arrivalDateGmt.substring(0, 10) }}
                        </p>
                      </div>
                      <!-- FIN DESTINO -->
                    </div>
                    <!-- FIN INFORMACION DE LA RUTA -->
                  </div>
                </b-button>

                <b-collapse
                  :id="
                    'responsiveCollapse_' +
                      hapagResult.accordion_id +
                      '_' +
                      routeKey
                  "
                  class="mt-2"
                >
                  <b-card>
                    <!-- RUTA -->
                    <div class="row">
                      <div class="col-12 d-flex align-items-center mt-3">
                        <div class="row" style="width: 100%">
                          <!-- INFORMACION DEL BARCO -->
                          <div class="col-sm-6 schedule-info">
                            <!-- Vessel Information -->
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="hdd-rack"></b-icon> Vessel
                              Information
                            </h5>

                            <ul>
                              <li>
                                <h5
                                  class="sub-title-schedule"
                                  v-if="route.details[0].imoNumber != null"
                                >
                                  <b>IMO:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{ route.details[0].imoNumber }}
                                </p>
                              </li>
                              <li>
                                <h5 class="sub-title-schedule">
                                  <b>Vessel/Voyage:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    route.details[0].vehiculeName +
                                      " / " +
                                      route.details[0].voyageNumber
                                  }}
                                </p>
                              </li>
                            </ul>

                            <!-- Vessel Information -->
                            <h5
                              class="title-schedule mb-3"
                              style="margin-top: 25px"
                            >
                              <b-icon icon="stopwatch"></b-icon> Deadlines
                            </h5>

                            <ul>
                              <li
                                v-for="(hapagDeadline,
                                hapagDeadlineKey) in route.details[0].deadlines"
                                :key="hapagDeadlineKey"
                              >
                                <h5 class="sub-title-schedule">
                                  <b>{{ hapagDeadline.deadlineKey }}:</b>
                                </h5>
                                <p class="text-schedule">
                                  {{
                                    hapagDeadline.deadline.substring(0, 10) +
                                      " " +
                                      hapagDeadline.deadline.substring(
                                        11,
                                        hapagDeadline.deadline.length - 4
                                      )
                                  }}
                                </p>
                              </li>
                            </ul>
                          </div>
                          <!-- FIN INFORMACION DEL BARCO -->

                          <div class="col-sm-6 schedule-route-info mt-3">
                            <h5 class="title-schedule mb-3">
                              <b-icon icon="calendar2-check"></b-icon> Itinerary
                              details
                            </h5>
                            <ul>
                              <li
                                v-for="(routeDetail,
                                detailKey) in route.details"
                                :key="detailKey"
                              >
                                <div>
                                  <p v-if="routeDetail.arrivalDateGmt">
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        0,
                                        10
                                      )
                                    }}
                                    {{
                                      routeDetail.arrivalDateGmt.substring(
                                        12,
                                        16
                                      )
                                    }}
                                  </p>
                                </div>
                                <div class="sri-circle"></div>
                                <div class="d-flex">
                                  <img
                                    src="/images/port.svg"
                                    width="25px"
                                    alt="port"
                                  />
                                  <p>{{ routeDetail.arrivalName }}</p>
                                </div>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- FIN RUTA -->
                  </b-card>
                </b-collapse>

                <!-- FIN INFORMACION PRINCIPAL -->
              </div>
            </div>
            <!-- FIN SCHEDULE INFORMATION RESPONSIVE -->
          </b-collapse>
          <!-- FIN SCHEDULES -->
        </div>
        <!-- FIN INFORMACION DESPLEGADA -->
      </div>
    </div>
    <!-- FIN TARJETA HAPAG -->

    <div v-if="false">
      <!-- eliminar div par que se muentren las tarjetas -->
      <!-- TARJETA YML -->
      <div
        class="mb-4"
        v-for="(cmaResult, cmaResultKey) in orderedCmaRates"
        :key="cmaResultKey + 'yml'"
      >
        <div class="result-search">
          <div class="banda-top yml"><span>YML</span></div>

          <!-- INFORMACION DE TARIFA -->
          <div class="row">
            <!-- CARRIER -->
            <div
              class="
                col-12 col-lg-2
                carrier-img
                d-flex
                justify-content-center
                align-items-center
              "
              style="border-right: 1px solid #f3f3f3"
            >
              <img
                :src="
                  'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
                    cmaResult.image
                "
                alt="logo"
                width="115px"
              />
            </div>
            <!-- FIN CARRIER -->

            <!-- INFORMACION PRINCIPAL -->
            <div class="row col-12 col-lg-8 margin-res">
              <!-- CONTRACT NAME -->
              <div class="col-12">
                <h6 class="mt-4 mb-5 contract-title">
                  {{ cmaResult.contractReference }}
                </h6>
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
                    <p class="mb-0">
                      {{ cmaResult.departureName }}
                    </p>
                    <p>{{ cmaResult.departureDateGmt.substring(0, 10) }}</p>
                  </div>
                  <!-- FIN ORGIEN -->

                  <!-- LINEA DE RUTA -->
                  <div
                    class="
                      d-flex
                      flex-column
                      justify-content-center
                      align-items-center
                    "
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
                      <p class="mb-1">
                        <b>Transit Time: </b>
                        {{ cmaResult.transitTime + " days" }}
                      </p>
                      <p><b>Vessel: </b> {{ cmaResult.vehiculeName }}</p>
                    </div>
                  </div>
                  <!-- FIN LINEA DE RUTA -->

                  <!-- DESTINO -->
                  <div class="destination ml-4">
                    <span>destination</span>
                    <p class="mb-0">
                      {{ cmaResult.arrivalName }}
                    </p>
                    <p>{{ cmaResult.arrivalDateGmt.substring(0, 10) }}</p>
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
                      <p class="mb-1">
                        {{ cmaResult.departureName }}
                      </p>
                      <p>{{ cmaResult.departureDateGmt.substring(0, 10) }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- DESTINO -->
                    <div class="destination align-items-start mb-3">
                      <span>destination</span>
                      <p class="mb-1">
                        {{ cmaResult.arrivalName }}
                      </p>
                      <p>{{ cmaResult.arrivalDateGmt.substring(0, 10) }}</p>
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
                          <p class="mb-1">
                            <b>Transit Time: </b
                            >{{ cmaResult.transitTime + " days" }}
                          </p>
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
                      v-for="(cont, contCode) in request.containers"
                      :key="contCode"
                    >
                      <p>
                        <b>{{ cont.code }}</b>
                      </p>
                    </div>
                  </div>
                  <!-- FIN PRECIO RESPONSIVE -->

                  <!-- PRECIO -->
                  <div class="row card-amount card-amount__res">
                    <div
                      class="col-2 pl-0 pr-0 prices-card-res"
                      :class="countContainersClass()"
                      v-for="(cmaGlobalTotal, cmaTotalKey) in cmaResult
                        .pricingDetails.totalRatePerContainer"
                      :key="cmaTotalKey"
                    >
                      <p>
                        <b style="font-size: 16px">
                          {{ cmaGlobalTotal.total }}
                          <span style="font-size: 10px">{{
                            cmaGlobalTotal.currencyCode
                          }}</span></b
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
                  <span style="color: #006bfa; text-transform: capitalize"
                    ><b-icon icon="check-circle-fill"></b-icon> CMA CGM My
                    PRICES</span
                  >
                  <p class="ml-4 mb-0">
                    <b style="font-size:11px;">VALIDITY:</b>
                    {{
                      cmaResult.validityFrom.substring(0, 10) +
                        " / " +
                        cmaResult.validityTo.substring(0, 10)
                    }}
                  </p>
                </div>

                <div class="d-flex justify-content-end align-items-center">
                  <b-button
                    class="rs-btn"
                    v-b-toggle="
                      'schedules_' +
                        String(cmaResult.contractReference) +
                        '_' +
                        String(cmaResult.accordion_id)
                    "
                    ><b>schedules</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                  <b-button
                    class="rs-btn"
                    v-b-toggle="
                      'details_' +
                        String(cmaResult.contractReference) +
                        '_' +
                        String(cmaResult.accordion_id)
                    "
                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                </div>
              </div>
              <!-- FIN OPCIONES E INFORMACION EXTRA -->
            </div>
            <!-- FIN INFORMACION PRINCIPAL -->

            <!-- ADD QUOTE BTN -->
            <div
              class="
                col-12 col-lg-2
                d-flex
                justify-content-center
                align-items-center
                btn-quote-res
              "
              style="border-left: 1px solid #f3f3f3"
            >
              <b-form-checkbox
                v-model="cmaResult.addToQuote"
                class="btn-add-quote"
                name="check-button"
                button
                @change="addResultToQuote(cmaResult)"
              >
                <b>add to quote</b>
              </b-form-checkbox>
            </div>
          </div>
          <!-- FIN INFORMACION DE TARIFA -->

          <!-- INFORMACION DESPLEGADA -->
          <div
            :id="'my-accordion-' + cmaResult.accordion_id"
            class="row mr-0 ml-0 accordion"
            role="tablist"
          >
            <!-- DETALLES DE TARIFA -->
            <b-collapse
              :id="
                'details_' +
                  String(cmaResult.contractReference) +
                  '_' +
                  String(cmaResult.accordion_id)
              "
              class="pt-5 pb-5 pl-5 pr-5 col-12"
              :accordion="'my-accordion-' + cmaResult.accordion_id"
              role="tabpanel"
              v-model="cmaResult.detailCollapse"
            >
              <div
                v-for="(cmaSurchargeType, cmaSurchargeKey) in cmaResult
                  .pricingDetails.surcharges"
                :key="cmaSurchargeKey"
              >
                <h5>
                  <b>{{
                    cmaSurchargeKey
                      .substring(0, cmaSurchargeKey.length - 10)
                      .charAt(0)
                      .toUpperCase() +
                      cmaSurchargeKey
                        .substring(0, cmaSurchargeKey.length - 10)
                        .slice(1)
                  }}</b>
                </h5>

                <b-table-simple hover small responsive class="sc-table">
                  <b-thead>
                    <b-tr>
                      <b-th style="width: 300px">Charge</b-th>
                      <b-th style="width: 325px">Detail</b-th>
                      <!-- <b-th></b-th>
                          <b-th></b-th> -->
                      <b-th
                        style="
                          padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;
                        "
                        v-for="(requestContainer,
                        rContainerKey) in request.containers"
                        :key="rContainerKey"
                        >{{ requestContainer.code }}
                      </b-th>
                    </b-tr>
                  </b-thead>

                  <b-tbody>
                    <b-tr
                      v-for="(cmaSurchargeName, cmaNameKey) in cmaSurchargeType"
                      :key="cmaNameKey"
                    >
                      <b-td
                        ><b>{{
                          cmaSurchargeName.chargeName != null
                            ? cmaSurchargeName.chargeCode +
                              " - " +
                              cmaSurchargeName.chargeName
                            : cmaSurchargeName.chargeCode
                        }}</b></b-td
                      >
                      <b-td>{{ cmaSurchargeName.calculationType }}</b-td>
                      <!-- <b-td></b-td>
                          <b-td></b-td> -->
                      <b-td
                        v-for="(cmaSurchargeContainer,
                        cmaContainerKey) in cmaSurchargeName.containers"
                        :key="cmaContainerKey"
                        ><p>
                          <b
                            >{{ cmaSurchargeContainer.currencyCode }}
                            {{ cmaSurchargeContainer.amount }}</b
                          >
                        </p></b-td
                      >
                    </b-tr>

                    <b-tr>
                      <!-- <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td> -->
                      <b-td colspan="2" style="text-align: right"
                        ><b
                          >Total
                          {{
                            cmaSurchargeKey
                              .substring(0, cmaSurchargeKey.length - 10)
                              .charAt(0)
                              .toUpperCase() +
                              cmaSurchargeKey
                                .substring(0, cmaSurchargeKey.length - 10)
                                .slice(1)
                          }}</b
                        ></b-td
                      >
                      <b-td
                        v-for="(cmaTypeTotal, cmaTypeTotalKey) in cmaResult
                          .pricingDetails.totalRatePerType[
                          'totalRate' +
                            cmaSurchargeKey
                              .substring(0, cmaSurchargeKey.length - 10)
                              .charAt(0)
                              .toUpperCase() +
                            cmaSurchargeKey
                              .substring(0, cmaSurchargeKey.length - 10)
                              .slice(1)
                        ]"
                        :key="cmaTypeTotalKey"
                      >
                        <b
                          >{{ cmaTypeTotal.currencyCode }}
                          {{ cmaTypeTotal.total }}
                        </b></b-td
                      >
                    </b-tr>
                  </b-tbody>
                </b-table-simple>
              </div>
            </b-collapse>
            <!-- FIN DETALLES DE TARIFA-->

            <!-- SCHEDULES -->
            <b-collapse
              :id="
                'schedules_' +
                  String(cmaResult.contractReference) +
                  '_' +
                  String(cmaResult.accordion_id)
              "
              class="pt-5 pb-5 pl-5 pr-5 col-12 schedule"
              :accordion="'my-accordion-' + cmaResult.accordion_id"
              role="tabpanel"
              v-model="cmaResult.scheduleCollapse"
            >
              <h5 class="mb-5 title-schedule"><b>Schedule Information</b></h5>

              <!-- SCHEDULE INFORMATION -->
              <b-tabs
                pills
                card
                vertical
                class="d-none d-lg-flex"
                v-model="cmaResult.activeTab"
              >
                <b-tab
                  v-for="(route, routeKey) in cmaResult.routingDetails"
                  :key="routeKey"
                >
                  <!-- INFORMACION PRINCIPAL -->
                  <template #title>
                    <div class="margin-res">
                      <!-- NOMBRE -->
                      <div class="col-12">
                        <h6 class="mt-4 mb-5 contract-title">
                          {{ cmaResult.contractReference }}
                        </h6>
                      </div>
                      <!-- FIN NOMBRE -->

                      <!-- RUTA -->
                      <div class="row col-12 mr-0 ml-0">
                        <div
                          class="
                            col-12
                            d-none d-lg-flex
                            justify-content-between
                          "
                        >
                          <!-- ORGIEN -->
                          <div class="origin mr-4">
                            <span>origin</span>
                            <p class="mb-0">
                              {{ route.details[0].departureName }}
                            </p>
                            <p>
                              {{
                                route.details[0].departureDateGmt.substring(
                                  0,
                                  10
                                )
                              }}
                            </p>
                          </div>
                          <!-- FIN ORGIEN -->

                          <!-- LINEA DE RUTA -->
                          <div
                            class="
                              d-flex
                              flex-column
                              justify-content-center
                              align-items-center
                            "
                          >
                            <div class="direction-form">
                              <img
                                src="/images/logo-ship-blue.svg"
                                alt="bote"
                                style="top: -30px"
                              />

                              <div
                                class="route-indirect d-flex align-items-center"
                              >
                                <div class="circle mr-2"></div>
                                <div class="line"></div>
                                <div
                                  class="circle fill-circle-gray mr-2 ml-2"
                                ></div>
                                <div class="line line-blue"></div>
                                <div class="circle fill-circle ml-2"></div>
                              </div>
                            </div>

                            <div class="direction-desc mt-2">
                              <p class="mb-1">
                                <b>Transit Time: </b
                                >{{ route.transitTime }} days
                              </p>
                              <p v-if="route.details.length > 1">
                                <b>Via: </b>{{ route.details[0].arrivalName }}
                              </p>
                              <p>
                                <b>Service: </b
                                >{{
                                  route.details.length > 1
                                    ? "Transhipment"
                                    : "Direct"
                                }}
                              </p>
                            </div>
                          </div>
                          <!-- FIN LINEA DE RUTA -->

                          <!-- DESTINO -->
                          <div class="destination ml-4">
                            <span>destination</span>
                            <p class="mb-0">
                              {{ route.details[0].arrivalName }}
                            </p>
                            <p>
                              {{
                                route.details[0].arrivalDateGmt.substring(0, 10)
                              }}
                            </p>
                          </div>
                          <!-- FIN DESTINO -->
                        </div>
                      </div>
                      <!-- FIN RUTA -->
                    </div>
                  </template>
                  <!-- FIN INFORMACION PRINCIPAL -->

                  <!-- INFORMACION DE LA RUTA -->
                  <div class="row">
                    <div
                      class="col-12 d-none d-lg-flex align-items-center pl-5"
                      style="border-left: 1px solid #eee"
                    >
                      <div class="row" style="width: 100%">
                        <!-- INFORMACION DEL BARCO -->
                        <div class="col-xl-6 schedule-info">
                          <!-- VESSEL Information -->
                          <h5 class="title-schedule mb-3">
                            <b-icon icon="hdd-rack"></b-icon> Vessel Information
                          </h5>
                          <ul>
                            <li>
                              <h5
                                class="sub-title-schedule"
                                v-if="route.details[0].imoNumber != null"
                              >
                                <b>IMO:</b>
                              </h5>
                              <p class="text-schedule">
                                {{ route.details[0].imoNumber }}
                              </p>
                            </li>
                            <li>
                              <h5 class="sub-title-schedule">
                                <b>Vessel/Voyage:</b>
                              </h5>
                              <p class="text-schedule">
                                {{
                                  route.details[0].vehiculeName +
                                    " / " +
                                    route.details[0].voyageNumber
                                }}
                              </p>
                            </li>
                          </ul>

                          <!-- DEADLINE Information -->
                          <h5
                            class="title-schedule mb-3"
                            style="margin-top: 25px"
                          >
                            <b-icon icon="stopwatch"></b-icon> Deadlines
                          </h5>
                          <ul>
                            <li
                              v-for="(cmaDeadline, cmaDeadlineKey) in route
                                .details[0].deadlines"
                              :key="cmaDeadlineKey"
                            >
                              <h5 class="sub-title-schedule">
                                <b>{{ cmaDeadline.deadlineKey }}:</b>
                              </h5>
                              <p class="text-schedule">
                                {{
                                  cmaDeadline.deadline.substring(0, 10) +
                                    " " +
                                    cmaDeadline.deadline.substring(
                                      11,
                                      cmaDeadline.deadline.length - 4
                                    )
                                }}
                              </p>
                            </li>
                          </ul>
                        </div>
                        <!-- FIN INFORMACION DEL BARCO -->

                        <!-- DIAGRAMA DE LA RUTA -->
                        <div class="col-xl-6 schedule-route-info">
                          <h5 class="title-schedule mb-3">
                            <b-icon icon="calendar2-check"></b-icon> Itinerary
                            details
                          </h5>
                          <ul>
                            <li
                              v-for="(routeDetail, detailKey) in route.details"
                              :key="detailKey"
                            >
                              <div>
                                <p>
                                  {{
                                    routeDetail.arrivalDateGmt.substring(0, 10)
                                  }}
                                  {{
                                    routeDetail.arrivalDateGmt.substring(12, 16)
                                  }}
                                </p>
                              </div>
                              <div class="sri-circle"></div>
                              <div class="d-flex">
                                <img
                                  src="/images/port.svg"
                                  width="25px"
                                  alt="port"
                                />
                                <p>{{ routeDetail.arrivalName }}</p>
                              </div>
                            </li>
                          </ul>
                        </div>
                        <!-- FIN DIAGRAMA DE LA RUTA -->
                      </div>
                    </div>
                  </div>
                  <!-- FIN INFORMACION DE LA RUTA -->
                </b-tab>
              </b-tabs>
              <!-- FIN SCHEDULE INFORMATION -->

              <!-- SCHEDULE INFORMATION RESPONSIVE -->
              <div>
                <div
                  class="d-block d-lg-none si-responsive mb-3"
                  v-for="(route, routeKey) in cmaResult.routingDetails"
                  :key="routeKey"
                >
                  <!-- INFORMACION PRINCIPAL -->
                  <b-button
                    v-b-toggle="
                      'responsiveCollapse_' +
                        cmaResult.accordion_id +
                        '_' +
                        routeKey
                    "
                    style="width: 100%"
                  >
                    <div class="row margin-res">
                      <!-- CONTRACT NAME -->
                      <div class="col-12">
                        <h6 class="mt-4 mb-5 contract-title">
                          {{ cmaResult.contractReference }}
                        </h6>
                      </div>
                      <!-- FIN CONTRACT NAME -->

                      <!-- INFORMACION DE LA RUTA -->
                      <div class="col-12 mr-0 ml-0 si-route-info">
                        <!-- ORGIEN -->
                        <div class="origin">
                          <span>origin</span>
                          <p class="mb-0">
                            {{ route.details[0].departureName }}
                          </p>
                          <p>
                            {{
                              route.details[0].departureDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN ORGIEN -->

                        <!-- LINEA DE RUTA -->
                        <div class="direction-desc">
                          <p class="mb-1">
                            <b>Transit Time: </b>{{ route.transitTime }} days
                          </p>
                          <p v-if="route.details.length > 1">
                            <b>Via: </b>{{ route.details[0].arrivalName }}
                          </p>
                          <p>
                            <b>Service: </b
                            >{{
                              route.details.length > 1
                                ? "Transhipment"
                                : "Direct"
                            }}
                          </p>
                        </div>
                        <!-- FIN LINEA DE RUTA -->

                        <!-- DESTINO -->
                        <div class="destination">
                          <span>destination</span>
                          <p class="mb-0">{{ route.details[0].arrivalName }}</p>
                          <p>
                            {{
                              route.details[0].arrivalDateGmt.substring(0, 10)
                            }}
                          </p>
                        </div>
                        <!-- FIN DESTINO -->
                      </div>
                      <!-- FIN INFORMACION DE LA RUTA -->
                    </div>
                  </b-button>

                  <b-collapse
                    :id="
                      'responsiveCollapse_' +
                        cmaResult.accordion_id +
                        '_' +
                        routeKey
                    "
                    class="mt-2"
                  >
                    <b-card>
                      <!-- RUTA -->
                      <div class="row">
                        <div class="col-12 d-flex align-items-center mt-3">
                          <div class="row" style="width: 100%">
                            <!-- INFORMACION DEL BARCO -->
                            <div class="col-sm-6 schedule-info">
                              <!-- Vessel Information -->
                              <h5 class="title-schedule mb-3">
                                <b-icon icon="hdd-rack"></b-icon> Vessel
                                Information
                              </h5>

                              <ul>
                                <li>
                                  <h5
                                    class="sub-title-schedule"
                                    v-if="route.details[0].imoNumber != null"
                                  >
                                    <b>IMO:</b>
                                  </h5>
                                  <p class="text-schedule">
                                    {{ route.details[0].imoNumber }}
                                  </p>
                                </li>
                                <li>
                                  <h5 class="sub-title-schedule">
                                    <b>Vessel/Voyage:</b>
                                  </h5>
                                  <p class="text-schedule">
                                    {{
                                      route.details[0].vehiculeName +
                                        " / " +
                                        route.details[0].voyageNumber
                                    }}
                                  </p>
                                </li>
                              </ul>

                              <!-- Vessel Information -->
                              <h5
                                class="title-schedule mb-3"
                                style="margin-top: 25px"
                              >
                                <b-icon icon="stopwatch"></b-icon> Deadlines
                              </h5>

                              <ul>
                                <li
                                  v-for="(cmaDeadline, cmaDeadlineKey) in route
                                    .details[0].deadlines"
                                  :key="cmaDeadlineKey"
                                >
                                  <h5 class="sub-title-schedule">
                                    <b>{{ cmaDeadline.deadlineKey }}:</b>
                                  </h5>
                                  <p class="text-schedule">
                                    {{
                                      cmaDeadline.deadline.substring(0, 10) +
                                        " " +
                                        cmaDeadline.deadline.substring(
                                          11,
                                          cmaDeadline.deadline.length - 4
                                        )
                                    }}
                                  </p>
                                </li>
                              </ul>
                            </div>
                            <!-- FIN INFORMACION DEL BARCO -->

                            <div class="col-sm-6 schedule-route-info mt-3">
                              <h5 class="title-schedule mb-3">
                                <b-icon icon="calendar2-check"></b-icon>
                                Itinerary details
                              </h5>
                              <ul>
                                <li
                                  v-for="(routeDetail,
                                  detailKey) in route.details"
                                  :key="detailKey"
                                >
                                  <div>
                                    <p>
                                      {{
                                        routeDetail.arrivalDateGmt.substring(
                                          0,
                                          10
                                        )
                                      }}
                                      {{
                                        routeDetail.arrivalDateGmt.substring(
                                          12,
                                          16
                                        )
                                      }}
                                    </p>
                                  </div>
                                  <div class="sri-circle"></div>
                                  <div class="d-flex">
                                    <img
                                      src="/images/port.svg"
                                      width="25px"
                                      alt="port"
                                    />
                                    <p>{{ routeDetail.arrivalName }}</p>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- FIN RUTA -->
                    </b-card>
                  </b-collapse>

                  <!-- FIN INFORMACION PRINCIPAL -->
                </div>
              </div>
              <!-- FIN SCHEDULE INFORMATION RESPONSIVE -->
            </b-collapse>
            <!-- FIN SCHEDULES -->
          </div>
          <!-- FIN INFORMACION DESPLEGADA -->
        </div>
      </div>
      <!-- FIN TARJETA YML -->
    </div>
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
      results: {
        maersk: [],
        cmacgm: [],
        evergreen: [],
        "hapag-lloyd": [],
      },
      containerCodesMaerskPenalties: [],
      containerCodesMaerskDetentions: [],
      resultsForQuote: [],
      accordion_id: 0,
      searchType: "FCL",
      searchActions: {},
    };
  },
  methods: {
    countContainersClass() {
      if (
        this.request.containers.length == 5 ||
        this.request.containers.length == 4
      ) {
        return "col-2";
      }

      if (this.request.containers.length == 3) {
        return "col-3";
      }
      if (this.request.containers.length == 2) {
        return "col-4";
      }
    },

    openModal(quoteId) {
      this.$bvModal.show("qty-modal_" + quoteId);
    },

    callAPIs() {
      let component = this;
      let apiOriginPorts = [];
      let apiDestinationPorts = [];
      let apiDate = component.request.dateRange.startDate.substring(0, 10);
      let apiContainers = "";
      let fullResponseLength = 0;
      var params = [];
      let reqCounter = 0;

      component.$emit("apiSearchStarted",'apiSearchStart');

      component.accordion_id = 0;

      component.request.originPorts.forEach(function(originPort) {
        if (!apiOriginPorts.includes(originPort.code)) {
          apiOriginPorts.push(originPort.code);
        }
      });

      component.request.destinationPorts.forEach(function(destinationPort) {
        if (!apiDestinationPorts.includes(destinationPort.code)) {
          apiDestinationPorts.push(destinationPort.code);
        }
      });

      apiContainers = component.setApiContainers();
      component.datalists.carriers_api.forEach(function(carrier) {
        component.results[carrier.code] = [];
      });

      if (
        this.request.carriersApi.length > 0 &&
        this.request.selectedContainerGroup.id == 1
      ) {
        
        this.request.carriersApi.forEach(function(apiCarrier){
          apiOriginPorts.forEach(function (origin) {
            apiDestinationPorts.forEach(function (destination) {
              if(component.request.selectedContainerGroup.id == 1){
                params.push({
                    originPort: origin,
                    destinationPort: destination,
                    equipmentSizeType: apiContainers,
                    departureDate: apiDate,
                    uemail: component.datalists.user.email,
                    brands: apiCarrier.code,
                  });
              }
            });
          });
        });

        params.forEach(function (paramObject){
          axios
            .get(component.datalists.api_url, {
              params: paramObject,
              headers: {
                Authorization:
                  "Bpu7Ijd4iau5zphybdbDUbfiKhPNlSXkmRBkrky0QJPQ1Aj2Ha",
                Accept: "application/json",
                "Content-type": "application/json",
              },
            })
            .then((response) => {
              response.data.forEach(function (respData) {
                if (
                  respData.company == "Maersk Spot" ||
                  respData.company == "Sealand Spot"
                ) {
                  component.results["maersk"].push(respData);
                  component.setPenalties(respData);
                  component.setDetention(respData);
                } else {
                  component.results[paramObject.brands].push(respData);
                } 

                component.request.carriersApi.forEach(function(apiCarrier){
                  if(apiCarrier.code == respData.companyCode){
                    respData.image = apiCarrier.image;
                  }
                });

                component.accordion_id += 1;
                respData.accordion_id = component.accordion_id;

                respData.addToQuote = false;
                respData.search = component.request;
                respData.originPort = paramObject.originPort;
                respData.destinationPort = paramObject.destinationPort;
                component.hideCharges(respData);
              });

              //Sending data to MixPanel
              component.$mixpanel.track("Rates Spot", {
                distinct_id: component.datalists.user.id,
                Brands: paramObject.brands,
                Company: component.datalists.company_user.name,
                Origin: paramObject.originPort,
                Destination: paramObject.destinationPort,
                Container_type: apiContainers,
              });

              reqCounter += 1;
              fullResponseLength += response.data.length;

              if(reqCounter == params.length){
                component.$emit("apiSearchDone", fullResponseLength);
              }
            })
            .catch((error) => {
              console.log(error);
              component.$emit("apiSearchDone", fullResponseLength);
            });
        });
      } else {
        component.$emit("apiSearchDone", fullResponseLength);
      }
    },

    setPenalties(responseData) {
      let finalPenalties = [];
      let penaltyCodes = [];
      let component = this;

      if (
        responseData.additionalData.penaltyFees != null &&
        responseData.additionalData.penaltyFees.length > 0
      ) {
        responseData.additionalData.penaltyFees.forEach(function(
          penaltyPerContainer
        ) {
          penaltyPerContainer.charges.forEach(function(penaltyCont) {
            if (!penaltyCodes.includes(penaltyCont.penaltyType)) {
              penaltyCodes.push(penaltyCont.penaltyType);
              finalPenalties.push({
                name: penaltyCont.displayName,
              });
            }

            if (
              !component.containerCodesMaerskPenalties.includes(
                penaltyPerContainer.containerSizeType
              )
            ) {
              component.containerCodesMaerskPenalties.push(
                penaltyPerContainer.containerSizeType
              );
            }
          });
        });

        responseData.additionalData.penaltyFees.forEach(function(
          penaltyPerContainer
        ) {
          penaltyPerContainer.charges.forEach(function(penaltyCont) {
            finalPenalties.forEach(function(final) {
              if (penaltyCont.displayName == final.name) {
                final[penaltyPerContainer.containerSizeType] =
                  penaltyCont.chargeFee;
                final[penaltyPerContainer.containerSizeType + "currency"] =
                  penaltyPerContainer.currency;
              }
            });
          });
        });

        responseData.formattedPenalties = finalPenalties;
      }
    },

    setDetention(responseData) {
      let component = this;
      let finalDetentions = [];
      let detentionCodes = [];

      if (
        responseData.additionalData.importDnDConditions != null &&
        responseData.additionalData.importDnDConditions.length > 0
      ) {
        responseData.additionalData.importDnDConditions.forEach(function(
          detention
        ) {
          if (!detentionCodes.includes(detention.chargeType)) {
            detentionCodes.push(detention.chargeType);
            finalDetentions.push({
              name: detention.chargeType,
              event: detention.freetimeStartEvent,
            });
          }

          if (
            !component.containerCodesMaerskDetentions.includes(
              detention.containerSizeType
            )
          ) {
            component.containerCodesMaerskDetentions.push(
              detention.containerSizeType
            );
          }
        });

        responseData.additionalData.importDnDConditions.forEach(function(
          detention
        ) {
          finalDetentions.forEach(function(final) {
            if (detention.chargeType == final.name) {
              final[detention.containerSizeType] =
                detention.freetimeGrantInDays;
            }
          });
        });
      }
      responseData.formattedDetentions = finalDetentions;
    },

    hideCharges(responseData) {
      if (!this.request.originCharges && !this.request.destinationCharges) {
        delete responseData.pricingDetails.surcharges.originSurcharges;
        delete responseData.pricingDetails.surcharges.destinationSurcharges;

        responseData.pricingDetails.totalRatePerContainer.forEach(function(
          totalPerCont
        ) {
          let newTotal = 0;

          newTotal =
            responseData.pricingDetails.totalRatePerType.totalRateFreight[
              responseData.pricingDetails.totalRatePerContainer.indexOf(
                totalPerCont
              )
            ].total;

          if(newTotal%1 != 0){
            newTotal = newTotal.toFixed(2);
          }
          
          totalPerCont.total = newTotal;
        });
      } else if (
        !this.request.originCharges &&
        this.request.destinationCharges
      ) {
        delete responseData.pricingDetails.surcharges.originSurcharges;

        responseData.pricingDetails.totalRatePerContainer.forEach(function(
          totalPerCont
        ) {
          let newTotal = 0;
          newTotal =
            totalPerCont.total -
            responseData.pricingDetails.totalRatePerType.totalRateOrigin[
              responseData.pricingDetails.totalRatePerContainer.indexOf(
                totalPerCont
              )
            ].total;

          newTotal = newTotal.toFixed(2);
          responseData.pricingDetails.totalRatePerContainer[
            responseData.pricingDetails.totalRatePerContainer.indexOf(
              totalPerCont
            )
          ].total = newTotal;
        });

        responseData.pricingDetails.totalRatePerType.totalRateOrigin = null;
      } else if (
        this.request.originCharges &&
        !this.request.destinationCharges
      ) {
        delete responseData.pricingDetails.surcharges.destinationSurcharges;

        responseData.pricingDetails.totalRatePerContainer.forEach(function(
          totalPerCont
        ) {
          let newTotal = 0;

          newTotal =
            totalPerCont.total -
            responseData.pricingDetails.totalRatePerType.totalRateDestination[
              responseData.pricingDetails.totalRatePerContainer.indexOf(
                totalPerCont
              )
            ].total;

          newTotal = newTotal.toFixed(2);
          responseData.pricingDetails.totalRatePerContainer[
            responseData.pricingDetails.totalRatePerContainer.indexOf(
              totalPerCont
            )
          ].total = newTotal;
        });

        responseData.pricingDetails.totalRatePerType.totalRateDestination = null;
      }
    },

    setApiContainers() {
      let component = this;
      let finalContainers = [];
      let finalContainerString = "";

      component.request.containers.forEach(function (container) {
        let containerOptions = JSON.parse(container.options);

        if (containerOptions.has_api) {
          finalContainers.push(container);
        }
      });

      finalContainers.forEach(function (container) {
        let containerString = "1x" + container.code.substring(0, 2);
        
        if (container.code.includes("HC")) {
          containerString += "HC";
        }
        if (container.code.includes("DV")) {
          containerString += "DRY";
        }
        if (container.code.includes("RF")) {
          containerString += "RF";
        }

        containerString += "x2";

        finalContainerString += containerString;

        if (
          finalContainers[
            finalContainers.indexOf(container) + 1
          ] != undefined
        ) {
          finalContainerString += ",";
        }
      });

      return finalContainerString;
    },

    addResultToQuote(result) {
      let component = this;

      if (result.addToQuote) {
        component.resultsForQuote.push(result);
      } else {
        component.resultsForQuote.forEach(function(resultQ) {
          if (result.id == resultQ.id) {
            component.resultsForQuote.splice(
              component.resultsForQuote.indexOf(resultQ),
              1
            );
          }
        });
      }

      component.$emit("addedToQuote", component.resultsForQuote);
    },

    alert(msg, type) {
      this.$toast.open({
        message: msg,
        type: type,
        duration: 5000,
        dismissible: true,
      });
    },

    completeBook(link, origin, destination) {
      let component = this;
      let qty_array = [];
      let string = "";
      let qty = "";
      let link_str = "";

      $.each(component.container_qty, function(key, value) {
        if (value > 0) {
          qty_array.push(value + "x" + key);
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
      component.alert("Redirecting to the Maersk/Sealand site", "success");
      component.$root.$emit("bv::hide::modal", "qty-modal");

      //Sending data to MixPanel
      this.$mixpanel.track("Booking", {
        distinct_id: component.datalists.user.id,
        Brand: "Maersk",
        Company: component.datalists.company_user.name,
        Origin: origin,
        Destination: destination,
        Qty: qty_array,
      });
    },
  },
  computed: {
    orderedMaerskRates: function() {
      return _.orderBy(
        this.results.maersk,
        (item) => item.pricingDetails.totalRatePerContainer[0].total,
        ["asc"]
      );

      /**var sortedArray = _(this.results.maersk).chain().sortBy(function(result) {
            if(result.pricingDetails.totalRatePerContainer[0]){
                return result.pricingDetails.totalRatePerContainer[0].total;
            }
        }).sortBy(function(result) {
            if(result.pricingDetails.totalRatePerContainer[1]){
                return result.pricingDetails.totalRatePerContainer[1].total;
            }
        }).sortBy(function(result) {
            if(result.pricingDetails.totalRatePerContainer[2]){
                return result.pricingDetails.totalRatePerContainer[2].total;
            }
        }).sortBy(function(result) {
            if(result.pricingDetails.totalRatePerContainer[3]){
                return result.pricingDetails.totalRatePerContainer[3].total;
            }
        }).value();

        return sortedArray;**/
    },

    orderedCmaRates: function() {
      return _.orderBy(
        this.results.cmacgm,
        (item) => item.pricingDetails.totalRatePerContainer[0].total,
        ["asc"]
      );
    },

    orderedEvergreenRates: function() {
      return _.orderBy(
        this.results.evergreen,
        (item) => item.pricingDetails.totalRatePerContainer[0].total,
        ["asc"]
      );
    },

    orderedHapagRates: function() {
      return _.orderBy(
        this.results["hapag-lloyd"],
        (item) => item.pricingDetails.totalRatePerContainer[0].total,
        ["asc"]
      );
    },
  },
};
</script>
