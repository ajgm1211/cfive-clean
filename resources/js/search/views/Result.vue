<template>
    <div class="container-cards">
        
        <!-- FILTERS -->
        <div class="row mb-3" style="margin-top: 80px">
            <div class="col-12 col-sm-6 d-flex align-items-center result-and-filter">
                <h2 class="mr-5 t-recent">results found: <b>{{rates.length}}</b></h2>
                <div class="d-flex filter-search">
                    <b>filter by:</b>
                    <div style="width: 160px !important; height: 33.5px; position:relative; top: -3px ">
                            <multiselect
                                v-model="filterBy"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :options="optionsFilter"
                                placeholder="Select Filter"
                                class="s-input no-select-style "
                            >
                            </multiselect>
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type"></b-icon>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 addcontract-createquote">

                <b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>
                
                <b-button b-button variant="primary"  @click="createQuote">Create Quote</b-button>

            </div>
        </div>

        <!-- HEADER FCL -->
        <div class="row mt-4 mb-4 result-header" >

            <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center"><b>carrier</b></div>
            <div class="row col-12 col-sm-4"></div>
            <div class="row col-12 col-sm-4 d-flex align-items-center justify-content-end">
                <div 
                    class="col-12 col-sm-2 d-flex justify-content-end"
                    v-for="(container,requestKey) in request.containers"
                    :key="requestKey"
                ><b>
                    {{container.code}}
                </b></div>
            </div>

        </div>

        <!-- HEADER LCL -->
        <div class="row mt-4 mb-4 result-header" v-if="false">

            <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center"><b>carrier</b></div>
            <div class="row col-12 col-sm-8 d-flex align-items-center justify-content-between">
                <div class="col-12 col-sm-3"><b>ORIGEN</b></div>
                <div class="col-12 col-sm-3 d-flex justify-content-end"><b>DESTINO</b></div>
                <div class="col-12 col-sm-6 d-flex justify-content-center"><b>PRICE</b></div>
            </div>

        </div>

        <!-- RESULTS -->
        <div v-if="rates.length != 0" class="row" id="top-results">

            <!-- LCL CARD -->
            <div class="col-12 mb-4" v-if="false">

                <div class="result-search">

                    <!-- CONTENT MAIN CARD -->
                    <div class="row">

                        <!-- CARRIER -->
                        <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                            <img src="/images/maersk.png" alt="logo" width="115px">
                        </div>

                        <!-- NAME, ORIGEN AND DESTINATION INFO -->
                        <div class="row col-12 col-sm-8">

                            <!-- NAME -->
                            <div class="col-12">
                               <h6 class="mt-4 mb-5">contract reference title</h6>
                            </div>

                            <!-- INFO CONTRACT -->
                            <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                                <!-- INFO CONTRACT -->
                                <div class="col-12 col-sm-6 d-flex">

                                        <!-- ORIGEN -->
                                        <div class="origin mr-4">

                                            <span>origin</span>
                                            <p>Lisboa, Lis</p>

                                        </div>

                                        <!-- TT -->
                                        <div class="via d-flex flex-column justify-content-center align-items-center">

                                            <div class="direction-form">

                                                <img src="/images/logo-ship-blue.svg" alt="bote">

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
                                        <div class="col-12 col-sm-2"><p><b>50.00</b>USD</p></div>
                                    </div>
                                </div>

                            </div>

                            <!-- OPTIONS AND VALIDITY -->
                            <div class="col-12 mt-3 mb-3  result-action d-flex justify-content-between align-items-center">

                                    <!-- VALIDITY -->
                                    <div class="d-flex align-items-center">
                                        <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                        <a href="#">download contract</a>
                                    </div>

                                    <!-- OPTIONS -->
                                    <div class="d-flex justify-content-end align-items-center">
                                        <b-button v-b-toggle.remarks1 class="rs-btn">remarks <b-icon icon="caret-down-fill"></b-icon></b-button>
                                        <b-button v-b-toggle.detailed1 class="rs-btn">detailed cost <b-icon icon="caret-down-fill"></b-icon></b-button>
                                    </div>

                            </div>

                        </div>

                        <!-- ADD QUOTE BTN -->
                        <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-left: 1px solid #f3f3f3">

                                <b-form-checkbox v-model="rate.addToQuote" class="btn-add-quote" name="check-button" button>
                                    <b>add to quote</b>
                                </b-form-checkbox>

                        </div>

                    </div>

                    <div class="row">
                    
                            <b-collapse id="detailed1" class="pt-5 pb-5 pl-5 pr-5 col-12">
                            
                                    <h5><b>Freight</b></h5>

                                    <b-table-simple hover small responsive borderless class="sc-table">

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
            <div 
                class="col-12 mb-4" 
                v-for="(rate,key) in rates"
                :key="key"
            >

                <div class="result-search">

                    <!-- INFORMACION DE TARIFA -->
                    <div class="row">

                       <!-- CARRIER -->
                        <div class="col-12 col-lg-2 carrier-img d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                            <img 
                                :src="'/imgcarrier/' + rate.carrier.image"  
                                alt="logo" 
                                width="115px">
                        </div>
                        <!-- FIN CARRIER -->

                        <!-- INFORMACION PRINCIPAL -->
                        <div class="row col-12 col-lg-8 margin-res">

                            <!-- CONTRACT NAME -->
                            <div class="col-12">
                                <h6 class="mt-4 mb-5 contract-title">{{rate.contract.name}}</h6>
                            </div>
                            <!-- FIN CONTRACT NAME -->

                            <!-- RUTA Y PRECIOS -->
                            <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                                <!-- RUTA -->
                                <div class="col-12 col-lg-6 d-none d-lg-flex" style="border-bottom: 1px solid #eeeeee">

                                    <!-- ORIGIN -->
                                    <div class="origin mr-4">

                                        <span>origin</span>
                                        <p>{{rate.port_origin.display_name}}</p>

                                    </div>

                                    <!-- LINEA DE RUTA -->
                                    <div class="d-flex flex-column justify-content-center align-items-center">

                                        <div class="direction-form">

                                            <img src="/images/logo-ship-blue.svg" alt="bote">

                                            <div class="route-indirect d-flex align-items-center">
                                                <div class="circle mr-2"></div>
                                                <div class="line"></div>
                                                <div class="circle fill-circle-gray mr-2 ml-2"></div>
                                                <div class="line line-blue"></div>
                                                <div class="circle fill-circle ml-2"></div>
                                            </div>

                                        </div>

                                    
                                        <div class="direction-desc">

                                            <b class="mt-2">{{rate.transit_time ? rate.transit_time.via : "Direct"}}</b>
                                            <p><b>TT:</b> {{rate.transit_time ? rate.transit_time.transit_time : "None"}}</p>

                                        </div>

                                    </div>

                                    <!-- DESTINO -->
                                    <div class="destination ml-4">

                                        <span>destination</span>
                                        <p>{{rate.port_destiny.display_name}}</p>

                                    </div>

                                </div>

                                <!-- RUTA RESPONSIVA -->
                                <div class="row col-lg-6 d-lg-none mr-0 ml-0" style="border-bottom: 1px solid #eeeeee">
                                    
                                    <!-- DESTINOS -->
                                    <div class="col-sm-6">
                                        <!-- ORGIEN -->
                                        <div class="origin mb-3">

                                            <span>origin</span>
                                            <p>{{rate.port_origin.display_name}}</p>

                                        </div>
                                        <!-- FIN ORGIEN -->

                                        <!-- DESTINO -->
                                        <div class="destination align-items-start mb-3">

                                            <span>destination</span>
                                            <p>{{rate.port_destiny.display_name}}</p>

                                        </div>
                                        <!-- FIN DESTINO -->
                                    </div>
                                    <!-- FIN DESTINOS -->

                                    <!-- TRANSIT TIME -->
                                    <div class="col-sm-6">
                                        <!-- LINEA DE RUTA -->
                                        <div class="via">

                                            
                                            <ul class="pl-0" style="list-style: none">
                                                <li><b class="mt-2">{{rate.transit_time ? rate.transit_time.via : "Direct"}}</b></li>
                                                <li><p ><b>TT:</b> {{rate.transit_time ? rate.transit_time.transit_time : "None"}}</p></li>
                                            </ul>

                                        </div>
                                        <!-- FIN LINEA DE RUTA -->
                                    </div>
                                    <!-- FIN TRANSIT TIME -->

                                </div>
                                <!-- FIN RUTA RESPONSIVA -->

                                <!-- PRICES -->
                                <div class="col-12 col-lg-6 ">

                                    <!-- PRECIO RESPONSIVE -->
                                    <div class="row card-amount card-amount-header__res" >
                                        <div 
                                            class="col-2 pl-0 pr-0 prices-card-res"
                                            v-for="(container,requestKey) in request.containers"
                                            :key="requestKey"    
                                        >
                                            <p><b>{{container.code}}</b></p>
                                        </div>
                                    </div>
                                    <!-- FIN PRECIO RESPONSIVE -->

                                    <div class="row card-amount card-amount__res">
                                        <div 
                                            class="col-2 pl-0 pr-0 prices-card-res"
                                            v-for="(container,contKey) in request.containers"
                                            :key="contKey"
                                        >
                                            <p><b style="font-size:16px">{{ rate.totals_with_markups ? rate.totals_with_markups['C'+container.code].toFixed(2) : rate.totals['C'+container.code] }} <span style="font-size: 10px">{{rate.client_currency.alphacode}}</span></b></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- OPCIONES E INFORMACION EXTRA -->
                            <div class="col-12 mt-3 mb-3 result-action">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Validity:</b> {{rate.contract.validity + " / " + rate.contract.expire}}</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button 
                                        class="rs-btn"
                                        :class="rate.remarksCollapse ? null : 'collapsed'"
                                        :aria-expanded="rate.remarksCollapse ? 'true' : 'false'"
                                        :aria-controls="'remarks_' + + String(rate.id)"
                                        @click="rate.remarksCollapse = !rate.remarksCollapse"
                                    ><b>remarks</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button 
                                        class="rs-btn"
                                        :class="rate.detailCollapse ? null : 'collapsed'"
                                        :aria-expanded="rate.detailCollapse ? 'true' : 'false'"
                                        :aria-controls="'remarks_' + + String(rate.id)"
                                        @click="rate.detailCollapse = !rate.detailCollapse"
                                    ><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                </div>

                            </div>

                        </div>

                        <!-- ADD QUOTE BTN -->
                        <div class="col-12 col-lg-2 d-flex justify-content-center align-items-center btn-quote-res" style="border-left: 1px solid #f3f3f3">
                                <b-form-checkbox v-model="rate.addToQuote" class="btn-add-quote" name="check-button" button>
                                    <b>add to quote</b>
                                </b-form-checkbox>
                        </div>

                   </div>

                   <div class="row">
                   
                        <b-collapse :id="'details_' + String(rate.id)" class="pt-5 pb-5 pl-5 pr-5 col-12" v-model="rate.detailCollapse">
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
                        </b-collapse>
                        <b-collapse :id="'remarks_' + String(rate.id)" class="pt-5 pb-5 pl-5 pr-5 col-12" v-model="rate.remarksCollapse">

                                <h5><b>Remarks</b></h5>
                                
                                <b-card>
                                    <p v-html="rate.remarks"></p>
                                </b-card>
                            
                        </b-collapse>
                   
                   </div>

                </div>
            </div>

        </div>

        <div v-else><h1><b>No rates found for this particular route</b></h1></div>

        <!-- STICKY HEADER -->
        <div id="sticky-header-results" v-bind:class="{ activeSticky: isActive }">
            <div class="container-fluid">
                <div class="row result-header">
                    <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center"><b>carrier</b></div>
                    <div class="col-12 col-sm-10 btn-action-sticky">

                        <b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>
                        
                        <b-button @click="createQuote" style="color:#0072FC; font-weight: bolder; border: 2px solid #0072FC !important">Create Quote</b-button>

                    </div>
                    <!-- <div 
                        class="col-12 col-sm-2 d-flex justify-content-end result-header"
                        v-for="(container,requestKey) in request.containers"
                        :key="requestKey"
                    ><b>
                        {{container.code}}
                    </b></div> -->
                    
                </div>
            </div>
        </div>

       

    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone';
