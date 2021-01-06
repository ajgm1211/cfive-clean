<template>
    <div style="padding: 0px 25px">

        <!-- Quote inputs -->
        <b-card class="q-card">

            <div class="row">

                <!-- Quote ID -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">quote id</span>
                        <b-form-input class="q-input" placeholder="CA-1545"></b-form-input>
                    </label>

                </div>
                <!-- End Quote ID -->

                <!-- Services -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">service</span>
                        <multiselect
                            v-model="services"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Service">
                        </multiselect>
                    </label>

                </div>
                <!-- End Services -->

                <!-- Customer -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">customer</span>
                        <multiselect
                            v-model="customer"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Customer">
                        </multiselect>
                    </label>

                </div>
                <!-- End Customer -->

                <!-- Commodity -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">commodity</span>
                        <b-form-input class="q-input" placeholder="Insert Commodity"></b-form-input>
                    </label>

                </div>
                <!-- End Commodity -->

                <!-- Status -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">status</span>
                        <multiselect
                            v-model="status"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Status">
                        </multiselect>
                    </label>

                </div>
                <!-- End Status -->

                <!-- Type -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">type</span>
                        <b-form-input class="q-input" placeholder="Port to Port" disabled></b-form-input>
                    </label>

                </div>
                <!-- End Type -->

                <!-- Contact -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">contact</span>
                        <multiselect
                            v-model="contact"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Contact">
                        </multiselect>
                    </label>

                </div>
                <!-- End Contact -->

                <!-- Kind of cargo -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">kind of cargo</span>
                        <multiselect
                            v-model="cargo"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Cargo">
                        </multiselect>
                    </label>

                </div>
                <!-- End Kind of cargo -->

                <!-- Date Issued -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">date issued</span>
                        <b-form-datepicker 
                            id="issued-datepicker" 
                            v-model="value" 
                            class="mb-2"
                            :locale="locale"
                            :date-format-options="dateFormat">
                        </b-form-datepicker>
                    </label>

                </div>
                <!-- End Date Issued -->

                <!-- Owner -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">owner</span>
                        <multiselect
                            v-model="onwer"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Owner">
                        </multiselect>
                    </label>

                </div>
                <!-- End Owner -->

                <!-- Payment -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">payment conditions</span>
                        <b-form-input placeholder="Payment Condition" name="payment" class="q-input"></b-form-input>
                    </label>

                </div>
                <!-- End Payment -->

                <div class="col-12 col-sm-3 col-lg-3"></div>

                <!-- Vality -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">vality</span>
                        <b-form-datepicker 
                            id="vality-datepicker" 
                            v-model="value" 
                            class="mb-2"
                            :locale="locale"
                            :date-format-options="dateFormat">
                        </b-form-datepicker>
                    </label>

                </div>
                <!-- End Vality -->

                <!-- Incoterm -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">incoterm</span>
                        <multiselect
                            v-model="incoterm"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Incoterm">
                        </multiselect>
                    </label>

                </div>
                <!-- End Incoterm -->

                <!-- Language -->
                <div class="col-12 col-sm-3 col-lg-3">

                    <label class="q-label">
                        <span class="label-text">language</span>
                        <multiselect
                            v-model="language"
                            :options="options"
                            :searchable="true"
                            :close-on-select="false"
                            :show-labels="false"
                            placeholder="Select Language">
                        </multiselect>
                    </label>

                </div>
                <!-- End Language -->
                
            </div>

        </b-card>
        <!-- End Quote inputs -->

        <!-- Cargo Type -->
        <b-card class="q-card mt-5">

            <!-- Cargo Type -->
            <div class="row justify-content-start">

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text">CARGO TYPE</span>
                        <b-form-input class="q-input" placeholder="CA-1545"></b-form-input>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <div class="col-12 col-sm-1"></div>

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">total quantity</span>
                        <b-form-input class="q-input q-width" placeholder="5"></b-form-input>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text">total weight</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>Kg
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text">total VOLUME</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>Kg
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">CHARGEABLE WEIGHT</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>m³
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

            </div>
            <!-- End Cargo Type -->

            <!-- Cargo Type -->
            <div class="row justify-content-between">

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-2">

                    <label class="q-label">
                        <span class="label-text">CARGO TYPE</span>
                        <b-form-input class="q-input" placeholder="CA-1545"></b-form-input>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <div class="col-12 col-sm-1 d-none d-lg-block"></div>

                <div class="col-12 col-sm-2 col-lg-1">

                     <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">quantity</span>
                        <b-form-input class="q-input q-width" placeholder="4"></b-form-input>
                    </label>
                    
                </div>

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-1">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">height</span>
                        <b-form-input class="q-input q-width" placeholder="5"></b-form-input>cm
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-1">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">width</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="9"></b-form-input>cm
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID --> 
                <div class="col-12 col-sm-2 col-lg-1">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">weight</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>Kg
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2 col-lg-1">

                    <label class="q-label">
                        <span class="label-text" style="white-space:nowrap">volumen</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>m³
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

                <div class="col-1 d-none d-lg-block"></div>

                <!-- CARGO TYPE ID -->
                <div class="col-12 col-sm-2">

                    <label class="q-label">
                        <span class="label-text">total weight</span>
                        <div class="d-flex align-items-end">
                            <b-form-input class="q-input q-width" placeholder="5"></b-form-input>m³
                        </div>
                    </label>

                </div>
                <!-- End CARGO TYPE ID -->

            </div>
            <!-- End Cargo Type -->

            <!-- Cargo Type Total -->
            <div class="row justify-content-between">

                <div class="col-12 col-sm-3">
                    <label class="q-label">
                       CHARGEABLE WEIGHT <span class="label-text">0.034 m³</span>
                    </label>
                </div>

                <div class="col-12 col-sm-2">
                    <label class="q-label">
                       <span class="label-text">Total: 34 un 0.06 m3 338 kg</span>
                    </label>
                </div>

            </div>
            <!-- End Cargo Type Total -->

        </b-card>
        <!-- End Cargo Type -->
        <!-- Terms and Condition -->
        <b-card class="mt-5">

            <h5 class="q-title">Terms and Conditions</h5>

            <textarea name id cols="30" rows="10" class="q-textarea">Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda quibusdam, at eveniet cupiditate omnis accusamus tempora error, laboriosam cumque soluta modi quas sapiente recusandae, labore non nemo! Sequi, molestias quidem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi nobis numquam quas ullam asperiores repellendus, assumenda officiis? Ratione doloremque sequi explicabo deleniti dolorem, ad, alias ipsa temporibus id, voluptatem sed?</textarea>

        </b-card>
        <!-- End Terms and Condition -->

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
      vdata: {},
      services: '',
      cargo: '',
      payment: '',
      customer: '',
      status: '',
      contact: '',
      owner: '',
      language: '',
      incoterm: '',
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
  }
};
</script>