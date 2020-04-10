<template>
    <b-card>
        <div class="row">
            <div class="col-6">
                <b-card-title>Ocean Freight</b-card-title>
            </div>
            <div class="col-6">
                <div class="float-right">
                    <button class="btn btn-link" v-b-modal.add-fcl>+ Export Contract</button>
                    <button class="btn btn-primary btn-bg">+ Add Freight</button>
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
        <!-- Action BTN -->
        <b-popover target="popover-button-variant" class="btns-action" variant="" triggers="focus" placement="bottomleft">
            <button class="btn-action">Edit</button>
            <button class="btn-action">Duplicate</button>
            <button class="btn-action">Delete</button>
        </b-popover>
        <!-- Action BTN END -->
        <!-- checkbox -->
        <input type="checkbox" class="input-check" id="check">
        <label  for="check"></label>
        <!-- checkbox end -->
        <!-- paginator -->
        <b-pagination v-model="currentPage" :total-rows="rows" align="right"></b-pagination>
    </b-card>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import DateRangePicker from 'vue2-daterange-picker';

    Vue.component('multiselect', Multiselect);
    export default {
        components: { 
            DateRangePicker,
            Multiselect
        },
        data() {
            return {
                isBusy:true, // Loader
                data: null,

                fields: [
                    { key: 'name', label: 'Reference', sortable: true },
                    { key: 'status', label: 'Status', sortable: true },
                    { key: 'from', label: 'Valid From', sortable: true },
                    { key: 'until', label: 'Valid Until', sortable: true },
                    { key: 'carriers', label: 'Carriers', 
                     formatter: value => {
                         let $carriers = [];

                         value.forEach(function(val){
                             $carriers.push(val.name);
                         });
                         return $carriers.join(', ');
                     } 
                    },
                    { key: 'equipment', label: 'Equipment', sortable: false },
                    { key: 'direction', label: 'Direction', formatter: value => { return value.name } 
                    }

                ],
                carrier: '',
                equipment: '',
                direction: '',
                options: [
                    'opcion 1',
                    'opcion 2',
                    'opcion 3'
                ],
                startDate: '2017-09-05',
                endDate: '2017-09-15',
                locale: {
                    direction: 'ltr', //direction of text
                    format: 'DD-MM-YYYY', //fomart of the dates displayed
                    separator: ' - ', //separator between the two ranges
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    weekLabel: 'W',
                    customRangeLabel: 'Custom Range',
                    daysOfWeek: moment.weekdaysMin(), //array of days - see moment documenations for details
                    monthNames: moment.monthsShort(), //array of month names - see moment documenations for details
                    firstDay: 1 //ISO first day of week - see moment documenations for details
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
            },
            confirmAction() {
                console.log('hola');
            }
        }
    }
</script>
