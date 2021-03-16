<template>
    <div class="search pt-5">

         <b-form ref="form">

             <!-- Type / Delivery type / Additional Services -->
            <div class="row mr-0 ml-0">

                <div class="col-12 col-sm-6 col-lg-3 d-flex">

                        <!-- Type (FCL LCL AIR)-->
                        <div style="width: 85px !important; position:relative">
                            <multiselect
                                v-model="type"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :options="optionsType"
                                placeholder="Select"
                                class="s-input no-select-style"
                            >
                            </multiselect>
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="type-mode img-icon"></b-icon>
                        </div>

                        <!-- Delivery Type (Door to Door, Door to Port, Port to Port, Port to Door)-->
                        <div style="width: 160px !important; position:relative; z-index:100 ">
                            <multiselect
                                v-model="deliveryType"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :options="optionsDeliveryType"
                                placeholder="Select"
                                class="s-input no-select-style "
                            >
                            </multiselect>
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type img-icon"></b-icon>
                        </div>


                </div>

                <!-- Button Additional services -->
                <div class="col-12 col-sm-6 col-lg-9">
                    <b-button v-b-toggle.collapse-1 class="btn-aditonal-services">additional services <b-icon icon="caret-down-fill" class="ml-1"></b-icon></b-button>
                </div>

            </div>

            <!-- Ipunts Serch -->
            <div class="row mr-0 ml-0">

                <!-- Import / Export -->
                <div class="col-12 col-sm-1">

                    <b-form-radio-group
                        v-model="direction"
                        :options="options"
                        buttons
                        button-variant="outline-primary"
                        size="lg"
                        name="direction"
                        class="radio-direction type-style"
                    ></b-form-radio-group>

                </div>

                <!-- Origin Port -->
                <div class="col-12 col-sm-3 origen-search input-search-form" style="position:relative; z-index:60"> 
                    <multiselect
                        v-model="valueOrigen"
                        :multiple="true"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="optionsOrigenPort"
                        placeholder="From" 
                        class="s-input"
                    >
                    </multiselect>
                    <img src="/images/port.svg" class="img-icon" alt="port">
                </div>

                <!-- Destination Port -->
                <div class="col-12 col-sm-3 input-search-form" style="position:relative; z-index:60">
                        <multiselect
                            v-model="valueDestination"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionsDestinationPort"
                            placeholder="To" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/port.svg" class="img-icon" alt="port">
                </div>
                
                <!-- Date Picker-->
                <div class="col-12 col-sm-3 input-search-form">
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
                            class="s-input"
                        ></date-range-picker>
                        <img src="/images/calendario.svg" class="img-icon" alt="calendario">
                </div>

                <!-- Containers -->
                <div class="col-12 col-sm-2 input-search-form containers-search" style="padding-left: 5px;">
                        <!-- <multiselect
                            v-model="container"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionContainer"
                            placeholder="Containers" 
                            class="s-input"
                        >
                        </multiselect> -->
                        <b-dropdown id="dropdown-containers" text="Containers" ref="dropdown" class="m-2">
                            <b-dropdown-form>
                                <b-form-group label="Type" v-slot="{ ariaDescribedby }" @submit.stop.prevent>
                                    <b-form-radio-group
                                        id="carrier"
                                        v-model="selectedContainers"
                                        :options="optionsContainers"
                                        :aria-describedby="ariaDescribedby"
                                    ></b-form-radio-group>
                                </b-form-group>
                                <b-form-group label="Equipment List" v-slot="{ ariaDescribedby }" @submit.stop.prevent>
                                    <b-form-checkbox-group
                                        id="equipment"
                                        v-model="selected"
                                        :options="options+clase"
                                        :aria-describedby="ariaDescribedby"
                                        name="equipment"
                                    ></b-form-checkbox-group>
                                </b-form-group>
                            </b-dropdown-form>
                        </b-dropdown>
                        <img src="/images/container.svg" class="img-icon" alt="port">
                </div>

            </div>

            <!-- Input From and To PORT -->
            <div v-if="ptdActive || dtpActive || dtdActive" class="row mr-0 ml-0">

                <div class="col-12 col-sm-1"></div>

                <div v-if="ptdActive" class="col-12 col-sm-3" style="padding-left: 30px; padding-right: inherit"></div>

                <!-- Origin City -->
                <div v-if="dtpActive || dtdActive" class="col-12 col-sm-3 origen-search input-search-form" style="position:relative; z-index:50">

                        <multiselect
                            v-model="origenPort"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionsOrigenPort"
                            placeholder="From" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/city.svg" class="img-icon" alt="port">
                </div>

                <!-- Destination City -->
                <div v-if="ptdActive || dtdActive" class="col-12 col-sm-3 input-search-form" style="position:relative; z-index:50">
                    
                        <multiselect
                            v-model="destinationPort"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionsDestinationPort"
                            placeholder="To" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/city.svg" class="img-icon" alt="port">

                </div>

            </div>

            <!-- ADDITIONAL SERVICES -->
            <b-collapse id="collapse-1" class="mt-2">

                <h6 class="t-as mt-3 mb-3 ml-4">ADDITIONAL SERVICES</h6>
            
                <div class="row mr-0 ml-0">

                    <div class="col-12 col-sm-3">
                            <multiselect
                            v-model="company"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionCompany"
                            placeholder="Company" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/empresa.svg" class="img-icon" alt="port">
                        
                    </div>
                
                    <div class="col-12 col-sm-3">
                            <multiselect
                            v-model="contact"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionContact"
                            placeholder="Contact" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/contacto.svg" class="img-icon" alt="port">
                    </div>

                    <div class="col-12 col-sm-3">
                            <multiselect
                            v-model="pricelevel"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionPriceLevel"
                            placeholder="Price Level" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/pricelevel.svg" class="img-icon" alt="port">
                    </div>

                    <div class="col-12 col-sm-3">
                            <multiselect
                            v-model="carriers"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionCarriers"
                            placeholder="Carriers" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/carrier.svg" class="img-icon" alt="port">
                    </div>

                </div>

                <div class="row mr-0 ml-4 mt-5 d-flex justify-content-start">
                    <b-form-checkbox
                        id="originCharges"
                        v-model="originCharges"
                        name="originCharges"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="mr-5 as-checkbox"
                    >
                        Include origin charges
                    </b-form-checkbox>
                    <b-form-checkbox
                        id="destinationCharges"
                        v-model="destinationCharges"
                        name="destinationCharges"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="as-checkbox"
                    >
                        Include destination charges
                    </b-form-checkbox>
                </div>
                
            </b-collapse>

            <!-- LCL FORM INPUTS -->
            <div class="row" v-if="type == 'LCL'">
                    <!-- Tabs Section -->
				<b-card no-body class="card-tabs col-12 font-tabs">
					<b-tabs card>

						<b-tab title="CALCULATE BY TOTAL SHIPMENT" active>
                            
                            <div class="row">

                                <div class="col-3">
                                    <label class="d-flex align-items-center">
                                        <b-form-input
                                            v-model="packages"
                                            placeholder="Packages" 
                                            class="s-input-form mr-1"
                                        >
                                        </b-form-input>
                                        <img src="/images/paquete.svg" class="img-icon" alt="paquete">
                                        <div class="type-packages">
                                                <multiselect
                                                    v-model="typePallet"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="false"
                                                    :show-labels="false"
                                                    :options="optionsTypePallet"
                                                    placeholder="Select Filter"
                                                    class="s-input no-select-style "
                                                >
                                                </multiselect>
                                                <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type"></b-icon>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-3">
                                    <label class="d-flex align-items-center">
                                        <b-form-input
                                            v-model="weight"
                                            placeholder="Total Weight" 
                                            class="s-input-form mr-1"
                                        >
                                        </b-form-input>
                                        <img src="/images/peso.svg" class="img-icon" alt="peso">
                                        <span>KG</span>
                                    </label>
                                </div>

                                <div class="col-3">
                                    <label class="d-flex align-items-center">
                                        <b-form-input
                                            v-model="volumen"
                                            placeholder="Total Volumen" 
                                            class="s-input-form mr-1"
                                        >
                                        </b-form-input>
                                        <img src="/images/espacio-de-trabajo.svg" class="img-icon" alt="volumen">
                                        <span>M<sup>3</sup></span>
                                    </label>
                                </div>

                                <div class="col-3" style="text-align:center">
                                    <h6><b>CHARGEABLE WEIGHT</b></h6>
                                    <p>12.00<sup>m3</sup></p>
                                </div>
                            </div>

						</b-tab>

						<b-tab title="CALCULATE BY PACKAGING">

							<div class="row">

                                <div v-if="invalidCalculate" class="col-12 mb-3">
                                    <h6 class="invalid-data"><b-icon icon="exclamation-circle" class="mr-2"></b-icon>Complete all the fileds</h6>
                                </div>

                                <div id="surcharges-list" class="col-12">

                                    <div class="row surcharge-content">
                                        <div class="col-12 col-sm-1">
                                            <label>
                                                <multiselect
                                                    v-model="pallets"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="false"
                                                    :show-labels="false"
                                                    :options="optionsTypePallet"
                                                    placeholder="Choose"
                                                    class="input-modal surcharge-input"
                                                >
                                                </multiselect>
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-1">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="quantity"
                                                    placeholder="Quantity" 
                                                    class="s-input-form"
                                                >
                                                </b-form-input>
                                                <img src="/images/paquete.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="height"
                                                    placeholder="Height" 
                                                    class="s-input-form"
                                                >
                                                </b-form-input>
                                                <img src="/images/paquete.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="width"
                                                    placeholder="Width" 
                                                    class="s-input-form"
                                                >
                                                </b-form-input>
                                                <img src="/images/paquete.svg" class="img-icon"alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2">
                                            <label>
                                                <b-form-input
                                                    v-model="large"
                                                    placeholder="Large"
                                                    class="s-input-form"
                                                ></b-form-input>
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2">
                                            <label>
                                                <b-form-input
                                                    v-model="weight"
                                                    placeholder="Weight"
                                                    class="s-input-form"
                                                ></b-form-input>
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-1 d-flex align-items-center justify-content-center">
                                            <span>{{ total }} M<sup>3</sup></span>
                                        </div>

                                        <div class="col-12 col-sm-1 d-flex justify-content-end align-items-center">
                                            <span v-on:click="addSurcharger" class="btn-add-surch"><b-icon icon="check-circle"></b-icon> ADD LOAD</span>
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                            <div class="row">
                                <div class="row col-12 mt-3 mb-3 mr-0 ml-0 pr-0 pl-0 data-surcharges" v-for="(item, index) in dataPackaging">

                                    <div class="col-12 col-sm-1"><p>{{ item.type }}</p></div>
                                    <div class="col-12 col-sm-1"><p>{{ item.quantity }}</p></div>
                                    <div class="col-12 col-sm-2"><p>{{ item.height }}</p></div>
                                    <div class="col-12 col-sm-2"><p>{{ item.width }}</p></div>
                                    <div class="col-12 col-sm-2"><p>{{ item.large }}</p></div>
                                    <div class="col-12 col-sm-2"><p>{{ item.weight }}</p></div>
                                    <div class="col-12 col-sm-1"><p>{{ item.total }}</p></div>
                                    <div class="col-12 col-sm-1"><span v-on:click="deleteSurcharger(index)"><b-icon icon="x-circle"></b-icon></span></div>

                                </div>
                            </div>
						</b-tab>

					</b-tabs>
				</b-card>
				<!-- End Tabs Section -->
            </div>

            <div class="row justify-content-center mr-0 ml-0">
                <div class="col-2 d-flex justify-content-center"><button class="btn-search" >SEARCH</button></div>
            </div>

        </b-form>

    </div>
