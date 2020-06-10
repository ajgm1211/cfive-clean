<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Transit Times FCL</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-link" v-b-modal.addTransitTime>+ Add new</button>
                                <a href="/RequestFcl/NewRqFcl" class="btn btn-primary btn-bg" >+ Import file</a>
                            </div>
                        </div>
                    </div>


                    <DataTable 
                        v-if="loaded"
                        :fields="fields"
                        :inputFields="form_fields"
                        :vdatalists="datalists"
                        :actions="actions.transit_time"
                        @onEdit="onEdit"
                        >
                    </DataTable>

                </b-card>
            </div>
        </div>
        
        <!-- Create Form -->
        <b-modal id="addTransitTime" size="lg" hide-header-close title="Add Contract" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Transit Time"
                @exit="closeModal('addTransitTime')"
                @success="success('addTransitTime')"
                :actions="actions.transit_time"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

        <!-- Edit Form -->
        <b-modal id="editTransitTime" size="lg" cancel-title="Cancel" hide-header-close title="Update Ocean Freight" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Update Transit Time"
                @exit="closeModal('editTransitTime')"
                @success="success('editTransitTime')"
                :actions="actions.transit_time"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

    </div>

</template>


<script>
    import DataTable from '../../components/DataTable';
    import actions from '../../actions';
    import FormView from '../../components/views/FormView.vue';

    export default {
        components: { 
            DataTable,
            FormView
        },
        data() {
            return {
                actions: actions,
                currentData: {},
                loaded: false,
                
                // Dropdown Lists
                datalists: {
                  'carriers': [],
                  'harbors': [],
                  'services': []
                },

                /* Table headers */
                fields: [
                    { key: 'origin', label: 'Origin Port', formatter: (value)=> { return value.display_name } }, 
                    { key: 'destination', label: 'Destination Port', formatter: (value)=> { return value.display_name } }, 
                    { key: 'carrier', label: 'Carrier', formatter: (value)=> { return value.name } }, 
                    { key: 'transit_time', label: 'Transit Time' },
                    { key: 'service', label: 'Service', formatter: (value)=> { return value.name } },  
                    { key: 'via', label: 'Via' }, 
                ],

                /* Form Modal Fields */
                form_fields: {
                    origin: { 
                        label: 'Origin Port', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select Origin Port', 
                        options: 'harbors' 
                    },
                    destination: { 
                        label: 'Destination Port', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select Destination Port', 
                        options: 'harbors' 
                    },
                    carrier: { 
                        label: 'Carrier', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select Carrier', 
                        options: 'carriers' 
                    },
                    transit_time: {
                        label: 'Transit Time', 
                        type: 'text', 
                        rules: 'required',
                        placeholder: 'Add Transit Time'
                    },
                    service: { 
                        label: 'Service', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select Service', 
                        options: 'services' 
                    },
                    via: {
                        label: 'Via', 
                        type: 'text', 
                        placeholder: 'Add Via'
                    }
                }
            }
        },
        created() {

            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/transit_time/data', (err, data) => {
                this.setDropdownLists(err, data.data);
            });

        },
        methods: {
            /* Set the Dropdown lists to use in form */
            setDropdownLists(err, data){
                this.datalists = data;
                this.loaded = true;
            },

            closeModal(modal){
                this.$bvModal.hide(modal);
                this.currentData = {};
            },

            success(modal){
                this.closeModal(modal);
                let component = this;

                component.loaded = false;
                setTimeout(function(){ component.loaded = true; }, 100);

                this.currentData = {};
            },

            /* Single Actions */
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editTransitTime');
            },

            badgecarriers(value){

                let carriers = "";

                if(value){
                    value.forEach(function(val){
                        carriers += "<span class='badge badge-primary'>"+val.name+"</span> ";
                    });

                    return carriers;
                } else {
                    return '-';
                }

            },
            
        }
    }
</script>
