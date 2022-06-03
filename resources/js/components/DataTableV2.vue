<template>
    <div>
        <!-- Search Input -->
        <div v-if="searchBar" class="row my-3">
            <div class="col-12 col-sm-4">
                <b-form inline>
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <b-input
                        id="inline-form-input-name"
                        class="mb-2 mr-sm-2 mb-sm-0"
                        v-model="search"
                        placeholder="Search"
                    ></b-input>
                </b-form>
            </div>
        </div>
        <!-- DataTable --> 
        <b-table-simple small responsive borderless :class="classTable">
            <!-- Header table -->
            <b-thead>
                <b-tr>
                    <!-- check boxs -->
                    <b-th>
                        <b-form-checkbox
                            v-if="massiveSelect"
                            v-model="allSelected"
                            :indeterminate="false"
                            @change="toggleAll"
                            class="checkbox-thead"
                        >
                        </b-form-checkbox>
                    </b-th>

                    <!-- header filters prueba --> 
                    <b-th v-for="(field, key) in fields" :key="`filter_${key}`" :id="field.key">
                        <HeaderFilters 
                            v-if="field.filterisOpen"
                            @onNewFilterValues="onFilters"
                            :field="field"
                            :options="options[field.key]" />
                        <div v-else>
                            {{ field.label }}
                        </div>   
                    </b-th>

                    <!-- header options -->
                    <b-th>
                        <b-button
                            v-bind:id="'popover_all'"
                            class="action-app action-thead"
                            href="#"
                            tabindex="0"
                            ><i class="fa fa-ellipsis-h" aria-hidden="true"></i
                        ></b-button>
                        <b-popover
                            v-bind:target="'popover_all'"
                            class="btns-action"
                            variant=""
                            triggers="focus"
                            placement="bottomleft"
                        >
                            <button
                                v-if="massiveactions.includes('delete')"
                                class="btn-action"
                                v-on:click="onDeleteAll()"
                            >
                                Delete
                            </button>
                            <button
                                v-if="
                                    massiveactions.includes(
                                        'changecontainersview'
                                    )
                                "
                                class="btn-action"
                                v-on:click="onChangeContainersView()"
                            >
                                Change View Container
                            </button>
                            <button
                                v-if="
                                    massiveactions.includes(
                                        'openmodalcontainer'
                                    )
                                "
                                class="btn-action"
                                v-on:click="onOpenModalContainer()"
                            >
                                Edit Multiple Containers
                            </button>
                            <button
                                v-if="
                                    massiveactions.includes(
                                        'openmodalharbororigin'
                                    )
                                "
                                class="btn-action-harbor"
                                v-on:click="onOpenModalHarborOrig()"
                            >
                                Edit Multiple Origin Harbors
                            </button>
                            <button
                                v-if="
                                    massiveactions.includes(
                                        'openmodalharbordestination'
                                    )
                                "
                                class="btn-action-harbor"
                                v-on:click="onOpenModalHarborDest()"
                            >
                                Edit Multiple Destination Harbors
                            </button>
                        </b-popover>
                    </b-th>
                </b-tr>
            </b-thead>

            <!-- Loader gif -->
            <b-tbody v-if="isBusy">
                <b-tr class="b-table-busy-slot">
                    <b-td :colspan="fields.length" role="cell" class="">
                        <div class="text-center text-primary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Loading...</strong>
                        </div>
                    </b-td>
                </b-tr>
            </b-tbody>
            <!-- Loader gif -->

            <!-- Body table -->
            <b-tbody v-if="!isBusy" style="border-bottom: 1px solid #eee">
                <!-- Form add new item -->
                <b-tr v-if="!isEmpty(inputFields) && addTableInsert" :id="key">
                    <b-td v-if="firstEmpty"></b-td>

                    <b-td
                        v-for="(item, key) in inputFields"
                        :key="key"
                        :style="'max-width:' + item.width"
                    >
                        <!-- Text Input -->
                        <div v-if="item.type == 'text'">
                            <b-form-input
                                v-model="fdata[key]"
                                :placeholder="item.placeholder"
                                :disabled="item.disabled"
                                :id="key"
                                @change="cleanInput(key)"
                            >
                            </b-form-input>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                            ></span>
                        </div>
                        <!-- End Text Input -->
                        <!-- Text Input -->
                        <div v-if="item.type == 'number'">
                            <b-form-input
                                v-model="fdata[key]"
                                :placeholder="item.placeholder"
                                :disabled="item.disabled"
                                :id="key"
                                @change="cleanInput(key)"
                                @keypress="isNumber($event)"
                            >
                            </b-form-input>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                            ></span>
                        </div>
                        <!-- End Text Input -->

                        <!-- Based Dinamical Select Input -->
                        <div v-if="item.type == 'pre_select' && refresh">
                            <multiselect
                                v-model="fdata[key]"
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
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                                style="margin-top: -4px"
                            ></span>
                        </div>
                        <!-- Based Dinamycal Select -->

                        <!-- Select Input -->
                        <div v-if="item.type == 'select'" :id="key">
                            <multiselect
                                v-model="fdata[key]"
                                :id="key"
                                :multiple="false"
                                :options="datalists[item.options]"
                                :disabled="item.disabled"
                                :searchable="item.searchable"
                                :close-on-select="true"
                                :clear-on-select="false"
                                track-by="id"
                                :label="item.trackby"
                                :show-labels="false"
                                :placeholder="item.placeholder"
                                :class="view"
                                @select="cleanInput(key)"
                            >
                            </multiselect>
                            <!-- :class="item.class" -->
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                                style="margin-top: -4px"
                            ></span>
                        </div>
                        <!-- End Select -->

                        <!-- MultiSelect Input -->
                        <div
                            v-if="item.type == 'multiselect' && refresh"
                            :id="key"
                        >
                            <multiselect
                                v-model="fdata[key]"
                                :multiple="true"
                                :options="datalists[item.options]"
                                :searchable="item.searchable"
                                :close-on-select="true"
                                :clear-on-select="true"
                                track-by="id"
                                :id="key"
                                :class="view"
                                :label="item.trackby"
                                :show-labels="false"
                                :placeholder="item.placeholder"
                                @input="refreshValues"
                                @select="cleanInput(key)"
                            >
                            </multiselect>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                                style="margin-top: -4px"
                            ></span>
                        </div>

                        <div
                            v-if="item.type == 'multiselect_data' && refresh"
                            :id="key"
                        >
                            <multiselect
                                v-model="item.values"
                                :multiple="true"
                                :options="datalists[item.options]"
                                :searchable="item.searchable"
                                :close-on-select="true"
                                :clear-on-select="true"
                                track-by="id"
                                :id="key"
                                :class="view"
                                :label="item.trackby"
                                :show-labels="false"
                                :placeholder="item.placeholder"
                                @input="refreshValues"
                                @select="cleanInput(key)"
                            >
                            </multiselect>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                                style="margin-top: -4px"
                            ></span>
                        </div>
                        <!-- End Select -->
                    </b-td>

                    <b-td>
                        <b-button
                            class="action-app"
                            href="#"
                            tabindex="0"
                            v-on:click="onSubmit()"
                            ><i class="fa fa-check" aria-hidden="true"></i
                        ></b-button>
                        <b-button
                            v-if="changeAddMode"
                            class="action-app"
                            href="#"
                            tabindex="0"
                            v-on:click="addInsert()"
                            ><i class="fa fa-minus" aria-hidden="true"></i
                        ></b-button>
                    </b-td>
                </b-tr>
                <!-- End of form -->

                <!-- Extra fixed autoupdate form -->
                <b-tr v-if="!isEmpty(extraFields) && extraRow == true">
                    <b-td v-if="firstEmpty"></b-td>

                    <b-td
                        v-for="(item, key) in extraFields"
                        :key="key"
                        :style="'max-width:' + item.width"
                    >
                    
                        <!-- Text field -->
                        <div v-if="item.type == 'extraText'">
                            <b-form-input
                                v-model="fixedData[key]"
                                :placeholder="item.placeholder"
                                :disabled="item.disabled"
                                :id="key"
                                v-on:blur="onSubmitFixed()"
                            >
                            </b-form-input>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                            ></span>
                        </div>
                        <!-- Text field end -->

                        <!-- Select field -->
                        <div v-if="item.type == 'extraSelect'">
                            <multiselect
                                v-model="fixedData[key]"
                                :id="key"
                                :multiple="false"
                                :disabled="item.disabled"
                                :options="datalists[item.options]"
                                :searchable="item.searchable"
                                :close-on-select="true"
                                :clear-on-select="false"
                                track-by="id"
                                :label="item.trackby"
                                :show-labels="false"
                                :placeholder="item.placeholder"
                                @input="onSubmitFixed()"
                                :class="item.class"
                            >
                            </multiselect>
                            <span
                                :id="'id_f_table_' + key"
                                class="invalid-feedback"
                                style="margin-top: -4px"
                            ></span>
                        </div>
                        <!-- Select field end -->
                    </b-td>
                </b-tr>
                <!-- Extra form end -->

                <!-- Data List -->
                <b-tr v-for="(item, key) in data" :key="key" :id="key">
                    <!-- Checkbox column -->
                    <b-td>
                        <b-form-checkbox-group>
                            <b-form-checkbox
                                v-bind:value="item"
                                v-bind:id="'check' + item.id"
                                v-model="selected"
                            >
                            </b-form-checkbox>
                        </b-form-checkbox-group>
                    </b-td>
                    <!-- end Checkbox column -->

                    <!-- Fields data -->
                    <b-td v-for="(col, inKey) in fields" :key="inKey" :id="col.key">
                        <div v-if="autoupdateDataTable">
                            <b-form-input
                                v-if="col.type == 'text'"
                                v-model="item[col.key]"
                                :disabled="col.disabled"
                                :id="String(item['id'])"
                                @blur="onSubmitAutoupdate(item['id'])"
                            ></b-form-input>

                            <multiselect
                                v-else-if="col.type == 'select'"
                                v-model="item[col.key]"
                                :searchable="true"
                                :disabled="col.disabled"
                                :close-on-select="true"
                                :options="datalists[col.options]"
                                :show-labels="false"
                                :id="String(item['id'])"
                                :label="col.trackby"
                                :track-by="col.trackby"
                                @input="onSubmitAutoupdate(item['id'])"
                            >
                            </multiselect>
                        </div>

                        <div v-else>
                            <span
                                v-if="'formatter' in col"
                                v-html="col.formatter(item[col.key])"
                            ></span>
                            <div v-else-if="'collapse' in col">
                                <b-button
                                    v-if="item[col.key].length > 1"
                                    v-b-toggle="'collapse' + key + inKey"
                                    variant="primary"
                                    >{{ col.collapse }}</b-button
                                >
                                <b-collapse
                                    v-if="item[col.key].length > 1"
                                    :id="'collapse' + key + inKey"
                                >
                                    <b-card>
                                        <li
                                            v-for="(address, addKey) in item[col.key]"
                                            :key="addKey"
                                        >
                                            {{ address }}
                                        </li>
                                    </b-card>
                                </b-collapse>
                                <span v-else-if="item[col.key].length == 1">{{
                                    item[col.key][0]
                                }}</span>
                                <span v-else>--</span>
                            </div>
                            <span v-else>{{ item[col.key] }}</span>
                        </div>
                    </b-td>
                    <!-- End Fields Data -->

                    <!-- Actions column -->
                    <b-td>
                        <b-button
                            v-bind:id="'popover' + item.id"
                            class="action-app"
                            href="#"
                            tabindex="0"
                            ><i class="fa fa-ellipsis-h" aria-hidden="true"></i
                        ></b-button>
                        <b-popover
                            v-bind:target="'popover' + item.id"
                            class="btns-action"
                            variant=""
                            triggers="focus"
                            placement="bottomleft"
                        >
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('edit')"
                                v-on:click="onEdit(item)"
                            >
                                Edit
                            </button>
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('duplicate')"
                                v-on:click="onDuplicate(item.id)"
                            >
                                Duplicate
                            </button>
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('delete')"
                                v-on:click="onDelete(item.id)"
                            >
                                Delete
                            </button>
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('specialduplicate') && item.type != 'LCL'"
                                @click="onSpecialDuplicate(item.id)"
                            >
                                Use as template
                            </button>
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('generatePDF')"
                                v-on:click="onGeneratePDF(item)"
                            >
                                Generate PDF
                            </button>
                            <button
                                class="btn-action"
                                v-if="singleActions.includes('seeProgressDetails')"
                                @click="onOpenProgressModal(item.id)"
                            >
                                Processing progress
                            </button>
                        </b-popover>
                    </b-td>
                    <!-- End Actions column -->
                </b-tr>
                <!-- End Data list -->
            </b-tbody>
            <!-- Body table -->


           
        </b-table-simple>
        <!-- End DataTable -->
        <p v-if="totalResults">Total Results: {{ this.totalData }}</p>
        
        <!-- Pagination -->

        <paginate
            v-if="paginated"
            :page-count="pageCount"
            :click-handler="clickCallback"
            :prev-text="'Prev'"
            :next-text="'Next'"
            :page-class="'page-item'"
            :page-link-class="'page-link'"
            :container-class="'pagination'"
            :prev-class="'page-item'"
            :prev-link-class="'page-link'"
            :next-class="'page-item'"
            :next-link-class="'page-link'"
            :initialPage="initialPage"
        >
        </paginate>
        <!-- Pagination end -->
        
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import paginate from "./paginate";
import HeaderFilters from "./HeaderFilters";

