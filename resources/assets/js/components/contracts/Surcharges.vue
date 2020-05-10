<template>
    <b-card>
        <div class="row">
            <div class="col-6">
                <b-card-title>Surcharges</b-card-title>
            </div>
            <div class="col-6">
                <div class="float-right">
                    <button class="btn btn-link" v-b-modal.add-fcl>+ Export Contract</button>
                    <button class="btn btn-primary btn-bg">+ Add Surcharge</button>
                </div>
            </div>
        </div>

        <DataTable 
            :fields="fields"
            :inputFields="input_fields"
            :vdatalists="datalists"
            :actions="actions"
            ></DataTable>
    </b-card>
</template>


<script>
    import DataTable from '../DataTable';

    export default {
        components: { 
            DataTable
        },
        props: {
            datalists: Object,
            actions: Object
        },
        data() {
            return {
                isBusy:true, // Loader
                data: null,

                /* Table headers */
                fields: [
                    { key: 'type', label: 'Type', formatter: (value)=> { return value.name } }, 
                    { key: 'origin', label: 'Origin Port', formatter: (value)=> { return this.badgeports(value) } }, 
                    { key: 'destination', label: 'Destination Port', formatter: (value)=> { return this.badgeports(value) } }, 
                    { key: 'destination_type', label: 'Change Type', formatter: (value)=> { return value.description } }, 
                    { key: 'carriers', label: 'Carrier', formatter: (value)=> { return this.badgecarriers(value) } }, 
                    { key: 'calculation_type', label: 'Calculation Type', formatter: (value)=> { return value.name } }, 
                    { key: 'amount', label: 'Amount' }, 
                    { key: 'currency', label: 'Currency', formatter: (value)=> { return value.alphacode } },
                ],

                /* Table input inline fields */
                input_fields: {
                    typeofroute: { searchable: true, type: 'pre_select', rules: 'required', trackby: 'name', placeholder: '', options: 'route_types', initial: { id: 'port', name: 'Port', vselected: 'harbors' }, target: 'dynamical_ports' },
                    surcharge: { searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select option', options: 'surcharges' },
                    origin: { searchable: true, type: 'multiselect', rules: 'required', trackby: 'name', placeholder: 'Select options', options: 'dynamical_ports' },
                    destination: { searchable: true, type: 'multiselect', rules: 'required', trackby: 'name', placeholder: 'Select options', options: 'dynamical_ports' },
                    destination_type: { searchable: true, type: 'select', rules: 'required', trackby: 'description', placeholder: 'Select option', options: 'destination_types' },
                    carriers: { searchable: true, type: 'multiselect', rules: 'required', trackby: 'name', placeholder: 'Select options', options: 'carriers' },
                    calculation_type: { searchable: true, type: 'select', rules: 'required', trackby: 'name', placeholder: 'Select option', options: 'calculation_types' },
                    amount: { type: 'text', rules: 'required', placeholder: 'Amount' },
                    currency: { searchable: true, type: 'select', rules: 'required', trackby: 'alphacode', placeholder: 'Select option', options: 'currencies' },
                },

            }
        },
        created() {
        },
        methods: {
            badgecarriers(value){
                let carriers = "";

                if(value){
                    value.forEach(function(val){
                        carriers += "<span class='badge badge-primary'>"+val.name+"</span> ";
                    });

                    return carriers;
                } else {
                    return '-';
                }

            },
            badgeports(value){
                let carriers = "";

                if(value){
                    value.forEach(function(val){
                        carriers += "<span class='badge badge-warning'>"+val.name+"</span> ";
                    });

                    return carriers;
                } else {
                    return '-';
                }

            },
            
        }
    }
</script>
