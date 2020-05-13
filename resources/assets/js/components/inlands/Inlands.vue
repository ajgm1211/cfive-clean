<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">

                <form ref="form" @submit.stop.prevent="handleSubmit" class="modal-input">
                    <div class="row">
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Provider"
                                          label-for="Provider"
                                          invalid-feedback="Provider is required"
                                          >
                                <b-form-input
                                              id="Provider"
                                              v-model="provider"
                                              required
                                              v-on:blur="updateInland()"
                                              >

                                </b-form-input>
                            </b-form-group>
                        </div>
                                   <div class="col-12 col-sm-6 col-lg-2">
                            <b-form-group
                                          label="Status"
                                          label-for="status"
                                          invalid-feedback="Status is required"
                                          >
                                     <b-form-input
                                              id="status"
                                              v-model="status"
                                              required
                                              v-on:blur="updateInland()"
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
                                <multiselect v-model="direction" :options="directions" :searchable="false" :close-on-select="true"   label="name" :show-labels="false" placeholder="Select"></multiselect>


                            </b-form-group>
                        </div>
       
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Validity"
                                          label-for="validity"
                                          invalid-feedback="Validity is required"
                                          >
              <date-range-picker
                                                   ref="picker"
                                                   :locale-data="{ firstDay: 1 }"
                                                   :singleDatePicker="singleDatePicker"
                                                   v-model="dateRange"
                                                   @update="updateValues"
                                                   @toggle="checkOpen"
                                                   :linkedCalendars="linkedCalendars"
                                                   :dateFormat="dateFormat"
                                                   >
                                </date-range-picker>
                            </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Equipment"
                                          label-for="equipment"
                                          invalid-feedback="Equipment is required"
                                          >
                                <multiselect v-model="equipment" :options="equipments" :searchable="false" :close-on-select="true" track-by="id" label="name" :show-labels="false" placeholder="Select"></multiselect>
                            </b-form-group>
                        </div> 
               
                    </div>
                </form>

                <b-card no-body class="card-tabs">
                    <b-tabs card>
                        <b-tab title="Per Ranges">
                            <inland-ranges></inland-ranges>
                        </b-tab>
                        <b-tab title="Per Km">
                            <inland-km></inland-km>
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
    import InlandRanges from './InlandRanges';
    import InlandKm from './InlandKm';
    
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
    import 'vue-multiselect/dist/vue-multiselect.min.css';
    export default {
        components: { 
            DateRangePicker,
            Multiselect,
            InlandRanges,
            InlandKm
        },
        data() {
            return {
                
                isBusy:true, // Loader
                data: null,
                carrier: null,
                direction: null,
                equipment: null,
                status: null,
                calculation: null,
                provider: null,
                statusclass: '',
                // Dropdown Lists
                directions: [],
                carriers: [],
                equipments: [],
                companies: [],
                calculations: [],

                /* Ocean Freight */
                containers: null,
                
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
            }
        },
        created() {
            let inland_id = this.$route.params.id;
            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/inlands/data', (err, data) => {
                this.setDropdownLists(err, data.data);
            });
            api.call('get', '/api/v2/inlands/retrieve/'+inland_id, {})
                .then( ( response ) => {
                this.provider = response.data.data.provider;
                this.direction = response.data.data.type;
                this.equipment = response.data.data.gp_container;
                this.status = response.data.data.status;
                this.valid = response.data.data.validity;
                this.expired = response.data.data.expired;
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
    
                this.equipments = data.equipments;
                this.directions = data.directions;
                this.containers = data.containers;
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
               
            },
            updateContract(){
                const data = this.prepareData();
                const inland_id = this.$route.params.id;
                api.call('post', '/api/v2/contracts/'+inland_id+'/update', data)
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