<template>
	<div>

    <!-- Inline Contract Form -->
    <form ref="form" @submit.stop.prevent="handleSubmit">
        <div class="row">

            <!-- Reference -->
            <div v-for="(item, key) in fields" :key="key" class="col-12 col-sm-3 col-lg-2">

                <!-- Text Field -->
                <div v-if="item.type == 'text'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :label-for="'id_'+key"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                                  >
                        <b-form-input
                                v-model="vdata[key]"
                                :placeholder="item.placeholder"
                                v-on:blur="onSubmit()" 
                                    >
                        </b-form-input>

                    </b-form-group>
                </div>
                <!-- End Text Field -->

                <!-- Select Field -->
                <div v-if="item.type == 'select'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                          >
                        <multiselect 
                             v-model="vdata[key]" 
                             :multiple="false" 
                             :options="datalists[item.options]" 
                             :searchable="item.searchable"
                             :close-on-select="true"
                             :clear-on-select="true"
                             track-by="id" 
                             :label="item.trackby" 
                             :show-labels="false" 
                             :placeholder="item.placeholder"
                             @input="onSubmit()"
                             >
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- End Select Field -->

                <!-- MultiSelect Field -->
                <div v-if="item.type == 'multiselect'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                          >
                        <multiselect 
                             v-model="vdata[key]"
                             :multiple="true" 
                             :options="datalists[item.options]" 
                             :searchable="item.searchable"
                             :close-on-select="true"
                             :clear-on-select="true"
                             track-by="id" 
                             :label="item.trackby" 
                             :show-labels="false" 
                             :placeholder="item.placeholder"
                             @input="onSubmit()"
                             >
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- End MultiSelect Field -->

                <!-- DateRange Field -->
                <div v-if="item.type == 'daterange'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                            >
                              <date-range-picker
                                  :opens="'center'"
                                  :locale-data="{ firstDay: 1, format: 'yyyy/mm/dd' }"
                                  :singleDatePicker="false"
                                  :autoApply="true"
                                  :timePicker="false"
                                  v-model="vdata[key]"
                                  :linkedCalendars="true"
                                  @select="onSubmit()">

                              </date-range-picker>
                    </b-form-group>
                </div>
                <!-- End DateRange Field -->


                <!-- Status Field-->
                <div v-if="item.type == 'status'">
                    <div class="col-12 col-sm-3 col-lg-2">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                            >
                            <span class="status-st" :class="vdata[key]" ></span>
                        </b-form-group>
                    </div>
                </div>
                <!-- End Status Field -->

            </div>
        </div>  
    </form>
    <!-- End Inline Contract Form -->

	</div>
</template>

<script>

    import Multiselect from 'vue-multiselect';
    import DateRangePicker from 'vue2-daterange-picker';

    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
    import 'vue-multiselect/dist/vue-multiselect.min.css';


    export default {
        components: {
            Multiselect,
            DateRangePicker
        },
    	props: {
            data: Object,
            fields: Object,
            datalists: Object,
            btnTxt: {
                type: String,
                required: false,
                default: 'Save'
            },
            actions: Object,
            update: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        data() {
            return {
                vdata: {}
            }
        },
        created() {
            console.log(this.data);
            this.vdata = this.data;
        },
        methods: {
            closeModal(){
                this.$emit('exit', true);
            },

            getClass(item){

                if('colClass' in item)
                    return item.colClass;

                return 'col-sm-6';

            },

            isEmpty(value){
                //console.log(typeof value);
                if(typeof value == 'string')
                    return value == '' || value == null;

                if(typeof value == 'object')
                    return value == null || Object.keys(value).length === 0;

                return false;
            },

            validateForm(){
                let validate = true;
                let component = this;
                let fields_keys = Object.keys(this.fields);

                fields_keys.forEach(function(key){
                    const item = component.fields[key];

                    if('rules' in item){
                        if(item.rules.includes('required')){
                            if(component.isEmpty(component.vdata[key])){
                                $(`#id_${key} .invalid-feedback`).css({'display':'block'});

                                validate = false;
                            }
                        }
                    }
                });

                return validate;
            },
            prepareData(){
                let data = {};
                let component = this;
                let fields_keys = Object.keys(this.fields);

                fields_keys.forEach(function(key){
                    const item = component.fields[key];

                    switch (item.type) {
                        case 'text':
                            if(component.vdata[key])
                                data[key] = component.vdata[key];
                            break;
                        case 'select':
                            if(component.vdata[key])
                                data[key] = component.vdata[key].id;
                            break;
                        case 'multiselect':
                            if(component.vdata[key].length)
                                data[key] = component.vdata[key].map(e => e.id );
                            break;
                        case 'daterange':
                            if(component.vdata[key]['startDate'] && component.vdata[key]['endDate']){ 
                                data[item.sdName] = moment(component.vdata[key].startDate).format('YYYY/MM/DD');
                                data[item.edName] = moment(component.vdata[key].endDate).format('YYYY/MM/DD');
                            }
                            break;

                    }
                });

                return data;
            },

            /* Handle the submit Form and 
              send the data to store the item */
            onSubmit(){
                if(this.validateForm()){
                    let data = this.prepareData();

                    if(this.update){

                        this.actions.update(this.vdata.id, data, this.$route)
                            .then( ( response ) => {
                                this.$emit('success', response.data.data);
                        })
                            .catch(( data ) => {
                                console.log(data);
                        });

                    } else {

                        this.actions.create(data, this.$route)
                            .then( ( response ) => {
                                this.$emit('success', response.data.data);
                                this.vdata = {};
                        })
                            .catch(( data ) => {
                                console.log(data);
                        });
                    }

                }

            },
        },
        watch: {
            data: {
                handler(val, oldval){
                    this.vdata = val;
                },
                deep: true
            },
        }

    }
</script>