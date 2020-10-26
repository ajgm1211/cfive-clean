<template>

    <DataTable 
        v-if="isLoaded"
        :fields="fields"
        :inputFields="form_fields"
        :extraFields="extra_fields"
        :vdatalists="datalists"
        :searchBar="searchBar"
        :multiList="multiList"
        :multiId="multiId"
        :withTotals="withTotals"
        :totalsFields="totalsFields"
        :totalActions="totalActions"
        :paginated="paginated"
        :actions="actions"
        :massiveactions="massiveactions"
        :extraRow="extraRow"
        :singleActions="singleActions"
        :autoupdateDataTable="autoupdateDataTable"
        :portType="portType"
        :autoAdd="autoAdd"
        :changeAddMode="changeAddMode"
        :portAddress="portAddress"
        :massiveSelect="massiveSelect"
        @onEdit="onEdit"
        @onChangeContainersView="onChangeContainersView"
        @onOpenModalContainerView="openModalContainerView"
        ref="table"
        >
    </DataTable>

</template>

<script>
    import DataTable from '../DataTable';
    import FormView from '../views/FormView';

    export default {
        components: { 
            DataTable,
            FormView,
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
            totalActions: Object,
            initialFields: Array,
            initialFormFields: Object,
            fixedFormFields: Object,
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
            },
            paginated: {
                type: Boolean,
                required: false,
                default: true
            },
            extraRow: {
                type: Boolean,
                required: false,
                default: false
            },
            withTotals: {
                type: Boolean,
                required: false,
                default: false
            },
            totalsFields: {
                type: Object,
                required: false,
                default: () => { return {} }
            },
            singleActions: {
                type: Array,
                required: false,
                default: () => { return ['edit', 'duplicate', 'delete'] }                
            },
            autoupdateDataTable: {
                type: Boolean,
                required: false,
                default: false
            },
            portType: {
                type: String,
                required: false,
                default: ''
            },
            autoAdd: {
                type: Boolean,
                required:false,
                default: true
            },
            changeAddMode: {
                type: Boolean,
                required:false,
                default: false
            },
            portAddress: {
                type: Object,
                required: false,
                default: () => { return {} }
            },
            massiveSelect: {
                type: Boolean,
                required: false,
                default: true,
            },
        },
        data() {
            return {
                fields: [],
                form_fields: {},
                extra_fields: {},
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
                if(!this.multiId){
                    this.$emit('onEditSuccess', data);
                }else{
                    this.$emit('onEditSuccess', data,this.multiId);
                }
            },

            /* Reset all fields */
            resetValues(){
                this.isLoaded = false;
                this.fields = [];
                this.form_fields = {};
                this.extra_fields = {};
            },

            /* Set firsts fields */
            setFirstColumns(component){
                let fields = this.initialFields.slice(0, 2);
                let fixedKey = '';

                fields.forEach(function (item){
                    if(component.extraRow){
                        if(item.key.includes('_id')){
                            fixedKey = 'fixed_'.concat(item.key.replace('_id',''));
                            component.extra_fields[fixedKey] = component.fixedFormFields[fixedKey];
                        }
                    }
                    component.fields.push(item);
                    component.form_fields[item.key] = component.initialFormFields[item.key];
                });

            },

            /* Set middle fields */
            setMiddleColumns(equipment, component){
                
                let rate = '';
                let freight = '';
                let containers = {};
                let all_containers = _.cloneDeep(this.datalists.containers);
                let new_containers = [];
                let table = this;

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
                        freight = 'freights_'+item.code;
                        rate = 'rates_'+item.code;
                        component.fields.push({ key: rate, label: item.name, type: 'text' });
                        component.form_fields[rate] = { type: 'text', label: item.name, placeholder: item.name };
                        containers[rate] = { type: 'text', label: item.name, placeholder: item.name };
                        if(table.extraRow){
                           component.extra_fields[freight] = { type: 'extraText', placeholder: item.name };
                           containers[freight] = { type: 'extraText', placeholder: item.name }
                        }
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
                let fixedKey = '';

                if(this.initialFields.length > 1)
                    fields = this.initialFields.slice(2);
                else
                    fields = this.initialFields;

                fields.forEach(function (item){
                        if(component.extraRow){
                        if(item.key.includes('_id')){
                            fixedKey = 'fixed_'.concat(item.key.replace('_id',''));
                            component.extra_fields[fixedKey] = component.fixedFormFields[fixedKey];
                        }
                    }
                    component.fields.push(item);
                    component.form_fields[item.key] = component.initialFormFields[item.key];
                });
            },

            /* Set Container Columns and fields by equipment */
            setColumns(equipment){

                if(Object.keys(equipment).length!=0){
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
                }else{
                    let component = this;

                    this.resetValues();

                    component.initialFields.forEach(function (item){
                        component.fields.push(item);
                        component.form_fields[item.key] = component.initialFormFields[item.key];
                        component.extra_fields[item.key] = component.initialFormFields[item.key];
                        if(item.key == 'surcharge_id'){
                            component.extra_fields[item.key]['type'] = "extraText";
                            component.extra_fields[item.key]['disabled'] = true;
                            component.extra_fields[item.key]['placeholder'] = "Freight";
                        }
                        if(component.extra_fields[item.key]['type'] == "text"){
                            component.extra_fields[item.key]['type'] = "extraText";
                        } else if(component.extra_fields[item.key]['type'] == "select"){
                            component.extra_fields[item.key]['type'] = "extraSelect";
                        }
                    });
                    this.isLoaded = true;
                }
            },
            
            onChangeContainersView(value){
                this.extra_field_state = !this.extra_field_state;
            },
            
            openModalContainerView(ids){
                this.$emit('onOpenModalContainer', ids);
            },
            
            refreshTable(){
                this.$refs.table.refreshData();
            },

            addInsert(){
                this.$refs.table.addInsert();
            },
           
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
