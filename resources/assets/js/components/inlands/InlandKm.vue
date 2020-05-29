<template>
    <div>

        <b-card>
            <div class="row">
                <div class="col-6">
                    <b-card-title>Per Km</b-card-title>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <button class="btn btn-link" v-b-modal.addKm>+ Add Km</button>
                    </div>
                </div>
            </div>

            <DynamicalDataTable 
                :initialFields="fields"
                :initialFormFields="vform_fields"
                :datalists="datalists"
                :equipment="equipment"
                :actions="actions"
                @onEditSuccess="onEdit"
                @onFormFieldUpdated="formFieldUpdated"
                :onLast="true"
                ></DynamicalDataTable>

        </b-card>

        <!-- Edit Form -->
        <b-modal id="editKm" size="lg" cancel-title="Cancel" hide-header-close title="Update Km" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Update"
                @exit="closeModal('editKm')"
                @success="closeModal('editKm')"
                :actions="actions"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

        <!-- Create Form -->
        <b-modal id="addKm" size="lg" hide-header-close title="Add Km" hide-footer>
            <FormView 
                :data="{}" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Ocean Freight"
                @exit="closeModal('addKm')"
                @success="closeModal('addKm')"
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
                currentData: {},
                form_fields: {},

                /* Table headers */
                fields: [ 
                    { key: 'currency', label: 'Currency', formatter: (value)=> { return value.alphacode } }
                ],

                /* Table input inline fields */
                vform_fields: {
                    currency: { 
                        label: 'Currency', 
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
                this.$bvModal.show('editKm');
            },

            /* Single Actions */
            formFieldUpdated(containers_fields){
                this.form_fields = {...this.vform_fields, ...containers_fields};
            },

            /* Close modal form by modal name */
            closeModal(modal){
                this.$bvModal.hide(modal);
            },

            link(){
                 window.location = '/RequestFcl/NewRqFcl';
            }
           
        }
    }
</script>