</template>

<script>
import Search from './Search'; 
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue-multiselect/dist/vue-multiselect.min.css";

export default {
    components: {
        Search,
        Multiselect,
        DateRangePicker,
    },
    data() {
        return {
            date: '20asd 52',
            ptdActive: false,
            dtpActive: false,
            dtdActive: false,
            direction: 'import',
            dataPackaging: [],
            type: 'FCL',
            company: '',
            carriers: '',
            contact: '',
            pricelevel: '',
            pallets: 'PALLETS',
            quantity: '',
            width: '',
            weight: '',
            height: '',
            large: '',
            total: '',
            typePallet: 'PALLETS',
            container: [],
            selected: 'radio1',
            deliveryType: 'PORT TO PORT',
            valueOrigen: [],
            valueDestination: [],
            origenPort: [],
            invalidCalculate: false,
            destinationPort: [],
            optionsOrigenPort: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            optionsDestinationPort: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            optionsDestination: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            options: [
                { text: 'Import', value: 'import' },
                { text: 'Export', value: 'export' }
            ],
            optionsTypePallet: ['PALLETS', 'PACKAGES'],
            optionCompany: ['Select option', 'Cargofive', 'Altius', 'Lantia', 'FreightBros'],
            optionContact: ['Select option', 'Genesis', 'Ruben', 'Sebastian', 'Julio'],
            optionPriceLevel: ['Select option', 'Precio 1', 'Precio 2', 'Precio 3', 'Precio 4'],
            optionCarriers: ['APL', 'CCNI', 'CMA CGM', 'COSCO', 'CSAV', 'Evergreen', 'Hamburg Sub', 'Hanjin', 'Hapag Lloyd'],
            optionContainer: ['Select option', '20DV', '40DV', '40CH', '45HC', '40NOR', '20RF', '40RF', '40HCRF', '20OT', '40OT', '20FR', '40FR'],
            optionsType: ['FCL', 'LCL', 'AIR'],
            optionsDeliveryType: ['PORT TO PORT', 'PORT TO DOOR', 'DOOR TO PORT', 'DOOR TO DOOR'],
            selectedContainers: 'dry', // Must be an array reference!
            optionsContainers: [
                { text: 'DRY', value: 'dry' },
                { text: 'REEFER', value: 'reefer' },
                { text: 'OPEN TOP', value: 'open' },
                { text: 'FLAT RACK', value: 'rack' },
            ],
            selectedCarries: [], // Must be an array reference!
            optionsCarries: [
                { text: 'APL', value: 'orange' },
                { text: 'CCNI', value: 'apple' },
                { text: 'CMA CGM', value: 'pineapple' },
                { text: 'COSCO', value: 'grape' },
                { text: 'CSAV', value: 'orange' },
                { text: 'Evergreen', value: 'apple' },
                { text: 'Hamburg Sub', value: 'pineapple' },
                { text: 'Hanjin', value: 'grape' },
                { text: 'HaHapag Lloydnjin', value: 'grape' },
            ],

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
        deleteSurcharger(index){
                    this.dataPackaging.splice(index, 1);
                    console.log(this.dataPackaging);
                },

        addSurcharger() {

            if(this.pallets == "" || this.quantity == "" || this.height == "" || this.width == "" || this.large == "" || this.weight == "" ) {
                this.invalidCalculate = true;
                return
            }

            this.invalidCalculate = false;

            var totalPackging = this.quantity + this.height + this.width + this.large + this.weight;

            var packaging = {
                type: this.pallets,
                quantity: this.quantity,
                height: this.height,
                width: this.width,
                large: this.large,
                weight: this.weight,
                total: this.totalPackging,
            };
            console.log(packaging);
            this.dataPackaging.push(packaging);
            
            this.pallets = ""; this.quantity = ""; this.height = ""; 
            this.width = "";   this.large = "";    this.weight = ""; 
            this.total = "";
        },
    },
    watch: {
        deliveryType: function() {
            if ( this.deliveryType == "PORT TO PORT" ) {

                this.ptdActive = false; this.dtpActive = false; this.dtdActive = false; 
                return;

            } else if (this.deliveryType == "PORT TO DOOR") {

                this.dtpActive = false; this.dtdActive = false; 

                this.ptdActive = !this.ptdActive;
                return;

            } else if (this.deliveryType == "DOOR TO PORT") {

                this.ptdActive = false; this.dtdActive = false; 

                this.dtpActive = !this.dtpActive;
                return;

            } else if (this.deliveryType == "DOOR TO DOOR") {

                this.ptdActive = false; this.dtpActive = false; 
               
                this.dtdActive = !this.dtdActive;
                return;

            }
        }
    
    }
}
</script>