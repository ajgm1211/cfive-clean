<template>
	<div>

    <b-form ref="form" class="modal-input">
        <div class="row">
            <div v-for="(item, key) in fields" :key="key" :class="getClass(item)">

                <!-- Text Field -->
                <div v-if="item.type == 'text'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            :label-for="'id_'+key"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                                  >
                        <b-form-input
                                v-model="vdata[key]"
                                :placeholder="item.placeholder" 
                                    >
                        </b-form-input>

                    </b-form-group>
                </div>
                <!-- End Text Field -->

                <!-- Based Dinamical Select Input -->
                <div v-if="item.type == 'pre_select' && refresh">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                          >
                        <multiselect 
                             v-model="vdata[key]"
                             :id="key"
                             :multiple="false" 
                             :options="datalists[item.options]" 
                             :searchable="item.searchable"
                             :close-on-select="true"
                             :clear-on-select="false"
                             track-by="id" 
                             :label="item.trackby" 
                             :show-labels="false"
                             :placeholder="item.placeholder"
                             @select="dispatch">
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- Based Dinamycal Select -->

                <!-- Select Field -->
                <div v-if="item.type == 'select'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
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
                             :placeholder="item.placeholder">
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- End Select Field -->

                <!-- MultiSelect Field -->
                <div v-if="item.type == 'multiselect'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
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
                             :placeholder="item.placeholder">
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- End MultiSelect Field -->

                <!-- DateRange Field -->
                <div v-if="item.type == 'daterange'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
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
                                  :linkedCalendars="true">

                              </date-range-picker>
                    </b-form-group>
                </div>
                <!-- End DateRange Field -->

            </div>

            <div class="btns-form-modal">
                <button class="btn" @click="close" type="button">Cancel</button>
                <button class="btn btn-primary btn-bg" type="button" @click="onSubmit" >{{ btnTxt }}</button>
            </div>
        </div>

    </b-form>
          
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
            vdatalists: {
                type: Object,
                required: false,
                default: () => { return {} }
            },
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
                vdata: {},
                datalists: {},
                refresh: true
            }
        },
        created() {
            this.vdata = this.data;
            this.updateDinamicalFieldOptions();
        },
        methods: {
            /* Dispatch an event when click in cancel close */
            close(){
                this.$emit('exit', true);
            },

            /* Set the class of the field */
            getClass(item){

                if('colClass' in item)
                    return item.colClass;

                return 'col-sm-6';

            },

            /* Reset the Dynamical Fields */
            resetDynamicalFields(target){

                for (const key in this.fields) {
                    if(this.fields[key]['options'] == target)
                        this.vdata[key] = null;
                }

            },

            /* Execute when pre select field is updated */
            dispatch(val, item){
                this.refresh = false;
                this.datalists[this.fields[item].target] = this.datalists[val.vselected];
                this.resetDynamicalFields(this.fields[item].target);
                this.refresh = true;
            },

            /* Update Dynamical Fields */
            updateDinamicalFieldOptions(){

                this.datalists = JSON.parse(JSON.stringify(this.vdatalists));

                for (const key in this.fields) {
                    if(this.fields[key]['type'] == 'pre_select')
                        this.datalists[this.fields[key]['target']] = this.datalists[this.fields[key]['initial'].vselected];
                }
            },

            /* Check if value is empty by type */
            isEmpty(value){
                //console.log(typeof value);
                if(typeof value == 'string')
                    return value == '' || value == null;

                if(typeof value == 'object')
                    return value == null || Object.keys(value).length === 0;

                return false;
            },

            /* Validate Form */
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

            /* Prepare the data to submit */
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
                        case 'pre_select':
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
                    console.log('data', data);

                    if(this.update){

                        this.actions.update(this.vdata.id, data, this.$route)
                            .then( ( response ) => {
                                this.$emit('success', response.data.data.id);
                                this.vdata = {};
                        })
                            .catch(( data ) => {
                                console.log(data);
                        });

                    } else {

                        this.actions.create(data, this.$route)
                            .then( ( response ) => {
                                this.$emit('success', response.data.data.id);
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
            vdatalists: {
                handler(val, oldval){
                    this.updateDinamicalFieldOptions();
                },
                deep: true
            },
        }

    }
</script>