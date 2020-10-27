<template>
    <div>
        <!-- Inline Contract Form -->
        <form ref="form" @submit.stop.prevent="handleSubmit">
            <div class="row" style="padding: 0px 25px;">
                <!-- Reference -->
                <div v-for="(item, key) in fields" :key="key" :class="getClass(item)">
                    <!-- Text Field -->
                    <div v-if="item.type == 'text' && !item.hidden">
                        <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            :hidden="item.hidden"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <b-form-input
                                v-model="vdata[key]"
                                :parentId="item.parentId"
                                :placeholder="item.placeholder"
                                v-on:blur="onSubmit()"
                                @change="cleanInput(key)"
                                :disabled="item.disabled"
                                class="input-h"
                            ></b-form-input>
                            <span :id="'id_f_inline_'+key" class="invalid-feedback"></span>
                        </b-form-group>
                    </div>
                    <!-- End Text Field -->

                    <!-- Textarea Field -->
                    <div v-if="item.type == 'textarea'">
                        <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <b-textarea
                                id="inline-form-input-name"
                                :parentId="item.parentId"
                                v-model="vdata[key]"
                                class="mb-2 mr-sm-2 mb-sm-0 remarks"
                                v-on:blur="onSubmit()"
                                @change="cleanInput(key)"
                            ></b-textarea>
                            <span :id="'id_f_inline_'+key" class="invalid-feedback"></span>
                            <span class="update-remark">
                                <i class="fa fa-repeat" aria-hidden="true"></i>
                            </span>
                        </b-form-group>
                    </div>
                    <!-- End Textarea Field -->

                    <!-- CKEditor -->
                    <div v-if="item.type == 'ckeditor'">
                         <b-form-group
                            :label="item.label"
                        >
                        <br>
                            <ckeditor
                                id="inline-form-input-name"
                                :parentId="item.parentId"
                                type="classic"
                                v-model="vdata[key]"
                                v-on:blur="onSubmit()"
                                @change="cleanInput(key)"
                            ></ckeditor>
                            <span :id="'id_f_inline_'+key" class="invalid-feedback"></span>
                            <span class="update-remark">
                                <i class="fa fa-repeat" aria-hidden="true"></i>
                            </span>
                        </b-form-group>
                    </div>
                    <!-- End CKEditor -->

                    <!-- Select Field -->
                    <div v-if="item.type == 'select'">
                        <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                            :disabled="item.disabled"
                            :isLocker="item.isLocker"
                            :selectLock="item.selectLock"
                            :lockerTrack="item.lockerTrack"
                            :hiding="item.hiding"
                            :isHiding="item.isHiding"
                            :showCondition="item.showCondition"
                        >
                            <multiselect
                                v-model="vdata[key]"
                                :parentId="item.parentId"
                                :multiple="false"
                                :options="getOptions(item.options)"
                                :disabled="item.disabled"
                                :all_options="item.all_options"
                                :searchable="item.searchable"
                                :close-on-select="true"
                                :clear-on-select="true"
                                track-by="id"
                                :label="item.trackby"
                                :show-labels="false"
                                :placeholder="item.placeholder"
                                @input="onSubmit(),unlock(item,key)"
                                @select="cleanInput(key)"
                                class="input-h"
                            ></multiselect>
                            <span
                                :id="'id_f_inline_'+key"
                                class="invalid-feedback"
                                style="margin-top:-4px"
                            ></span>
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
                                :parentId="item.parentId"
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
                                @select="cleanInput(key)"
                                class="input-h"
                            ></multiselect>
                            <span
                                :id="'id_f_inline_'+key"
                                class="invalid-feedback"
                                style="margin-top:-4px"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End MultiSelect Field -->

                    <!-- DateRange Field -->
                    <div v-if="item.type == 'daterange'">
                        <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block input-h"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <date-range-picker
                                :opens="'center'"
                                :parentId="item.parentId"
                                :locale-data="{ firstDay: 1, format: 'yyyy/mm/dd' }"
                                :singleDatePicker="false"
                                :autoApply="true"
                                :timePicker="false"
                                v-model="vdata[key]"
                                :linkedCalendars="true"
                                @update="cleanInputSubmit(key)"
                                class="input-h"
                            ></date-range-picker>
                            <span
                                :id="'id_f_inline_'+key"
                                class="invalid-feedback"
                                style="margin-top:-4px"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End DateRange Field -->

                    <div v-if="item.type == 'datepicker'">
                        <b-form-group :label="item.label" class="d-block" style="border:none">
                            <b-form-datepicker
                                :id="'id_'+key"
                                :parentId="item.parentId"
                                :label="item.label"
                                :invalid-feedback="key+' is required'"
                                valid-feedback="key+' is done!'"
                                v-model="vdata[key]"
                                :locale="locale"
                                :date-format-options="dateFormat"
                                @input="cleanInputSubmit(key)"
                                class="input-h"
                                >
                            ></b-form-datepicker>
                        </b-form-group>
                    </div>

                    <!-- Status Field-->
                    <div v-if="item.type == 'status'">
                        <b-form-group
                            :id="'id_'+key"
                            :label="item.label"
                            class="d-block input-h"
                            :invalid-feedback="key+' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <span class="status-st" :class="vdata[key]"></span>
                        </b-form-group>
                    </div>
                    <!-- End Status Field -->

                    <!-- Span field -->
                    <div v-if="item.type == 'span'">
                        <span 
                            style="font-weight:bold;"
                            v-html="vdata[key]">
                        </span>
                    </div>
                    <!-- Span field end -->
                </div>
            </div>
        </form>
        <!-- End Inline Contract Form -->
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";

