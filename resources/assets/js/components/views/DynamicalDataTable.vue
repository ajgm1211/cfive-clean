<template>

    <DataTable 
        v-if="isLoaded"
        :fields="fields"
        :inputFields="form_fields"
        :vdatalists="datalists"
        :actions="actions"
        :massiveactions="['changecontainersview', 'delete']"
        @onEdit="onEdit"
        @onChangeContainersView="onChangeContainersView"
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
            initialFormFields: Object,
            onLast: {
                type: Boolean,
                default: false,
                required: false
            }
        },
        data() {
            return {
                fields: [],
                form_fields: {},
                isLoaded: true,
                extra_field_state: false
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

            /* Set middle fields */
            setMiddleColumns(equipment, component){
                
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

                return containers;

            },

            /* Set extra field */
            setExtraColumns(equipment, component){

                let containers = {};

                component.fields.push({ key: 'per_container', label: 'Per container '+equipment.name });
                component.form_fields['per_container'] = { type: 'text', label: 'Per container '+equipment.name, placeholder: '0' };
                containers['per_container'] = { type: 'text', label: 'Per container'+equipment.name, placeholder: '0' };

                return containers;

            },

            /* Set lasts fields */
            setLastColumns(component){
                let fields = [];

                if(this.initialFields.length > 1)
                    fields = this.initialFields.slice(2);
                else
                    fields = this.initialFields;

                fields.forEach(function (item){
                    component.fields.push(item);
                    component.form_fields[item.key] = component.initialFormFields[item.key];
                });

            },

            /* Set Container Columns and fields by equipment */
            setColumns(equipment){
               
                this.resetValues();

                let component = this;
                let containers = {};

                if(!this.onLast)
                    this.setFirstColumns(component);

                if(this.extra_field_state)
                    containers = this.setExtraColumns(equipment, component);
                else
                    containers = this.setMiddleColumns(equipment, component);

                this.setLastColumns(component);
                this.isLoaded = true;
                this.$emit('onFormFieldUpdated', containers);
            },
            onChangeContainersView(value){
                this.extra_field_state = !this.extra_field_state;
            }
           
        },
        watch: {
            equipment: function(val, oldVal) {
                this.setColumns(val)
            },
            extra_field_state: function(val, oldVal) {
                this.setColumns(this.equipment)
            }
        }
    }
</script>
