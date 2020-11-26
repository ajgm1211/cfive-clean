<template>
    <div class="pr-5 pl-5">

        <div class="row mt-5 mb-3">
            <div class="col-12 col-sm-6 d-flex align-items-center">
                <h2 class="mr-2 t-recent">results found: <b>10</b></h2>
                <div>
                    <b>filter by</b>
                </div>
            </div>

            <div class="col-12 col-sm-6 d-flex justify-content-end align-items-center">

                <b-button v-b-modal.add-contract class="add-contract">+ Add Contract</b-button>
                
                <b-button class="btn-create-quote">Create Quote</b-button>

            </div>
        </div>

        <div class="row mt-4 mb-4 result-header">

            <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center"><b>carrier</b></div>
            <div class="col-12 col-sm-3"></div>
            <div class="row col-12 col-sm-5 d-flex align-items-center justify-content-end">
                <div class="col-12 col-sm-2"><b>20DV</b></div>
                <div class="col-12 col-sm-2"><b>40DV</b></div>
                <div class="col-12 col-sm-2"><b>40HC</b></div>
            </div>

        </div>

        <div class="row" id="top-results">

            <div class="col-12 mb-4">
                <div class="result-search">

                   <div class="row">

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                        <img src="/images/altius.png" alt="logo">
                       </div>

                       <div class="row col-12 col-sm-8">

                           <div class="col-12">
                               <h6 class="mt-4 mb-5">contact reference title</h6>
                           </div>

                           <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                               <div class="col-12 col-sm-5 d-flex">
                                    <div class="origin mr-4">

                                        <span>origin</span>
                                        <p>Lisboa, Lis</p>

                                    </div>
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
                                    <div class="destination ml-4">

                                        <span>destination</span>
                                        <p>Buenos Aires, Arg</p>

                                    </div>
                               </div>

                               <div class="col-12 col-sm-7">
                                   <div class="row justify-content-end card-amount">
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                   </div>
                               </div>

                           </div>

                           <div class="col-12 mt-2 mb-2  result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks class="rs-btn">remarks <b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed class="rs-btn">detailed cost <b-icon icon="caret-down-fill"></b-icon></b-button>
                                </div>

                            </div>

                        </div>

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-left: 1px solid #f3f3f3">

                            <b-form-checkbox v-model="checked1" class="btn-add-quote" name="check-button" button>
                                <b>add to quote</b>
                            </b-form-checkbox>

                       </div>

                   </div>

                   <div class="row">
                   
                        <b-collapse id="detailed" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
                                <h5><b>freight</b></h5>

                                <b-table-simple hover small class="sc-table">

                                    <b-thead>
                                        <b-tr>
                                            <b-th>Charge</b-th>
                                            <b-th>Detail</b-th>
                                            <b-th></b-th>
                                            <b-th></b-th>
                                            <b-th>20DV</b-th>
                                            <b-th>40DV</b-th>
                                            <b-th>40HC</b-th>
                                        </b-tr>
                                    </b-thead>

                                    <b-tbody>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td><b>Total Freight</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                        </b-tr>
                                    </b-tbody>
                                
                                </b-table-simple>

                        </b-collapse>
                        <b-collapse id="remarks" class="pt-5 pb-5 pl-5 pr-5 col-12">

                                <h5><b>remarks</b></h5>
                                
                                <b-card>
                                    <p>esos son los remarks</p>
                                </b-card>
                            
                        </b-collapse>
                   
                   </div>

                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="result-search">

                   <div class="row">

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                        <img src="/images/altius.png" alt="logo">
                       </div>

                       <div class="row col-12 col-sm-8">

                           <div class="col-12">
                               <h6 class="mt-4 mb-5">contact reference title</h6>
                           </div>

                           <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                               <div class="col-12 col-sm-5 d-flex">
                                   <div class="origin mr-4">

                                   <span>origin</span>
                                   <p>Lisboa, Lis</p>

                               </div>
                               <div class="via d-flex flex-column justify-content-center align-items-center">

                                   <div class="direction-form route-indirect">

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

                                       <b>madrid españa</b>
                                       <p><b>TT:</b> 45 Days</p>

                                   </div>

                               </div>
                               <div class="destination ml-4">

                                   <span>destination</span>
                                   <p>Buenos Aires, Arg</p>

                               </div>
                               </div>

                               <div class="col-12 col-sm-7">
                                   <div class="row justify-content-end card-amount">
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                   </div>
                               </div>

                           </div>

                           <div class="col-12 mt-2 mb-2 result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks class="rs-btn"><b>remarks</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed class="rs-btn"><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                </div>

                            </div>

                        </div>

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-left: 1px solid #f3f3f3">

                            <b-form-checkbox v-model="checked2" class="btn-add-quote" name="check-button" button>
                                <b>add to quote</b>
                            </b-form-checkbox>

                       </div>

                   </div>

                   <div class="row">
                   
                        <b-collapse id="detailed" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
                                <h5><b>freight</b></h5>

                                <b-table-simple hover small class="sc-table">

                                    <b-thead>
                                        <b-tr>
                                            <b-th>Charge</b-th>
                                            <b-th>Detail</b-th>
                                            <b-th></b-th>
                                            <b-th></b-th>
                                            <b-th>20DV</b-th>
                                            <b-th>40DV</b-th>
                                            <b-th>40HC</b-th>
                                        </b-tr>
                                    </b-thead>

                                    <b-tbody>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                            <b-td>100<span class="profit">+100</span><b>USD 200</b></b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td><b>Total Freight</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                        </b-tr>
                                    </b-tbody>
                                
                                </b-table-simple>

                        </b-collapse>
                        <b-collapse id="remarks" class="pt-5 pb-5 pl-5 pr-5 col-12">

                                <h5><b>remarks</b></h5>
                                
                                <b-card>
                                    <p>esos son los remarks</p>
                                </b-card>
                            
                        </b-collapse>
                   
                   </div>

                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="result-search">

                   <div class="row">

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-right: 1px solid #f3f3f3">
                        <img src="/images/altius.png" alt="logo">
                       </div>

                       <div class="row col-12 col-sm-8">

                           <div class="col-12">
                               <h6 class="mt-4 mb-5">contact reference title</h6>
                           </div>

                           <div class="row col-12 mr-0 ml-0" style="border-bottom: 1px solid #f3f3f3">

                               <div class="col-12 col-sm-5 d-flex">
                                    <div class="origin mr-4">

                                        <span>origin</span>
                                        <p>Lisboa, Lis</p>

                                    </div>
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
                                    <div class="destination ml-4">

                                        <span>destination</span>
                                        <p>Buenos Aires, Arg</p>

                                    </div>
                               </div>

                               <div class="col-12 col-sm-7">
                                   <div class="row justify-content-end card-amount">
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                       <div class="col-12 col-sm-2"><p><b>1.00</b><sub>USD</sub></p></div>
                                   </div>
                               </div>

                           </div>

                           <div class="col-12 mt-2 mb-2  result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks class="rs-btn">remarks <b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed class="rs-btn">detailed cost <b-icon icon="caret-down-fill"></b-icon></b-button>
                                </div>

                            </div>

                        </div>

                       <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center" style="border-left: 1px solid #f3f3f3">

                            <b-form-checkbox v-model="checked3" class="btn-add-quote" name="check-button" button>
                                <b>add to quote</b>
                            </b-form-checkbox>

                       </div>

                   </div>

                   <div class="row">
                   
                        <b-collapse id="detailed" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
                                <h5><b>freight</b></h5>

                                <b-table-simple hover small class="sc-table">

                                    <b-thead>
                                        <b-tr>
                                            <b-th>Charge</b-th>
                                            <b-th>Detail</b-th>
                                            <b-th></b-th>
                                            <b-th></b-th>
                                            <b-th>20DV</b-th>
                                            <b-th>40DV</b-th>
                                            <b-th>40HC</b-th>
                                        </b-tr>
                                    </b-thead>

                                    <b-tbody>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td><b>Ocean Freight</b></b-td>
                                            <b-td>Per Container</b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                            <b-td>USD 1.00</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td><b>Total Freight</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                            <b-td><b>USD 5000</b></b-td>
                                        </b-tr>
                                    </b-tbody>
                                
                                </b-table-simple>

                        </b-collapse>
                        <b-collapse id="remarks" class="pt-5 pb-5 pl-5 pr-5 col-12">

                                <h5><b>remarks</b></h5>
                                
                                <b-card>
                                    <p>esos son los remarks</p>
                                </b-card>
                            
                        </b-collapse>
                   
                   </div>

                </div>
            </div>

        </div>

        <div id="sticky-header-results" v-bind:class="{ activeSticky: isActive }">
            <div class="container-fluid">
                <div class="row result-header">
                    <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center"><b>carrier</b></div>
                    <div class="col-12 col-sm-3"></div>
                    <div class="row col-12 col-sm-5 d-flex align-items-center justify-content-end">
                        <div class="col-12 col-sm-2"><b>20DV</b></div>
                        <div class="col-12 col-sm-2"><b>40DV</b></div>
                        <div class="col-12 col-sm-2"><b>40HC</b></div>
                    </div>
                    <div class="col-12 col-sm-2 d-flex justify-content-end align-items-center">

                        <b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>
                        
                        <b-button class="btn-create-quote">Create Quote</b-button>

                    </div>
                </div>
            </div>
        </div>

        <b-modal  id="add-contract" centered title="Created Contract" ref="my-modal" hide-footer>
            <form action="/action_page.php">

                <fieldset v-if="stepOne">
                    <legend>Contract</legend>

                    <div class="row">
                        <div class="col-12">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <multiselect
                                    v-model="valueOrigen"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsEquipment"
                                    placeholder="From"
                                >
                        </multiselect>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <multiselect
                                    v-model="valueEq"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsEquipment"
                                    placeholder="Equipment"
                                >
                        </multiselect>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                    </div>
                </fieldset>
    
                <fieldset v-if="stepTwo">
                    <legend>Update Ocean Freight</legend>

                    <div class="row">
                        <div class="col-12">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>

                        <div v-for="(item, index) in items" class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                :name="item.name"
                                :placeholder="item.placeholder"
                                ></b-form-input>
                            </label>
                        </div>
                    </div>
                    
                </fieldset>

                <fieldset v-if="stepThree">
                    <legend>Surcharges</legend>

                    <div class="row">
                        <div class="col-12">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                ></b-form-input>
                            </label>
                        </div>
                    </div>
                    
                </fieldset>

                <fieldset v-if="stepFour">
                    <legend>Files:</legend>
                </fieldset>

                <div class="footer-add-contract-modal">   
                    <b-button v-if="stepTwo || stepThree || stepFour" v-on:click="backStep" variant="link">Back</b-button>
                    <b-button v-on:click="nextStep" class="btn-create-quote">Continue</b-button>
                    <b-button v-if="stepFour" class="btn-create-quote">Created Contract</b-button>
                </div>

            </form> 
        </b-modal>

    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue-multiselect/dist/vue-multiselect.min.css";
