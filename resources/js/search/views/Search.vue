<template>
    <div class="search pt-5">

         <div v-if="loaded">

            <!-- Type / Delivery type / Additional Services -->
            <div class="row mr-0 ml-0">

                <div class="col-12 col-sm-6 col-lg-3 d-flex">

                        <!-- Type (FCL LCL AIR)-->
                        <div class="type-input">
                            <multiselect
                                v-model="searchRequest.type"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"
                                :options="typeOptions"
                                placeholder="Select"
                                class="s-input no-select-style"
                            >
                            </multiselect>
                            <b-icon icon="caret-down-fill" aria-hidden="true" class="type-mode"></b-icon>
                        </div>

                        <!-- Delivery Type (Door to Door, Door to Port, Port to Port, Port to Door)-->
                        <div class="delivery-input">
                            <multiselect
                                v-model="deliveryType"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :show-labels="false"                                
                                :options="deliveryTypeOptions"
                                label="name"
                                track-by="name"
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

            <!-- Inputs Search -->
            <div class="row mr-0 ml-0">

                <!-- Import / Export -->
                <div class="col-12 col-sm-1">

                    <b-form-radio-group
                        v-model="searchRequest.direction"
                        :options="directionOptions"
                        buttons
                        button-variant="outline-primary"
                        size="lg"
                        name="direction"
                        class="radio-direction type-style"
                    ></b-form-radio-group>

                </div>

                <!-- Origin Port -->
                <div class="col-12 col-sm-3 origen-search input-search-form mb-2" style="position:relative; z-index:70"> 
                    <multiselect
                        v-model="searchRequest.originPorts"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="originPortOptions"
                            label="display_name"
                            track-by="display_name"
                            placeholder="From" 
                            class="s-input"
                    >
                    </multiselect>
                    <img src="/images/port.svg" class="img-icon img-icon-left" alt="port">
                    <span v-if="errorsExist && 'originPorts' in responseErrors" style="color:red">The Origin Port field is required!</span>
                </div>

                <!-- Destination Port -->
                <div class="col-12 col-sm-3 input-search-form mb-2" style="position:relative; z-index:70">
                        <multiselect
                            v-model="searchRequest.destinationPorts"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="destinationPortOptions"
                            label="display_name"
                            track-by="display_name"
                            placeholder="To" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/port.svg" class="img-icon" alt="port">
                        <span v-if="errorsExist && 'destinationPorts' in responseErrors" style="color:red">The Destination Port field is required!</span>
                </div>
                
                <!-- Date Picker-->
                <div class="col-12 col-sm-3 input-search-form">
                        <date-range-picker
                            :opens="'center'"
                            :locale-data="{
                                firstDay: 0,
                                format: 'yyyy/mm/dd',
                            }"
                            :singleDatePicker="false"
                            :autoApply="true"
                            :timePicker="false"
                            v-model="searchRequest.dateRange"
                            :linkedCalendars="true"
                            class="s-input"
                        ></date-range-picker>
                        <img src="/images/calendario.svg" class="img-icon" alt="calendario">
                        <span v-if="errorsExist && 'dateRange.startDate' in responseErrors" style="color:red">Please pick a date</span>
                </div>

                <!-- Containers -->
                <div class="col-12 col-sm-2 input-search-form containers-search" style="padding-left: 5px;">
                        <b-dropdown id="dropdown-containers" :text="containerText.join(', ')" ref="dropdown" class="m-2">
                            <b-dropdown-form>
                                <b-form-group label="Type">
                                    <b-form-radio-group
                                        id="containers"
                                        v-model="selectedContainerGroup"
                                        :options="containerGroupOptions"

                                    ></b-form-radio-group>
                                </b-form-group>
                                <b-form-group label="Equipment List">
                                    <b-form-checkbox-group
                                        id="equipment"
                                        v-model="containers"
                                        :options="containerOptions"
                                    ></b-form-checkbox-group>
                                </b-form-group>
                            </b-dropdown-form>
                        </b-dropdown>
                        <img src="/images/container.svg" class="img-icon" alt="port">
                        <span v-if="errorsExist && 'containers' in responseErrors" style="color:red">Choose at least one container</span>
                </div>

            </div>

            <!-- Input From and To PORT -->
            <div v-if="ptdActive || dtpActive || dtdActive" class="row mr-0 ml-0">

                <div class="col-12 col-sm-1"></div>

                <div v-if="ptdActive" class="col-12 col-sm-3" style="padding-left: 30px; padding-right: inherit"></div>

                <!-- Origin City -->
                <div v-if="dtpActive || dtdActive" class="col-12 col-sm-3 origen-search input-search-form" style="position:relative; z-index:60">

                        <multiselect
                            v-model="searchRequest.originAddress"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="originAddressOptions"
                            placeholder="From" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/city.svg" class="img-icon img-icon-left" alt="port">
                </div>

                <!-- Destination City -->
                <div v-if="ptdActive || dtdActive" class="col-12 col-sm-3 input-search-form" style="position:relative; z-index:60">
                    
                        <multiselect
                            v-model="searchRequest.destinationAddress"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="destinationAddressOptions"
                            placeholder="To" 
                            class="s-input"
                        >
                        </multiselect>
                        <img src="/images/city.svg" class="img-icon" alt="port">

                </div>

            </div>

            <!-- ADDITIONAL SERVICES -->
            <b-collapse id="collapse-1" class="mt-3">

                <h6 class="t-as mt-5 mb-3 ml-4">ADDITIONAL SERVICES</h6>
            
                <div class="row mr-3 ml-3">

                    <div class="col-12 col-sm-3 input-search-form">
                            <multiselect
                            v-model="searchRequest.company"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="companyOptions"
                            label="business_name"
                            track-by="business_name"
                            placeholder="Company" 
                            class="s-input"
                            @input="unlockContacts()"
                            >
                            </multiselect>
                            <img src="/images/empresa.svg" class="img-icon" alt="port">
                        
                    </div>
                
                    <div class="col-12 col-sm-3 input-search-form">
                            <multiselect
                            v-model="searchRequest.contact"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="contactOptions"
                            :disabled="!companyChosen"
                            label="name"
                            track-by="name"
                            placeholder="Contact" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/contacto.svg" class="img-icon" alt="port">
                    </div>

                    <div class="col-12 col-sm-3 input-search-form">
                            <multiselect
                            v-model="searchRequest.pricelevel"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :show-labels="false"
                            :options="priceLevelOptions"
                            label="name"
                            track-by="name"
                            placeholder="Price Level" 
                            class="s-input"
                            >
                            </multiselect>
                            <img src="/images/pricelevel.svg" class="img-icon" alt="port">
                    </div>

                    <div class="col-12 col-sm-3 input-search-form">
                        
                            <b-dropdown id="dropdown-carriers" :text="carrierText" ref="dropdown" class="m-2">
                                <b-dropdown-form>
                                    <label class="mt-2">
                                        <span>All Carriers</span>
                                        <b-form-checkbox 
                                            v-model="allCarriers" 
                                            class="switch-all-carriers"
                                        ></b-form-checkbox>
                                    </label>
                                    <b-form-group label="Carriers">
                                        <b-form-checkbox-group
                                            id="carriers-list"
                                            v-model="carriers"
                                            :options="carrierOptions"
                                        ></b-form-checkbox-group>
                                    </b-form-group>
                                </b-dropdown-form>
                            </b-dropdown>
                            <img src="/images/carrier.svg" class="img-icon" alt="port">
                    </div>

                </div>

                <div class="row mr-0 ml-4 mt-5 d-flex justify-content-start">
                    <b-form-checkbox
                        id="originChargesCheckbox"
                        v-model="searchRequest.originChargesCheckbox"
                        name="originChargesCheckbox"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="mr-5 as-checkbox"
                    >
                        Include origin charges
                    </b-form-checkbox>
                    <b-form-checkbox
                        id="destinationChargesCheckbox"
                        v-model="searchRequest.destinationChargesCheckbox"
                        name="destinationChargesCheckbox"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="as-checkbox"
                    >
                        Include destination charges
                    </b-form-checkbox>
                </div>
                
            </b-collapse>

            <!-- LCL FORM INPUTS -->
            <div class="row mr-0 ml-0 lcl-inputs" v-if="searchRequest.type == 'LCL'">
                    <!-- Tabs Section -->
				<b-card no-body class="card-tabs col-12 font-tabs">
					<b-tabs card>

						<b-tab title="CALCULATE BY TOTAL SHIPMENT" active>
                            
                            <div class="row">

                                <div class="col-12 col-sm-3">
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
                                                    placeholder="Select"
                                                    class="s-input no-select-style "
                                                >
                                                </multiselect>
                                                <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type" style="right: -10px !important"></b-icon>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-12 col-sm-3">
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

                                <div class="col-12 col-sm-3">
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

                                <div class="col-12 col-sm-3" style="text-align:center">
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
                                        <div class="col-12 col-sm-1 pr-0">
                                            
                                            <div class="type-packages">
                                                <multiselect
                                                    v-model="typePallet"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="false"
                                                    :show-labels="false"
                                                    :options="optionsTypePallet"
                                                    placeholder="Select"
                                                    class="s-input no-select-style "
                                                >
                                                </multiselect>
                                                <b-icon icon="caret-down-fill" aria-hidden="true" class="delivery-type "></b-icon>
                                            </div>
                                            
                                        </div>

                                        <div class="col-12 col-sm-1 pr-0">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="quantity"
                                                    placeholder="Quantity" 
                                                    class="s-input-form input-quantity"
                                                >
                                                </b-form-input>
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2 pr-0">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="height"
                                                    placeholder="Height" 
                                                    class="s-input-form"
                                                >
                                                </b-form-input>
                                                <img src="/images/espacio-de-trabajo.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2 pr-0">
                                            <label class="d-flex align-items-center">
                                                <b-form-input
                                                    v-model="width"
                                                    placeholder="Width" 
                                                    class="s-input-form"
                                                >
                                                </b-form-input>
                                                <img src="/images/espacio-de-trabajo.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2 pr-0">
                                            <label>
                                                <b-form-input
                                                    v-model="large"
                                                    placeholder="Large"
                                                    class="s-input-form"
                                                ></b-form-input>
                                                <img src="/images/espacio-de-trabajo.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-2 pr-0">
                                            <label>
                                                <b-form-input
                                                    v-model="weight"
                                                    placeholder="Weight"
                                                    class="s-input-form"
                                                ></b-form-input>
                                                <img src="/images/espacio-de-trabajo.svg" class="img-icon" alt="paquete">
                                            </label>
                                        </div>

                                        <div class="col-12 col-sm-1 pr-0 d-flex align-items-center justify-content-center">
                                            <span>1un M<sup>3</sup> 12KG</span>
                                        </div>

                                        <div class="col-12 col-sm-1 pr-0 d-flex justify-content-center align-items-center">
                                            <span v-on:click="addSurcharger" class="btn-add-surch"><b-icon icon="check-circle"></b-icon></span>
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                            <div class="row">
                                <div class="row col-12 mt-3 mb-3 mr-0 ml-0 pr-0 pl-0 data-surcharges" v-for="(item, index) in dataPackaging">

                                    <div class="col-12 col-sm-1 pr-0"><p>{{ item.type }}</p></div>
                                    <div class="col-12 col-sm-1 pr-0"><p>{{ item.quantity }}</p></div>
                                    <div class="col-12 col-sm-2 pr-0"><p>{{ item.height }}</p></div>
                                    <div class="col-12 col-sm-2 pr-0"><p>{{ item.width }}</p></div>
                                    <div class="col-12 col-sm-2 pr-0"><p>{{ item.large }}</p></div>
                                    <div class="col-12 col-sm-2 pr-0"><p>{{ item.weight }}</p></div>
                                    <div class="col-12 col-sm-1 pr-0"><p>{{ item.total }}</p></div>
                                    <div class="col-12 col-sm-1 pr-0"><span v-on:click="deleteSurcharger(index)"><b-icon icon="x-circle"></b-icon></span></div>

                                </div>
                            </div>
						</b-tab>

					</b-tabs>
				</b-card>
				<!-- End Tabs Section -->
            </div>

            <div class="col-lg-4">
                <div
                v-if="Array.isArray(foundRates) && foundRates.length == 0"
                class="alert alert-danger"
                role="alert"
                >
                    No results for this particular route. Create a manual quote or try another search
                </div>
            </div>

            <div class="row justify-content-center mr-0 ml-0">
                <div class="col-2 d-flex justify-content-center">
                    <button
                        v-if="!searching" 
                        class="btn-search"
                        @click="requestSearch"
                    >
                        SEARCH
                    </button>
                    <button
                        v-else
                        class="btn-search"
                    >
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button></div>
            </div>

        </div>

    </div>
