<template>
  <div class="container-cards" v-if="loaded">
    <!-- RESULTS -->
    <div v-if="finalRates.length != 0" class="row" id="top-results">
      <!-- LCL CARD -->
      <div class="col-12 mb-4" v-if="false">
        <div class="result-search">
          <!-- CONTENT MAIN CARD -->
          <div class="row">
            <!-- CARRIER -->
            <div
              class="col-12 col-sm-2 d-flex justify-content-center align-items-center"
              style="border-right: 1px solid #f3f3f3"
            >
              <img src="/images/maersk.png" alt="logo" width="115px" />
            </div>

            <!-- NAME, ORIGEN AND DESTINATION INFO -->
            <div class="row col-12 col-sm-8">
              <!-- NAME -->
              <div class="col-12">
                <h6 class="mt-4 mb-5">contract reference title</h6>
              </div>

              <!-- INFO CONTRACT -->
              <div
                class="row col-12 mr-0 ml-0"
                style="border-bottom: 1px solid #f3f3f3"
              >
                <!-- INFO CONTRACT -->
                <div class="col-12 col-sm-6 d-flex">
                  <!-- ORIGEN -->
                  <div class="origin mr-4">
                    <span>origin</span>
                    <p>Lisboa, Lis</p>
                  </div>

                  <!-- TT -->
                  <div
                    class="via d-flex flex-column justify-content-center align-items-center"
                  >
                    <div class="direction-form">
                      <img src="/images/logo-ship-blue.svg" alt="bote" />

                      <div class="line-route-direct">
                        <div class="circle mr-2"></div>
                        <div class="line"></div>
                        <div class="circle fill-circle ml-2"></div>
                      </div>
                    </div>
                    <div class="direction-desc">
                      <b>direct</b>
                      <p><b>TT:</b> 45 Days</p>
                    </div>
                  </div>

                  <!-- DESTINATION -->
                  <div class="destination ml-4">
                    <span>destination</span>
                    <p>Buenos Aires, Arg</p>
                  </div>
                </div>

                <!-- PRICE -->
                <div class="col-12 col-sm-6">
                  <div class="row justify-content-center card-amount">
                    <div class="col-12 col-sm-2">
                      <p><b>50.00</b>USD</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- OPTIONS AND VALIDITY -->
              <div
                class="col-12 mt-3 mb-3 result-action d-flex justify-content-between align-items-center"
              >
                <!-- VALIDITY -->
                <div class="d-flex align-items-center">
                  <p class="mr-4 mb-0">
                    <b>Vality:</b> 2020-20-20 / 2020-20-20
                  </p>
                  <a href="#">download contract</a>
                </div>

                <!-- OPTIONS -->
                <div class="d-flex justify-content-end align-items-center">
                  <b-button v-b-toggle.remarks1 class="rs-btn"
                    >remarks <b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                  <b-button v-b-toggle.detailed1 class="rs-btn"
                    >detailed cost <b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                </div>
              </div>
            </div>

            <!-- ADD QUOTE BTN -->
            <div
              class="col-12 col-sm-2 d-flex justify-content-center align-items-center"
              style="border-left: 1px solid #f3f3f3"
            >
              <b-form-checkbox
                v-model="rate.addToQuote"
                class="btn-add-quote"
                name="check-button"
                button
                @change="addRateToQuote(rate)"
              >
                <b>add to quote</b>
              </b-form-checkbox>
            </div>
          </div>

          <div class="row">
            <b-collapse id="detailed1" class="pt-5 pb-5 pl-5 pr-5 col-12">
              <h5><b>Freight</b></h5>

              <b-table-simple
                hover
                small
                responsive
                borderless
                class="sc-table"
              >
                <b-thead>
                  <b-tr>
                    <b-th>Charge</b-th>
                    <b-th></b-th>
                    <b-th></b-th>
                    <b-th>Units</b-th>
                    <b-th>Price Per Units</b-th>
                    <b-th>Amount</b-th>
                    <b-th>Markup</b-th>
                    <b-th>Total</b-th>
                  </b-tr>
                </b-thead>

                <b-tbody>
                  <b-tr>
                    <b-td>
                      <b>Ocean Freight</b>
                      <p>W/M</p>
                    </b-td>
                    <b-td></b-td>
                    <b-td></b-td>
                    <b-td>1.00</b-td>
                    <b-td><b>20.00</b> EUR</b-td>
                    <b-td>20</b-td>
                    <b-td>0.00</b-td>
                    <b-td><b>20.00</b> EUR</b-td>
                  </b-tr>
                </b-tbody>
              </b-table-simple>
            </b-collapse>
            <b-collapse id="remarks1" class="pt-5 pb-5 pl-5 pr-5 col-12">
              <h5><b>Remarks</b></h5>

              <b-card>
                <p v-html="rate.remarks"></p>
              </b-card>
            </b-collapse>
          </div>
        </div>
      </div>

      <!-- FCL CARD -->
      <div class="col-12 mb-4" v-for="(rate, key) in finalRates" :key="key">
        <div class="result-search">
          <!-- INFORMACION DE TARIFA -->
          <div class="row">
            <!-- CARRIER -->
            <div
              class="col-12 col-lg-2 carrier-img d-flex justify-content-center align-items-center"
              style="border-right: 1px solid #f3f3f3"
            >
              <img
                :src="'/imgcarrier/' + rate.carrier.image"
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
                  {{ rate.contract.name }}
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
                  <!-- ORIGIN -->
                  <div class="origin mr-4">
                    <span>origin</span>
                    <p>{{ rate.port_origin.display_name }}</p>
                  </div>

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

                    <div class="direction-desc">
                      <b
                        v-if="
                          rate.transit_time != undefined &&
                          rate.transit_time != undefined
                        "
                        >{{
                          rate.transit_time.service == 1
                            ? "Direct"
                            : "Transhipment"
                        }}</b
                      >
                      <b
                        v-if="
                          rate.transit_time != undefined &&
                          rate.transit_time != undefined
                        "
                        >{{
                          rate.transit_time.via ? rate.transit_time.via : ""
                        }}</b
                      >
                      <p
                        v-if="
                          rate.transit_time != null &&
                          rate.transit_time.transit_time != null
                        "
                      >
                        <b>TT:</b> {{ rate.transit_time.transit_time }}
                      </p>
                    </div>
                  </div>

                  <!-- DESTINO -->
                  <div class="destination ml-4">
                    <span>destination</span>
                    <p>{{ rate.port_destiny.display_name }}</p>
                  </div>
                </div>

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
                      <p>{{ rate.port_origin.display_name }}</p>
                    </div>
                    <!-- FIN ORGIEN -->

                    <!-- DESTINO -->
                    <div class="destination align-items-start mb-3">
                      <span>destination</span>
                      <p>{{ rate.port_destiny.display_name }}</p>
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
                          <b class="mt-2">{{
                            rate.transit_time ? rate.transit_time.via : "Direct"
                          }}</b>
                        </li>
                        <li>
                          <p>
                            <b>TT:</b>
                            {{
                              rate.transit_time
                                ? rate.transit_time.transit_time
                                : "None"
                            }}
                          </p>
                        </li>
                      </ul>
                    </div>
                    <!-- FIN LINEA DE RUTA -->
                  </div>
                  <!-- FIN TRANSIT TIME -->
                </div>
                <!-- FIN RUTA RESPONSIVA -->

                <!-- PRICES -->
                <div class="col-12 col-lg-6">
                  <!-- PRECIO RESPONSIVE -->
                  <div class="row card-amount card-amount-header__res">
                    <div
                      class="col-2 pl-0 pr-0 prices-card-res"
                      v-for="(container, requestKey) in request.containers"
                      :key="requestKey"
                    >
                      <p>
                        <b>{{ container.code }}</b>
                      </p>
                    </div>
                  </div>
                  <!-- FIN PRECIO RESPONSIVE -->

                  <div class="row card-amount card-amount__res">
                    <div
                      class="col-2 pl-0 pr-0 prices-card-res"
                      :class="countContainersClass()"
                      v-for="(container, contKey) in request.containers"
                      :key="contKey"
                    >
                      <p>
                        <b style="font-size: 16px"
                          >{{
                            rate.totals_with_markups
                              ? rate.totals_with_markups["C" + container.code]
                              : rate.totals["C" + container.code]
                          }}
                          <span style="font-size: 10px">{{
                            rate.client_currency.alphacode
                          }}</span></b
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- OPCIONES E INFORMACION EXTRA -->
              <div class="col-12 mt-3 mb-3 result-action">
                <div class="d-flex align-items-center">
                  <p class="mr-4 mb-0">
                    <b>Validity:</b>
                    {{ rate.contract.validity + " / " + rate.contract.expire }}
                  </p>
                  <!--<a href="#">download contract</a>-->
                </div>

                <div class="d-flex justify-content-end align-items-center">
                  <b-button
                    v-if="rate.remarks != '<br><br>' && rate.remarks != '<br>'"
                    class="rs-btn"
                    :class="rate.remarksCollapse ? null : 'collapsed'"
                    :aria-expanded="rate.remarksCollapse ? 'true' : 'false'"
                    :aria-controls="'remarks_' + +String(rate.id)"
                    @click="rate.remarksCollapse = !rate.remarksCollapse"
                    ><b>remarks</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                  <b-button
                    class="rs-btn"
                    :class="rate.detailCollapse ? null : 'collapsed'"
                    :aria-expanded="rate.detailCollapse ? 'true' : 'false'"
                    :aria-controls="'remarks_' + +String(rate.id)"
                    @click="rate.detailCollapse = !rate.detailCollapse"
                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon
                  ></b-button>
                </div>
              </div>
            </div>

            <!-- ADD QUOTE BTN -->
            <div
              class="col-12 col-lg-2 d-flex justify-content-center align-items-center btn-quote-res"
              style="border-left: 1px solid #f3f3f3"
            >
              <b-form-checkbox
                v-model="rate.addToQuote"
                class="btn-add-quote"
                name="check-button"
                button
                @change="addRateToQuote(rate)"
              >
                <b>add to quote</b>
              </b-form-checkbox>
            </div>
            <!-- FIN ADD QUOTE BTN -->
          </div>
          <!-- FIN INFORMACION DE TARIFA -->

          <!-- DETALLES DE TARIFA -->
          <div class="row">
            <!-- TARIFAS -->
            <b-collapse
              :id="'details_' + String(rate.id)"
              class="pt-5 pb-5 pl-5 pr-5 col-12"
              v-model="rate.detailCollapse"
            >
              <div
                v-for="(chargeArray, chargeType) in rate.charges"
                :key="chargeType"
              >
                <h5>
                  <b>{{ chargeType }}</b>
                </h5>

                <b-table-simple hover small responsive class="sc-table">
                  <b-thead>
                    <b-tr>
                      <b-th style="width: 300px">Charge</b-th>
                      <b-th style="width: 325px">Detail</b-th>
                      <!-- <b-th></b-th>
                                            <b-th></b-th> -->
                      <b-th
                        v-for="(container, contKey) in request.containers"
                        :key="contKey"
                        style="
                          padding: 0.75rem 0.75rem 0.3rem 0.75rem !important;
                        "
                      >
                        {{ container.code }}
                      </b-th>
                    </b-tr>
                  </b-thead>

                  <b-tbody>
                    <b-tr
                      v-for="(charge, chargeKey) in chargeArray"
                      :key="chargeKey"
                    >
                      <b-td
                        ><b>{{ charge.surcharge.name }}</b></b-td
                      >
                      <b-td>{{
                        charge.joint
                          ? "Per Container"
                          : charge.calculationtype.name
                      }}</b-td>
                      <!-- <b-td></b-td>
                                            <b-td></b-td> -->
                      <b-td
                        v-for="(container, contKey) in request.containers"
                        :key="contKey"
                      >
                        <p v-if="charge.container_markups != undefined">
                          {{
                            charge.joint_as == "client_currency"
                              ? charge.containers_client_currency[
                                  "C" + container.code
                                ]
                              : charge.containers["C" + container.code]
                          }}
                        </p>
                        <span
                          v-if="
                            charge.container_markups != undefined &&
                            charge.container_markups['C' + container.code] !=
                              undefined
                          "
                          class="profit"
                          >+{{
                            charge.joint_as == "client_currency"
                              ? charge.totals_markups["C" + container.code]
                              : charge.container_markups["C" + container.code]
                          }}</span
                        >
                        <b v-if="chargeType == 'Freight'">{{
                          rate.currency.alphacode
                        }}</b>
                        <b v-else-if="charge.joint_as == 'client_currency'">{{
                          charge.client_currency.alphacode
                        }}</b>
                        <b v-else-if="charge.joint_as != 'client_currency'">{{
                          charge.currency.alphacode
                        }}</b>
                        <b v-if="charge.container_markups != undefined">{{
                          charge.joint_as == "client_currency"
                            ? charge.totals_with_markups["C" + container.code]
                            : charge.containers_with_markups[
                                "C" + container.code
                              ]
                        }}</b>
                        <b v-else>{{
                          charge.joint_as == "client_currency"
                            ? charge.containers_client_currency[
                                "C" + container.code
                              ]
                            : charge.containers["C" + container.code]
                        }}</b>
                      </b-td>
                    </b-tr>

                    <b-tr>
                      <!-- <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td></b-td> -->
                      <b-td colspan="2" style="text-align: right"
                        ><b>Total {{ chargeType }}</b></b-td
                      >
                      <b-td
                        v-for="(container, contKey) in request.containers"
                        :key="contKey"
                        ><b
                          >{{
                            chargeType == "Freight"
                              ? rate.currency.alphacode
                              : rate.client_currency.alphacode
                          }}
                          {{
                            rate.charge_totals_by_type[chargeType][
                              "C" + container.code
                            ]
                          }}</b
                        ></b-td
                      >
                    </b-tr>
                  </b-tbody>
                </b-table-simple>
              </div>
            </b-collapse>
            <!-- FIN TARIFAS -->

            <!-- REMARKS -->
            <b-collapse
              :id="'remarks_' + String(rate.id)"
              class="pt-5 pb-5 pl-5 pr-5 col-12"
              v-model="rate.remarksCollapse"
            >
              <h5><b>Remarks</b></h5>

              <b-card>
                <p v-html="rate.remarks"></p>
              </b-card>
            </b-collapse>
            <!-- FIN REMARKS -->
          </div>
          <!-- FIN DETALLES DE TARIFA -->
        </div>
      </div>
    </div>

    <div v-else>
      <h1><b>No rates found for this particular route</b></h1>
    </div>

    <!-- STICKY HEADER -->
    <div id="sticky-header-results" v-bind:class="{ activeSticky: isActive }">
      <div class="container-fluid">
        <div class="row result-header">
          <div
            class="col-12 col-sm-2 d-flex justify-content-center align-items-center"
          >
            <b>carrier</b>
          </div>
          <div class="col-12 col-sm-10 btn-action-sticky">
            <b-button
              v-b-modal.add-contract
              class="btn-add-contract-fixed mr-4"
              style="
                border: none !important;
                color: #0072fc;
                font-weight: bolder;
              "
              >+ Add Contract</b-button
            >

            <b-button
              v-if="!creatingQuote"
              @click="createQuote"
              style="
                color: #0072fc;
                font-weight: bolder;
                border: 2px solid #0072fc !important;
              "
              >
                Create Quote
              
            </b-button>

            <b-button v-else b-button variant="primary">
              <div class="spinner-border text-light" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </b-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import actions from "../../actions";
