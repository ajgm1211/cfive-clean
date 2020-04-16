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
                    <b-button id="popover-button-variant" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>

                    <b-popover target="popover-button-variant" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                        <button class="btn-action">Edit</button>
                        <button class="btn-action">Duplicate</button>
                        <button class="btn-action">Delete</button>
                    </b-popover>
                    <!-- status -->
                    <span class="status-st published"></span>
                    <span class="status-st expired"></span>
                    <span class="status-st incompleted"></span>
                    <!-- status end -->
                    <!-- checkbox -->
                    <input type="checkbox" class="input-check" id="check">
                    <label  for="check"></label>
                    <!-- checkbox end -->
                    <!-- paginator -->
                    <!--<b-pagination v-model="currentPage" :total-rows="rows" align="right"></b-pagination>-->
                </b-card>
                <b-modal ref="addFCL" id="add-fcl" cancel-title="Cancel" ok-title="Add Contract" hide-header-close
                         title="Add FCL Contract">

                    <form ref="form" @submit.stop.prevent="handleSubmit" class="modal-input">
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
                                          ></b-form-input>
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
                                     :opens="opens"
                                     :locale-data="{ firstDay: 1 }"
                                     :singleDatePicker="singleDatePicker"
                                     :showWeekNumbers="showWeekNumbers"
                                     :showDropdowns="showDropdowns"
                                     v-model="dateRange"
                                     @update="updateValues"
                                     @toggle="checkOpen"
                                     :linkedCalendars="linkedCalendars"
                                     :dateFormat="dateFormat"
                                     >

                                    <template v-slot:input="picker"  style="min-width: 350px;">
                                        <i class="fa fa-calendar"></i>
                                        {{ picker.startDate | date }} - {{ picker.endDate | date }}
                                    </template>
                                </date-range-picker>


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
                    </form>
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
                    { key: 'name', label: 'Reference', sortable: true },
                    { key: 'status', label: 'Status', sortable: true, isHtml: true,                     
                    formatter: value => {
                        if (value == 'publish')
                          return '<span class="status-st published"></span>';
                        else if (value == 'expired')
                          return '<span class="status-st expired"></span>';
                        else if (value == 'incompleted')
                         return '<span class="status-st incompleted"></span>';
                     } 
                    },
                    { key: 'validity', label: 'Valid From', sortable: true },
                    { key: 'expire', label: 'Valid Until', sortable: true },
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
                    }

                ],
                carrier: '',
                equipment: '',
                direction: '',
                carriers: [],
                directions: [],
                equipments: [],
                dateRange: { 
                    startDate: '', 
                    endDate:  ''
                }, 
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

            api.getData({}, '/api/v2/contracts', (err, data) => {
                this.setData(err, data);
            });

            api.getData({}, '/api/v2/contracts/data', (err, data) => {
              this.setDropdownLists(err, data.data);
            });

        },
        methods: {
            setDropdownLists(err, data){
              this.carriers = data.carriers;
              this.equipments = data.equipments;
              this.directions = data.directions;
            },
            setData(err, { data: records, links, meta }) {
                this.isBusy = false;

                if (err) {
                    this.error = err.toString();
                } else {
                    this.data = records;
                }
            },
            prepareData(){

                /*return {
                  'name': this.reference,
                  'direction': this.direction,
                  'validity': this.date[0],
                  'expire': this.date[1],
                  'status': 'publish',
                  'remarks': '',
                  'gp_container': this.equipment,
                  'carriers': this.carrier
                }*/
            },
            handleSubmit(){

              const data = this.prepareData();

              api.call('post', 'api/v2/contracts/', { data: this.data })
              .then( ( response ) => {
                  
              })
              .catch(( data ) => {
                  this.$refs.observer.setErrors(data.data.errors);
              });

            },
            confirmAction() {
                console.log('hola');
            }
        }
    }
</script>