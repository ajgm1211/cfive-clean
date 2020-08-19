<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Sale codes</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button class="btn btn-primary btn-bg" v-b-modal.addSaleCode>+ Add Sale code</button>
                            </div>
                        </div>
                    </div>

                    <DataTable
                        v-if="loaded"
                        :fields="fields"
                        :actions="actions.sale_codes"
                        @onEdit="onEdit"
                        ></DataTable>
                </b-card>
            </div>
        </div>
        
        <!-- Create Form -->
        <b-modal id="addSaleCode" size="md" hide-header-close title="Add Sale code" hide-footer>
            <FormView 
                :data="fdata" 
                :fields="form_fields"
                btnTxt="Save"
                @exit="closeModal('addSaleCode')"
                @success="closeModal('addSaleCode')"
                :actions="actions.sale_codes"
                >
            </FormView>
        </b-modal>
        <!-- End Create Form -->

        <!-- Edit Form -->
        <b-modal id="editSaleCode" size="lg" cancel-title="Cancel" ok-title="Add Contract" hide-header-close title="Update Sale Code" hide-footer>
            <FormView 
                :data="currentData" 
                :fields="form_fields"
                btnTxt="Update"
                @exit="closeModal('editSaleCode')"
                @success="closeModal('editSaleCode')"
                :actions="actions.sale_codes"
                :update="true"
                >
            </FormView>
        </b-modal>
        <!-- End Edit Form -->

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
                loaded: true,
                fdata: { validity: { startDate: null, endDate: null } },
                datalists: {},
                currentData: {},

                /* Table headers */
                fields: [
                    { key: "name", label: "Name" },
                    { key: "description", label: "Description" }, 
                ],

                /* Form Modal Fields */
                form_fields: {
                    name: { 
                        label: 'Name', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Name', 
                    },
                    description: { 
                        label: 'Description', 
                        type: 'text',
                        placeholder: 'Enter a description', 
                    },
                }
            }
        },
        methods: {

            /* Single Actions */
            onEdit(data){
                this.currentData = data;
                this.$bvModal.show('editSaleCode');
            },

            /* Dispatched event */
            closeModal(modal){
                this.$bvModal.hide(modal);

                let component = this;

                component.loaded = false;
                setTimeout(function(){ component.loaded = true; }, 100);
            },
        }
    }
</script>