export default {
    props: {
        totalResults: Boolean,
        classTable: String,
        view: String,
        filter: {
            type: Boolean,
            required: false,
            default: false,
        },
        fields: Array,
        equipment: Object,
        inputFields: {
            type: Object,
            required: false,
            default: () => {
                return {};
            },
        },
        vdatalists: {
            type: Object,
            required: false,
            default: () => {
                return {};
            },
        },
        massiveactions: {
            type: Array,
            required: false,
            default: () => {
                return ["delete"];
            },
        },
        singleActions: {
            type: Array,
            required: false,
            default: () => {
                return ["edit", "duplicate", "delete"];
            },
        },
        actions: Object,
        totalActions: Object,
        firstEmpty: {
            type: Boolean,
            required: false,
            default: true,
        },
        searchBar: {
            type: Boolean,
            required: false,
            default: true,
        },
        multiList: {
            type: Boolean,
            required: false,
            default: false,
        },
        multiId: {
            type: Number,
            required: false,
            default: 1,
        },
        paginated: {
            type: Boolean,
            required: false,
            default: true,
        },
        extraRow: {
            type: Boolean,
            required: false,
            default: false,
        },
        extraFields: {
            type: Object,
            required: false,
            default: () => {
                return {};
            },
        },
        withTotals: {
            type: Boolean,
            required: false,
            default: false,
        },
        totalsFields: {
            type: Object,
            required: false,
            default: () => {
                return {};
            },
        },
        autoupdateDataTable: {
            type: Boolean,
            required: false,
            default: false,
        },
        portType: {
            type: String,
            required: false,
            default: "",
        },
        autoAdd: {
            type: Boolean,
            required: false,
            default: true,
        },
        changeAddMode: {
            type: Boolean,
            required: false,
            default: false,
        },
        massiveSelect: {
            type: Boolean,
            required: false,
            default: true,
        },
    },
    components: {
        Multiselect,
        paginate,
        HeaderFilters
    },
    data() {
        return {
            totalData: '',
            isBusy: false,
            data: {},
            fdata: {},
            currentData: [],
            totalsData: [],
            fixedData: [],
            autoupdateTableData: [],
            refresh: true,
            autoAddRequested: false,
            datalists: {},
            search: null,
            /* Pagination */
            initialPage: 1,
            pageCount: 0,
            /* Checkboxes */
            selected: [],
            allSelected: false,
            indeterminate: false,
            filterOptions: {},
            filtered: {},
            filterSet: false,
            fullListData: {},
            data_cliente:null,
            field_filter:null,

            options: [],
            filterValues: {}
        };
    },
    computed: {
        addTableInsert: function () {
            if (this.autoAddRequested || this.autoAdd) {
                return true;
            } else if (!this.autoAdd) {
                return false;
            }
        },
    },
    created() {
        this.initialData();
        this.updateDinamicalFieldOptions();
    },
    methods: {
        onFilters(filterObject) {
            this.isBusy = true;  
            
            /* Refresh */   
            this.$router.push({});
            this.initialPage = 1;

            this.filterValues[filterObject.key] = filterObject.values;            
            this.actions.list(   
                { 
                    params: this.filterValues,
                    page: this.initialPage
                },
                (err, data) => {
                    this.setData(err, data);
                }
            );
        },


        initialData() {

            this.isBusy = true;
            let params = this.$route.query;
            if (params.page) this.initialPage = Number(params.page);
            this.getData(params);
            
        },

        /* Request the data with axios */
        getData(pageNumber = {}) { 
            
            //Cleaning data array
            this.data = {};
            console.log('prueba', pageNumber.q);
            if (!this.multiList) {  
                this.actions.list( 
                    {
                        params: this.filterValues, 
                        page: pageNumber.page,
                        q: pageNumber.q
                    },
                    (err, data) => {
                        this.setData(err, data);
                    },
                    this.$route
                );
            }            
        },

        /* Set the data into datatable */
        setData(err, { data: records, links, meta }) {
            this.isBusy = false;
            this.totalData = meta.total;

            if (err) {
                this.error = err.toString();
            } else {
                this.data = records;
                this.autoupdateTableData = records;
                this.pageCount = Math.ceil(meta.total / meta.per_page);
            }
        },

        /* Refresh Data */
        refreshData() {
            this.$router.push({});
            this.initialPage = 1;
            this.initialData({});
        },

        /* Pagination Callback */
        clickCallback(pageNum) {
            this.isBusy = true;

            let qs = {
                page: pageNum,
            };

            if (this.$route.query.sort) qs.sort = this.$route.query.sort;
            if (this.$route.query.q) qs.q = this.$route.query.q;

            this.routerPush(qs);
        },

        /* Update url and execute api call */
        routerPush(qs) {
            this.$router.push({ query: qs }); 
            console.log('qs', qs);
            this.getData(qs);
        },

        /* Prepare data to submit */
        prepareData(type) {
            let data = {};
            let keys = [];

            if (type == "table") {
                for (const key in this.inputFields) {
                    keys.push(key);
                    if (this.inputFields[key].type == "text")
                        data[key] = this.fdata[key];
                    else if(this.inputFields[key].type == "number"){
                        data[key] = this.fdata[key];
                    }
                    else if (
                        ["select", "pre_select"].includes(
                            this.inputFields[key].type
                        ) &&
                        typeof this.fdata[key] !== "undefined"
                    )
                        data[key] = this.fdata[key].id;
                    else if (this.inputFields[key].type == "multiselect") {
                        data[key] = [];

                        this.fdata[key].forEach(function (item) {
                            data[key].push(item.id);
                        });
                    } else if (
                        this.inputFields[key].type == "multiselect_data"
                    ) {
                        data[key] = [];

                        this.inputFields[key].values.forEach(function (item) {
                            data[key].push(item.id);
                        });
                    }
                }

                if (this.autoupdateDataTable) {
                    data["type"] = this.portType;
                }
            } else if (type == "extra") {
                for (const ekey in this.extraFields) {
                    keys.push(ekey);
                    if (this.extraFields[ekey].type == "extraText")
                        data[ekey] = this.fixedData[ekey];
                    else if (
                        this.extraFields[ekey].type == "extraSelect" &&
                        typeof this.fixedData[ekey] !== "undefined"
                    )
                        data[ekey] = this.fixedData[ekey].id;
                }
            } else if (type == "totals") {
                for (const tkey in this.totalsFields) {
                    for (const innerkey in this.totalsFields[tkey]) {
                        keys.push(innerkey);
                        if (this.totalsFields[tkey][innerkey].type == "text")
                            data[innerkey] = this.totalsData[innerkey];
                        else if (
                            this.totalsFields[tkey][innerkey].type ==
                                "select" &&
                            typeof this.totalsData[innerkey] !== "undefined"
                        )
                            data[innerkey] = this.totalsData[innerkey].id;
                    }
                }
            }

            data["keys"] = keys;

            return data;
        },

        /* Clear Form Data */
        clearForm() {
            this.fdata = {};
        },

        /* Set all the checkbox */
        toggleAll(checked) {
            this.selected = checked ? this.data.slice() : []; //Selected all the checkbox
        },

        /* Submit form new data */
        onSubmit() {
            let data = this.prepareData("table");

            //this.isBusy = true;
            if (!this.multiList) {
                this.actions
                    .create(data, this.$route)
                    .then((response) => {
                        this.clearForm();
                        this.refreshData();
                        this.updateDinamicalFieldOptions();
                        if (this.changeAddMode) {
                            this.autoAddRequested = !this.autoAddRequested;
                        }
                    })
                    .catch((error, errors) => {
                        let errors_key = Object.keys(error.data.errors);

                        errors_key.forEach(function (key) {
                            $(`#id_f_table_${key}`).css({ display: "block" });
                            $(`#id_f_table_${key}`).html(
                                error.data.errors[key]
                            );
                        });
                    });
            } else {
                this.actions
                    .create(this.multiId, data, this.$route)
                    .then((response) => {
                        this.clearForm();
                        this.refreshData();
                        this.updateDinamicalFieldOptions();
                        if (this.changeAddMode) {
                            this.autoAddRequested = !this.autoAddRequested;
                        }
                    })
                    .catch((error, errors) => {
                        let errors_key = Object.keys(error.data.errors);

                        errors_key.forEach(function (key) {
                            $(`#id_f_table_${key}`).css({ display: "block" });
                            $(`#id_f_table_${key}`).html(
                                error.data.errors[key]
                            );
                        });
                    });
            }
        },

        onSubmitFixed() {
            let data = this.prepareData("extra");

            //this.isBusy = true;
            if (!this.multiList) {
                this.actions
                    .update(data, this.$route)
                    .then((response) => {
                        this.updateDinamicalFieldOptions();
                        this.refreshData();
                    })
                    .catch((error, errors) => {
                        let errors_key = Object.keys(error.data.errors);

                        errors_key.forEach(function (key) {
                            $(`#id_f_table_${key}`).css({ display: "block" });
                            $(`#id_f_table_${key}`).html(
                                error.data.errors[key]
                            );
                        });
                    });
            } else {
                this.actions
                    .update(this.fixedData.id, data, this.$route)
                    .then((response) => {
                        this.updateDinamicalFieldOptions();
                        this.refreshData();
                    })
                    .catch((error, errors) => {
                        let errors_key = Object.keys(error.data.errors);

                        errors_key.forEach(function (key) {
                            $(`#id_f_table_${key}`).css({ display: "block" });
                            $(`#id_f_table_${key}`).html(
                                error.data.errors[key]
                            );
                        });
                    });
            }
        },

        onSubmitTotals() {
            let data = this.prepareData("totals");

            if (!this.multiList) {
                this.totalActions
                    .update(data, this.$route)
                    .then((response) => {
                        this.updateDinamicalFieldOptions();
                        this.refreshData();
                    })
                    .catch((error, errors) => {
                        let errors_key = Object.keys(error.data.errors);

                        errors_key.forEach(function (key) {
                            $(`#id_f_table_${key}`).css({ display: "block" });
                            $(`#id_f_table_${key}`).html(
                                error.data.errors[key]
                            );
                        });
                    });
            } else {
                if (!this.autoupdateDataTable) {
                    this.totalActions
                        .update(this.multiId, data, this.$route)
                        .then((response) => {
                            this.updateDinamicalFieldOptions();
                            this.refreshData();
                        })
                        .catch((error, errors) => {
                            let errors_key = Object.keys(error.data.errors);

                            errors_key.forEach(function (key) {
                                $(`#id_f_table_${key}`).css({
                                    display: "block",
                                });
                                $(`#id_f_table_${key}`).html(
                                    error.data.errors[key]
                                );
                            });
                        });
                } else {
                    this.totalActions
                        .updateTotals(this.multiId, data, this.$route)
                        .then((response) => {
                            this.updateDinamicalFieldOptions();
                            this.refreshData();
                        })
                        .catch((error, errors) => {
                            let errors_key = Object.keys(error.data.errors);

                            errors_key.forEach(function (key) {
                                $(`#id_f_table_${key}`).css({
                                    display: "block",
                                });
                                $(`#id_f_table_${key}`).html(
                                    error.data.errors[key]
                                );
                            });
                        });
                }
            }
        },

        onSubmitAutoupdate(id) {
            let component = this;
            let uData = {};
            let keys = [];

            component.data.forEach(function (item) {
                for (const key in component.inputFields) {
                    if (item["id"] == id) {
                        keys.push(key);
                        uData[key] = item[key];
                    }
                }
            });

            uData["keys"] = keys;

            this.actions.update(id, uData, this.$route).then((response) => {
                this.updateDinamicalFieldOptions();
                this.refreshData();
            });
        },

        /* Single Actions */
        onEdit(data) {
            this.currentData = data;
            this.$bvModal.show("editModal");
            this.$emit("onEdit", data);
            this.refreshData();
        },
        onGeneratePDF(data) {
            this.currentData = data;
            this.$emit("onGeneratePDF", data);            
        },
        onDelete(id) {
            swal({
                title: 'Are you sure?',
                text: "You will not be able to reverse this!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonClass: 'btn btn-danger',
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'Yes, delete it!',
            })
                .then((result)=> {
                    if(result.value){
                    this.isBusy = true;
                        this.actions
                            .delete(id)
                            .then((response) => {
                                this.refreshData();
                            }) 
                    }  
                })    
        },

        onDeleteAll() {
            this.isBusy = true;

            let ids = this.selected.map((item) => item.id);

            this.actions
                .deleteAll(ids)
                .then((response) => {
                    this.refreshData();
                })
                .catch((data) => {});
        },

        onDuplicate(id) {
            this.isBusy = true;

            this.actions
                .duplicate(id, {})
                .then((response) => {
                    this.refreshData();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },

        onSpecialDuplicate(quote_id){
            let searchRequestType = 1;
            var searchRequest = { renew: true };

            this.actions
                .updateSearch(
                    quote_id,
                    searchRequest
                )
                .then((response) => {
                    this.$router.push({ name: 'searchV2.index', query: { requested: searchRequestType, model_id: quote_id } });
                    this.$router.go();
                })
        },
        /* End single actions */

        closeModal(modal) {
            this.$bvModal.hide(modal);
        },

        resetDinamicalFields(target) {
            for (const key in this.inputFields) {
                if (this.inputFields[key]["options"] == target)
                    this.fdata[key] = [];
            }
        },

        dispatch(val, item) {
            this.refresh = false;
            this.datalists[
                "ori_" + this.inputFields[item].target
            ] = this.datalists["ori_" + val.vselected];
            this.datalists[
                "des_" + this.inputFields[item].target
            ] = this.datalists["des_" + val.vselected];
            this.resetDinamicalFields(this.inputFields[item].target);
            this.refresh = true;
            $(`#id_f_table_${item}`).css({ display: "none" });
        },

        /* Clean validation message */
        cleanInput(key) {
            $(`#id_f_table_${key}`).css({ display: "none" });
        },

        refreshValues(val, item) {
            let component = this;
            component.refresh = false;
            setTimeout(function () {
                component.refresh = true;
            }, 0.4);
        },

        updateDinamicalFieldOptions() {
            this.actions
                .filterOptions()
                .then((response) => {
                    this.options = response.data;
                })
                .catch((data) => {
                    console.log("error")
                });
        },

        isEmpty(obj) {
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) return false;
            }
            return true;
        },

        onChangeContainersView() {
            this.$emit("onChangeContainersView", true);
        },

        onOpenModalContainer() {
            let ids = this.selected.map((item) => item.id);
            this.$emit("onOpenModalContainerView", ids);
        },

        onOpenModalHarborOrig() {
            let ids = this.selected.map((item) => item.id);
            this.$emit("onOpenModalHarborOrigView", ids);
        },
        onOpenModalHarborDest() {
            let ids = this.selected.map((item) => item.id);
            this.$emit("onOpenModalHarborDestView", ids);
        },
        onOpenProgressModal(id) {
            this.$emit("onOpenModalProgressDetails", id);
        },

        addInsert() {
            this.autoAddRequested = !this.autoAddRequested;
        },

        isNumber: function (evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (
                charCode > 31 &&
                (charCode < 48 || charCode > 57) &&
                charCode !== 46
            ) {
                evt.preventDefault();
            } else {
                return true;
            }
        },
    },
    watch: {
        vdatalists: {
            handler(val, oldval) {
                this.updateDinamicalFieldOptions();
            },
            deep: true,
        },
        selected() {
            this.$emit("input", this.selected);
        },
        search: {
            handler: function (val, oldVal) {
                let qs = { q: val };
                
                this.routerPush(qs);
            },
        },
    },
};
</script>