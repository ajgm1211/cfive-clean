<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>FCL Contracts</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-link" v-b-modal.addContract>+ Add Contract</button>
                                <a href="/RequestFcl/NewRqFcl" class="btn btn-primary btn-bg" >+ Import Contracts</a>
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
        <b-modal id="addContract" size="md" hide-header-close title="Add Contract" hide-footer>
            <FormView 
                :data="fdata" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Contract"
                @exit="closeModal('addContract')"
                @success="success"
                :actions="actions.contracts"
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
                isBusy:true, // Loader
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
                    { key: 'name', label: 'Reference' }, 
                    { key: 'status', label: 'Status', formatter: value => { return `<span class="status-st ${value}"></span>` } },
                    { key: 'validity', label: 'Valid From' }, 
                    { key: 'expire', label: 'Valid Until' }, 
                    { key: 'carriers', label: 'Carrier', formatter: (value)=> { return this.badgecarriers(value) } }, 
                    { key: 'gp_container', label: 'Equipment', formatter: (value)=> { return value.name } }, 
                    { key: 'direction', label: 'Direction', formatter: (value)=> { return value.name } }, 
                ],

                /* Form Modal Fields */
                form_fields: {
                    name: { 
                        label: 'Reference', 
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
                    carriers: { 
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
                this.datalists = {
                  'carriers': data.carriers,
                  'equipments': data.equipments,
                  'directions': data.directions,
                  'containers': data.containers,
                }
            },

            link(){
                 window.location = '/RequestFcl/NewRqFcl';
            },

            closeModal(modal){
                this.$bvModal.hide(modal);
            },

            success(id){
                window.location = `/api/contracts/${id}/edit`;
            },

            /* Single Actions */
            onEdit(data){
                window.location = `/api/contracts/${data.id}/edit`;
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
