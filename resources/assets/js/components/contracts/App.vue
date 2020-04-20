<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>FCL Contracts</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-link" v-b-modal.add-fcl>+ Add Contract</button>
                                <button class="btn btn-primary btn-bg">+ Import Contract</button>
                            </div>
                        </div>
                    </div>


                    <div class="row my-3">
                        <div class="col-4">
                            <b-form inline>
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <b-input
                                         id="inline-form-input-name"
                                         class="mb-2 mr-sm-2 mb-sm-0"
                                         placeholder="Search"
                                         ></b-input>
                            </b-form>
                        </div>
                    </div>
                     <b-table borderless hover :fields="fields" :items="data" :current-page="currentPage"></b-table>
                    <!--<b-button id="popover-button-variant" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>-->

                    <!-- <b-popover target="popover-button-variant" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                        <button class="btn-action">Edit</button>
                        <button class="btn-action">Duplicate</button>
                        <button class="btn-action">Delete</button>
                    </b-popover> -->
                    <!-- status -->
                    <!-- <span class="status-st published"></span>
<span class="status-st expired"></span>
<span class="status-st incompleted"></span> -->
                    <!-- status end -->
                    <!-- checkbox -->
                    <!-- <input type="checkbox" class="input-check" id="check">
<label  for="check"></label> -->
                    <!-- checkbox end -->
                    <!-- paginator -->
                </b-card>

                <b-modal ref="addFCL" id="add-fcl" cancel-title="Cancel" ok-title="Add Contract" hide-header-close
                         title="Add FCL Contract" hide-footer>

                    <b-form ref="form" @submit.stop.prevent="handleSubmit" class="modal-input">
                        <b-form-group
                                      label="Reference"
                                      label-for="reference"
                                      invalid-feedback="Reference is required"
                                      >
                            <b-form-input
                                          id="reference"
                                          v-model="name"
                                          :state="nameState"
                                          placeholder="Reference" 
                                          required
                                          >
                                            
                            </b-form-input>
                        </b-form-group>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <b-form-group
                                              label="Validity"
                                              label-for="validity"
                                              invalid-feedback="Validity is required"
                                              >
                                    <date-range-picker
                                                       ref="picker"
                                                       :opens="'center'"
                                                       :locale-data="{ firstDay: 1, format: 'MMM DD, YYYY' }"
                                                       :singleDatePicker="false"
                                                       :timePicker="false"
                                                       v-model="selectedDates"
                                                       :linkedCalendars="true">
                                    </date-range-picker>
                                    <!-- <ValidationProvider :vid="name" :rules="rules" :name="label" v-slot="{ errors }">
<b-form-input v-show="false"
class="border-light"
:class="{'is-invalid': errors.length }"
:name="name"
v-model="model">                                  
</b-form-input>
<span class="invalid-feedback">{{ errors[0] }}</span>
</ValidationProvider> -->



                                </b-form-group>
                            </div>
                            <div class="col-12 col-sm-6 ">
                                <b-form-group
                                              label="Carrier"
                                              label-for="carrier"
                                              invalid-feedback="Carrier is required"
                                              >
                                    <multiselect v-model="carrier" :options="carriers" :searchable="false" :close-on-select="true" track-by="id" label="name" :show-labels="false" placeholder="Select Carrier"></multiselect>



                                </b-form-group>
                            </div>
                            <div class="col-12 col-sm-6">
                                <b-form-group
                                              label="Equipment"
                                              label-for="equipment"
                                              invalid-feedback="Equipment is required"
                                              >
                                    <multiselect v-model="equipment" :options="equipments" :searchable="false" :close-on-select="true" track-by="id" label="name" :show-labels="false" placeholder="Select Equipment"></multiselect>

                                </b-form-group>
                            </div>
                            <div class="col-12 col-sm-6">
                                <b-form-group
                                              label="Direction"
                                              label-for="direction"
                                              invalid-feedback="Direction is required"
                                              >
                                    <multiselect v-model="direction" :options="directions" :searchable="false" :close-on-select="true"  track-by="id" label="name" :show-labels="false" placeholder="Select Direction"></multiselect>


                                </b-form-group>
                            </div>
                        </div>

                        <div class="btns-form-modal">
                            <button class="btn" @click="modalClose" type="button">Cancel</button>

                            <button class="btn btn-primary btn-bg">Add Contract</button>
                        </div>
                    </b-form>
                </b-modal>

            </div>

        </div>

    </div>

