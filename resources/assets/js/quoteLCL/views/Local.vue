<template>
    <div style="padding: 0px 25px">

        <b-card class="q-card">

            <div class="row justify-content-between">

                <!-- Titulo y Pais -->
                <div class="col-12 col-lg-6 d-flex align-items-center">

                    <h5 style="line-height: 2">
                        <b>Local Charges at:</b>
                    </h5>

                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Port"
                        class="q-select ml-3">
                    </multiselect>

                </div>
                <!-- End Titulo y Pais -->

                <!-- Agregar Charges -->
                <div class="col-12 col-lg-6 d-flex justify-content-end align-items-center">

                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Template"
                        class="q-select mr-3"
                        style="position: relative; top: 4px">
                    </multiselect>

                    <button class="btn btn-primary btn-bg" id="show-btn" @click="showModal">+ Add Charges</button>

                </div>
                <!-- End Agregar Charges -->

                <div class="col-12 mt-5">

                    <!-- DataTable -->
                    <b-table-simple hover small responsive borderless :striped="false">

                        <!-- Header table -->
                        <b-thead class="q-thead">

                            <b-tr>
                                <b-th>
                                    <span class="label-text">charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">UNITS</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">RATE</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">TOTAL</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">currency</span>
                                </b-th>
                            </b-tr>

                        </b-thead>

                        <!-- Body table -->
                        <b-tbody>

                            <b-tr class="q-tr">

                                <b-td >
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="TON/M3" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input q-width "></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1000" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Pick a value">
                                    </multiselect>
                                </b-td>
                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>

                            <b-tr>

                                <b-td>
                                    <b-form-input placeholder="Freight" class="q-input"></b-form-input>
                                </b-td>
                                
                                <b-td>
                                    <b-form-input placeholder="TON/M3" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input q-width "></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1000" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Pick a value">
                                    </multiselect>
                                </b-td>
                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>

                            <b-tr class="q-total">

                                <b-td colspan="3"></b-td>

                                <b-td><span><b>Total</b></span></b-td>

                                <b-td><span><b>150</b></span></b-td>

                                <b-td><span><b>EUR</b></span></b-td>

                                <b-td></b-td>

                            </b-tr>

                        </b-tbody>
                        <!-- End Body table -->

                    </b-table-simple>
                    <!-- End DataTable -->

                </div>

            </div>

        </b-card>

        <!-- Remarks -->
        <b-card class="mt-5">

            <h5 class="q-title">Remarks</h5>
            <textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea>
            
        </b-card>

        <!--  Modal  -->
        <b-modal ref="my-modal" size="xl" centered hide-footer title="Add Charges">
            
            <div class="row">

                <div class="col-12 col-lg-6 d-flex alig-items-center">

                    <h5><b>Origin Costs at:</b></h5>

                    <span class="ml-3">
                        <img src="https://i.ibb.co/ZTq7994/spain.png" alt="bandera">
                        Barcelona, ESBCN
                    </span>

                </div>

                <div class="col-12 mt-5">

                    <!-- DataTable -->
                    <b-table-simple hover small responsive borderless>

                        <!-- Header table -->
                        <b-thead class="q-thead">

                            <b-tr>

                                <b-th></b-th>

                                <b-th>
                                    <span class="label-text">charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Show  As</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">provider</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Units </span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">RATE</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Profit</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">TOTAL</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">currency</span>
                                </b-th>

                                <b-th></b-th>

                            </b-tr>

                        </b-thead>

                        <b-tbody>
                        
                            <b-tr class="q-tr">
                                
                                <b-td>
                                    <b-form-checkbox value="carrier"></b-form-checkbox>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Per Container" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="-">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="MSC" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                 <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>

                             <b-tr class="q-tr">
                                
                                <b-td>
                                    <b-form-checkbox value="carrier"></b-form-checkbox>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="Per Container" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="-">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="MSC" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                 <b-td>
                                    <b-form-input placeholder="1500" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <multiselect
                                        v-model="value"
                                        :options="options"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :show-labels="false"
                                        placeholder="Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>

                            <b-tr class="q-total">

                                <b-td colspan="7"></b-td>

                                <b-td><span><b>Total</b></span></b-td>

                                <b-td><span><b>150</b></span></b-td>

                                <b-td><span><b>EUR</b></span></b-td>

                                <b-td></b-td>

                            </b-tr>

                        </b-tbody>
                        
                    </b-table-simple>
                    <!-- End DataTable -->

                </div>

                <div class="col-12 d-flex justify-content-end mb-5 mt-3">

                    <button class="btn btn-link mr-2">+ Add New</button>
                    <button class="btn btn-primary btn-bg">+ Add Charges</button>

                </div>

            </div>

        </b-modal>
        <!--  End Modal  -->

    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
export default {
    components: {
        Multiselect
    },
    data() {
        return {
        openModal: false,
        vdata: {},
        value: "",
        options: [
            "Select option",
            "options",
            "selected",
            "mulitple",
            "label",
            "searchable",
            "clearOnSelect",
            "hideSelected",
            "maxHeight",
            "allowEmpty",
            "showLabels",
            "onChange",
            "touched"
        ]
        };
    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show();
        },
    }
};
</script>