<template>
    <b-card>
            <!-- Form Contract Inline -->
            <FormInlineView
                :data="currentData" 
                :fields="form_fields"
                :datalists="datalists"
                :actions="actions"
                >
            </FormInlineView>
            <!-- End Form Contract Inline -->
    </b-card>
</template>


<script>
    import Multiselect from 'vue-multiselect';
    import FormInlineView from '../views/FormInlineView';

    export default {
        components: { 
            Multiselect,
            FormInlineView
        },
        props: {
            datalists: Object,
            actions: Object,
            data: Object
        },
        created(){
            this.currentData = this.data;
        },
        data() {
            return {
                isBusy:true, // Loader
                users: '',
                company: '',
                currentData: {},

                /* Form Fields */
                form_fields: {
                    companies: { 
                        label:'Company', 
                        searchable: true, 
                        type: 'multiselect', 
                        trackby: 'business_name', 
                        placeholder: 'Select options', 
                        options: 'companies',
                        colClass: 'col-sm-12'
                    },
                    users: { 
                        label:'Users', 
                        searchable: true, 
                        type: 'multiselect',
                        trackby: 'name', 
                        placeholder: 'Select options', 
                        options: 'users',
                        colClass: 'col-sm-12'
                    },
                }
            }
        },
        
       
    }
</script>