<template>
	<div>

    <b-form ref="form" class="modal-input">
        <div class="row">
            <div v-for="(item, key) in fields" :key="key" class="col-12 col-sm-6">

                <!-- Text Field -->
                <div v-if="item.type == 'text'">
                    <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            :label-for="'id_'+key"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="Reference is done!"
                                  >
                        <b-form-input
                                v-model="vdata[key]"
                                :placeholder="item.placeholder" 
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
                            valid-feedback="Reference is done!"
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
                            <!-- <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span></template> -->
                        </multiselect>
                    </b-form-group>
                </div>
                <!-- End Select Field -->

            </div>

            <div class="btns-form-modal">
                <button class="btn" @click="closeModal" type="button">Cancel</button>
                <button class="btn btn-primary btn-bg" type="button" @click="onSubmit" >{{ btnTxt }}</button>
            </div>
        </div>

    </b-form>
          
	</div>
</template>

<script>

    import Multiselect from 'vue-multiselect';

    export default {
        components: {
            Multiselect
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
            this.vdata = this.data;
        },
        methods: {
            closeModal(){
                this.$emit('exit', true);
            },
            isEmpty(value){
                //console.log(typeof value);
                if(typeof value == 'string')
                    return value == '';

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
                    const item = component.vdata[key];

                    if(typeof item == 'object')
                        data[key] = item.id;
                    else 
                        data[key] = item
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
                                this.$emit('success', true);
                        })
                            .catch(( data ) => {
                                console.log(data);
                        });

                    } else {

                        this.actions.create(data, this.$route)
                            .then( ( response ) => {
                                this.$emit('success', true);
                        })
                            .catch(( data ) => {
                                console.log(data);
                        });
                    }

                }

            },
        }

    }
</script>