<template>
    <div style="padding: 0px 25px">

        <div style="width: 100%" class="mb-3 d-flex justify-content-end">
            <a href="#" id="show-btn" @click="showModal" class="btn btn-link">+ Add Freight</a>
        </div>

        <!-- Freight Card -->
        <b-card class="q-card q-freight-card">

            <div class="row">

                <!-- Logo(compañia), origen, destino y add freight -->
                <div class="col-12 d-flex justify-content-between">
                    
                    <!-- Logo, origen, destino -->
                    <div>

                        <img src="https://i.ibb.co/BNf0WNM/download-logo-HLAG-1.png" alt="logo" class="mr-4">
                    
                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/ZTq7994/spain.png" alt="bandera">
                            Barcelona, ESBCN
                        </span>

                        <i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px"></i>

                        <span class="mr-4 ml-4">
                            <img src="https://i.ibb.co/7WffMF5/china-1-1.png" alt="bandera">
                            Shanghai, CNSHA
                        </span>

                    </div>
                    <!-- End Logo, origen, destino -->

                    <!-- Add Freight -->       
                    <button type="button" class="btn" v-b-toggle="'collapse-1'">
                        <i class="fa fa-angle-down" aria-hidden="true" style="font-size: 35px"></i>
                    </button>

                </div>
                <!-- End Logo(compañia), origen, destino y add freight -->

            </div>

            <b-collapse id="collapse-1" class="row">

                <!-- Inputs Freight -->
                <div class="col-12 d-flex mt-3">

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">transi time</span>
                        <b-form-input placeholder="33" class="q-input"></b-form-input>

                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">services</span>
                        <b-form-input placeholder="Directo" class="q-input"></b-form-input>

                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">valid until</span>
                        <b-form-datepicker 
                            id="valid-datepicker" 
                            v-model="value" 
                            class="mb-2"
                            :locale="locale"
                            :date-format-options="dateFormat">
                        </b-form-datepicker>
                    
                    
                    </label>

                    <label class="q-label mr-5" style="width:15%">

                        <span class="label-text">reference</span>
                        <b-form-input placeholder="QT123456798" class="q-input"></b-form-input>
                    
                    </label>

                </div>
                <!-- End Inputs Freight -->

                <!-- Tabla Freight -->
                <div class="col-12">

                    <!-- DataTable -->
                    <b-table-simple small responsive borderless>

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
                                    <span class="label-text">Minimum</span>
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
                        <!-- End Header table -->

                        <!-- Body table -->
                        <b-tbody >

                            <!-- Data -->
                            <b-tr>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="TON/M3" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="2" class="q-input q-width "></b-form-input>
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
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>
                            <!-- End Data -->

                            <!-- Data -->
                            <b-tr>

                                <b-td>
                                    <b-form-input placeholder="Surcharge" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="TON/M3" class="q-input"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="50" class="q-input q-width"></b-form-input>
                                </b-td>

                                <b-td>
                                    <b-form-input placeholder="2" class="q-input q-width "></b-form-input>
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
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>

                                <b-td>
                                    <button type="button" class="btn-delete">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </b-td>

                            </b-tr>
                            <!-- End Data -->

                            <!-- Profit -->
                            <b-tr>

                                <b-td colspan="4"></b-td>

                                <b-td>
                                    <span><b>Profit</b></span>
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
                                        placeholder="Select Currency">
                                    </multiselect>
                                </b-td>
                                
                                <b-td></b-td>

                            </b-tr>
                            <!-- End Profit -->

                            <!-- Total -->
                            <b-tr class="q-total">

                                <b-td colspan="4"></b-td>

                                <b-td><span><b>Total</b></span></b-td>
                                
                                <b-td><span><b>150</b></span></b-td>

                                <b-td><span><b>EUR</b></span></b-td>

                                <b-td></b-td>

                            </b-tr>
                            <!-- End Total -->

                        </b-tbody>
                        <!-- Body table -->

                    </b-table-simple>
                    <!-- End DataTable -->

                </div>
                <!-- End Tabla Freight -->

                <!-- Checkbox Freight-->
                <div class="col-12 d-flex mt-3 mb-3">

                    <b-form-checkbox value="carrier" class="mr-4">
                        <span>Show Carrier</span>
                    </b-form-checkbox>

                    <b-form-checkbox value="freight">
                        <span>Freight All-In</span>
                    </b-form-checkbox>

                </div>
                <!-- End Checkbox Freight-->

            </b-collapse>

        </b-card>
        <!-- End Freight Card -->

        <!-- Remarks -->
        <b-card class="mt-5">

            <h5 class="q-title">Remarks</h5>
            <textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea>

        </b-card>
        <!-- End Remarks -->

        <!--  Modal  -->
        <b-modal ref="my-modal" hide-footer title="Add Freight">
            <div class="d-flex flex-column align-items-center justify-content-center mb-5" >
                <label style="width:50%">
                    POL
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <label style="width:50%">
                    POD
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <label style="width:50%">
                    Carrier
                    <multiselect
                        v-model="value"
                        :options="options"
                        :searchable="true"
                        :close-on-select="false"
                        :show-labels="false"
                        placeholder="Select Currency">
                    </multiselect>
                </label>
                <a href="#" class="btn btn-primary btn-bg mt-2">Add Freight</a>
            </div>
        </b-modal>
        <!--  End Modal  -->

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
    DateRangePicker
  },
  data() {
    return {
      openFreight: false,
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
      ],
       //Datepicker Options
      locale: 'en-US',
      dateFormat: { 'year': 'numeric', 'month': 'long', 'day': 'numeric'}
    };
  },
  methods: {
    showModal() {
        this.$refs['my-modal'].show();
      },
  }
};
</script>