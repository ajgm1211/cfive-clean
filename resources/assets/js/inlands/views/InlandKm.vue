<template>
    <div>

        <b-card>
            <div class="row">
                <div class="col-6">
                    <b-card-title>Per Km</b-card-title>
                </div>
            </div>

            <FormInlineView 
                v-if="isLoaded"
                :data="currentData" 
                :fields="form_fields"
                :datalists="datalists"
                @exit="closeModal('editKm')"
                @success="closeModal('editKm')"
                :actions="actions"
                :update="true"
                >
            </FormInlineView>

        </b-card>

    </div>
</template>

<script>
    import FormInlineView from '../../components/views/FormInlineView.vue';

    export default {
        components: { 
            FormInlineView
        },
        props: {
            equipment: Object,
            datalists: Object,
            actions: Object
        },
        data() {
            return {
                currentData: {},
                isLoaded: false,
                form_fields: {},

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
        created() {
            this.setColumns(this.equipment);

            this.actions.retrieve(this.$route)
                .then( ( response ) => {
                    console.log(response);
                    this.currentData = response.data.data;
                    this.isLoaded = true;
                })
                .catch(( data ) => {
                    console.log(data);
                });
        },
        methods: {

            /* Reset all fields */
            resetValues(){
                this.isLoaded = false;
                this.form_fields = {};
            },

            /* Set middle fields */
            setMiddleColumns(equipment, component){
                
                let rate = '';
                let containers = {};
                
                this.datalists.containers.forEach(function(item){
                    
                    if(item.gp_container_id === equipment.id)
                    {
                        rate = 'rates_'+item.code;
                        component.form_fields[rate] = { type: 'text', label: item.name, placeholder: item.name };
                    }

                });

            },

            /* Set lasts fields */
            setLastColumns(component){
                component.form_fields['currency'] = component.vform_fields['currency'];
            },

            /* Set Container Columns and fields by equipment */
            setColumns(equipment){
               
                this.resetValues();

                let component = this;
                let containers = {};

                containers = this.setMiddleColumns(equipment, component);

                this.setLastColumns(component);
                this.isLoaded = true;
            },
           
        },

        watch: {
            equipment: function(val, oldVal) {
                this.setColumns(val)
            }
        }
    }
</script>