</template>

<script>
import Search from './Search'; 
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue-multiselect/dist/vue-multiselect.min.css";
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import actions from "../../actions";

export default {
    components: {
        Search,
        Multiselect,
        DateRangePicker,
    },
    data() {
        return {
            loaded: false,
            searching: false,
            actions: actions,
            datalists: {},
            IDRequest: {},
            searchRequest: {
                direction: 2,
                type: 'FCL',
                destinationChargesCheckbox: false,
                originChargesCheckbox: false,
                deliveryType: {},
                selectedContainerGroup: {},
                containers: [],
                originPorts: [],
                destinationPorts: [],
                company: '',
                contact: '',
                pricelevel: '',
                carriers: [],
                originAddress: [],
                destinationAddress: [],
                dateRange: {
                    startDate: new Date().toISOString(),
                    endDate: new Date().toISOString(),
                },
            },
            selectedContainerGroup: {},
            containers: [],
            deliveryType: {},
            carriers: [],
            containerOptions: [],
            typeOptions: ['FCL', 'LCL'],
            deliveryTypeOptions: {},
            directionOptions: {},
            originPortOptions: {},
            destinationPortOptions: {},
            originAddressOptions: [],
            destinationAddressOptions: [],
            companyOptions: {},
            contactOptions: [],
            priceLevelOptions: {},
            carrierOptions: [],
            containerText: [],
            allCarriers: false,
            carrierText: 'All Carriers Selected',
            errorsExist: false,
            responseErrors: {},
            foundRates: {},
            companyChosen: false,
            //Gene defined
            ptdActive: false,
            dtpActive: false,
            dtdActive: false,
            dataPackaging: [],
            indeterminate: false,
            typePallet: 'PALLETS',
            selected: 'radio1',
            invalidCalculate: false,
            optionsTypePallet: ['PALLETS', 'PACKAGES'],  

            //DATEPICKER
            locale: 'en-US',
            dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'},
            dateRange: {
                startDate: '',
                endDate: '',
            },
        }
    },
    created() {
        //console.log("watching");
        api.getData({}, "/api/search/data", (err, data) => {
            this.setDropdownLists(err, data.data);
            this.setSearchDisplay();
        });
    },
    methods: {
        //Set lists of data
        setDropdownLists(err, data) {
            this.datalists = data;
            this.$emit("initialDataLoaded",this.datalists);
        },

        //set UI elements
        setSearchDisplay() {
            let component = this;

            component.originPortOptions = component.datalists.harbors;
            component.destinationPortOptions = component.datalists.harbors;
            component.directionOptions = [
                { text: component.datalists.directions[0].name, value: component.datalists.directions[0].id },
                { text: component.datalists.directions[1].name, value: component.datalists.directions[1].id }
            ];
            component.containerGroupOptions =  [
                { text: component.datalists.container_groups[0].name, value: component.datalists.container_groups[0] },
                { text: component.datalists.container_groups[1].name, value: component.datalists.container_groups[1] },
                { text: component.datalists.container_groups[2].name, value: component.datalists.container_groups[2] },
                { text: component.datalists.container_groups[3].name, value: component.datalists.container_groups[3] }
            ];
            component.datalists.carriers.forEach(function (carrier){
                component.carrierOptions.push({ text: carrier.name, value: carrier });
            });
            component.selectedContainerGroup = component.datalists.container_groups[0];
            component.containerOptions = component.datalists.containers;
            component.companyOptions = component.datalists.companies;
            component.contactOptions = component.datalists.contacts;
            component.priceLevelOptions = component.datalists.price_levels;
            component.deliveryTypeOptions = component.datalists.delivery_types;
            component.deliveryType = component.deliveryTypeOptions[0];
            component.allCarriers = true;
            component.loaded = true;
        },

        //Send Search Request to Controller
        requestSearch() {
            this.$emit("searchRequest");
            this.searching = true;
            this.searchRequest.selectedContainerGroup = this.selectedContainerGroup;
            this.searchRequest.containers = this.containers;
            this.searchRequest.deliveryType = this.deliveryType;
            this.searchRequest.carriers = this.carriers;
            this.searchRequest.harbors = this.datalists.harbors;
            this.searchRequest.surcharges = this.datalists.surcharges;
            this.searchRequest.currency = this.datalists.currency;
            this.searchRequest.calculation_type = this.datalists.calculation_type;
            this.errorsExist = false;
            actions.search
                .process(this.searchRequest)
                .then((response) => {
                    response.data.data.forEach(function (rate){
                        if(typeof rate.containers == "string"){
                            rate.containers = JSON.parse(rate.containers)
                        }
                    });
                    this.foundRates = response.data.data;
                    this.searching = false;
                    this.$emit("searchSuccess",response.data.data,this.searchRequest);
                    })
                .catch(error => {
                    this.errorsExist = true;
                    this.searching = false;
                    if(error.status === 422) {
                        this.responseErrors = error.data.errors;
                    }
                })
        },

        unlockContacts() {
            let component = this;
            let dlist = this.datalists;
            
            if(component.searchRequest.company != null){
                component.contactOptions = [];
                
                dlist.contacts.forEach(function (contact){
                    if(contact.company_id == component.searchRequest.company.id){
                        component.contactOptions.push(contact);
                    }
                });
                component.companyChosen = true;
            }else{
                component.companyChosen = false;
            }
            
        },

        deleteSurcharger(index){
            this.dataPackaging.splice(index, 1);
            //console.log(this.dataPackaging);
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
            //console.log(packaging);
            this.dataPackaging.push(packaging);
            
            this.pallets = ""; this.quantity = ""; this.height = ""; 
            this.width = "";   this.large = "";    this.weight = ""; 
            this.total = "";
        },
          
    },
    watch: {
        deliveryType: function() {
            if ( this.deliveryType.id == 1 ) {

                this.ptdActive = false; this.dtpActive = false; this.dtdActive = false; 
                return;

            } else if (this.deliveryType.id == 2) {

                this.dtpActive = false; this.dtdActive = false; 

                this.ptdActive = !this.ptdActive;
                return;

            } else if (this.deliveryType.id == 3) {

                this.ptdActive = false; this.dtdActive = false; 

                this.dtpActive = !this.dtpActive;
                return;

            } else if (this.deliveryType.id == 4) {

                this.ptdActive = false; this.dtpActive = false; 
               
                this.dtdActive = !this.dtdActive;
                return;

            }
        },

        selectedContainerGroup: function() {
            let component = this;
            let fullContainersByGroup = [];
            let selectedContainersByGroup = [];

            component.containerOptions = [];

            component.datalists.containers.forEach(function (container){
                if(component.selectedContainerGroup.id == container.gp_container_id){
                    selectedContainersByGroup.push(container);
                    fullContainersByGroup.push(container);
                }
            });

            if(selectedContainersByGroup.length > 3){
                selectedContainersByGroup.splice(3,2);
            }

            fullContainersByGroup.forEach(function (cont){
                component.containerOptions.push({ text: cont.code, value: cont });
            });
            component.containers = selectedContainersByGroup;
        },
        
        containers: function() {
            let component = this;

            component.containerText = [];

            component.containers.forEach(function (container){
                component.containerText.push(container.code)
            });

            if(this.containers == []){
                this.containerText = ['Select Containers'];
            }
        },

        carriers() {
            let component = this;

            if (component.carriers.length == component.carrierOptions.length) {
                component.carrierText = 'All Carriers Selected';
            } else if (component.carriers.length >= 5) {
                component.carrierText = component.carriers.length + " Carriers Selected";
            } else if (component.carriers.length == 0) {
                component.carrierText = 'Select a Carrier';
            } else {
                let selectedCarriers = [];

                component.carriers.forEach(function (carrier){
                    selectedCarriers.push(carrier.name);
                });

                component.carrierText = selectedCarriers.join(', ');
            }
        },

        allCarriers() {
            let component = this;

            component.carriers = []; 
            if(component.allCarriers){ 
                // Check all 
                component.datalists.carriers.forEach(function (carrier){
                    component.carriers.push(carrier);
                });
            }
        }
    }

}
</script>