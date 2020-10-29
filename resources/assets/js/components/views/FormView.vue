<template>
    <div>
        <b-form ref="form" class="modal-input">
            <div class="row">
                <div
                    v-for="(item, key) in fields"
                    :key="key"
                    :class="getClass(item)"
                >
                    <!-- Text Field -->
                    <div v-if="item.type == 'text'">
                        <b-form-group
                            :label="item.label"
                            :invalid-feedback="key + ' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <b-form-input
                                v-model="vdata[key]"
                                :placeholder="item.placeholder"
                                :id="key"
                                @change="cleanInput(key)"
                            >
                            </b-form-input>

                            <span
                                :id="'id_f_' + key"
                                class="invalid-feedback"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End Text Field -->

                    <!-- Based Dinamical Select Input -->
                    <div v-if="item.type == 'pre_select' && refresh">
                        <b-form-group
                            :label="item.label"
                            :invalid-feedback="key + ' is required'"
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
                                @select="dispatch"
                            >
                            </multiselect>
                            <span
                                :id="'id_f_' + key"
                                class="invalid-feedback"
                                style="margin-top: -5px"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- Based Dinamycal Select -->

                    <!-- Select Field -->
                    <div v-if="item.type == 'select'">
                        <b-form-group
                            :label="item.label"
                            :invalid-feedback="key + ' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <multiselect
                                :id="key"
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
                                @select="cleanInput(key)"
                            >
                            </multiselect>

                            <span
                                :id="'id_f_' + key"
                                class="invalid-feedback"
                                style="margin-top: -5px"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End Select Field -->

                    <!-- MultiSelect Field -->
                    <div v-if="item.type == 'multiselect' || item.type == 'multiselect_data'">
                        <b-form-group
                            :label="item.label"
                            :invalid-feedback="key + ' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <multiselect
                                :id="key"
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
                                @select="cleanInput(key)"
                            >
                            </multiselect>
                            <span
                                :id="'id_f_' + key"
                                class="invalid-feedback"
                                style="margin-top: -5px"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End MultiSelect Field -->

                    <!-- DateRange Field -->
                    <div v-if="item.type == 'daterange'">
                        <b-form-group
                            :label="item.label"
                            :invalid-feedback="key + ' is required'"
                            valid-feedback="key+' is done!'"
                        >
                            <date-range-picker
                                :id="key"
                                :opens="'center'"
                                :locale-data="{
                                    firstDay: 1,
                                    format: 'yyyy/mm/dd',
                                }"
                                :singleDatePicker="false"
                                :autoApply="true"
                                :timePicker="false"
                                v-model="vdata[key]"
                                :linkedCalendars="true"
                                @select="cleanInput(key)"
                            >
                            </date-range-picker>
                            <span
                                :id="'id_f_' + key"
                                class="invalid-feedback"
                            ></span>
                        </b-form-group>
                    </div>
                    <!-- End DateRange Field -->
                </div>

                <div class="btns-form-modal" v-if="!download">
                    <button class="btn" @click="close" type="button">
                        Cancel
                    </button>
                    <button
                        class="btn btn-primary btn-bg"
                        type="button"
                        @click="onSubmit"
                    >
                        {{ btnTxt }}
                    </button>
                </div>
                <div class="btns-form-modal" v-if="download">
                    <button class="btn" @click="close" type="button">
                        Cancel
                    </button>
                    <button
                        class="btn btn-primary btn-bg"
                        type="button"
                        @click="downloadFile"
                    >
                        <span v-if="!downloading">{{ btnTxt }}</span>
                        <span v-if="downloading">Processing <i class="fa fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </b-form>
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
        vdatalists: {
            type: Object,
            required: false,
            default: () => {
                return {};
            },
        },
        massivedata: {
            type: Array,
            required: false,
            default: () => {
                return [];
            },
        },
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
        massivechange: {
            type: Boolean,
            required: false,
            default: false,
        },
        multi: {
            type: Boolean,
            required: false,
            default: false,
        },
        multiId: {
            type: Number,
            required: false,
            deafult: 1,
        },
        download: {
            type: Boolean,
        },
    },
    data() {
        return {
            vdata: {},
            datalists: {},
            refresh: true,
            downloading: false,
        };
    },
    created() {
        this.vdata = Object.assign({}, this.data);
        this.updateDinamicalFieldOptions();
    },
    methods: {
        /* Dispatch an event when click in cancel close */
        close() {
            this.$emit("exit", true);
        },

        /* Set the class of the field */
        getClass(item) {
            if ("colClass" in item) return item.colClass;

            return "col-sm-6";
        },

        /* Reset the Dynamical Fields */
        resetDynamicalFields(target) {
            for (const key in this.fields) {
                if (this.fields[key]["options"] == target)
                    this.vdata[key] = null;
            }
        },

        /* Execute when pre select field is updated */
        dispatch(val, item) {
            this.refresh = false;
            this.datalists["ori_" + this.fields[item].target] = this.datalists[
                "ori_" + val.vselected
            ];
            this.datalists["des_" + this.fields[item].target] = this.datalists[
                "des_" + val.vselected
            ];
            this.resetDynamicalFields(this.fields[item].target);
            this.refresh = true;
            $(`#id_f_${item}`).css({ display: "none" });
        },

        /* Update Dynamical Fields */
        updateDinamicalFieldOptions() {
            this.datalists = JSON.parse(JSON.stringify(this.vdatalists));

            for (const key in this.fields) {
                if (this.fields[key]["type"] == "pre_select") {
                    this.datalists[
                        "ori_" + this.fields[key]["target"]
                    ] = this.datalists[
                        "ori_" + this.fields[key]["initial"].vselected
                    ];
                    this.datalists[
                        "des_" + this.fields[key]["target"]
                    ] = this.datalists[
                        "des_" + this.fields[key]["initial"].vselected
                    ];
                }
            }
        },

        /* Clean validation message */
        cleanInput(key) {
            $(`#id_f_${key}`).css({ display: "none" });
        },

        /* Check if value is empty by type */
        isEmpty(value) {
            //console.log(typeof value);
            if (typeof value == "string") return value == "" || value == null;

            if (typeof value == "object")
                return value == null || Object.keys(value).length === 0;

            return false;
        },

        /* Validate Form */
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

        /* Prepare the data to submit */
        prepareData() {
            let data = {};
            let component = this;
            let fields_keys = Object.keys(this.fields);

            fields_keys.forEach(function (key) {
                const item = component.fields[key];

                switch (item.type) {
                    case "text":
                        if (component.vdata[key])
                            data[key] = component.vdata[key];
                        break;
                    case "pre_select":
                    case "select":
                        if (component.vdata[key])
                            data[key] = component.vdata[key].id;
                        break;
                    case "multiselect":
                        if (
                            key in component.vdata &&
                            component.vdata[key].length > 0
                        )
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
                }

                data["keys"] = fields_keys;

                if (component.massivechange)
                    data["ids"] = component.massivedata;
            });

            return data;
        },

        /* Handle the submit Form and 
              send the data to store the item */
        onSubmit() {
            if (this.validateForm()) {
                let data = this.prepareData();

                if (this.massivechange) {
                    this.actions
                        .massiveChange(data, this.$route)
                        .then((response) => {
                            this.$emit("success", true);
                            this.vdata = {};
                        })
                        .catch((data) => {});
                } else if (this.update) {
                    this.actions
                        .update(this.vdata.id, data, this.$route)
                        .then((response) => {
                            this.$emit("success", response.data.data.id);
                            this.vdata = {};
                        })
                        .catch((error, errors) => {
                            let errors_key = Object.keys(error.data.errors);

                            errors_key.forEach(function (key) {
                                $(`#id_f_${key}`).css({ display: "block" });
                                $(`#id_f_${key}`).html(error.data.errors[key]);
                            });
                        });
                } else {
                    if (!this.multi) {
                        this.actions
                            .create(data, this.$route)
                            .then((response) => {
                                this.$emit("success", response.data.data.id);
                                this.vdata = {};
                            })
                            .catch((error, errors) => {
                                let errors_key = Object.keys(error.data.errors);

                                errors_key.forEach(function (key) {
                                    $(`#id_f_${key}`).css({ display: "block" });
                                    $(`#id_f_${key}`).html(
                                        error.data.errors[key]
                                    );
                                });
                            });
                    } else {
                        this.actions
                            .create(this.multiId, data, this.$route)
                            .then((response) => {
                                this.$emit("success", response.data.data.id);
                                this.vdata = {};
                            })
                            .catch((error, errors) => {
                                let errors_key = Object.keys(error.data.errors);

                                errors_key.forEach(function (key) {
                                    $(`#id_f_${key}`).css({ display: "block" });
                                    $(`#id_f_${key}`).html(
                                        error.data.errors[key]
                                    );
                                });
                            });
                    }
                }
            }
        },
        downloadFile() {
            let data = this.prepareData();
            this.downloading = true;
            axios({
                method: "post",
                url: "/contracts/export",
                responseType: "arraybuffer",
                data: {
                    data,
                },
            })
                .then((response) => {
                    this.forceFileDownload(response, data);
                    this.close();
                    this.downloading = false;
                })
                .catch(() => console.log("error"));
        },

        forceFileDownload(response, data) {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "rates.csv"); //or any other extension
            document.body.appendChild(link);
            link.click();
        },
    },
    watch: {
        vdatalists: {
            handler(val, oldval) {
                this.updateDinamicalFieldOptions();
            },
            deep: true,
        },
    },
};
</script>