export default {
  props: {
    rates: Array,
    pricelevels: Array,
    request: Object,
    datalists: Object,
  },
  components: {
    Multiselect,
  },
  data() {
    return {
      loaded: false,
      actions: actions,
      requestData: {},
      finalRates: [],
      errorsExist: false,
      responseErrors: {},
      filterBy: "",
      filterOptions: [],
      isActive: false,
      items: [],
      ratesForQuote: [],
      creatingQuote: false,
    };
  },
  created() {
    this.requestData = this.$route.query;
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
    createQuote() {
      let component = this;
      let ratesForQuote = [];

      component.creatingQuote = true;
      component.finalRates.forEach(function (rate) {
        if (rate.addToQuote) {
          ratesForQuote.push(rate);
        }
      });

      if (ratesForQuote.length == 0) {
        component.noRatesAdded = true;
        component.creatingQuote = false;
        setTimeout(function () {
          component.noRatesAdded = false;
        }, 2000);
      } else {
        if (component.requestData.requested == 0) {
          component.actions.quotes
            .create(ratesForQuote, component.$route)
            .then((response) => {
              window.location.href =
                "/api/quote/" + response.data.data.id + "/edit";
              component.creatingQuote = false;
            })
            .catch((error) => {
              if (error.status === 422) {
                component.responseErrors = error.data.errors;
                component.creatingQuote = false;
              }
            });
        } else if (component.requestData.requested == 1) {
          component.actions.quotes
            .specialduplicate(ratesForQuote)
            .then((response) => {
              window.location.href =
                "/api/quote/" + response.data.data.id + "/edit";
              component.creatingQuote = false;
            })
            .catch((error) => {
              if (error.status === 422) {
                component.responseErrors = error.data.errors;
                component.creatingQuote = false;
              }
            });
        }
      }
    },

    setFilters() {
      let component = this;

      if (component.filterBy != "") {
        component.rates.forEach(function (rate) {
          if (component.filterBy == rate.carrier.name) {
            component.finalRates.push(rate);
          }
        });
      } else {
        component.finalRates = component.rates;
      }
      component.rates.forEach(function (rate) {
        if (!component.filterOptions.includes(rate.carrier.name)) {
          component.filterOptions.push(rate.carrier.name);
        }
      });
    },
    filterCarriers() {
      let component = this;
        //console.log(this.request);
      if (component.filterBy != "") {
        component.rates.forEach(function (rate) {
          if (component.filterBy == rate.carrier.name) {
            component.finalRates.push(rate);
          }
        });
      } else {
        component.finalRates = component.rates;
      }
    },
    addRateToQuote(rate){
      let component = this;

      if(rate.addToQuote){
        component.ratesForQuote.push(rate);
      }else{
        component.ratesForQuote.forEach(function(rateQ){
          if(rate.id == rateQ.id){
            component.ratesForQuote.splice(component.ratesForQuote.indexOf(rateQ),1);
          }
        });
      }

      component.$emit("addedToQuote",component.ratesForQuote);
    },
    createQuote(){
      this.$emit('createQuote');
    }
  },
  mounted(){
    let component = this;
    //console.log(component.datalists);
  component.rates.forEach(function (rate) {
    rate.addToQuote = false;
  });
  component.finalRates = component.rates;
    //component.setFilters();
    window.document.onscroll = () => {
        let navBar = document.getElementById('top-results');
        if(window.scrollY > navBar.offsetTop){
            component.isActive = true;
        } else {
            component.isActive = false;
        }
    }
    
    this.loaded = true;
  },
}
</script>