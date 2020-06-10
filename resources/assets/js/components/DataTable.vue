<template>
    <div>
        <!-- Search Input -->
        <div class="row my-3">
            <div class="col-12 col-sm-4">
                <b-form inline>
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <b-input
                         id="inline-form-input-name"
                         class="mb-2 mr-sm-2 mb-sm-0"
                         v-model="search"
                         placeholder="Search"
                         ></b-input>
                </b-form>
            </div>
        </div>
        <!-- End Search Input -->
 
        <!-- DataTable -->
        <b-table-simple hover small responsive borderless>
            
            <!-- Header table -->
            <b-thead>
                <b-tr>
                    <b-th>
                        <b-form-checkbox
                             v-model="allSelected"
                             :indeterminate="false"
                             >
                        </b-form-checkbox>
                    </b-th>

                    <b-th v-for="(value, key) in fields" :key="key">
                        {{value.label}}
                    </b-th>

                    <b-th>
                        <b-button v-bind:id="'popover_all'" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
                        <b-popover v-bind:target="'popover_all'" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                            <button v-if="massiveactions.includes('delete')" class="btn-action" v-on:click="onDeleteAll()">Delete</button>
                            <button v-if="massiveactions.includes('changecontainersview')" class="btn-action" v-on:click="onChangeContainersView()">Change View Container</button>
                        </b-popover>
                    </b-th>
                </b-tr>
            </b-thead>

            <!-- Loader gif -->
            <b-tbody v-if="isBusy">
                <b-tr class="b-table-busy-slot">
                    <b-td :colspan="fields.length" role="cell" class="">
                        <div class="text-center text-primary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Loading...</strong>
                        </div>
                    </b-td>
                </b-tr>
            </b-tbody>
            <!-- Loader gif -->

            <!-- Body table -->
            <b-tbody v-if="!isBusy">

                <!-- Form add new item -->
                <b-tr v-if="!isEmpty(inputFields)">

                    <b-td v-if="firstEmpty"></b-td>

                    <b-td v-for="(item, key) in inputFields" :key="key" :style="'max-width:'+item.width">
                       
                       <!-- Text Input -->
                       <div v-if="item.type == 'text'">
                            <b-form-input
                                v-model="fdata[key]"
                                :placeholder="item.placeholder" 
                                    >
                            </b-form-input>
                        </div>
                        <!-- End Text Input -->

                        <!-- Based Dinamical Select Input -->
                        <div v-if="item.type == 'pre_select' && refresh">
                            <multiselect 
                                 v-model="fdata[key]"
                                 :id="key"
                                 :multiple="false" 
                                 :options="datalists[item.options]" 
                                 :searchable="item.searchable"
                                 :close-on-select="true"
                                 :clear-on-select="false"
                                 track-by="id" 
                                 :label="item.trackby" 
                                 :show-labels="false"
                                 :placeholder="item.placeholder"
                                 @select="dispatch">
                            </multiselect>
                        </div>
                        <!-- Based Dinamycal Select -->

                        <!-- Select Input -->
                        <div v-if="item.type == 'select'">
                            <multiselect 
                                 v-model="fdata[key]"
                                 :id="key"
                                 :multiple="false" 
                                 :options="datalists[item.options]" 
                                 :searchable="item.searchable"
                                 :close-on-select="true"
                                 :clear-on-select="false"
                                 track-by="id" 
                                 :label="item.trackby" 
                                 :show-labels="false"
                                 :placeholder="item.placeholder"
                                 >
                            </multiselect>
                        </div>
                        <!-- End Select -->

                        <!-- MultiSelect Input -->
                        <div v-if="item.type == 'multiselect' && refresh">
                            <multiselect 
                                 v-model="fdata[key]" 
                                 :multiple="true" 
                                 :options="datalists[item.options]" 
                                 :searchable="item.searchable"
                                 :close-on-select="true"
                                 :clear-on-select="true"
                                 track-by="id" 
                                 :label="item.trackby" 
                                 :show-labels="false"
                                 :placeholder="item.placeholder"
                                 @input="refreshValues">
                            </multiselect>
                        </div>
                        <!-- End Select -->

                    </b-td>

                    <b-td>
                        <b-button class="action-app" href="#" tabindex="0" v-on:click="onSubmit()"><i class="fa fa-check" aria-hidden="true"></i></b-button>
                    </b-td>
                    
                </b-tr>
                <!-- End of form -->

                <!-- Data List -->
                <b-tr v-for="(item, key) in data" :key="key">      

                    <!-- Checkbox column -->              
                    <b-td>
                       <b-form-checkbox-group >
                            <b-form-checkbox 
                                v-bind:value="item"
                                v-bind:id="'check'+item.id"
                                v-model="selected"
                            >
                            </b-form-checkbox>
                        </b-form-checkbox-group>
                    </b-td>
                    <!-- end Checkbox column -->

                    <!-- Fields data -->
                    <b-td v-for="(col, key) in fields" :key="key">
                       <span v-if="'formatter' in col" v-html="col.formatter(item[col.key])"></span>
                       <span v-else>{{item[col.key]}}</span>
                    </b-td>
                    <!-- End Fields Data -->

                    <!-- Actions column -->
                    <b-td>
                        <b-button v-bind:id="'popover'+item.id" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
                        <b-popover v-bind:target="'popover'+item.id" class="btns-action" variant="" triggers="focus" placement="bottomleft">
                            <button class="btn-action" v-on:click="onEdit(item)">Edit</button>
                            <button class="btn-action" v-on:click="onDuplicate(item.id)">Duplicate</button>
                            <button class="btn-action" v-on:click="onDelete(item.id)">Delete</button>
                        </b-popover>
                    </b-td>
                    <!-- End Actions column -->


                </b-tr>
                <!-- End Data list -->

            </b-tbody>
            <!-- Body table -->

        </b-table-simple>
        <!-- End DataTable -->
        
        <!-- Pagination -->
        <paginate
                  :page-count="pageCount"
                  :click-handler="clickCallback"
                  :prev-text="'Prev'"
                  :next-text="'Next'"
                  :page-class="'page-item'"
                  :page-link-class="'page-link'"
                  :container-class="'pagination'"
                  :prev-class="'page-item'"
                  :prev-link-class="'page-link'"
                  :next-class="'page-item'"
                  :next-link-class="'page-link'"
                  :initialPage="initialPage">
        </paginate>
        <!-- Pagination end -->
    </div>
