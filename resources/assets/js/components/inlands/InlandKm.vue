<template>
    <b-card>
        <div class="row">
            <div class="col-6">
                <b-card-title>Inland Per Km</b-card-title>
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
        <!--  <p> {{ selected }} </p>  -->

        <b-button id="popover-all" class="action-app all-action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></b-button>
        <b-popover target="popover-all" class="btns-action" variant="" triggers="focus" placement="bottomleft">
            <button class="btn-action">Edit</button>
            <button class="btn-action">Duplicate</button>
            <button class="btn-action">Delete</button>
        </b-popover>

       <!-- Table -->
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
    </b-card>
</template>


<script>
    export default {
        props: {
            equipment: Object,
            containers: Object
        },
        components: { 

        },
        data() {
            return {
                isBusy:true, // Loader
                data: null,
                fields: [],
                fields: [
                    { key: 'checkbox', label: '', tdClass: 'checkbox-add-fcl', isHtml: true},
                    { key: '20dv', label: '20 DV', sortable: false },
                    { key: '40dv', label: '40 DV', sortable: false },
                    { key: '40hc', label: '40 HC', sortable: false },
                    { key: '40nor', label: '40 NOR', sortable: false },
                    { key: '45hc', label: '45 HC', sortable: false },
                    { key: 'currency', label: 'Currency', sortable: false,
                     formatter: value => { return value.alphacode; }
                    },
                    { key: 'actions', label: '', tdClass: 'actions-add-fcl'}
                ],

            }
        },
        created() {
            const contract_id = this.$route.params.id;
            api.getData({}, '/api/v2/contracts/'+contract_id+'/', (err, data) => {
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
        },
        watch: {
            equipment: function(val, oldVal) {
                let data = this;
                this.fields = [];
                this.start_fields.forEach(item => data.fields.push(item));
                this.containers.forEach(function(item){
                    if(item.gp_container_id === val.id)
                    {
                        data.fields.push( { key: item.code, label: item.name, sortable: false } );
                    }
                });
                this.end_fields.forEach(item => data.fields.push(item));
            }
        }
    }
</script>