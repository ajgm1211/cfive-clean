<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <HelpDropdown
					:options="helpOptions"
				></HelpDropdown>
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Inlands</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-primary btn-bg" v-b-modal.addInland>+ Add Inland</button>
                            </div>
                        </div>
                    </div>

                    <DataTable
                        :fields="fields"
                        :actions="actions.inlands"
                        @onEdit="onEdit"
                        :totalResults="totalResults"
                        ></DataTable>
                </b-card>
            </div>
        </div>
        
        <!-- Create Form -->
        <b-modal id="addInland" size="md" hide-header-close title="Add Inland" hide-footer>
            <FormView 
                :data="fdata" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Inland"
                @exit="closeModal('addInland')"
                @success="success"
                :actions="actions.inlands"
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
    import HelpDropdown from "../../components/HelpDropdown";
    export default {
        components: { 
            DataTable,
            FormView,
            HelpDropdown
        },
        data() {
            return {
                totalResults: true,
                actions: actions,
                fdata: { validity: { startDate: null, endDate: null } },
                datalists: {},
                /* Table headers */
                fields: [
                    { key: "reference", label: "Reference" },
                    { key: 'status', label: 'Status', formatter: value => { return `<span class="status-st ${value}"></span>` } },
                    { key: 'type', label: 'Type', formatter: (value)=> { return value.name } },
                    { key: 'gp_container', label: 'Equipment', formatter: (value)=> { return value.name } }, 
                    { key: 'validity', label: 'Valid From' }, 
                    { key: 'expire', label: 'Valid Until' }, 
                    { key: 'direction', label: 'Direction', formatter: (value)=> { return value.name } },
                    { key: 'providers', label: 'Provider', formatter: (value)=> { return value.name } },
                ],
                /* Form Modal Fields */
                form_fields: {
                    reference: { 
                        label: 'Reference', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Reference', 
                        colClass: 'col-sm-12' 
                    },
                    type: { 
                        label: 'Type', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'types' 
                    },
                    ports: { 
                        label: 'Ports', 
                        searchable: true, 
                        type: 'multiselect', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select options', 
                        options: 'harbors',
                        initial: []
                    },
                    gp_container: { 
                        label: 'Equipment', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'equipments' 
                    },
                    validity: { 
                        label: 'Validity', 
                        rules: 'required', 
                        type: "daterange", 
                        sdName: 'validity', 
                        edName: 'expire'
                    },
                    direction: { 
                        label:'Direction', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'directions' 
                    },
                    providers: { 
                        label:'Provider', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'providers' 
                    },
                },

                //HELP DROPDOWN
                helpOptions: [
                    {
                        title: "How to create an inland contract",
                        link: "https://support.cargofive.com/how-to-create-an-inland-contract/"
                    },
                ]
            }
        },
        created() {
            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/inland/data', (err, data) => {
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
                window.location = `/api/inlands/${id}/edit`;
            },
            /* Single Actions */
            onEdit(data){
                window.location = `/api/inlands/${data.id}/edit`;
            },
        }
    }
</script>