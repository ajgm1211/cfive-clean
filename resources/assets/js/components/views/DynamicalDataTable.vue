<template>

    <DataTable 
        v-if="isLoaded"
        :fields="fields"
        :inputFields="form_fields"
        :vdatalists="datalists"
        :searchBar="searchBar"
        :multiList="multiList"
        :multiId="multiId"
        :quoteEquip="quoteEquip"
        :actions="actions"
        :massiveactions="massiveactions"
        @onEdit="onEdit"
        @onChangeContainersView="onChangeContainersView"
        @onOpenModalContainerView="openModalContainerView"
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
            quoteEquip: Array,
            massiveactions: {
                type: Array,
                required: false,
                default: () => { return ['delete'] }
            },
            actions: Object,
            initialFields: Array,
            initialFormFields: Object,
            groupContainer: {
                type: Boolean,
                default: false,
                required: false
            },
            onLast: {
                type: Boolean,
                default: false,
                required: false
            },
            limitEquipment: {
                type: Boolean,
                default: false,
                required: false
            },
            searchBar: {
                type: Boolean,
                default: true,
                required: false
            },
            multiList: {
                type: Boolean,
                required: false,
                default:false
            },
            multiId: {
                type: Number,
                required: false,
                default:1
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
            this.extra_field_state = this.groupContainer;
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
                let all_containers = _.cloneDeep(this.datalists.containers);
                let new_containers = [];

                if(this.limitEquipment){
                    all_containers.forEach(function(cont){
                        if(component.quoteEquip.includes(cont.code)){
                            new_containers.push(cont)
                        }
                    });
                    all_containers = new_containers
                }
       
                all_containers.forEach(function(item){
                    
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
            },
            openModalContainerView(ids){
                this.$emit('onOpenModalContainer', ids);
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
