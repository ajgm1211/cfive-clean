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

        <b-table-simple hover small responsive borderless>
            <b-thead>
                <b-tr v-if="booleano">
                    <b-th v-for="(value, key) in efields" :key="key">
                        {{value}}
                    </b-th>
                </b-tr>
            </b-thead>
            <b-tbody>

                <!-- Form add new item -->
                <b-tr>
                    <b-td>

                    </b-td>
                    <b-td>
                        <multiselect 
                             v-model="destination"
                             :options="harbors" 
                             :searchable="true"
                             :close-on-select="true"
                             :clear-on-select="false"
                             track-by="id" label="name" 
                             :show-labels="true" 
                             placeholder="Select Origin">
                        </multiselect>
                    </b-td>
                    <b-td>
                        <multiselect 
                             v-model="origin" 
                             :options="harbors" 
                             :searchable="true"
                             :close-on-select="true"
                             :clear-on-select="false"
                             track-by="id" label="name" 
                             :show-labels="true" 
                             placeholder="Select Destination">
                        </multiselect>
                    </b-td>
                    <b-td v-for="(val, k) in container_fields" :key="k" class="th-max">
                        <b-form-input
                                      :placeholder="val"
                                      v-model="rates['rates_'+val]" 
                                      >
                        </b-form-input>
                    </b-td>
                    <b-td>
                        <multiselect 
                             v-model="carrier" 
                             :options="carriers" 
                             :searchable="true"
                             :close-on-select="true"
                             :clear-on-select="false"
                             track-by="id" label="name" 
                             :show-labels="true" 
                             placeholder="Select Carrier">
                        </multiselect>
                    </b-td>
                    <b-td>
                        <multiselect 
                             v-model="currency"
                             :options="currencies" 
                             :searchable="true"
                             :close-on-select="true"
                             :clear-on-select="false"
                             track-by="id" label="alphacode" 
                             :show-labels="true" 
                             placeholder="Select Currency">
                        </multiselect>
                    </b-td>
                    <b-td>
                        <b-button class="action-app" href="#" tabindex="0" v-on:click="onSubmit()"><i class="fa fa-check" aria-hidden="true"></i></b-button>
                    </b-td>
                </b-tr>
                <!-- End of form -->

                <!-- Data List -->
                <b-tr v-for="(value, key) in data" :key="key">                    
                    <b-td>
                       <b-form-checkbox-group >
                            <b-form-checkbox 
                                             v-bind:value="data.value"
                                             v-bind:id="'check'+value.id"
                                             v-model="selected"
                                             >
                            </b-form-checkbox>
                        </b-form-checkbox-group>
                    </b-td>
                    <b-td>
                       {{value.origin.name}}
                    </b-td>
                    <b-td>
                       {{value.destination.name}}
                    </b-td>

                    <b-td v-for="(val, k) in container_fields" :key="k">
                        {{value[val]}}
                    </b-td>

                    <b-td>
                       {{value.carrier.name}}
                    </b-td>
                    <b-td>
                       {{value.currency.alphacode}}
                    </b-td>
                    <b-td>
                        <b-button v-bind:id="'popover'+value.id" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
                        <b-popover v-bind:target="'popover'+value.id" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                            <button class="btn-action">Edit</button>
                            <button class="btn-action">Duplicate</button>
                            <button class="btn-action">Delete</button>
                        </b-popover>
                    </b-td>


                </b-tr>
                <!-- End Data list -->

            </b-tbody>
        </b-table-simple>
        
        <!-- Pagination -->
        <paginate
                  :page-count="pageCount"
                  :click-handler="clickCallback"
                  :prev-text="'Prev'"
                  :next-text="'Next'"
                  :page-class="'page-item'"
                  :page-link-class="'page-link'"
                  :container-class="'pagination justify-content-end'"
                  :prev-class="'page-item'"
                  :prev-link-class="'page-link'"
                  :next-class="'page-item'"
                  :next-link-class="'page-link'"
                  :initialPage="initialPage">
        </paginate>
        <!-- Pagination end -->

    </b-card>
