<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Sale Templates FCL</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-primary btn-bg" v-b-modal.addSaleterm>+ Add New</button>
                            </div>
                        </div>
                    </div>

                    <DataTable
                        :fields="fields"
                        :actions="actions.sale_terms"
                        @onEdit="onEdit"
                        ></DataTable>
                </b-card>
            </div>
        </div>
        
        <!-- Create Form -->
        <b-modal id="addSaleterm" size="md" hide-header-close title="Add Sale Template" hide-footer>
            <FormView 
                :data="fdata" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Save"
                @exit="closeModal('addSaleterm')"
                @success="success"
                :actions="actions.sale_terms"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

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
                fdata: { validity: { startDate: null, endDate: null } },
                datalists: {},

                /* Table headers */
                fields: [
                    { key: "name", label: "Name" },
                    { key: 'port', label: 'Port', formatter: (value)=> { return value.name } },
                    { key: 'type', label: 'Type', formatter: (value)=> { return value.name } },
                    { key: 'group_container', label: 'Equipment', formatter: (value)=> { return value.name } }, 
                ],

                /* Form Modal Fields */
                form_fields: {
                    name: { 
                        label: 'Name', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Name', 
                        colClass: 'col-sm-12' 
                    },
                    type_id: { 
                        label: 'Type', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'types' 
                    },
                    port_id: { 
                        label: 'Port', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select option', 
                        options: 'harbors',
                        initial: []
                    },
                    group_container_id: { 
	                    label: 'Equipment', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select option', 
	                    options: 'equipments',
	                },
                }
            }
        },
        created() {

            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/sale_terms/data', (err, data) => {
                this.setDropdownLists(err, data.data);
            });

        },
        methods: {
            /* Set the Dropdown lists to use in form */
            setDropdownLists(err, data){
                this.datalists = data;
            },

            closeModal(modal){
                this.$bvModal.hide(modal);
            },

            success(id){
                // After Create the item redirect to:
                window.location = `/api/sale_terms/${id}/edit`;
            },

            /* Single Actions */
            onEdit(data){
                // Single actions to redirect to:
                window.location = `/api/sale_terms/${data.id}/edit`;
            },
            
        }
    }
</script>
