<template>
    <div>

        <b-card>
            <div class="row">
                <div class="col-6">
                    <b-card-title>Per Range</b-card-title>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <button class="btn btn-link" v-b-modal.addRange>+ Add Range</button>
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
        <b-modal id="editRange" size="lg" cancel-title="Cancel" hide-header-close title="Update Range" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Update"
                @exit="closeModal('editRange')"
                @success="closeModal('editRange')"
                :actions="actions"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

        <!-- Create Form -->
        <b-modal id="addRange" size="lg" hide-header-close title="Add Range" hide-footer>
            <FormView 
                :data="{}" 
                :fields="form_fields"
                :vdatalists="datalists"
                btnTxt="Add Ocean Freight"
                @exit="closeModal('addRange')"
                @success="closeModal('addRange')"
                :actions="actions"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

    </div>
</template>

<script>
    import DynamicalDataTable from '../../components/views/DynamicalDataTable';
    import FormView from '../../components/views/FormView.vue';

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
                    { key: 'lower', label: 'Lower Limit' }, 
                    { key: 'upper', label: 'Upper Limit' }, 
                    { key: 'currency', label: 'Currency', formatter: (value)=> { return value.alphacode } }
                ],

                /* Table input inline fields */
                vform_fields: {
                    lower: { 
                        label: 'Lower Limit',
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Km',  
                    },
                    upper: { 
                        label: 'Upper Limit', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Km', 
                    },
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
                this.$bvModal.show('editRange');
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