import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import "vue-multiselect/dist/vue-multiselect.min.css";

export default {
    components: {
        Multiselect,
        DateRangePicker,
    },
    props: {
        data: Object,
        fields: Object,
        datalists: Object,
        btnTxt: {
            type: String,
            required: false,
            default: "Save",
        },
        actions: Object,
        update: {
            type: Boolean,
            required: false,
            default: false,
        },
        multi: {
            type: Boolean,
            required: false,
            default: false,
        },
        
    },
    data() {
        return {
             //Datepicker Options
            locale: 'en-US',
            dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'},
            vdata: {},
        };
    },
    created() {
        let fields_keys = Object.keys(this.fields);
        let component = this;

        //console.log(this.data);
        this.vdata = this.data;

        fields_keys.forEach(function (key) {
            if(component.multi){
                component.fields[key].parentId = component.$parent.id;
            }
            if(component.fields[key].isLocker){
                component.unlock(component.fields[key],key)
            }
            /**if(component.fields[key].isHiding){
                component.showHidden(component.fields[key],key)
            }**/
        });        
    },
    methods: {
        closeModal() {
            this.$emit("exit", true);
        },

        getClass(item) {
            if (item.label == "Carriers") {
                return "col-lg-3";
            }

            if (item.label == "Direction" || item.label == "Equipment") {
                return "col-lg-1";
            }

            if ("colClass" in item) {
                return item.colClass;
            }

            return "col-12 col-sm-3 col-lg-2";
        },

        isEmpty(value) {
            //console.log(typeof value);
            if (typeof value == "string") return value == "" || value == null;

            if (typeof value == "object")
                return value == null || Object.keys(value).length === 0;

            return false;
        },

        validateForm() {
            return true;
            let validate = true;
            let component = this;
            let fields_keys = Object.keys(this.fields);

            fields_keys.forEach(function (key) {
                const item = component.fields[key];

                if ("rules" in item) {
                    if (item.rules.includes("required")) {
                        if (component.isEmpty(component.vdata[key])) {
                            $(`#id_${key} .invalid-feedback`).css({
                                display: "block",
                            });

                            validate = false;
                        }
                    }
                }
            });

            return validate;
        },
        prepareData() {
            let data = {};
            let component = this;
            let fields_keys = Object.keys(this.fields);

            data["keys"] = fields_keys;
            fields_keys.forEach(function (key) {
                const item = component.fields[key];

                switch (item.type) {
                    case "text":
                    case "textarea":
                    case "ckeditor":
                        if (component.vdata[key])
                            data[key] = component.vdata[key];
                        break;
                    case "select":
                        if (component.vdata[key])
                            data[key] = component.vdata[key].id;
                        break;
                    case "multiselect":
                        if (component.vdata[key].length)
                            data[key] = component.vdata[key].map((e) => e.id);
                        break;
                    case "daterange":
                        if (
                            component.vdata[key]["startDate"] &&
                            component.vdata[key]["endDate"]
                        ) {
                            data[item.sdName] = moment(
                                component.vdata[key].startDate
                            ).format("YYYY/MM/DD");
                            data[item.edName] = moment(
                                component.vdata[key].endDate
                            ).format("YYYY/MM/DD");
                        }
                        break;
                    case "datepicker":
                        if (component.vdata[key])
                            data[key] = component.vdata[key];
                        break;
                }
            });

            return data;
        },

        /* Clean validation message */
        cleanInputSubmit(key) {
            $(`#id_f_inline_${key}`).css({ display: "none" });
            this.onSubmit();
        },

        /* Clean validation message */
        cleanInput(key) {
            $(`#id_f_inline_${key}`).css({ display: "none" });
        },

        /* Handle the submit Form and 
              send the data to store the item */
        onSubmit() {
            if (this.validateForm()) {
                let data = this.prepareData();

                if (this.update) {
                    this.actions
                        .update(this.vdata.id, data, this.$route)
                        .then((response) => {
                            this.$emit("success", response.data.data);
                        })
                        .catch((error, errors) => {
                            let errors_key = Object.keys(error.data.errors);

                            errors_key.forEach(function (key) {
                                $(`#id_f_inline_${key}`).css({
                                    display: "block",
                                });
                                $(`#id_f_inline_${key}`).html(
                                    error.data.errors[key]
                                );
                            });
                        });
                } else {
                    this.actions
                        .create(data, this.$route)
                        .then((response) => {
                            this.$emit("success", response.data.data);
                        })
                        .catch((error, errors) => {
                            let errors_key = Object.keys(error.data.errors);

                            errors_key.forEach(function (key) {
                                $(`#id_f_inline_${key}`).css({
                                    display: "block",
                                });
                                $(`#id_f_inline_${key}`).html(
                                    error.data.errors[key]
                                );
                            });
                        });
                }
            }
        },
        
        unlock(item,key) {
            let component = this;
            let fields_keys = Object.keys(this.fields);
            let caller = item;
            let callerKey = key;
            let dlist = this.datalists;
            
            if(caller.isLocker){
                fields_keys.forEach(function (key) {
                    const lockedItem = component.fields[key];
                    if (lockedItem.selectLock && callerKey==lockedItem.locking){
                        const opts = dlist[lockedItem.all_options];
                        const lockTracker = lockedItem.lock_tracker;
                        const tracker = lockedItem.trackby;
                        const validator = component.vdata[callerKey];
                        if(validator != null){
                            lockedItem.options = [];
                            lockedItem.disabled = false;
                            opts.forEach(function(opt) {
                            if(opt[lockTracker] == validator.id){
                                lockedItem.options.push({id:opt.id,name:opt[tracker]});
                                }
                            });
                        } else {
                            component.vdata[key] = "";
                            lockedItem.disabled = true;
                        }
                    }   
                });
            } 
        },

        showHidden(item,key) {
            let component = this;
            let fields_keys = Object.keys(this.fields);
            let caller = item;
            let callerKey = key;

            if(component.vdata[callerKey]==null || component.vdata[callerKey].name!=caller.showCondition){
                console.log(caller.parentId,'not good')
                /**if(caller.parentId == component.fields[caller.showCondition].parentId){
                    component.fields[caller.hiding].hidden = true;
                    component.vdata[caller.hiding] = null;               
                }**/
            }else if(component.vdata[callerKey].name==caller.showCondition){
                console.log(caller.parentId,'is good')
            }
        },

        getOptions(options){
            if(typeof options=="string"){
                return this.datalists[options]
            } else {
                return options
            }
        }
    },
    watch: {
        data: {
            handler(val, oldval) {
                this.vdata = val;
            },
            deep: true,
        },
    },
};
</script>