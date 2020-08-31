


<template>
    <b-card>
            <!-- Form Contract Inline -->
            <FormInlineView
                :data="currentData" 
                :fields="fields"
                :actions="actions"
                >
            </FormInlineView>
            <!-- End Form Contract Inline -->
    </b-card>
</template>


<script>
    import FormInlineView from '../views/FormInlineView';

    export default {
        components: { 
            FormInlineView
        },
        props: {
            actions: Object,
            data: Object
        },
        created(){
            this.currentData = this.data;
        },
        data() {
            return {
                currentData: {},

                /* Form Fields */
                fields: {
                    remarks: { 
                        label:'Remarks', 
                        type: 'textarea',
                        colClass: 'col-sm-12'
                    },
                }
            }
        },
        
       
    }
</script>