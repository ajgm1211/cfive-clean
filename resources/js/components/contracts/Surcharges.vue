<template>
    <div>
        <b-card>
            <div class="row">
                <div class="col-6">
                    <b-card-title>Surcharges</b-card-title>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <!--<button class="btn btn-link" v-b-modal.addSurcharge>+ Add Surcharge</button>-->
                        <button class="btn btn-primary btn-bg btn-adds" v-b-modal.addSurcharge>+ Add Surcharge</button>
                    </div>
                </div>
            </div>

            <DataTable 
                v-if="loaded"
                :fields="fields"
                :inputFields="input_fields"
                :vdatalists="fdatalists"
                :actions="actions"
                @onEdit="onEdit"
                :firstEmpty="false"
                :view="'surcharges'"
                :classTable="classTable"
                ></DataTable>


        </b-card>

        <!-- Edit Form -->
        <b-modal id="editSurcharge" size="lg" cancel-title="Cancel" ok-title="Add Contract" hide-header-close title="Update Ocean Freight" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="input_fields"
                :vdatalists="fdatalists"
                btnTxt="Update Surcharge"
                @exit="closeModal('editSurcharge')"
                @success="closeModal('editSurcharge')"
                :actions="actions"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

        <!-- Create Form -->
        <b-modal id="addSurcharge" size="lg" hide-header-close title="Add Ocean Freight" hide-footer>
            <FormView 
                :data="{ typeofroute: { id: 'port', name: 'Port', vselected: 'harbors' } }" 
                :fields="input_fields"
                :vdatalists="fdatalists"
                btnTxt="Add Surcharge"
                @exit="closeModal('addSurcharge')"
                @success="closeModal('addSurcharge')"
                :actions="actions"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->
    </div>
</template>


<script>
    import DataTable from '../DataTable';
    import FormView from '../views/FormView';

    export default {
        components: { 
            DataTable,
            FormView
        },
        props: {
            datalists: Object,
            actions: Object,
            contractData: Object,
            classTable: String
        },
        data() {
            return {
                loaded: false,
                isBusy:true, // Loader
                data: null,
                currentData: {},

                /* Table headers */
                fields: [
                    { key: 'surcharge', label: 'Type', formatter: (value)=> { return value.name } }, 
                    { key: 'origin', label: 'Origin', formatter: (value)=> { return this.badges(value, 'warning') } }, 
                    { key: 'destination', label: 'Destination', formatter: (value)=> { return this.badges(value, 'warning') } }, 
                    { key: 'destination_type', label: 'Change Type', formatter: (value)=> { return value.description } }, 
                    { key: 'carriers', label: 'Carrier', formatter: (value)=> { return this.badgescarriers(value) } }, 
                    { key: 'calculation_type', label: 'Calculation Type', formatter: (value)=> { return value.name } }, 
                    { key: 'amount', label: 'Amount' }, 
                    { key: 'currency', label: 'Currency', formatter: (value)=> { return value.alphacode } },
                ],

                /* Table input inline fields */
                input_fields: {
                    typeofroute: { label: 'Type of route', searchable: true, type: 'pre_select', rules: 'required', trackby: 'name', placeholder: '', options: 'route_types', initial: { id: 'port', name: 'Port', vselected: 'harbors' }, target: 'dynamical_ports' },
                    surcharge: { label: 'Surcharge', searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select option', options: 'surcharges' },
                    origin: { label: 'Origin', searchable: true, type: 'multiselect', rules: 'required', trackby: 'display_name', placeholder: 'Select options', options: 'ori_dynamical_ports', initial: [] },
                    destination: { label: 'Destination', searchable: true, type: 'multiselect', rules: 'required', trackby: 'display_name', placeholder: 'Select options', options: 'des_dynamical_ports', initial: [] },
                    destination_type: { label: 'Destination Type', searchable: true, type: 'select', rules: 'required', trackby: 'description', placeholder: 'Select option', options: 'destination_types' },
                    carriers: { label: 'Carriers', searchable: true, type: 'multiselect_data', rules: 'required', trackby: 'name', placeholder: 'Select options', options: 'carriers',values: this.contractData.carriers, },
                    calculation_type: { label: 'Calculation type', searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select option', options: 'calculation_types' },
                    amount: { label: 'Amount', type: 'text', rules: 'required', placeholder: 'Amount' },
                    currency: { label: 'Currency', searchable: true, type: 'select', rules: 'required', trackby: 'alphacode', placeholder: 'Currency', options: 'currencies' },
                },

            }
        },
        created() {

            let id = this.$route.params.id;
            this.fdatalists = JSON.parse(JSON.stringify(this.datalists));

            /* Return the lists data for dropdowns */
            api.getData({}, `/api/v2/contracts/${id}/surcharge_data`, (err, data) => {
                this.fdatalists = {...this.fdatalists, ...data.data};
                this.loaded = true;

                console.log('fdatalists', this.fdatalists);
            });
            
        },
        methods: {
            /* Single Actions */
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editSurcharge');
            },

            /* Dispatched event */
            closeModal(modal){
                this.$bvModal.hide(modal);

                let component = this;

                component.loaded = false;
                setTimeout(function(){ component.loaded = true; }, 100);
            },

            badges(value, color='primary'){
                let carriers = "";

                if(value){
                    if(Array.isArray(value)){
                        
                        value.forEach(function(val){
                            carriers += `<span class='badge badge-${color}'>${val.display_name}</span>`;
                        });

                    } else {

                        let fields_keys = Object.keys(value);

                        fields_keys.forEach(function(key){
                            const item = value[key];
                            carriers += `<span class='badge badge-${color}'>${item.display_name}</span>`;
                        });                        
                    }

                    return carriers;
                } else {
                    return '-';
                }

            },
            badgescarriers(value, color='primary'){
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
