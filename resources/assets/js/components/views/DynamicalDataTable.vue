<template>

    <DataTable 
        v-if="isLoaded"
        :fields="fields"
        :inputFields="form_fields"
        :vdatalists="datalists"
        :actions="actions"
        @onEdit="onEdit"
        >
    </DataTable>

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
            equipment: Object,
            datalists: Object,
            actions: Object,
            initialFields: Array,
            initialFormFields: Object
        },
        data() {
            return {
                fields: [],
                form_fields: {},
                isLoaded: true,
            }
        },
        created() { 
            this.setColumns(this.equipment);
        },
        methods: {
            /* Single Actions */
            onEdit(data){
                this.$emit('onEditSuccess', data);
            },

            /* Reset all fields */
            resetValues(){
                this.isLoaded = false;
                this.fields = [];
                this.form_fields = {};
            },

            /* Set firsts fields */
            setFirstColumns(component){
                let fields = this.initialFields.slice(0, 2);

                fields.forEach(function (item){
                    component.fields.push(item);
                    component.form_fields[item.key] = component.initialFormFields[item.key];
                });

            },

            /* Set lasts fields */
            setLastColumns(component){
                let fields = this.initialFields.slice(2);

                fields.forEach(function (item){
                    component.fields.push(item);
                    component.form_fields[item.key] = component.initialFormFields[item.key];
                });

            },

            /* Set Container Columns and fields by equipment */
            setColumns(equipment){
               
                this.resetValues();

                let component = this;

                this.setFirstColumns(component);

                let rate = '';
                let containers = {};

                this.datalists.containers.forEach(function(item){
                    if(item.gp_container_id === equipment.id)
                    {
                        rate = 'rates_'+item.code;
                        component.fields.push({ key: rate, label: item.name });
                        component.form_fields[rate] = { type: 'text', label: item.name, placeholder: item.name };
                        containers[rate] = { type: 'text', label: item.name, placeholder: item.name };
                    }
                });

                this.setLastColumns(component);
                this.isLoaded = true;
                this.$emit('onFormFieldUpdated', containers);
            },
           
        },
        watch: {
            equipment: function(val, oldVal) {
                this.setColumns(val)
            }
        }
    }
</script>
