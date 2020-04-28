<template>
    <b-card>
        <div class="row">
            <div class="col-6">
                <b-card-title>Surcharges</b-card-title>
            </div>
            <div class="col-6">
                <div class="float-right">
                    <button class="btn btn-link" v-b-modal.add-fcl>+ Export Contract</button>
                    <button class="btn btn-primary btn-bg">+ Add Surcharge</button>
                </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-12 col-sm-4">
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

        <b-form-checkbox-group>
            <b-form-checkbox
                             class="select-all"
                             v-model="allSelected"
                             :indeterminate="indeterminate"
                             @change="toggleAll"
                             >
            </b-form-checkbox>
        </b-form-checkbox-group>

        <b-button id="popover-all" class="action-app all-action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
        <b-popover target="popover-all" class="btns-action" variant="" triggers="focus" placement="bottomleft">
            <button class="btn-action">Edit</button>
            <button class="btn-action">Duplicate</button>
            <button class="btn-action">Delete</button>
        </b-popover>

        <b-table borderless hover :fields="fields" :items="data" :current-page="currentPage">
            <template v-slot:cell(checkbox)="data">
                <b-form-checkbox-group >
                    <b-form-checkbox 
                                     v-bind:value="data.item"
                                     v-bind:id="'check'+data.item.id"
                                     v-model="selected"
                                     >
                    </b-form-checkbox>
                </b-form-checkbox-group>
            </template>

            <template v-slot:cell(actions)="data">
                <b-button v-bind:id="'popover'+data.item.id" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
                <b-popover v-bind:target="'popover'+data.item.id" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                    <button class="btn-action">Edit</button>
                    <button class="btn-action">Duplicate</button>
                    <button class="btn-action">Delete</button>
                </b-popover>
            </template>
        </b-table>
        <!-- paginator -->
        <b-pagination v-model="currentPage" :total-rows="rows" align="right"></b-pagination>
    </b-card>
</template>


<script>
    export default {
        components: { 

        },
        data() {
            return {
                isBusy:true, // Loader
                data: null,

                fields: [
                    { key: 'checkbox', label: '', tdClass: 'checkbox-add-fcl', isHtml: true},
                    { key: 'charges', label: 'Charges', sortable: false },
                    { key: 'from', label: 'Origin Port', sortable: false },
                    { key: 'until', label: 'Destination Port', sortable: false },
                    { key: 'chargeType', label: 'Charge Type', sortable: false },
                    { key: 'calculation', label: 'Calculation Type', sortable: false },
                    { key: 'amount', label: 'Amount', sortable: false },
                    { key: 'currency', label: 'Currency', sortable: false },
                    { key: 'carriers', label: 'Carriers', 
                     formatter: value => {
                         let $carriers = [];

                         value.forEach(function(val){
                             $carriers.push(val.name);
                         });
                         return $carriers.join(', ');
                     } 
                    },
                    { key: 'actions', label: '', tdClass: 'actions-add-fcl'}



                ],

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
