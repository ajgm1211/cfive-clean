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
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="type-mode"></b-icon>
                        </div>

                        <!-- Delivery Type (Door to Door, Door to Port, Port to Port, Port to Door)-->
                        <div style="width: 160px !important; position:relative ">
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
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type"></b-icon>
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
                <div class="col-12 col-sm-3 origen-search input-search-form">
                    <label>
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
                        <img src="/images/port.svg" alt="port">
                    </label>
                </div>

                <!-- Destination Port -->
                <div class="col-12 col-sm-3 input-search-form">
                    <label>
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
                        <img src="/images/port.svg" alt="port">
                    </label>
                </div>
                
                <!-- Date Picker-->
                <div class="col-12 col-sm-3 input-search-form">
                    <label>
                        <date-range-picker
                                :opens="'center'"
                                :locale-data="{ firstDay: 1, format: 'yyyy/mm/dd' }"
                                :singleDatePicker="false"
                                :autoApply="true"
                                :timePicker="false"
                                v-model="dateRange"
                                :linkedCalendars="true"
                                class="s-input"
                            ></date-range-picker>
                        <img src="/images/calendario.svg" alt="calendario">
                    </label>
                </div>

                <!-- Containers -->
                <div class="col-12 col-sm-2 input-search-form containers-search" style="padding-left: 5px;">
                    <label>
                        <multiselect
                            v-model="container"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="optionContainer"
                            placeholder="Containers" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/container.svg" alt="port">
                    </label>
                </div>

            </div>

            <!-- Input From and To PORT -->
            <div v-if="ptdActive || dtpActive || dtdActive" class="row mr-0 ml-0">

                <div class="col-12 col-sm-1"></div>

                <div v-if="ptdActive" class="col-12 col-sm-3" style="padding-left: 30px; padding-right: inherit"></div>

                <!-- Origin City -->
                <div v-if="dtpActive || dtdActive" class="col-12 col-sm-3 origen-search input-search-form">
                    <label>
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
                        <img src="/images/city.svg" alt="port">
                    </label>
                </div>

                <!-- Destination City -->
                <div v-if="ptdActive || dtdActive" class="col-12 col-sm-3 input-search-form">
                    <label>
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
                        <img src="/images/city.svg" alt="port">
                    </label>
                </div>

            </div>

            <b-collapse id="collapse-1" class="mt-2">

                <h6 class="t-as mt-3 mb-3 ml-4">ADDITIONAL SERVICES</h6>
            
                <div class="row mr-0 ml-0">

                    <div class="col-12 col-sm-3">
                        <label>
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
                            <img src="/images/empresa.svg" alt="port">
                        </label>
                    </div>
                
                    <div class="col-12 col-sm-3">
                        <label>
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
                            <img src="/images/contacto.svg" alt="port">
                        </label>
                    </div>

                    <div class="col-12 col-sm-3">
                        <label>
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
                            <img src="/images/pricelevel.svg" alt="port">
                        </label>
                    </div>

                    <div class="col-12 col-sm-3">
                        <label>
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
                            <img src="/images/carrier.svg" alt="port">
                        </label>
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
            type: 'FCL',
            company: '',
            carriers: '',
            contact: '',
            pricelevel: '',
            container: [],
            selected: 'radio1',
            deliveryType: 'PORT TO PORT',
            valueOrigen: [],
            valueDestination: [],
            origenPort: [],
            destinationPort: [],
            optionsOrigenPort: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            optionsDestinationPort: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            optionsDestination: ['Select option', 'Buenos Aries, Arg', 'Puerto Cabello, Vnzl', 'Barcelona, Vnzl', 'São Paulo, Br', 'Shangai, Ch', 'Tokio, Jp', 'Lisboa, Pt'],
            options: [
                { text: 'Import', value: 'import' },
                { text: 'Export', value: 'export' }
            ],
            optionCompany: ['Select option', 'Cargofive', 'Altius', 'Lantia', 'FreightBros'],
            optionContact: ['Select option', 'Genesis', 'Ruben', 'Sebastian', 'Julio'],
            optionPriceLevel: ['Select option', 'Precio 1', 'Precio 2', 'Precio 3', 'Precio 4'],
            optionCarriers: ['APL', 'CCNI', 'CMA CGM', 'COSCO', 'CSAV', 'Evergreen', 'Hamburg Sub', 'Hanjin', 'Hapag Lloyd'],
            optionContainer: ['Select option', '20DV', '40DV', '40CH', '45HC', '40NOR', '20RF', '40RF', '40HCRF', '20OT', '40OT', '20FR', '40FR'],
            optionsType: ['FCL', 'LCL', 'AIR'],
            optionsDeliveryType: ['PORT TO PORT', 'PORT TO DOOR', 'DOOR TO PORT', 'DOOR TO DOOR'],

            //DATEPICKER
            locale: 'en-US',
            dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'},
            dateRange: {
                startDate: '2020-12-26',
                endDate: '2020-12-28',
            },
        }
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