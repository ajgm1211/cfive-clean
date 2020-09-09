<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Providers</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-primary btn-bg" v-b-modal.addproviders>+ Add Providers</button>
                            </div>
                        </div>
                    </div>

                    <DataTable
                        :fields="fields"
                        :actions="actions.providers"
                        @onEdit="onEdit"
                        ></DataTable>
                </b-card>
            </div>
        </div>
        
        <!-- Create Form -->
        <b-modal id="addproviders" size="md" hide-header-close title="Add Providers" hide-footer>
            <FormView 
                :data="fdata" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Save"
                @exit="closeModal('addproviders')"
                @success="success"
                :actions="actions.providers"
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
                    { key: 'name', label: 'Name'  },
                    { key: 'description', label: 'Description' },
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
                    description: { 
                        label: 'Description',  
                        type: 'text', 
                        rules: 'required',
                        placeholder: 'Description',
                        colClass: 'col-sm-12' 
                    },
                }
            }
        },
        created() {
            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/providers/data', (err, data) => {
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
                //window.location = `/api/providers/${id}/edit`;
            },
            /* Single Actions */
            onEdit(data){
                // Single actions to redirect to:
                //window.location = `/api/providers/${data.id}/edit`;
            },
            
        }
    }
</script>