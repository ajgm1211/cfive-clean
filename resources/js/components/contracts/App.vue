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
                                <button class="btn btn-link" v-b-modal.exportExcel><i class="fa fa-download"></i> Export Excel</button>
                                <button class="btn btn-link" v-b-modal.addContract><i class="fa fa-plus"></i> Add Contract</button>
                                <a href="/RequestFcl/NewRqFcl" class="btn btn-primary btn-bg" ><i class="fa fa-upload"></i> Import Contract</a>
                            </div> 
                        </div>
                    </div>

                    <DataTable 
                        :fields="fields"
                        :actions="actions.contracts"
                        :filter="true"
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

                
        <!-- Create Form -->
        <b-modal id="exportExcel" size="md" hide-header-close title="Export Contract" hide-footer>
            <FormView 
                :data="fdata2" 
                :fields="form_fields_excel"
                :vdatalists="datalists"
                :download=true
                btnTxt="Export Excel"
                @exit="closeModal('exportExcel')"
                @success="closeModal('exportExcel')"
                :actions="actions.excel"
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
                fdata2: { validity: { startDate: null, endDate: null } },
                
                // Dropdown Lists
                datalists: {
                  'carriers': [],
                  'equipments': [],
                  'directions': [],
                  'containers': []
                },

                /* Table headers */
                fields: [
                    { key: 'name', label: 'Reference', formatter: value => { return `<p class="truncate-contract" title="${value}">${value}</p>` }, filterIsOpen:true }, 
                    { key: 'carriers', label: 'Carrier', formatter: (value)=> { return this.badgecarriers(value) }, filterIsOpen:true, filterTrackBy: "name", trackLabel: "name"},
                    { key: 'status', label: 'Status', formatter: value => { return `<span class="status-st ${value}"></span>` }, filterIsOpen:true },
                    { key: 'validity', label: 'Valid From', filterIsOpen:true }, 
                    { key: 'expire', label: 'Valid Until', filterIsOpen:true }, 
                    { key: 'gp_container', label: 'Equipment', formatter: (value)=> { return value.name }, filterIsOpen:true, filterTrackBy: "name", trackLabel: "name"}, 
                    { key: 'direction', label: 'Direction', formatter: (value)=> { return value.name }, filterIsOpen:true, filterTrackBy: "name", trackLabel: "name"},
                    { key: 'created_at', label: 'Created At', filterIsOpen:true},

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
                },
                form_fields_excel: {
            
                    validity: { 
                        label: 'Validity', 
                        rules: 'required', 
                        type:"daterange", 
                        sdName: 'validity', 
                        edName: 'expire'
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
