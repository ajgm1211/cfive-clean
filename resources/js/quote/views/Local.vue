<template>
    <div style="padding: 0px 25px">
        <b-card class="q-card">
            <div class="row justify-content-between">
                <!-- Titulo y Pais -->
                <div class="col-12 col-lg-6 d-flex align-items-center responsive-localcharges-card">
                    <h5 style="line-height: 2">
                        <b>Local Charges at:</b>
                    </h5>

                    <multiselect
                        v-model="value"
                        :options="harbors"
                        :multiple="false"
                        :show-labels="false"
                        :close-on-select="true"
                        :preserve-search="true"
                        placeholder="Choose a Port"
                        label="display_name"
                        @input="getValues()"
                        track-by="display_name"
                        class="q-select ml-3"
                    ></multiselect>
                </div>
                <!-- End Titulo y Pais -->

                <!-- Agregar Charges -->
                <div
                    class="col-12 col-lg-6 d-flex justify-content-end align-items-center responsive-localcharges-card-select" 
                >
                    <multiselect
                        v-model="template"
                        :options="saleterms"
                        :multiple="false"
                        :show-labels="false"
                        :close-on-select="true"
                        :preserve-search="true"
                        placeholder="Select Sale Template"
                        label="name"
                        track-by="name"
                        @input="getCharges()"
                        class="q-select mr-3"
                        style="position: relative; top: 4px"
                        v-if="currentQuoteData.type == 'FCL'"
                    ></multiselect>

                    <a
                        href="/api/sale_terms"
                        target="_blank"
                        class="btn btn-link mr-4"
                        id="show-btn"
                        v-if="currentQuoteData.type == 'FCL'"
                    >
                        + Add Sale Template
                    </a>
                    <button
                        class="btn btn-primary btn-bg"
                        id="show-btn"
                        @click="showModal"
                    >
                        + Add Charges
                    </button>
                </div>
                <!-- End Agregar Charges -->
                <div class="col-12 mt-5">
                    <!-- DataTable -->
                    <b-table-simple
                        hover
                        small
                        responsive
                        borderless
                        :striped="false"
                        class="local_charge_table"
                    >
                        <!-- Header table -->
                        <b-thead class="q-thead">
                            <b-tr>
                                <b-th>
                                    <span class="label-text">Charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <span class="label-text">{{ item }}</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">units</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">rate</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">total</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Currency</span>
                                </b-th>
                            </b-tr>
                        </b-thead>

                        <!-- Body table -->
                        <b-tbody>
                            <b-tr
                                class="q-tr"
                                v-for="(charge, key) in this.charges"
                                :key="key"
                            >
                                <b-td>
                                    <b-form-input
                                        v-if="currentQuoteData.type == 'FCL'"
                                        v-model="charge.charge"
                                        class="q-input local_charge_input"
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.charge,
                                                'charge',
                                                1
                                            )
                                        "
                                    ></b-form-input>
                                    <b-form-input
                                        v-if="currentQuoteData.type == 'LCL'"
                                        v-model="charge.charge"
                                        class="q-input local_charge_input"
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.charge,
                                                'charge',
                                                9
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>
                                <b-td>
                                    <multiselect
                                        v-if="currentQuoteData.type == 'FCL'"
                                        v-model="charge.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation type"
                                        class="local_calculation_type"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.calculation_type.id,
                                                'calculation_type_id',
                                                1
                                            )
                                        "
                                    ></multiselect>
                                    <multiselect
                                        v-if="currentQuoteData.type == 'LCL'"
                                        v-model="charge.calculation_type"
                                        :options="
                                            datalists['calculationtypeslcl']
                                        "
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation type"
                                        label="name"
                                        track-by="name"
                                        class="local_calculation_type"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.calculation_type.id,
                                                'calculation_type_id',
                                                6
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <b-form-input
                                        v-model="charge.total['c' + item]"
                                        class="q-input local_charge_total_input"
                                        @keypress="isNumber($event)"
                                        v-on:blur="
                                            onUpdate(
                                                charge.id,
                                                charge.total['c' + item],
                                                'total->c' + item,
                                                1
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="charge.units"
                                        class="q-input local_charge_total_input"
                                        @keypress="isNumber($event)"
                                        v-on:change="
                                            onUpdate(
                                                charge.id,
                                                charge.units,
                                                'units',
                                                6
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="charge.price"
                                        class="q-input local_charge_total_input"
                                        @keypress="isNumber($event)"
                                        v-on:change="
                                            onUpdate(
                                                charge.id,
                                                charge.price,
                                                'price',
                                                6
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="charge.price * charge.units"
                                        class="q-input local_charge_total_input"
                                        disabled
                                    ></b-form-input>
                                </b-td>

                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <multiselect
                                        v-model="charge.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Currency"
                                        class="local_charge_currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.currency.id,
                                                'currency_id',
                                                6
                                            )
                                        "
                                    ></multiselect>
                                </b-td>
                                <b-td v-else-if="currentQuoteData.type == 'FCL'">
                                    <multiselect
                                        v-model="charge.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Select a currency"
                                        class="local_charge_currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                charge.id,
                                                charge.currency.id,
                                                'currency_id',
                                                1
                                            )
                                        "
                                    ></multiselect>
                                </b-td>
                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onDelete(charge.id, 1)"
                                    >
                                        <i
                                            class="fa fa-times"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>

                            <b-tr
                                class="q-total"
                                v-if="currentQuoteData.type == 'LCL'"
                            >
                                <b-td colspan="3"></b-td>

                                <b-td
                                    ><span><b>Total</b></span></b-td
                                >

                                <b-td
                                    ><span
                                        ><b>{{ totals.total }}</b></span
                                    ></b-td
                                >

                                <b-td>
                                    <multiselect
                                        v-model="totals.currency"
                                        :options="
                                            datalists['filtered_currencies']
                                        "
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Select a currency"
                                        class="local_charge_currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                totals.id,
                                                totals.currency.id,
                                                'currency_id',
                                                7
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td></b-td>
                            </b-tr>

                            <b-tr
                                class="q-total"
                                v-if="currentQuoteData.type == 'FCL'"
                            >
                                <b-td></b-td>

                                <b-td>
                                    <span>
                                        <b>Total</b>
                                    </span>
                                </b-td>

                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <span v-if="totals.total">
                                        <b>{{ totals.total["c" + item] }}</b>
                                    </span>
                                </b-td>

                                <b-td>
                                    <span v-if="loaded">
                                        <multiselect
                                            v-model="totals.currency"
                                            :options="
                                                datalists['filtered_currencies']
                                            "
                                            :multiple="false"
                                            :show-labels="false"
                                            :close-on-select="true"
                                            :preserve-search="true"
                                            placeholder="Select a currency"
                                            class="local_charge_currency"
                                            label="alphacode"
                                            track-by="alphacode"
                                            @input="
                                                onUpdate(
                                                    totals.id,
                                                    totals.currency.id,
                                                    'currency_id',
                                                    5
                                                )
                                            "
                                        ></multiselect>
                                    </span>
                                </b-td>

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
            <br />
            <ckeditor
                id="inline-form-input-name"
                type="classic"
                v-model="remarks"
                v-on:blur="updateRemarks(remarks)"
            ></ckeditor>
        </b-card>

        <!--  Modal  -->
        <b-modal
            ref="my-modal"
            id="localcharges"
            size="xl"
            centered
            hide-footer
            title="Add Charges"
        >
            <div class="row">
                <div class="col-12 col-lg-6 d-flex alig-items-center">
                    <h5>
                        <b>
                            <span v-if="this.value.type == 1">Origin</span>
                            <span v-if="this.value.type == 2">Destination</span>
                            Costs at:
                        </b>
                    </h5>

                    <span class="ml-3" v-if="this.port != ''">
                        <img
                            v-bind:src="
                                '/images/flags/1x1/' + this.code_port + '.svg'
                            "
                            alt="bandera"
                            width="20"
                            height="20"
                            style="border-radius: 50%"
                        />&nbsp;
                        <b>{{ this.port }}</b>
                    </span>
                </div>

                <div id="modal-localcharges-table" class="col-12 mt-5">
                    <!-- DataTable -->
                    <b-table-simple small responsive="sm" borderless>

                        <!-- Header table -->
                        <b-thead class="q-thead">
                            <b-tr>
                                <b-th></b-th>

                                <b-th>
                                    <span class="label-text">Charge</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Detail</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'FCL'">
                                    <span class="label-text">Show As</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Provider</span>
                                </b-th>

                                <b-th
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >
                                    <span class="label-text"
                                        >{{ item }} + Profit</span
                                    >
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">Units</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">Price</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">Profit</span>
                                </b-th>

                                <b-th v-if="currentQuoteData.type == 'LCL'">
                                    <span class="label-text">Total</span>
                                </b-th>

                                <b-th>
                                    <span class="label-text">Currency</span>
                                </b-th>

                                <b-th></b-th>
                            </b-tr>
                        </b-thead>

                        <b-tbody>

                            <!-- Fijo -->
                            <b-tr
                                class="q-tr"
                                v-for="(localcharge, key) in this.localcharges"
                                :key="key"
                            >

                                <!-- Checkboxes -->
                                <b-td>
                                    <b-form-checkbox
                                        v-model="selectedCharges"
                                        :id="'id_' + localcharge.id"
                                        :value="localcharge"
                                    ></b-form-checkbox>
                                     <b-form-input
                                        v-model="localcharge.id"
                                        class="q-input hide" 
                                    ></b-form-input>
                                </b-td>

                                <!-- Surcharges -->
                                <b-td>
                                    <multiselect
                                        v-model="localcharge.surcharge"
                                        :options="datalists['surcharges']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Surcharge"
                                        class="data-surcharge"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.surcharge.id,
                                                'surcharge_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <!-- Detail -->
                                <b-td>
                                    <multiselect
                                        v-if="currentQuoteData.type == 'FCL'"
                                        v-model="localcharge.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation Type"
                                        class="data-detail"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.calculation_type.id,
                                                'calculation_type_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                    <multiselect
                                        v-if="currentQuoteData.type == 'LCL'"
                                        v-model="localcharge.calculation_type"
                                        :options="
                                            datalists['calculationtypeslcl']
                                        "
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation Type"
                                        class="data-detail"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.calculation_type.id,
                                                'calculation_type_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <!-- Show As -->
                                <b-td v-if="currentQuoteData.type == 'FCL'">
                                    <multiselect
                                        v-model="localcharge.sale_codes"
                                        :options="datalists['sale_codes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Sale Code"
                                        class="data-showas"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Provider -->
                                <b-td>
                                    <multiselect
                                        v-model="
                                            localcharge.automatic_rate.carrier
                                        "
                                        v-if="localcharge.provider_name == null"
                                        :options="carriers"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        :disabled="true"
                                        placeholder="Provider"
                                        class="data-provider"
                                        label="name"
                                        track-by="name"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.automatic_rate
                                                    .carrier.id,
                                                'carrier',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                    <b-form-input
                                        v-if="localcharge.provider_name != null"
                                        v-model="localcharge.provider_name"
                                        class="q-input"
                                        :disabled="true"
                                    ></b-form-input>
                                </b-td>

                                <!-- Profit -->
                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"
                                >

                                    <div style="display:flex; width: 100px;">
                                        <b-form-input
                                            placeholder
                                            v-model="localcharge.price['c' + item]"
                                            class="q-input data-profit"
                                            @keypress="isNumber($event)"
                                            v-on:change="
                                                onUpdate(
                                                    localcharge.id,
                                                    localcharge.price['c' + item],
                                                    'c' + item,
                                                    3
                                                )
                                            "
                                        ></b-form-input>
                                        <b-form-input
                                            placeholder
                                            v-model="localcharge.markup['m' + item]"
                                            class="q-input data-profit"
                                            @keypress="isNumber($event)"
                                            v-on:change="
                                                onUpdate(
                                                    localcharge.id,
                                                    localcharge.markup['m' + item],
                                                    'm' + item,
                                                    4
                                                )
                                            "
                                        ></b-form-input>
                                    </div>
                                </b-td>

                                <!-- Profit -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="localcharge.units"
                                        class="q-input"
                                        style="width:80px;"
                                        @keypress="isNumber($event)"
                                        v-on:blur="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.units,
                                                'units',
                                                8
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <!-- Profit -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="localcharge.price_per_unit"
                                        style="width:80px;"
                                        class="q-input"
                                        @keypress="isNumber($event)"
                                        v-on:blur="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.price_per_unit,
                                                'price_per_unit',
                                                8
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <!-- Profit -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="localcharge.markup"
                                        style="width:80px;"
                                        class="q-input"
                                        @keypress="isNumber($event)"
                                        v-on:blur="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.markup,
                                                'markup',
                                                8
                                            )
                                        "
                                    ></b-form-input>
                                </b-td>

                                <!-- Profit -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        :value="
                                            setTotal(
                                                localcharge.units,
                                                localcharge.price_per_unit,
                                                localcharge.markup
                                            )
                                        "
                                        class="q-input"
                                        style="width:80px;"
                                        disabled
                                    ></b-form-input>
                                </b-td>

                                <!-- Currency -->
                                <b-td>
                                    <multiselect
                                        v-model="localcharge.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Currency"
                                        class="data-currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                        @input="
                                            onUpdate(
                                                localcharge.id,
                                                localcharge.currency.id,
                                                'currency_id',
                                                2
                                            )
                                        "
                                    ></multiselect>
                                </b-td>

                                <b-td>
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onDelete(localcharge.id, 2)"
                                    >
                                        <i
                                            class="fa fa-times"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>

                            <!-- Dinamico -->
                            <b-tr
                                class="q-tr"
                                v-for="(input, counter) in inputs"
                                :key="counter"
                            >
                                <!-- Checkboxes -->
                                <b-td>
                                    <b-form-checkbox
                                        v-model="selectedInputs"
                                        :id="'id_' + input.id"
                                        :value="input"
                                    ></b-form-checkbox>
                                </b-td>

                                <!-- Surcharges -->
                                <b-td>
                                    <multiselect
                                        v-model="input.surcharge"
                                        :options="datalists['surcharges']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Surcharge"
                                        class="data-surcharge"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Detail -->
                                <b-td v-if="currentQuoteData.type == 'FCL'">
                                    <multiselect
                                        v-model="input.calculation_type"
                                        :options="datalists['calculationtypes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation Type"
                                        class="data-detail"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Detail -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <multiselect
                                        v-model="input.calculation_type"
                                        :options="
                                            datalists['calculationtypeslcl']
                                        "
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Calculation Type"
                                        class="data-detail"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Show As -->
                                <b-td v-if="currentQuoteData.type == 'FCL'">
                                    <multiselect
                                        v-model="input.sale_codes"
                                        :options="datalists['sale_codes']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Sale Code"
                                        class="data-showas"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Provider -->
                                <b-td>
                                    <multiselect
                                        v-model="input.carrier"
                                        :options="carriers"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Provider"
                                        class="data-provider"
                                        label="name"
                                        track-by="name"
                                    ></multiselect>
                                </b-td>

                                <!-- Profits -->
                                <b-td
                                    v-for="(item, key) in quoteEquip"
                                    :key="key"

                                >
                                    <div style="display:flex; width: 100px;">
                                        <b-form-input
                                            placeholder
                                            v-model="input.price['c' + item]"
                                            @keypress="isNumber($event)"
                                            class="q-input data-profit"
                                        ></b-form-input>

                                        <b-form-input
                                            placeholder
                                            v-model="input.markup['m' + item]"
                                            @keypress="isNumber($event)"
                                            class="q-input data-profit"
                                        ></b-form-input>
                                    </div>
                                </b-td>

                                <!-- Profits -->
                                <b-td v-if="currentQuoteData.type == 'LCL'" >
                                    <b-form-input
                                        v-model="input.units"
                                        style="width:80px;"
                                        @keypress="isNumber($event)"
                                        class="q-input data-profit"
                                    ></b-form-input>
                                </b-td>

                                <!-- Profits -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="input.price"
                                        style="width:80px;"
                                        @keypress="isNumber($event)"
                                        class="q-input"
                                    ></b-form-input>
                                </b-td>

                                <!-- Profits -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="input.profit"
                                        style="width:80px;"
                                        @keypress="isNumber($event)"
                                        class="q-input"
                                    ></b-form-input>
                                </b-td>

                                <!-- Profits -->
                                <b-td v-if="currentQuoteData.type == 'LCL'">
                                    <b-form-input
                                        v-model="input.total"
                                        style="width:80px;"
                                        class="q-input"
                                        disabled
                                    ></b-form-input>
                                </b-td>

                                <!-- Currency -->
                                <b-td>
                                    <multiselect
                                        v-model="input.currency"
                                        :options="datalists['currency']"
                                        :multiple="false"
                                        :show-labels="false"
                                        :close-on-select="true"
                                        :preserve-search="true"
                                        placeholder="Currency"
                                        class="data-currency"
                                        label="alphacode"
                                        track-by="alphacode"
                                    ></multiselect>
                                </b-td>

                                <!-- Botones -->
                                <b-td>
                                    <!--<button
                                        type="button"
                                        class="btn-save"
                                        v-on:click="onSubmitCharge(counter)"
                                    >
                                        <i
                                            class="fa fa-check"
                                            aria-hidden="true"
                                        ></i>
                                    </button> -->
                                    <button
                                        type="button"
                                        class="btn-delete"
                                        v-on:click="onRemove(counter)"
                                    >
                                        <i
                                            class="fa fa-close"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </b-td>
                            </b-tr>
                            
                        </b-tbody>
                    </b-table-simple>
                    <!-- End DataTable -->
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div
                            v-if="modalWarning != ''"
                            class="alert alert-danger"
                            role="alert"
                        >
                            {{ modalWarning + " cannot be empty" }}
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end mb-5 mt-3">
                    <button class="btn btn-link mr-2" @click="add()">
                        + Add New
                    </button>
                    <button
                        class="btn btn-primary btn-bg"
                        @click="onSubmit"
                        @success="closeModal('localcharges')"
                    >
                        + Add Charges
                    </button>
                </div>
            </div>
        </b-modal>
        <!--  End Modal  -->
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import actions from "../../actions";
import FormInlineView from "../../components/views/FormInlineView.vue";
export default {
    components: {
        Multiselect,
        FormInlineView,
    },
    created() {
        let id = this.$route.params.id;
        this.getHarbors(id);
        this.getRemarks(id);
    },
    props: {
        equipment: Object,
        datalists: Object,
        quoteEquip: Array,
        currentQuoteData: Object,
    },
    data() {
        return {
            actions: actions.localcharges,
            currencies: this.datalists.currency,
            openModal: false,
            ids: [],
            sale_codes: [],
            options: [],
            saleterms: [],
            charges: [],
            localcharges: [],
            harbors: [],
            port: [],
            totals: [],
            inputs: [],
            inputId: 0,
            selectedCharges: [],
            selectedInputs: [],
            carriers: [],
            value: "",
            template: "",
            code_port: "",
            rate_id: "",
            sale_code: "",
            modalWarning: "",
            remarks: null,
            errors: null,
            loaded: false,
            remark_field: {
                localcharge_remarks: {
                    type: "ckeditor",
                    rules: "required",
                    placeholder: "Insert remarks",
                    colClass: "col-sm-12",
                },
            },
        };
    },
    watch: {
        charges: function(){
            let id = this.$route.params.id;

            this.$emit("chargesUpdated",id);}
    },
    methods: {
        add() {
            if (this.value != "") {
                this.inputId += 1;
                let currentInputId = this.inputId + 1;
                if (this.currentQuoteData.type == "FCL") {
                    this.inputs.push({
                        id: currentInputId,
                        surcharge: "",
                        calculation_type: "",
                        sale_codes: "",
                        price: {},
                        markup: {},
                        currency: "",
                    });
                } else {
                    this.inputs.push({
                        id: currentInputId,
                        surcharge: "",
                        calculation_type: "",
                        sale_codes: "",
                        units: 0,
                        price: 0,
                        profit: 0,
                        currency: "",
                    });
                }
            } else {
                this.alert(
                    "You must select a port before create a new charge",
                    "error"
                );
            }
        },
        showModal() {
            this.$refs["my-modal"].show();
        },
        closeModal() {
            this.$refs["my-modal"].hide();
        },
        addSaleCode(value) {
            this.sale_codes.push(value);
        },
        getHarbors(id) {
            actions.localcharges
                .harbors(id)
                .then((response) => {
                    this.harbors = response.data;
                    this.value = this.harbors[0];
                    this.getValues();
                })
                .catch((data) => {
                    //
                });
        },
        getValues() {
            this.getSaleTerms();
            this.getLocalCharges();
            this.getStoredCharges();
            this.getTotal();
            this.getCarriers();
        },
        getSaleTerms() {
            this.saleterms = [];
            this.charges = [];
            this.template = null;
            let data = {
                equipment: this.equipment.id,
                port_id: this.value.id,
                type: this.value.type,
            };
            actions.localcharges
                .saleterms(data)
                .then((response) => {
                    this.saleterms = response.data;
                })
                .catch((data) => {
                    //
                });
        },
        getCharges() {
            this.charges = [];
            this.totals = [];
            let data = {
                id: this.template.id,
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
            };
            actions.localcharges
                .charges(data)
                .then((response) => {
                    this.charges = response.data;
                    this.getTotal();
                })
                .catch((data) => {
                    //
                });
        },
        getStoredCharges() {
            this.charges = [];
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
                type: this.currentQuoteData.type,
            };
            actions.localcharges
                .storedCharges(data)
                .then((response) => {
                    this.charges = response.data;
                })
                .catch((data) => {
                    //
                });
        },
        getTotal() {
            this.totals = [];
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
            };
            if (this.currentQuoteData.type == "FCL") {
                actions.localcharges
                    .total(data)
                    .then((response) => {
                        this.totals = response.data;
                        this.loaded = true;
                    })
                    .catch((data) => {
                        //
                    });
            } else {
                actions.localchargeslcl
                    .total(data)
                    .then((response) => {
                        this.totals = response.data;
                        this.loaded = true;
                    })
                    .catch((data) => {
                        //
                    });
            }
        },
        getCarriers() {
            let self = this;
            let quote = this.$route.params.id;
            actions.localcharges
                .carriers(quote)
                .then((response) => {
                    self.carriers = response.data;
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        getLocalCharges() {
            this.localcharges = [];
            this.port = [];
            let self = this;
            let data = {
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type: this.value.type,
            };
            if (this.currentQuoteData.type == "FCL") {
                actions.localcharges
                    .localcharges(data)
                    .then((response) => {
                        self.localcharges = response.data.charges;
                        self.port = response.data.port.display_name;
                        self.code_port = response.data.port.country.code.toLowerCase();
                        self.rate_id = response.data.automatic_rate.id;
                    })
                    .catch((data) => {
                        //
                    });
            } else {
                actions.localchargeslcl
                    .localcharges(data)
                    .then((response) => {
                        self.localcharges = response.data.charges;
                        self.port = response.data.port.display_name;
                        self.code_port = response.data.port.country.code.toLowerCase();
                        self.rate_id = response.data.automatic_rate.id;
                    })
                    .catch((data) => {
                        //
                    });
            }
        },
        getRemarks(id) {
            let self = this;
            actions.localcharges
                .remarks(id)
                .then((response) => {
                    self.remarks = response.data.localcharge_remarks;
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        onDelete(id, type) {
            if (this.currentQuoteData.type == "FCL") {
                actions.localcharges
                    .delete(id, type)
                    .then((response) => {
                        this.alert("Record deleted successfully", "success");
                        this.getTotal();
                    })
                    .catch((data) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
            } else {
                actions.localchargeslcl
                    .delete(id, type)
                    .then((response) => {
                        this.alert("Record deleted successfully", "success");
                        this.getTotal();
                    })
                    .catch((data) => {
                        this.$refs.observer.setErrors(data.data.errors);
                    });
            }
            this.charges = this.charges.filter(function (item) {
                return id != item.id;
            });
            this.localcharges = this.localcharges.filter(function (item) {
                return id != item.id;
            });
        },
        onSubmit() {
            if (this.selectedCharges.length > 0 || this.selectedInputs.length > 0) {
                this.charges = [];
                this.totals = [];
                this.formatInputs();
                let data = {
                    selectedCharges: this.selectedCharges.concat(this.selectedInputs),
                    sale_codes: this.sale_codes,
                    quote_id: this.$route.params.id,
                    port_id: this.value.id,
                    type_id: this.value.type,
                };
                if (this.currentQuoteData.type == "FCL") {
                    actions.localcharges
                        .create(data)
                        .then((response) => {
                            this.charges = response.data;
                            this.getStoredCharges();
                            this.getTotal();
                            this.alert("Record saved successfully", "success");
                            this.closeModal();
                            this.selectedCharges = [];
                            this.selectedInputs = [];
                        })
                        .catch((data) => {
                            if(data.status == 422){
                                this.alert("Please complete the fields", "error");
                            }
                        });
                } else {
                    actions.localchargeslcl
                        .create(data)
                        .then((response) => {
                            this.charges = response.data;
                            this.getStoredCharges();
                            this.getTotal();
                            this.alert("Record saved successfully", "success");
                            this.closeModal();
                            this.selectedCharges = [];
                            this.selectedInputs = [];
                        })
                        .catch((data) => {
                            if(data.status == 422){
                                this.alert("Please complete the fields", "error");
                            }
                        });
                }
            } else {
                this.alert("You must select a charge at least", "error");
            }
        },
        onSubmitCharge(counter) {
            let data = {
                charges: this.inputs[counter],
                quote_id: this.$route.params.id,
                port_id: this.value.id,
                type_id: this.value.type,
                quote_type: this.currentQuoteData.type,
            };
            if (data.quote_type == "FCL") {
                actions.localcharges
                    .createCharge(data)
                    .then((response) => {
                        this.getLocalCharges();
                        this.onRemove(counter);
                        this.getStoredCharges();
                        this.getTotal();
                        this.closeModal();
                        this.alert("Record saved successfully", "success");
                    })
                    .catch((e) => {
                        let errors_key = Object.keys(e.data.errors);
                        let component = this;
                        errors_key.forEach(function (key) {
                            component.alert(e.data.errors[key][0], "error");
                        });
                    });
            } else {
                actions.localchargeslcl
                    .createCharge(data)
                    .then((response) => {
                        this.getLocalCharges();
                        this.onRemove(counter);
                        this.getStoredCharges();
                        this.getTotal();
                        this.closeModal();
                        this.alert("Record saved successfully", "success");
                    })
                    .catch((e) => {
                        let errors_key = Object.keys(e.data.errors);
                        let component = this;
                        errors_key.forEach(function (key) {
                            component.alert(e.data.errors[key][0], "error");
                        });
                    });
            }
        },
        onRemove(index) {
            this.inputs.splice(index, 1);
        },
        onUpdate(id, data, index, type) {
            this.totals = [];
            let self = this;
            actions.localcharges
                .update(id, data, index, type)
                .then((response) => {
                    this.getTotal();
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        updateRemarks(remarks) {
            let quote_id = this.$route.params.id;
            actions.localcharges
                .updateRemarks(remarks, quote_id)
                .then((response) => {
                    //
                })
                .catch((data) => {
                    this.$refs.observer.setErrors(data.data.errors);
                });
        },
        alert(msg, type) {
            this.$toast.open({
                message: msg,
                type: type,
                duration: 5000,
                dismissible: true,
            });
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
        setTotal(units, price, markup) {
            return parseFloat(units) * parseFloat(price) + parseFloat(markup);
        },
        formatInputs(){
            let component = this;

            component.selectedInputs.forEach(function (input){
                    input.currency_id=null;
                    input.surcharge_id=null;
                    input.calculation_type_id=null;
                    input.provider_name=null;

                if(input.currency.id != null){
                    input.currency_id = input.currency.id;
                }if(input.surcharge.id != null){
                    input.surcharge_id = input.surcharge.id;
                }if(input.calculation_type.id != null ){
                    input.calculation_type_id = input.calculation_type.id;
                }if(input.carrier != null){
                    input.provider_name = input.carrier.name;
                }
                
                    if (component.currentQuoteData.type == 'LCL') {
                            input.price_per_unit=null;
                            input.markup=null;

                        if(input.price!=null){
                           input.price_per_unit = input.price; 
                        }if(input.profit!=null){
                           input.markup = input.profit; 
                        } 
                    }
            })
        }
    },
};
</script>
