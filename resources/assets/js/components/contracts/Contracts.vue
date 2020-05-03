<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">

                <form ref="form" @submit.stop.prevent="handleSubmit" class="modal-input">
                    <div class="row">
                        <div class="col-12 col-sm-3 col-lg-2">
                        <b-form-group
                                      label="Reference"
                                      label-for="reference"
                                      invalid-feedback="Reference is required"
                                      >
                            <b-form-input
                                          id="reference"
                                          v-model="reference"
                                          required
                                          v-on:blur="updateContract()"
                                          >
                                            
                            </b-form-input>
                        </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Direction"
                                          label-for="direction"
                                          invalid-feedback="Direction is required"
                                          >
                                <multiselect v-model="direction" :options="datalists.directions" :searchable="false" :close-on-select="true"  track-by="id" label="name" :show-labels="false" placeholder="Select Direction"></multiselect>


                            </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Carrier"
                                          label-for="carrier"
                                          invalid-feedback="Carrier is required"
                                          >
                                    <multiselect v-model="carrier" :multiple="true" :options="datalists.carriers" :searchable="false" :close-on-select="true" track-by="id" label="name" :show-labels="false" placeholder="Select Carrier">
                                      
                                       <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span></template>
                                    </multiselect>



                            </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Validity"
                                          label-for="validity"
                                          invalid-feedback="Validity is required"
                                          >

                                          <!--<date-range-picker
                                                       ref="picker"
                                                       :opens="'center'"
                                                       :locale-data="{ firstDay: 1, format: 'MMM DD, YYYY' }"
                                                       :singleDatePicker="false"
                                                       :autoApply="true"
                                                       :timePicker="false"
                                                       v-model="selectedDates"
                                                       :linkedCalendars="true">
                                    </date-range-picker>-->

                                 <!--<date-range-picker

                                                   ref="picker"
                                                   :locale-data="{ firstDay: 1 }"
                                                   :singleDatePicker="singleDatePicker"
                                                   v-model="dateRange"
                                                   @update="updateValues"
                                                   @toggle="checkOpen"
                                                   :linkedCalendars="linkedCalendars"
                                                   :dateFormat="dateFormat"
                                                   >
                                </date-range-picker>-->

                            </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Equipment"
                                          label-for="equipment"
                                          invalid-feedback="Equipment is required"
                                          >
                                <multiselect v-model="equipment" :options="datalists.equipments" :searchable="false" :close-on-select="true" track-by="id" label="name" :show-labels="false" placeholder="Select Equipment"></multiselect>
                            </b-form-group>
                        </div>


                        <div class="col-12 col-sm-3 col-lg-2">

                            <b-form-group
                                          label="Status"
                                          label-for="status"
                                          invalid-feedback="Direction is required"
                                          >
                                <span class="status-st" :class="statusclass" ></span>
                                <span class="status-st"></span>
                                <span class="status-st"></span>

                            </b-form-group>
                        </div>
                    </div>
                </form>

                <b-card no-body class="card-tabs">
                    <b-tabs card>
                        <b-tab title="Ocean Freight" active>
                            <ocean-freight 
                            :equipment="equipment" 
                            :datalists="datalists"
                            :actions="actions.oceanfreights"
                            ></ocean-freight>
                        </b-tab>
                        <b-tab title="Surcharges">
                            <!--<surcharges></surcharges>-->
                        </b-tab>
                        <b-tab title="Restrictions">
                            <!--<restrictions></restrictions>-->
                        </b-tab>
                        <b-tab title="Remarks">
                            <!--<remarks></remarks>-->
                        </b-tab>
                        <b-tab title="Files">
                            <!--<files></files>-->
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>

        </div>

    </div>

</template>
<script>
    import Multiselect from 'vue-multiselect';
    import DateRangePicker from 'vue2-daterange-picker';
    import OceanFreight from './Freight';
    import Surcharges from './Surcharges';
    import Restrictions from './Restrictions';
    import Remarks from './Remarks';
    import Files from './Files';
    import actions from '../../actions';

    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
    import 'vue-multiselect/dist/vue-multiselect.min.css';

    export default {
        components: { 
            DateRangePicker,
            Multiselect,
            OceanFreight,
            Surcharges,
            Restrictions,
            Remarks,
            Files

        },
        data() {
            return {
                actions: actions,
                isBusy:true, // Loader
                data: null,
                carrier: null,
                direction: null,
                equipment: null,
                reference: null,
                statusclass: '',
                selectedDates: {},
                allSelected: false,
                indeterminate: false,
                
                // Dropdown Lists
                datalists: {
                  'carriers': [],
                  'equipments': [],
                  'directions': [],
                  'containers': [],
                  'harbors': [],
                  'currencies': []
                },
                
                dateRange: { 
                    startDate: '', 
                    endDate:  ''
                }, 
                locale:{
                    direction: 'ltr',
                    format: 'dd/mm/yyyy',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    customRangeLabel: 'Custom Range',
                    daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    firstDay: 1
                },

                /* Ocean Freight */
                containers: null
            }
        },
        created() {

            let contract_id = this.$route.params.id;

            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/contracts/data', (err, data) => {
                this.setDropdownLists(err, data.data);
            });

            api.call('get', '/api/v2/contracts/'+contract_id, {})
            .then( ( response ) => {
                this.reference = response.data.data.name;
                this.direction = response.data.data.direction;
                this.carrier = response.data.data.carriers;
                this.equipment = response.data.data.gp_container;
                this.statusclass = response.data.data.status;
                
              
              //window.location = 'http://127.0.0.1:8000/api/contracts//edit';
            })
            .catch(( data ) => {
                this.$refs.observer.setErrors(data.data.errors);
            });

        },
        methods: {

            /* Set the Dropdown lists to use in form */
            setDropdownLists(err, data){
                this.datalists = {
                  'carriers': data.carriers,
                  'equipments': data.equipments,
                  'directions': data.directions,
                  'containers': data.containers,
                  'harbors': data.harbors,
                  'currencies': data.currencies
                }

                console.log(this.datalists);
            },

            setData(err, { data: records, links, meta }) {
                this.isBusy = false;

                if (err) {
                    this.error = err.toString();
                } else {
                    this.data = records;
                }
            },

            /* Prepare the data to create a new Contract */
            prepareData(){
                let carriers = [];
                this.carrier.forEach(e => carriers.push(e.id));

                return {
                  'name': this.reference,
                  'direction': this.direction.id,
                  'validity': '2020-10-10',//this.dateRange.startDate, //this.dateRange.startDate,
                  'expire': '2020-10-10',//this.dateRange.endDate, //this.dateRange.endDate,
                  'remarks': '',
                  'gp_container': this.equipment.id,
                  'carriers': carriers
                }
            },

            updateContract(){

                const data = this.prepareData();
                const contract_id = this.$route.params.id;

                api.call('post', '/api/v2/contracts/'+contract_id+'/update', data)
                .then( ( response ) => {
                  console.log('Approval')
                })
                .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
            }
        },
        watch: {
            /*reference: function(val, oldVal) {
                if(oldVal)
                    this.updateContract();
            },*/
            carrier: function(val, oldVal) {
                if(oldVal)
                    this.updateContract();
            },
            direction: function(val, oldVal) {
                if(oldVal)
                    this.updateContract();
            },
            equipment: function(val, oldVal) {
                if(oldVal)
                    this.updateContract();
            }
        }
    }
</script>