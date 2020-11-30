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

                <b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>
                
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

                           <div class="col-12 mt-3 mb-3  result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks1 class="rs-btn">remarks <b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed1 class="rs-btn">detailed cost <b-icon icon="caret-down-fill"></b-icon></b-button>
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
                   
                        <b-collapse id="detailed1" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
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
                        <b-collapse id="remarks1" class="pt-5 pb-5 pl-5 pr-5 col-12">

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

                           <div class="col-12 mt-3 mb-3 result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks2 class="rs-btn"><b>remarks</b><b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed2 class="rs-btn"><b>detailed cost</b><b-icon icon="caret-down-fill"></b-icon></b-button>
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
                   
                        <b-collapse id="detailed2" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
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
                        <b-collapse id="remarks2" class="pt-5 pb-5 pl-5 pr-5 col-12">

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

                           <div class="col-12 mt-3 mb-3  result-action d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center">

                                    <p class="mr-4 mb-0"><b>Vality:</b> 2020-20-20 / 2020-20-20</p>
                                    <a href="#">download contract</a>

                                </div>


                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button v-b-toggle.remarks3 class="rs-btn">remarks <b-icon icon="caret-down-fill"></b-icon></b-button>
                                    <b-button v-b-toggle.detailed3 class="rs-btn">detailed cost <b-icon icon="caret-down-fill"></b-icon></b-button>
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
                   
                        <b-collapse id="detailed3" class="pt-5 pb-5 pl-5 pr-5 col-12">
                        
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
                        <b-collapse id="remarks3" class="pt-5 pb-5 pl-5 pr-5 col-12">

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

        <b-modal  id="add-contract" size="lg" centered title="Created Contract" ref="my-modal" hide-footer>

            <div class="row add-contract-form-steps pt-5 pb-5">

                <div class="col-12 step-add-contract col-sm-3 d-flex flex-column justify-content-center align-items-center" v-bind:class="{ stepComplete : isCompleteOne }">
                    <div class="add-contract-step">1</div>
                    <span>Contract</span>
                </div>

                <div class="col-12 col-sm-3 step-add-contract d-flex flex-column justify-content-center align-items-center" v-bind:class="{ stepComplete : isCompleteTwo }">
                    <div class="add-contract-step">2</div>
                    <span>Ocean Freight</span>
                </div>

                <div class="col-12 col-sm-3 step-add-contract d-flex flex-column justify-content-center align-items-center" v-bind:class="{ stepComplete : isCompleteThree }">
                    <div class="add-contract-step">3</div>
                    <span>Surcharges</span>
                </div>

                <div class="col-12 col-sm-3 step-add-contract d-flex flex-column justify-content-center align-items-center" v-bind:class="{ stepComplete : isCompleteFour }">
                    <div class="add-contract-step">4</div>
                    <span>Files</span>
                </div>

            </div>

            <form action="/action_page.php" class="add-contract-form">

                <fieldset v-if="stepOne">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label>
                                <b-form-input
                                    v-model="reference"
                                    placeholder="Reference"
                                    class="input-modal"
                                ></b-form-input>
                                <img src="/images/investigacion.svg" alt="reference" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                                <date-range-picker
                                    :opens="'center'"
                                    :locale-data="{ firstDay: 1, format: 'yyyy/mm/dd' }"
                                    :singleDatePicker="false"
                                    :autoApply="true"
                                    :timePicker="false"
                                    v-model="dateRange"
                                    :linkedCalendars="true"
                                    @update="updateValues"
                                    @toggle="checkOpen"
                                    class="input-h"
                                >
                                </date-range-picker>
                           </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="carrier"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsCarrier"
                                    placeholder="Carrier"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/carrier.svg" alt="carrier" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                                <multiselect
                                    v-model="valueEq"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsEquipment"
                                    placeholder="Equipment"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/container.svg" alt="container" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                                <multiselect
                                    v-model="direction"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsDirection"
                                    placeholder="Direction"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/entrega.svg" alt="entrega" width="25px" height="25px">
                            </label>
                        </div>
                    </div>
                </fieldset>
    
                <fieldset v-if="stepTwo">

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="origin"
                                    :multiple="true"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsCountries"
                                    placeholder="Origin"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/port.svg" alt="origen" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="destination"
                                    :multiple="true"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsCountries"
                                    placeholder="Destination"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/port.svg" alt="destination" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="carrier"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsCarrier"
                                    placeholder="Carrier"
                                    class="input-modal"
                                >
                                </multiselect>
                                <img src="/images/carrier.svg" alt="carrier" width="25px" height="25px">
                            </label>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="currency"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="optionsCurrency"
                                    placeholder="Currency"
                                    class="input-modal"
                                >
                                </multiselect> 
                                <img src="/images/dinero.svg" alt="currency" width="25px" height="25px">
                            </label>
                        </div>

                        <div v-for="(item, index) in items" class="col-12 col-sm-6">
                            <label>
                                <b-form-input
                                    :name="item.name"
                                    :placeholder="item.placeholder"
                                    class="input-modal mb-3"
                                    v-model="equipType"
                                    required
                                ></b-form-input>
                                <img src="/images/ordenar.svg" alt="ordenar" width="25px" height="25px">
                            </label>
                        </div>
                    </div>
                    
                </fieldset>

                <fieldset v-if="stepThree">

                    <div class="row">
                        <div class="col-12">

                           <b-buttom v-on:click="addSurchager"><b-icon icon="plus-circle-fill"></b-icon> ADD SURCHARGE</b-buttom>

                        </div>

                        <div id="surcharges-list" class="col-12">

                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <label>
                                        <multiselect
                                            v-model="typeContract"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :show-labels="false"
                                            :options="optionsTypeContract"
                                            placeholder="Type"
                                            class="input-modal surcharge-input"
                                            >
                                        </multiselect>
                                    </label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label>
                                        <multiselect
                                                v-model="calculationType"
                                                :multiple="false"
                                                :close-on-select="true"
                                                :clear-on-select="true"
                                                :show-labels="false"
                                                :options="optionsCalculationType"
                                                placeholder="Calculation Type"
                                                class="input-modal surcharge-input"
                                            >
                                        </multiselect>
                                </label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label>
                                        <multiselect
                                            v-model="currencySurcharge"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :show-labels="false"
                                            :options="optionsCurrency"
                                            placeholder="Currency"
                                            class="input-modal surcharge-input"
                                            >
                                        </multiselect>
                                </label>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <label>
                                        <b-form-input
                                            v-model="amount"
                                            placeholder="Amount"
                                            class="input-modal surcharge-input"
                                        ></b-form-input>
                                    </label>
                                </div>
                                <div class="col-1">
                                    <b-icon icon="dash-circle"></b-icon>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </fieldset>

                <fieldset v-if="stepFour">
                    <vue-dropzone
                        ref="myVueDropzone"
                        :useCustomSlot="true"
                        id="dropzone"
                        v-on:vdropzone-removed-file="removeThisFile"
                        v-on:vdropzone-success="success"
                        >
                        <div class="dropzone-container">
                                <div class="file-selector">
                                <h6 class="title-dropzone">Upload</h6>
                                <figure>
                                <svg
                                    width="104px"
                                    height="104px"
                                    viewBox="0 0 104 104"
                                    version="1.1"
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                >
                                    <defs>
                                    <circle id="path-1" cx="36" cy="36" r="36"></circle>
                                    <filter
                                        x="-37.5%"
                                        y="-29.2%"
                                        width="175.0%"
                                        height="175.0%"
                                        filterUnits="objectBoundingBox"
                                        id="filter-2"
                                    >
                                        <feOffset
                                        dx="0"
                                        dy="6"
                                        in="SourceAlpha"
                                        result="shadowOffsetOuter1"
                                        ></feOffset>
                                        <feGaussianBlur
                                        stdDeviation="8"
                                        in="shadowOffsetOuter1"
                                        result="shadowBlurOuter1"
                                        ></feGaussianBlur>
                                        <feColorMatrix
                                        values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.08 0"
                                        type="matrix"
                                        in="shadowBlurOuter1"
                                        result="shadowMatrixOuter1"
                                        ></feColorMatrix>
                                        <feOffset
                                        dx="0"
                                        dy="1"
                                        in="SourceAlpha"
                                        result="shadowOffsetOuter2"
                                        ></feOffset>
                                        <feGaussianBlur
                                        stdDeviation="1"
                                        in="shadowOffsetOuter2"
                                        result="shadowBlurOuter2"
                                        ></feGaussianBlur>
                                        <feColorMatrix
                                        values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.11 0"
                                        type="matrix"
                                        in="shadowBlurOuter2"
                                        result="shadowMatrixOuter2"
                                        ></feColorMatrix>
                                        <feMerge>
                                        <feMergeNode in="shadowMatrixOuter1"></feMergeNode>
                                        <feMergeNode in="shadowMatrixOuter2"></feMergeNode>
                                        </feMerge>
                                    </filter>
                                    </defs>
                                    <g
                                    id="Page-1"
                                    stroke="none"
                                    stroke-width="1"
                                    fill="none"
                                    fill-rule="evenodd"
                                    >
                                    <g
                                        id="Artboard"
                                        transform="translate(-460.000000, -125.000000)"
                                    >
                                        <g id="Group-4" transform="translate(412.000000, 129.000000)">
                                        <g id="Group-2" transform="translate(58.000000, 0.000000)">
                                            <circle
                                            id="Oval"
                                            fill="#3560FF"
                                            opacity="0.100000001"
                                            cx="42"
                                            cy="42"
                                            r="42"
                                            ></circle>
                                            <g id="Group" transform="translate(6.000000, 6.000000)">
                                            <g id="Oval">
                                                <use
                                                fill="black"
                                                fill-opacity="1"
                                                filter="url(#filter-2)"
                                                xlink:href="#path-1"
                                                ></use>
                                                <use
                                                fill="#FFFFFF"
                                                fill-rule="evenodd"
                                                xlink:href="#path-1"
                                                ></use>
                                            </g>
                                            <g
                                                id="upload-cloud"
                                                transform="translate(21.818182, 24.000000)"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                            >
                                                <polyline
                                                id="Path"
                                                stroke="#000000"
                                                points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
                                                ></polyline>
                                                <path
                                                d="M14.3565525,12.0897285 L14.3565525,24.1794569"
                                                id="Path"
                                                stroke="#3560FF"
                                                ></path>
                                                <path
                                                d="M25.6438239,20.7792208 C28.2965835,19.3021499 29.6312816,16.1761528 28.8860265,13.1856562 C28.1407715,10.1951596 25.5052337,8.10125672 22.4838689,8.09921935 L20.8179512,8.09921935 C19.7219904,3.76967373 16.1275086,0.577339516 11.7773112,0.0700384831 C7.42711383,-0.43726255 3.22057026,1.84535014 1.19724759,5.81113853 C-0.826075091,9.77692693 -0.247870665,14.6059952 2.6515151,17.9569414"
                                                id="Path"
                                                stroke="#3560FF"
                                                ></path>
                                                <polyline
                                                id="Path"
                                                stroke="#3560FF"
                                                points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
                                                ></polyline>
                                            </g>
                                            </g>
                                        </g>
                                        </g>
                                    </g>
                                    </g>
                                </svg>
                                </figure>
                                Drop Or Add Files Here
                                <p><span> or </span></p>
                                <button type="button" class="btn btn-primary btn-bg">Choose file</button>
                            </div>
                        </div>
				    </vue-dropzone>
                </fieldset>

                <h5 v-if="invalidInput">Please Complete all the fields</h5>

                <div class="footer-add-contract-modal pl-4 pr-4">   
                    <b-button v-if="stepTwo || stepThree || stepFour" v-on:click="backStep" variant="link" style="color: red" class="mr-3">Back</b-button>
                    <b-button v-on:click="nextStep" v-if="!stepFour" class="btn-create-quote">Save & Continue</b-button>
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
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
export default {
    components: {
        Multiselect,
        DateRangePicker,
        vueDropzone: vue2Dropzone
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
            invalidInput: false,
            valueEq: '', 
            amount: '', 
            currency: '', 
            origin: '', 
            destination: '', 
            carrier: '', 
            reference: '',
            direction: '',
            typeContract: '',
            calculationType: '',
            optionsDirection: ['Import', 'Export', 'Both'],
            optionsCurrency: ['USD', 'EUR', 'MXN'],
            optionsCountries: ['Argentina', 'Arabia', 'España', 'Mexico', 'Francia'],
            optionsEquipment: ['DRY', 'REEFER', 'OPEN TOP', 'FLAT RACK'],
            optionsCarrier: ['APL', 'CCNI', 'CMA CGM', 'COSCO', 'CSAV', 'Evergreen', 'Hamburg Sub', 'Hanjin', 'Hapag Lloyd'],
            optionsTypeContract: ['Type 1', 'Type 2', 'Type 3', 'Type 4'],
            optionsCalculationType: ['Calculation 1', 'Calculation 2', 'Calculation 3', 'Calculation 4'],
            items: [],
            isCompleteOne: true,
            isCompleteTwo: false,
            isCompleteThree: false,
            isCompleteFour: false,

            //Datepicker Options
            dateRange: '2020/20/20 - 2020/20/20',
            format: 'mm/dd/yyyy',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            weekLabel: 'W',
            customRangeLabel: 'Custom Range',
            daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            firstDay: 0
            /* dropzoneOptions: {
                url: `/api/v2/contracts/${this.$route.params.id}/storeMedia`, 
                url: `/api/v2/contracts/storeMedia`, 
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                headers: { "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content },
                addRemoveLinks: true,
                previewTemplate: this.template()
            } */
        }
    },
    methods: {

        addSurchager() {

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
			},

        nextStep() {
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

                if (this.typeContract == '' || this.calculationType == '' || this.currencySurcharge == '' || this.amount == '' ) {
                    this.invalidInput = true;
                    return
                }

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
        }

    },
    watch: {
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