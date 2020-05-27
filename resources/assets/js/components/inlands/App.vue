<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Inlands</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-link" v-b-modal.addInland>+ Add Inland</button>
                                <!--<a href="/RequestFcl/NewRqFcl" class="btn btn-primary btn-bg" >+ Import Contracts</a>-->
                            </div>
                        </div>
                    </div>

                    <DataTable 
                        :fields="fields"
                        :actions="actions.contracts"
                        @onEdit="onEdit"
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
                :actions="actions.inland"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

    </div>

</template>


<script>
    import DataTable from '../DataTable';
    import actions from '../../actions';
    import FormView from '../views/FormView.vue';

    export default {
        components: { 
            DataTable,
            FormView
        },
        data() {
            return {
                actions: actions,
                fdata: { validity: { startDate: null, endDate: null } },
                
                // Dropdown Lists
                datalists: {
                  'carriers': [],
                  'equipments': [],
                  'directions': [],
                  'containers': []
                },

                /* Table headers */
                fields: [
                    { key: "provider", label: "Provider" },
                    { key: "port", label: "Ports", formatter: (value)=> { return this.badges(value, 'warning') }},
                    { key: 'status', label: 'Status', formatter: value => { return `<span class="status-st ${value}"></span>` } },
                    { key: 'gp_container', label: 'Equipment', formatter: (value)=> { return value.name } }, 
                    { key: 'validity', label: 'Valid From' }, 
                    { key: 'expire', label: 'Valid Until' }, 
                    { key: 'type', label: 'Direction', formatter: (value)=> { return '<span class="badge badge-primary">'+ this.badgetypes(value) +'</span>'; } },
                    { key: "restrictions", label: "Company Restrictions" }, 
                ],

                /* Form Modal Fields */
                form_fields: {
                    provider: { 
                        label: 'Provider', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Reference', 
                        colClass: 'col-sm-12' 
                    },
                    validity: { 
                        label: 'Validity', 
                        rules: 'required', 
                        type:"daterange", 
                        sdName: 'validity', 
                        edName: 'expire'
                    },
                    direction: { 
                        label:'Carriers', 
                        searchable: true, 
                        type: 'multiselect', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select options', 
                        options: 'carriers' 
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
                    direction: { 
                        label:'Direction', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'directions' 
                    },
                }
            }
        },
        created() {

            /* Return the lists data for dropdowns */
            api.getData({}, '/api/v2/contracts/data', (err, data) => {
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
                window.location = `/api/contracts/${id}/edit`;
            },

            /* Single Actions */
            onEdit(data){
                // Single actions to redirect to:
                window.location = `/api/contracts/${data.id}/edit`;
            },

            /* Badge Types */
            badgetypes(value) {
                let variation = "";

                if (value == '1') {
                    variation += 'Import';
                    return variation;
                } else if (value == '2') {
                    variation += 'Export';
                    return variation;
                } else {
                    variation += "Both";
                    return variation;
                }
            },

            badges(value, color='primary'){
                let carriers = "";

                if(value){
                    value.forEach(function(val){
                        carriers += `<span class='badge badge-${color}'>${val.name}</span>`;
                    });

                    return carriers;
                } else {
                    return '-';
                }

            }
            
        }
    }
</script>
