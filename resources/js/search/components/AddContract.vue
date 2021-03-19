<template>
    <!-- MODAL ADD CONTRACT -->
        <b-modal  id="add-contract" size="lg" centered title="Created Contract" ref="my-modal" hide-footer>

            <!-- STEPS -->
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

                <!-- CONTRACT -->
                <fieldset v-if="stepOne">

                    <div class="row">

                        <div v-if="invalidInput" class="col-12 mt-3 mb-3">
                            <h5 class="invalid-data"><b-icon icon="exclamation-circle" class="mr-2"></b-icon>Please complete all the fields.</h5>
                        </div>

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
                                :locale-data="{
                                    firstDay: 1,
                                    format: 'yyyy/mm/dd',
                                    }"
                                :singleDatePicker="false"
                                :autoApply="true"
                                :timePicker="false"
                                v-model="dateRange"
                                :linkedCalendars="true"
                                class="input-h"
                                ></date-range-picker>
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
    
                <!-- OCEAN FREIGHT -->
                <fieldset v-if="stepTwo">

                        <div v-if="invalidInput" class="col-12 mt-3 mb-3">
                            <h5 class="invalid-data"><b-icon icon="exclamation-circle" class="mr-2"></b-icon>Please complete all the fields.</h5>
                        </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label>
                               <multiselect
                                    v-model="origin"
                                    :multiple="true"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :show-labels="false"
                                    :options="request.harbors"
                                    label="display_name"
                                    track-by="display_name"
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
                                    :options="request.harbors"
                                    label="display_name"
                                    track-by="display_name"
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

                <!-- SURCHARGES -->
                <fieldset v-if="stepThree">

                    <div class="row">

                        <div v-if="invalidSurcharger" class="col-12 mb-3">
                            <h5 class="invalid-data"><b-icon icon="exclamation-circle" class="mr-2"></b-icon>Complete all the fileds</h5>
                        </div>
                        <div id="surcharges-list" class="col-12">

                            <div class="row surcharge-content">
                                <div class="col-12 col-sm-3">
                                    <label>
                                        <multiselect
                                            v-model="typeContract"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :show-labels="false"
                                            :options="request.surcharges"
                                            label="name"
                                            track-by="name"
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
                                <div class="col-1 d-flex justify-content-center align-items-center">
                                    <span v-on:click="addSurcharger" class="btn-add-surch"><b-icon icon="check-circle"></b-icon></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="row col-12 mt-3 mb-3 mr-0 ml-0 pr-0 pl-0 data-surcharges" v-for="(item, index) in dataSurcharger">

                            <div class="col-12 col-sm-3"><p>{{ item.type }}</p></div>
                            <div class="col-12 col-sm-3"><p>{{ item.calculation }}</p></div>
                            <div class="col-12 col-sm-3"><p>{{ item.currency }}</p></div>
                            <div class="col-12 col-sm-2"><p>{{ item.amount }}</p></div>
                            <div class="col-12 col-sm-1"><span v-on:click="deleteSurcharger(index)"><b-icon icon="x-circle"></b-icon></span></div>

                        </div>
                    </div>
                    
                </fieldset>

                <!-- FILES -->
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

                <div class="footer-add-contract-modal pl-4 pr-4">   
                    <b-button v-if="stepTwo || stepThree || stepFour" v-on:click="backStep" variant="link" style="color: red" class="mr-3">Back</b-button>
                    <b-button v-on:click="nextStep" v-if="!stepFour" class="btn-create-quote">Save & Continue</b-button>
                    <b-button v-if="stepFour" class="btn-create-quote">Created Contract</b-button>
                </div>
            </form> 
        </b-modal>
        <!-- FIN MODAL ADD CONTRACT -->
</template>