export default {
    components: {
        Multiselect,
        DateRangePicker,
    },
    data() {
        return {
            checked1: false,
            checked2: false,
            checked3: false,
            isActive: false,
            stepOne: true,
            stepTwo: false,
            stepThree: false,
            stepFour: false,
            valueEq: '', 
            optionsEquipment: ['DRY', 'REEFER', 'OPEN TOP', 'FLAT RACK'],
            items: []
        }
    },
    methods: {

        nextStep() {
            if ( this.stepOne ) {
                this.stepOne = false; this.stepTwo = !this.stepTwo; 
                return
            } else if ( this.stepTwo ) {
                this.stepTwo = false; this.stepThree = !this.stepThree;
                return
            } else if ( this.stepThree ) {
                this.stepThree = false; this.stepFour = !this.stepFour;
                return
            }
        },

        backStep() {
            if ( this.stepFour ) {
                this.stepFour = false; this.stepThree = !this.stepThree;
                return
            } else if ( this.stepThree ) {
                this.stepThree = false; this.stepTwo = !this.stepTwo;
                return
            } else if ( this.stepTwo ) {
                this.stepTwo =  false; this.stepOne = !this.stepOne;
                return
            }
        }

    },
    watch: {
        valueEq: function() {

            if (this.valueEq == 'DRY') {
                this.items.push({name: 'C20DV', placeholder: '20DV'}, { name: 'C40DV', placeholder: '40DV' }, { name: 'C40HC', placeholder: '40HC' }, { name: 'C45HC', placeholder: '45HC' }, { name: 'C40NOR', placeholder: '40NOR' }); 
                return
            }

            if (this.valueEq == 'REEFER') {
                this.items.push({name: 'C20RF', placeholder: '20RF'}, { name: 'C40RF', placeholder: '40RF' }, { name: 'C40HCRF', placeholder: '40HCRF' }); 
                return
            }

            if (this.valueEq == 'OPEN TOP') {
                this.items.push({name: 'C20OT', placeholder: '20OT'}, { name: 'C40OT', placeholder: '40OT' }); 
                return
            }

            if (this.valueEq == 'FLAT RACK') {
                this.items.push({name: 'C20FR', placeholder: '20FR'}, { name: 'C40FR', placeholder: '40FR' }); 
                return
            }

        }
    },
    mounted(){
        window.document.onscroll = () => {
            let navBar = document.getElementById('top-results');
            if(window.scrollY > navBar.offsetTop){
                this.isActive = true;
                } else {
                this.isActive = false;
            }
        }
    }
}
</script>