</template>


<script>
    import Multiselect from 'vue-multiselect';
    import paginate from './paginate';

    export default {
        props: {
            fields: Array,
            inputFields: {
                type: Object,
                required: false,
                default: () => { return {} }
            },
            vdatalists: {
                type: Object,
                required: false,
                default: () => { return {} }
            },
            massiveactions: {
                type: Array,
                required: false,
                default: () => { return ['delete'] }
            },
            actions: Object,
            firstEmpty: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        components: { 
            Multiselect,
            paginate,
        },
        data() {
            return {
                isBusy: false,
                data: {},
                fdata: {},
                currentData: [],
                refresh: true,
                datalists: {},
                search: null,

                /* Pagination */
                initialPage: 1,
                pageCount: 0,

                /* Checkboxes */
                selected: [],
                allSelected: false,
                indeterminate: false,
            }
        },
        created() {
            this.initialData();
            this.updateDinamicalFieldOptions();

        },
        methods: {
            /* Response the lists data*/
            initialData(){
                let params = this.$route.query;

                if(params.page) this.initialPage = Number(params.page);

                this.getData(params);

                /* Set initial form data */
                for (const key in this.inputFields) {
                    if('initial' in this.inputFields[key])
                        this.fdata[key] = this.inputFields[key]['initial'];
                }
            },

            /* Request the data with axios */
            getData(params = {}){

                this.actions.list(params, (err, data) => {
                    this.setData(err, data);
                }, this.$route);

            },

            /* Set the data into datatable */
            setData(err, { data: records, links, meta }) {
                this.isBusy = false;

                if (err) {
                    this.error = err.toString();
                } else {
                    this.data = records;
                    this.pageCount = Math.ceil(meta.total/meta.per_page);
                }
            },

            /* Refresh Data */
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

            /* Prepare data to submit */
            prepareData(){

                let data = {};
                
                for (const key in this.inputFields) {

                    if(this.inputFields[key].type == "text")
                        data[key] = this.fdata[key];
                    else if(["select", "pre_select"].includes(this.inputFields[key].type))
                        data[key] = this.fdata[key].id;
                    else if(this.inputFields[key].type == "multiselect"){
                        data[key] = [];

                        this.fdata[key].forEach(function(item){
                            data[key].push(item.id)
                        });
                    }
                }

                return data;
            },

            /* Clear Form Data */
            clearForm(){
                this.fdata = {};
            },

            /* Set all the checkbox */
            toggleAll(checked) {
                this.selected = checked ? this.data.slice() : [] //Selected all the checkbox
            },

            /* Submit form new data */
            onSubmit() {
                
                let data = this.prepareData();

                //this.isBusy = true;

                this.actions.create(data, this.$route)
                    .then( ( response ) => {
                        this.clearForm();
                        this.refreshData();
                        this.updateDinamicalFieldOptions();
                })
                    .catch(( data ) => {
                });

            },

            /* Single Actions */
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editModal');
                this.$emit('onEdit', data);
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
            onDeleteAll(){
              
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

            closeModal(modal){
                this.$bvModal.hide(modal);
            },

            resetDinamicalFields(target){

                    for (const key in this.inputFields) {
                        if(this.inputFields[key]['options'] == target)
                            this.fdata[key] = [];
                    }
            },

            dispatch(val, item){
                this.refresh = false;
                this.datalists[this.inputFields[item].target] = this.datalists[val.vselected];
                this.resetDinamicalFields(this.inputFields[item].target);
                this.refresh = true;
            },
        
            refreshValues(val, item){
                let component = this;
                component.refresh = false;
                setTimeout(function(){ component.refresh = true; }, 0.4);
            },
            
            updateDinamicalFieldOptions(){

                this.datalists = JSON.parse(JSON.stringify(this.vdatalists));

                for (const key in this.inputFields) {
                    if(this.inputFields[key]['type'] == 'pre_select')
                        this.datalists[this.inputFields[key]['target']] = this.datalists[this.inputFields[key]['initial'].vselected];
                }
            },
            isEmpty(obj){
                for(var key in obj) {
                    if(obj.hasOwnProperty(key))
                        return false;
                }
                return true;
            },
            onChangeContainersView(){
                this.$emit('onChangeContainersView', true);
            }
        },
        watch: {
            vdatalists: {
                handler(val, oldval){
                    this.updateDinamicalFieldOptions();
                },
                deep: true
            },
            selected() {
                this.$emit('input', this.selected);
            },
            search: {
                handler: function (val, oldVal) {
                    let qs = { q: val };

                    this.routerPush(qs);
                }
            }
        }
    }
</script>