import Multiselect from "vue-multiselect";
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import DateRangePicker from "vue2-daterange-picker";
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
        DateRangePicker,
        vueDropzone: vue2Dropzone,
    },
    data() {
        return {
            actions: actions,
            //GENE DEFINED
            checked1: false,
            checked2: false,
            isActive: false,
            stepOne: true,
            stepTwo: false,
            stepThree: false,
            stepFour: false,
            invalidInput: false,
            invalidSurcharger: false,
            valueEq: '', 
            amount: '', 
            currency: 'USD', 
            currencySurcharge: 'USD', 
            origin: '', 
            destination: '', 
            carrier: '', 
            reference: '',
            direction: '',
            typeContract: '',
            calculationType: '',
            dataSurcharger: [],
            filterBy: 'LOWEST PRICE',
            optionsDirection: ['Import', 'Export', 'Both'],
            optionsCurrency: ['USD', 'EUR', 'MXN'],
            optionsCountries: ['Argentina', 'Arabia', 'España', 'Mexico', 'Francia'],
            optionsEquipment: ['DRY', 'REEFER', 'OPEN TOP', 'FLAT RACK'],
            optionsCarrier: ['APL', 'CCNI', 'CMA CGM', 'COSCO', 'CSAV', 'Evergreen', 'Hamburg Sub', 'Hanjin', 'Hapag Lloyd'],
            optionsTypeContract: ['Type 1', 'Type 2', 'Type 3', 'Type 4'],
            optionsCalculationType: ['Calculation 1', 'Calculation 2', 'Calculation 3', 'Calculation 4'],
            optionsFilter: ['LOWEST PRICE', 'HIGH PRICE', 'LAST DATE', 'OLD DATE'],
            items: [],
            isCompleteOne: true,
            isCompleteTwo: false,
            isCompleteThree: false,
            isCompleteFour: false,

            //DATEPICKER
            locale: 'en-US',
            dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'},
            dateRange: {
                startDate: '',
                endDate: '',
            },
        }
    },
    methods: {

        /* deleteSurcharger(index){
            this.dataSurcharger.splice(index, 1);
            //console.log(this.dataSurcharger);
        },

        addSurcharger() {

            if(this.typeContract == "" || this.calculationType == "" || this.currencySurcharge == "" ) {
                this.invalidSurcharger = true;
                return
            }

            this.invalidSurcharger = false;

            var surcharge = {
                type: this.typeContract,
                calculation: this.calculationType,
                currency: this.currencySurcharge,
                amount: this.amount
            };

            this.dataSurcharger.push(surcharge);
            
            this.typeContract = ""; this.calculationType = ""; this.currencySurcharge = ""; this.amount = "";
        },

        //FILES OPTIONS Modal
        setFiles(data){
				let file = {};
				let url = '';
				let vcomponent = this;
				let i = 0;

				let url_tags = document.getElementsByClassName("img-link");

				data.forEach(function(media){
					vcomponent.$refs.myVueDropzone.manuallyAddFile(media, media.url);
					url_tags[i].setAttribute('href', media.url);
					i+=1;
				});	
        },
            
        removeThisFile(file){
            let id = this.$route.params.id;
            
            this.actions.removefile(id, { 'id': file.id })
            .then( ( response ) => {
            })
            .catch(( data ) => {

            });
        }, */

        /* nextStep() {
            if ( this.stepOne ) {

                if (this.reference == '' || this.carrier == '' || this.valueEq == '' || this.direction == '' || this.vdata == '') {
                    this.invalidInput = true;
                    return
                }
                
                this.invalidInput = false;
                this.stepOne = false; this.stepTwo = !this.stepTwo; 
                this.isCompleteTwo = !this.isCompleteTwo;
                return
            } else if ( this.stepTwo ) {

                if (this.origin == '' || this.destination == '' || this.carrier == '' || this.currency == '' || this.equipType == '') {
                    this.invalidInput = true;
                    return
                }

                this.invalidInput = false;
                this.stepTwo = false; this.stepThree = !this.stepThree;
                this.isCompleteThree = !this.isCompleteThree;
                return
            } else if ( this.stepThree ) {

                this.invalidInput = false;
                this.stepThree = false; this.stepFour = !this.stepFour;
                this.isCompleteFour = !this.isCompleteFour;
                return
            }
        },

        backStep() {
            if ( this.stepFour ) {
                this.invalidInput = false;
                this.stepFour = false; this.stepThree = !this.stepThree;
                this.isCompleteFour = !this.isCompleteFour;
                return
            } else if ( this.stepThree ) {
                this.invalidInput = false;
                this.stepThree = false; this.stepTwo = !this.stepTwo;
                this.isCompleteThree = !this.isCompleteThree;
                return
            } else if ( this.stepTwo ) {
                this.invalidInput = false;
                this.stepTwo =  false; this.stepOne = !this.stepOne;
                this.isCompleteTwo = !this.isCompleteTwo;
                return
            }
        }, */
        
        createQuote() {
            let component = this;
            let ratesForQuote = [];

            component.rates.forEach(function (rate){
                if(rate.addToQuote){
                    ratesForQuote.push(rate);
                }
            });

            if(ratesForQuote.length == 0){
                component.noRatesAdded = true;
            }else{
                component.actions.quotes
                .create(ratesForQuote, this.$route)
                .then ((response) => {
                    window.location.href = "/api/quote/" + response.data.data.id + "/edit";
                })
            }
        },
    },
    /* watch: {
        valueEq: function() {

            if (this.valueEq == 'DRY') {
                this.items.splice({});
                this.items.push({name: 'C20DV', placeholder: '20DV'}, { name: 'C40DV', placeholder: '40DV' }, { name: 'C40HC', placeholder: '40HC' }, { name: 'C45HC', placeholder: '45HC' }, { name: 'C40NOR', placeholder: '40NOR' }); 
                return
            }

            if (this.valueEq == 'REEFER') {
                this.items.splice({});
                this.items.push({name: 'C20RF', placeholder: '20RF'}, { name: 'C40RF', placeholder: '40RF' }, { name: 'C40HCRF', placeholder: '40HCRF' }); 
                return
            }

            if (this.valueEq == 'OPEN TOP') {
                this.items.splice({});
                this.items.push({name: 'C20OT', placeholder: '20OT'}, { name: 'C40OT', placeholder: '40OT' }); 
                return
            }

            if (this.valueEq == 'FLAT RACK') {
                this.items.splice({});
                this.items.push({name: 'C20FR', placeholder: '20FR'}, { name: 'C40FR', placeholder: '40FR' }); 
                return
            }

        }
    }, */
    mounted(){
        let component = this;

        //console.log(component.request);
        //console.log(component.datalists);

        //console.log(component.rates);

        component.rates.forEach(function (rate){
            rate.addToQuote = false;
        });

        window.document.onscroll = () => {
            let navBar = document.getElementById('top-results');
            if(window.scrollY > navBar.offsetTop){
                component.isActive = true;
            } else {
                component.isActive = false;
            }
        }
    }
}
</script>