</template>


<script>
    import Multiselect from 'vue-multiselect';
    import paginate from '../paginate';


    export default {
        props: {
            equipment: Object,
            containers: Array,
            carriers: Array,
            harbors: Array,
            currencies: Array
        },
        components: { 
            Multiselect,
            paginate
        },
        data() {
            return {
                isBusy:true, // Loader
                booleano: false,
                data: null,
                e_startfields: [' ', 'Origin Port', 'Destination Port'],
                e_endfields: ['Carrier', 'Currency', ' '],
                e_fields: [],
                fields: [],
                pageCount: 0,
                initialPage: 1,
                carrier: null,
                origin: null,
                destination: null,
                currency: null,
                container_fields: [],
                selected: [],
                rates: {},
                contract_id: null,
                allSelected: false,
                indeterminate: false,
            }
        },
        created() {
            this.contract_id = this.$route.params.id;

            this.initialData();

        },
        methods: {
            /* Response the Rates lists data*/
            initialData(){
                let params = this.$route.query;

                if(params.page) this.initialPage = Number(params.page);

                this.getData(params);
            },

            /* Response the Rates lists data*/
            getData(params = {}){

                api.getData({}, `/api/v2/contracts/${this.contract_id}/ocean_freight`, (err, data) => {
                    this.setData(err, data);
                });

            },

            setData(err, { data: records, links, meta }) {
                this.isBusy = false;

                if (err) {
                    this.error = err.toString();
                } else {
                    this.data = records;
                    this.pageCount = Math.ceil(meta.total/meta.per_page);
                }
            },

            refreshData(){
                this.$router.push({});
                this.initialPage = 1;
                this.getData({});
            },

            /* Pagination Callback */
            clickCallback (pageNum) {
                this.isBusy = true;

                let qs = {
                    page: pageNum
                };

                if(this.$route.query.sort) qs.sort = this.$route.query.sort;
                if(this.$route.query.q) qs.q = this.$route.query.q;

                this.routerPush(qs);
            },

            /* Update url and execute api call */
            routerPush(qs) {
                this.$router.push({query: qs});

                this.getData(qs);
            },

            /* Prepare Rate Data to submit */
            prepareData(){
                
                let data = {
                    'origin': this.origin.id,
                    'destination': this.destination.id,
                    'carrier': this.carrier.id,
                    'currency': this.currency.id
                }

                return {...data, ...this.rates};
            },

            /* Clear Rate Form Data */
            clearForm(){
                this.origin = null;
                this.destination = null;
                this.carrier = null;
                this.currency = null;
                this.rates = [];
            },

            /* Submit Rate new Data */
            onSubmit() {
                
                let data = this.prepareData();

                this.isBusy = true;

                api.call('post', `/api/v2/contracts/${this.contract_id}/ocean_freight/store`, data)
                    .then( ( data ) => {
                        this.clearForm();
                        this.refreshData();
                })
                    .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });

            },

            /* Single Actions */
            onEdit(id){
                //window.location = `/api/contracts/${id}/edit`;
            },
            onDelete(id){
              
                this.isBusy = true;

                api.call('delete', `/api/v2/contracts/${id}/destroy`, {})
                    .then( ( response ) => {
                    this.refreshData();
                })
                    .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
            },
            onDuplicate(id){

                this.isBusy = true;
                
                api.call('post', '/api/v2/contracts/duplicate', data)
                    .then( ( response ) => {
                    this.$router.push({});
                    this.getData();
                })
                    .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
            }
            /* End single actions */
        },
        watch: {
            equipment: function(val, oldVal) {
                let data = this;
                this.efields = [];
                this.booleano = false;
                this.container_fields = [];

                this.e_startfields.forEach(item => data.efields.push(item));

                this.containers.forEach(function(item){
                    if(item.gp_container_id === val.id)
                    {
                        data.efields.push(item.name);
                        data.container_fields.push(item.code);
                    }
                });

                this.e_endfields.forEach(item => data.efields.push(item));

                this.booleano = true;

                console.log(this.efields);
            }
        }
    }
</script>
