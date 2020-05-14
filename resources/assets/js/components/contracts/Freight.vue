<template>
    <div>
    <b-card>
        <div class="row">
            <div class="col-6">
                <b-card-title>Ocean Freight</b-card-title>
            </div>
            <div class="col-6">
                <div class="float-right">
                    <button class="btn btn-link" v-b-modal.addOFreight>+ Add Freight</button>
                    <button class="btn btn-primary btn-bg" v-click="link">+ Import Contract</button>
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
                    <b-th>
                        <b-form-checkbox
                             v-model="allSelected"
                             :indeterminate="false"
                             >
                        </b-form-checkbox>
                    </b-th>

                    <b-th v-for="(value, key) in efields" :key="key">
                        {{value}}
                    </b-th>

                    <b-th>
                        <b-button v-bind:id="'popover_all'" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
                        <b-popover v-bind:target="'popover_all'" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                            <button class="btn-action" v-on:click="onDeleteAll()">Delete</button>
                        </b-popover>
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
                             v-model="origin"
                             :options="datalists.harbors" 
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
                             v-model="destination" 
                             :options="datalists.harbors" 
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
                                      placeholder="-"
                                      v-model="rates[val]" 
                                      >
                        </b-form-input>
                    </b-td>
                    <b-td>
                        <multiselect 
                             v-model="carrier" 
                             :options="datalists.carriers" 
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
                             :options="datalists.currencies" 
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
                                             v-bind:value="data[key]"
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
                            <button class="btn-action" v-on:click="onEdit(value)">Edit</button>
                            <button class="btn-action" v-on:click="onDuplicate(value.id)">Duplicate</button>
                            <button class="btn-action" v-on:click="onDelete(value.id)">Delete</button>
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

    <!-- Edit Form -->
    <b-modal id="editOFreight" size="lg" cancel-title="Cancel" ok-title="Add Contract" hide-header-close title="Update Ocean Freight" hide-footer>
        <FormView 
            :data="currentData" 
            :fields="fields"
            :vdatalists="datalists"
            btnTxt="Update Ocean Freight"
            @exit="closeModal('editOFreight')"
            @success="closeModal('editOFreight')"
            :actions="actions"
            :update="true"
            >
        </FormView>
    </b-modal>
    <!-- End Edit Form -->

    <!-- Create Form -->
    <b-modal id="addOFreight" size="lg" hide-header-close title="Add Ocean Freight" hide-footer>
        <FormView 
            :data="{}" 
            :fields="fields"
            :vdatalists="datalists"
            btnTxt="Add Ocean Freight"
            @exit="closeModal('addOFreight')"
            @success="closeModal('addOFreight')"
            :actions="actions"
            >
        </FormView>
    </b-modal>
    <!-- End Create Form -->

    </div>
</template>


<script>
    import Multiselect from 'vue-multiselect';
    import paginate from '../paginate';
    import FormView from '../views/FormView.vue';
    import actions from '../../actions';

    export default {
        props: {
            equipment: Object,
            datalists: Object,
            actions: Object
        },
        components: { 
            Multiselect,
            paginate,
            FormView
        },
        data() {
            return {
                efields: [],
                isBusy:true, // Loader
                booleano: false,
                data: null,
                e_startfields: ['Origin Port', 'Destination Port'],
                e_endfields: ['Carrier', 'Currency'],
                e_fields: [],
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
                currentData: {},
                fields: {},
                vfields: {
                    origin: { label: 'Origin Port', searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select Origin Port', options: 'harbors' },
                    destination: { label: 'Destination Port', searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select Destination Port', options: 'harbors' },
                    carrier: { label: 'Carrier', searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select Carrier Port', options: 'carriers' },
                    currency: { label: 'Origin Port', searchable: true, type: 'select', rules: 'required', trackby: 'alphacode', placeholder: 'Select Currency Port', options: 'currencies' }
                },
            }
        },
        created() {
            this.contract_id = this.$route.params.id;

            this.initialData();
            this.setContainersColumns(this.equipment);

        },
        methods: {
            
            link(){
                 window.location = '/RequestFcl/NewRqFcl';
            },

            /* Response the Rates lists data*/
            initialData(){
                let params = this.$route.query;

                if(params.page) this.initialPage = Number(params.page);

                this.getData(params);
            },

            /* Response the Rates lists data*/
            getData(params = {}){

                this.actions.list(params, (err, data) => {
                    this.setData(err, data);
                }, this.$route);

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
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editOFreight');
            },
            onDelete(id){
              
                this.isBusy = true;

                this.actions.delete(id)
                    .then( ( response ) => {
                        this.refreshData();
                    })
                        .catch(( data ) => {
                    });
            },
            onDeleteAll(id){
              
                this.isBusy = true;

                let ids = this.selected.map(item => item.id);

                this.actions.deleteAll(ids)
                    .then( ( response ) => {
                        this.refreshData();
                    })
                        .catch(( data ) => {
                    });
            },
            onDuplicate(id){

                this.isBusy = true;
                
                this.actions.duplicate(id, {})
                    .then( ( response ) => {
                    this.refreshData();
                })
                    .catch(( data ) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
            },
            /* End single actions */

            resetValues(){

                this.efields = [];
                this.booleano = false;
                this.container_fields = [];
                this.fields = Object.assign({}, this.vfields);
            },

            /* Set Container Columns and fields by equipment */
            setContainersColumns(val){
                let ofcomponent = this;
                this.resetValues();

                this.e_startfields.forEach(item => ofcomponent.efields.push(item));

                this.datalists.containers.forEach(function(item){
                    if(item.gp_container_id === val.id)
                    {
                        ofcomponent.efields.push(item.name);
                        ofcomponent.container_fields.push('rates_'+item.code);
                        ofcomponent.fields['rates_'+item.code] = { type: 'text', label: item.name, placeholder: item.name };
                    }
                });

                this.e_endfields.forEach(item => ofcomponent.efields.push(item));

                this.booleano = true;
            },
            closeModal(modal){
                this.$bvModal.hide(modal);
            }
        },
        watch: {
            equipment: function(val, oldVal) {
                console.log('equipment', val);
                this.setContainersColumns(val)
            }
        }
    }
</script>