</template>
<script>

    import Multiselect from 'vue-multiselect';
    import DateRangePicker from 'vue2-daterange-picker';

    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
    export default {
        props: {
            label: String,
            value: Array | String | Object,
            name: String,
            startDate: String,
            endDate: String,
            rules: String,
            placeholder: {
                type: String,
                required: false,
                default: 'Select Dates Range'
            }
        },
        components: { 
            DateRangePicker,
            Multiselect
        },

        data() {
            return {
                isBusy:true, // Loader
                data: null,
                currentPage: 1,
                nameState: true,

                fields: [
                    { key: 'checkbox', label: '', tdClass: 'checkbox-add-fcl', formatter: value => {
                        var checkbox = '<input type="checkbox" class="input-check" id="check"/><label  for="check"></label>';
                        $('.checkbox-add-fcl').append(checkbox);
                    }  
                    },
                    { key: 'name', label: 'Reference', sortable: false },
                    { key: 'status', label: 'Status', sortable: false, isHtml: true, tdClass: 'status-add-fcl',
                     formatter: value => {
                         var publish ='<span class="status-st published"></span>';
                         var expired ='<span class="status-st expired"></span>';
                         var incompleted ='<span class="status-st incompleted"></span>';

                         if (value == 'publish')
                             $('.status-add-fcl').append(publish);
                         else if (value == 'expired')
                             $('.status-add-fcl').append(expired);
                         else if (value == 'incompleted')
                             $('.status-add-fcl').append(incompleted);
                     } 
                    },
                    { key: 'validity', label: 'Valid From', sortable: false },
                    { key: 'expire', label: 'Valid Until', sortable: false },
                    { key: 'carriers', label: 'Carriers', 
                     formatter: value => {
                         let $carriers = [];

                         value.forEach(function(val){
                             $carriers.push(val.name);
                         });
                         return $carriers.join(', ');
                     } 
                    },
                    { key: 'gp_container', label: 'Equipment', sortable: false, formatter: value => {
                        return value.name;
                    }
                    },
                    { key: 'direction', label: 'Direction', formatter: value => { return value.name } 
                    },
                    { key: 'actions', label: '', tdClass: 'actions-add-fcl', formatter: value => {
                        var actions = '<label for="actions-box"><div class="actions-box"><i class="fa fa-ellipsis-h icon-add-fcl" aria-hidden="true"></i><input type="checkbox" id="actions-box"><div class="popup-actions"><button type="button" class="btn-action">Edit</button><button type="button" class="btn-action">Duplicate</button><button type="button" class="btn-action">Delete</button></div></div></label>';
                        $('.actions-add-fcl').append(actions);
                    }  
                    } 

                ],

                // Models Data
                name: null,
                carrier: [],
                equipment: '',
                direction: '',
                selectedDates: { 
                    startDate: '', 
                    endDate:  ''
                }, 

                //List Data
                carriers: [],
                directions: [],
                equipments: [],
                locale:{
                    direction: 'ltr',
                    format: 'mm/dd/yyyy',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    customRangeLabel: 'Custom Range',
                    daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    firstDay: 1
                }
            }
        },
        created() {

            /* Return the Contracts lists data*/
            api.getData({}, '/api/v2/contracts', (err, data) => {
                this.setData(err, data);
            });

            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/contracts/data', (err, data) => {
                this.setDropdownLists(err, data.data);
            });

            this.$emit('input', { startDate: null, endDate: null });
            this.setDates();

        },
        methods: {
            modalClose() {
                this.$bvModal.hide('add-fcl');
            },
            setDates() {
                if(this.startDate && this.endDate){
                    this.selectedDates = {
                        startDate: moment(this.startDate).format('MMM DD, YYYY'),
                        endDate: moment(this.endDate).format('MMM DD, YYYY')
                    }
                }
            },

            /* Set the Dropdown lists to use in form */
            setDropdownLists(err, data){
                this.carriers = data.carriers;
                this.equipments = data.equipments;
                this.directions = data.directions;
            },

            /* Set the data response in table */ 
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

                return {
                  'name': this.name,
                  'direction': this.direction.id,
                  'validity': '2020-02-20', //this.dateRange.startDate,
                  'expire': '2020-02-20', //this.dateRange.endDate,
                  'status': 'publish',
                  'remarks': '',
                  'gp_container': this.equipment.id,
                  'carriers': [this.carrier.id]
                }
            },
            /* Handle the submit of Create Form and 
              send the data to store a new contract */
            handleSubmit(){

                const data = this.prepareData();

                api.call('post', '/api/v2/contracts/store', data)
                .then( ( response ) => {
                  app.$router.push('http:/app.cargofive.com/');
                    
                })
                .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });

            }

        },
        watch: {
            selectedDates: {
                handler: function (val, oldVal) {
                    this.$emit('input', val);
                    this.model = 'example';
                },
                deep: true
            }
        }
    }
</script>