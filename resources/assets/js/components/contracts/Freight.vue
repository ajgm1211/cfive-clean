<template>
    <div>

        <b-card>
            <div class="row">
                <div class="col-6">
                    <b-card-title>Ocean Freight</b-card-title>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <button class="btn btn-link" v-b-modal.addOFreight>+ Add Freight</button>
                        <button class="btn btn-primary btn-bg btn-adds" v-on:click="link">+ Import Contract</button>
                    </div>
                </div>
            </div>

            <DynamicalDataTable 
                v-if="loaded"
                :initialFields="fields"
                :initialFormFields="vform_fields"
                :datalists="datalists"
                :equipment="equipment"
                :actions="actions"
                @onEditSuccess="onEdit"
                @onFormFieldUpdated="formFieldUpdated"
                ></DynamicalDataTable>

        </b-card>

        <!-- Edit Form -->
        <b-modal id="editOFreight" size="lg" cancel-title="Cancel" hide-header-close title="Add Ocean Freight" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Update Ocean Freight"
                @exit="closeModal('editOFreight')"
                @success="closeModal('editOFreight')"
                :actions="actions"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

        <!-- Create Form -->
        <b-modal id="addOFreight" size="lg" hide-header-close title="Update Ocean Freight" hide-footer>
            <FormView 
                :data="{}" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Ocean Freight"
                @exit="closeModal('addOFreight')"
                @success="closeModal('addOFreight')"
                :actions="actions"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

    </div>
</template>

<script>
    import DynamicalDataTable from '../views/DynamicalDataTable';
    import FormView from '../views/FormView';

    export default {
        components: { 
            DynamicalDataTable,
            FormView
        },
        props: {
            equipment: Object,
            datalists: Object,
            actions: Object
        },
        data() {
            return {
                loaded: true,
                currentData: {},
                form_fields: {},

                /* Table headers */
                fields: [ 
                    { key: 'origin', label: 'Origin Port', formatter: (value)=> { return value.display_name } }, 
                    { key: 'destination', label: 'Destination Port', formatter: (value)=> { return value.display_name } }, 
                    { key: 'carrier', label: 'Carrier', formatter: (value)=> { return value.name } }, 
                    { key: 'currency', label: 'Currency', formatter: (value)=> { return value.alphacode } }
                ],

                /* Table input inline fields */
                vform_fields: {
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
                        placeholder: 'Select Carrier Port', 
                        options: 'carriers' 
                    },
                    currency: { 
                        label: 'Origin Port', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'alphacode', 
                        placeholder: 'Select Currency Port', 
                        options: 'currencies' }
                },
            }
        },
        methods: {
            /* Single Actions */
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editOFreight');
            },

            /* Single Actions */
            formFieldUpdated(containers_fields){
                this.form_fields = {...this.vform_fields, ...containers_fields};
            },

            /* Close modal form by modal name */
            closeModal(modal){
                this.$bvModal.hide(modal);

                let component = this;

                component.loaded = false;
                setTimeout(function(){ component.loaded = true; }, 100);
            },

            link(){
                 window.location = '/RequestFcl/NewRqFcl';
            }
           
        }
    }
</script>
