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
                                          invalid-feedback="Reference date is required"
                                          >
                                <multiselect v-model="reference" :options="options" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select Carrier"></multiselect>
                            </b-form-group> 
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Direction"
                                          label-for="direction"
                                          invalid-feedback="Direction is required"
                                          >
                                <multiselect v-model="directions" :options="options" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select Direction"></multiselect>


                            </b-form-group>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Carrier"
                                          label-for="carrier"
                                          invalid-feedback="Carrier is required"
                                          >
                                <multiselect v-model="carrier" :options="options" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select Carrier"></multiselect>



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
                                                   :opens="opens"
                                                   :locale-data="{ firstDay: 1 }"
                                                   :singleDatePicker="singleDatePicker"
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
                        <div class="col-12 col-sm-3 col-lg-2">
                            <b-form-group
                                          label="Equipment"
                                          label-for="equipment"
                                          invalid-feedback="Equipment is required"
                                          >
                                <multiselect v-model="equipment" @click="prueba" :options="equipments" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select Equipment"></multiselect>
                            </b-form-group>
                        </div>


                        <div class="col-12 col-sm-3 col-lg-2">

                            <b-form-group
                                          label="Status"
                                          label-for="status"
                                          invalid-feedback="Direction is required"
                                          >
                                <span class="status-st published"></span>
                                <span class="status-st expired"></span>
                                <span class="status-st incompleted"></span>

                            </b-form-group>
                        </div>
                    </div>
                </form>

                <b-card no-body class="card-tabs">
                    <b-tabs card>
                        <b-tab title="Ocean Freight" active>
                            <ocean-freight></ocean-freight>
                        </b-tab>
                        <b-tab title="Surcharges">
                            <surcharges></surcharges>
                        </b-tab>
                        <b-tab title="Restrictions">
                            <restrictions></restrictions>
                        </b-tab>
                        <b-tab title="Remarks">
                            <remarks></remarks>
                        </b-tab>
                        <b-tab title="Files">
                            <files></files>
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
                isBusy:true, // Loader
                data: null,
                carrier: '',
                equipment: '',
                directions: '',
                reference: '',
                options: [
                    'opcion 1',
                    'opcion 2',
                    'opcion 3'
                ],
                equipments: [
                    'Dry',
                    'Reefer',
                    'Open Top',
                    'Flat Rack'
                ],
                
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

        },
        methods: {
            setData(err, { data: records, links, meta }) {
                this.isBusy = false;

                if (err) {
                    this.error = err.toString();
                } else {
                    this.data = records;
                }
            }
        }
    }
</script>