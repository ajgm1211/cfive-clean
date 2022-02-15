<template>
  <div id="search" class="search pt-5">
    <div v-if="loaded">
      <!-- OPCIONES DE DELIVERY Y ADDITIONAL SERVICES BOTON -->
      <div class="row mr-0 ml-0">
        <!-- TYPE - DELIVERY TYPE -->
        <div class="col-12 col-sm-6 col-lg-3 d-flex">
          <!-- TYPE (FCL, LCL) -->
          <div class="type-input">
            <multiselect
              v-model="searchRequest.type"
              :multiple="false"
              :close-on-select="true"
              :clear-on-select="false"
              :show-labels="false"
              :options="typeOptions"
              :searchable="false"
              :allow-empty="false"
              @input="setSearchType()"
              placeholder="Select Type"
              class="s-input no-select-style"
            >
            </multiselect>
          </div>
          <!-- FIN TYPE (FCL, LCL) -->

          <!-- DELIVERY TYPE (Door to Door, Door to Port, Port to Port, Port to Door)-->
          <div class="delivery-input">
            <multiselect
              v-model="searchRequest.deliveryType"
              :multiple="false"
              :close-on-select="true"
              :clear-on-select="false"
              :show-labels="false"
              :searchable="false"
              :allow-empty="false"
              :options="deliveryTypeOptions"
              label="name"
              track-by="name"
              placeholder="Select"
              class="s-input no-select-style"
            >
            </multiselect>
          </div>
          <!-- FIN DELIVERY TYPE (Door to Door, Door to Port, Port to Port, Port to Door)-->
        </div>
        <!-- FIN TYPE - DELIVERY TYPE -->

        <!-- BOTON ADDITIONAL SERVICES -->
        <div class="col-12 col-sm-6 col-lg-9">
          <b-button v-b-toggle.collapse-1 class="btn-aditonal-services"
            >additional services
            <b-icon icon="caret-down-fill" class="ml-1"></b-icon>
          </b-button>
        </div>
        <!-- FIN BOTON ADDITIONAL SERVICES -->
      </div>
      <!-- FIN OPCIONES DE DELIVERY Y ADDITIONAL SERVICES BOTON -->

      <!-- INPUTS DEL SEARCH -->
      <div class="row mr-0 ml-0">
        <!-- IMPORT - EXPORT -->
        <div class="col-lg-1 mb-2 export-import">
          <b-form-radio-group
            v-model="searchRequest.direction"
            :options="directionOptions"
            buttons
            button-variant="outline-primary"
            size="lg"
            name="direction"
            class="radio-direction type-style"
          ></b-form-radio-group>
        </div>
        <!-- FIN IMPORT - EXPORT -->

        <!-- ORIGIN PORT -->
        <div class="col-lg-3 mb-2 input-search-form origen-search">
          <multiselect
            v-model="searchRequest.originPorts"
            :multiple="true"
            :close-on-select="true"
            :clear-on-select="true"
            :show-labels="false"
            :options="originPortOptions"
            @input="updateQuoteSearchOptions()"
            label="display_name"
            track-by="display_name"
            placeholder="From"
            class="s-input"
          >
          </multiselect>
          <img
            src="/images/port.svg"
            class="img-icon img-icon-left img-icon-origin"
            alt="port"
          />
          <span
            v-if="errorsExist && 'originPorts' in responseErrors"
            style="color: red"
            >The Origin Port field is required!</span
          >
        </div>
        <!-- FIN ORIGIN PORT -->

        <!-- DESTINATION PORT -->
        <div class="col-lg-3 mb-2 input-search-form destination-search">
          <multiselect
            v-model="searchRequest.destinationPorts"
            :multiple="true"
            :close-on-select="true"
            :clear-on-select="true"
            :show-labels="false"
            :options="destinationPortOptions"
            @input="updateQuoteSearchOptions()"
            label="display_name"
            track-by="display_name"
            placeholder="To"
            class="s-input"
          >
          </multiselect>
          <img src="/images/port.svg" class="img-icon" alt="port" />
          <span
            v-if="errorsExist && 'destinationPorts' in responseErrors"
            style="color:red"
            >The Destination Port field is required!</span
          >
        </div>
        <!-- FIN DESTINATION PORT -->

        <!-- DATEPICKER -->
        <div class="col-lg-3 mb-2 input-search-form datepicker-search">
          <date-range-picker
            :opens="'center'"
            :locale-data="{
              firstDay: 0,
              format: 'yyyy/mm/dd',
            }"
            :singleDatePicker="false"
            :autoApply="true"
            :timePicker="false"
            v-model="searchRequest.dateRange"
            :linkedCalendars="true"
            class="s-input"
          ></date-range-picker>
          <img
            src="/images/calendario.svg"
            class="img-icon calendar-icon img-icon-left"
            alt="calendario"
          />
          <span
            v-if="errorsExist && 'dateRange.startDate' in responseErrors"
            style="color:red"
            >Please pick a date</span
          >
        </div>
        <!-- FIN DATEPICKER -->

        <!-- CONTAINERS -->
        <div
          class="col-lg-2 mb-2 input-search-form containers-search"
          v-show="searchRequest.type == 'FCL'"
        >
          <b-dropdown
            id="dropdown-containers"
            :disabled="searchRequest.requestData.requested == 1"
            :text="containerText.join(', ')"
            ref="dropdown"
            class="m-2"
          >
            <b-dropdown-form>
              <b-form-group label="Type">
                <b-form-radio-group
                  id="containers"
                  v-model="selectedContainerGroup"
                  :options="containerGroupOptions"
                ></b-form-radio-group>
              </b-form-group>
              <b-form-group label="Equipment List">
                <b-form-checkbox-group
                  id="equipment"
                  v-model="containers"
                  :options="containerOptions"
                ></b-form-checkbox-group>
              </b-form-group>
            </b-dropdown-form>
          </b-dropdown>
          <img src="/images/container.svg" class="img-icon" alt="port" />
          <span
            v-if="errorsExist && 'containers' in responseErrors"
            style="color:red"
            >Choose at least one container</span
          >
        </div>
        <!-- FIN CONTAINERS -->
      </div>
      <!-- FIN INPUTS DEL SEARCH -->

      <!-- INPUT FROM AND TO PORT -->
      <div v-if="ptdActive || dtpActive || dtdActive" class="row mr-0 ml-0">
        <div class="col-lg-1"></div>

        <div
          v-if="ptdActive"
          class="col-lg-3"
          style="padding-left: 30px; padding-right: inherit"
        ></div>

        <!-- Origin City -->
        <div
          v-if="dtpActive || dtdActive"
          class="col-lg-3 mb-2 origen-search input-search-form"
        >
          <multiselect
            v-if="originDistance"
            v-model="searchRequest.originAddress"
            disabled="true"
            :multiple="false"
            :close-on-select="true"
            :clear-on-select="true"
            :show-labels="false"
            :options="originAddressOptions"
            placeholder="Under construction"
            label="display_name"
            track-by="display_name"
            class="s-input"
          >
          </multiselect>
          <gmap-autocomplete
            v-else
            @place_changed="setOriginPlace"
            @input="commitOriginAutocomplete"
            :value="originAutocompleteValue"
            class="form-input form-control"
            placeholder="Start typing an address"
          >
          </gmap-autocomplete>
          <img
            src="/images/city.svg"
            class="img-icon img-icon-left img-icon-origin"
            alt="port"
          />
        </div>

        <!-- Destination City -->
        <div
          v-if="ptdActive || dtdActive"
          class="col-lg-3 mb-2 input-search-form"
        >
          <multiselect
            v-if="destinationDistance"
            v-model="searchRequest.destinationAddress"
            disabled="true"
            :multiple="false"
            :close-on-select="true"
            :clear-on-select="true"
            :show-labels="false"
            :options="destinationAddressOptions"
            placeholder="Under construction"
            label="display_name"
            track-by="display_name"
            class="s-input"
          >
          </multiselect>
          <gmap-autocomplete
            v-else
            @place_changed="setDestinationPlace"
            @input="commitDestinationAutocomplete"
            :value="destinationAutocompleteValue"
            class="form-input form-control"
            placeholder="Start typing an address"
          >
          </gmap-autocomplete>
          <img
            src="/images/city.svg"
            class="img-icon img-icon-left"
            alt="port"
          />
        </div>
      </div>
      <!-- FIN INPUT FROM AND TO PORT -->

      <!-- ADDITIONAL SERVICES -->
      <b-collapse :visible="additionalVisible" id="collapse-1" class="mt-3">
        <h6 class="t-as mt-5 mb-3 ml-4">ADDITIONAL SERVICES</h6>

        <!-- INPUTS -->
        <div class="row mr-3 ml-3">
          <div class="col-lg-3 mb-2 input-search-form">
            <multiselect
              v-model="searchRequest.company"
              :multiple="false"
              :close-on-select="true"
              :clear-on-select="true"
              :show-labels="false"
              :hide-selected="true"
              :options="datalists.companies"
              label="business_name"
              track-by="business_name"
              placeholder="Company"
              class="s-input"
              @input="
                (searchRequest.contact = ''),
                  unlockContacts(),
                  (searchRequest.pricelevel = null),
                  setPriceLevels(),
                  updateQuoteSearchOptions()
              "
            >
            </multiselect>
            <img
              src="/images/empresa.svg"
              class="img-icon img-icon-left"
              alt="port"
            />
            <button
              v-if="
                searchRequest.company != '' && searchRequest.company != null
              "
              type="button"
              class="close custom_close"
              aria-label="Close"
              @click="
                (searchRequest.company = ''),
                  (searchRequest.contact = ''),
                  (searchRequest.pricelevel = ''),
                  unlockContacts(),
                  setPriceLevels()
              "
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="col-lg-3 mb-2 input-search-form">
            <multiselect
              v-model="searchRequest.contact"
              :multiple="false"
              :close-on-select="true"
              :clear-on-select="true"
              :show-labels="false"
              :hide-selected="true"
              :options="contactOptions"
              :disabled="!companyChosen"
              label="name"
              track-by="name"
              placeholder="Contact"
              class="s-input"
              @input="updateQuoteSearchOptions"
            >
            </multiselect>
            <img
              src="/images/contacto.svg"
              class="img-icon img-icon-left"
              alt="port"
            />
            <button
              v-if="
                searchRequest.contact != '' && searchRequest.contact != null
              "
              type="button"
              class="close custom_close"
              aria-label="Close"
              @click="searchRequest.contact = ''"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="col-lg-3 mb-2 input-search-form">
            <multiselect
              v-model="searchRequest.pricelevel"
              :multiple="false"
              :close-on-select="true"
              :clear-on-select="true"
              :show-labels="false"
              :hide-selected="true"
              :options="priceLevelOptions"
              label="name"
              track-by="name"
              placeholder="Price Level"
              class="s-input"
              @input="updateQuoteSearchOptions"
            >
            </multiselect>
            <img
              src="/images/pricelevel.svg"
              class="img-icon img-icon-left"
              alt="port"
            />
            <button
              v-if="
                searchRequest.pricelevel != '' &&
                  searchRequest.pricelevel != null
              "
              type="button"
              class="close custom_close"
              aria-label="Close"
              @click="searchRequest.pricelevel = ''"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="col-lg-3 mb-2 input-search-form">
            <b-dropdown
              id="dropdown-carriers"
              :text="carrierText"
              ref="dropdown"
              class="m-2"
            >
              <b-dropdown-form
                :disabled="searchRequest.requestData.requested == 1"
              >
                <label class="mt-2">
                  <span>All Carriers</span>
                  <b-form-checkbox
                    v-model="allCarriers"
                    class="switch-all-carriers"
                  ></b-form-checkbox>
                </label>
                <b-form-group
                  v-if="datalists.carriers_api.length > 0"
                  label="SPOT Rates"
                >
                  <b-form-checkbox-group
                    v-model="searchRequest.carriersApi"
                    :options="carriersApiOptions"
                  ></b-form-checkbox-group>
                </b-form-group>
                <label>
                  <b-input
                    v-model="carrierSearchQuery"
                    placeholder="Search Carrier"
                  ></b-input>
                </label>
                <b-form-group label="Carriers">
                  <b-form-checkbox-group
                    id="carriers-list"
                    v-model="carriers"
                    :options="carrierOptionsSearch"
                  ></b-form-checkbox-group>
                </b-form-group>
              </b-dropdown-form>
            </b-dropdown>
            <img
              src="/images/carrier.svg"
              class="img-icon img-icon-left"
              alt="port"
            />
          </div>
        </div>
        <!-- FIN INPUTS -->

        <!-- INCLUDE CHECKBOX -->
        <div class="row mr-0 ml-4 mt-5 d-flex justify-content-start">
          <b-form-checkbox
            id="originCharges"
            v-model="searchRequest.originCharges"
            name="originCharges"
            class="mr-5 as-checkbox"
            @input="updateQuoteSearchOptions"
          >
            &nbsp;&nbsp;<b>Include origin charges</b>
          </b-form-checkbox>
          <b-form-checkbox
            id="destinationCharges"
            v-model="searchRequest.destinationCharges"
            name="destinationCharges"
            class="as-checkbox"
            @input="updateQuoteSearchOptions"
          >
            &nbsp;&nbsp;<b>Include destination charges</b>
          </b-form-checkbox>
        </div>
        <!-- FIN INCLUDE CHECKBOX -->
      </b-collapse>
      <!-- FIN ADDITIONAL SERVICES -->

      <!-- LCL FORM INPUTS -->
      <div class="row mr-0 ml-0 lcl-inputs" v-if="searchRequest.type == 'LCL'">
        <!-- Tabs Section -->
        <b-card no-body class="card-tabs col-12 font-tabs">
          <b-tabs card v-model="searchRequest.lclTypeIndex">
            <b-tab title="CALCULATE BY TOTAL SHIPMENT">
              <div class="row">
                <div v-if="invalidShipmentCalculation" class="col-12 mb-3">
                  <h6 class="invalid-data" style="color:red;">
                    <b-icon icon="exclamation-circle" class="mr-2"></b-icon
                    >Values cannot be empty or zero
                  </h6>
                </div>
                <div class="col-12 col-sm-3 d-flex align-items-center">
                  <label>
                    <b-form-input
                      v-model="lclShipmentQuantity"
                      :placeholder="
                        lclShipmentCargoType
                          ? lclShipmentCargoType.name
                          : 'Choose cargo type'
                      "
                      class="s-input-form mr-1"
                      type="number"
                      @input="setChargeableWeight()"
                    >
                    </b-form-input>
                    <img
                      src="/images/paquete.svg"
                      class="img-icon"
                      alt="paquete"
                    />
                  </label>
                  <div class="type-packages">
                    <multiselect
                      v-model="lclShipmentCargoType"
                      :multiple="false"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :show-labels="false"
                      :options="datalists.cargo_types"
                      :allow-empty="false"
                      track-by="name"
                      label="name"
                      placeholder="Select"
                      class="s-input no-select-style"
                    >
                    </multiselect>
                  </div>
                </div>

                <div class="col-12 col-sm-3">
                  <label class="d-flex align-items-center">
                    <b-form-input
                      v-model="lclShipmentWeight"
                      placeholder="Total Weight"
                      class="s-input-form mr-1"
                      type="number"
                      @input="setChargeableWeight()"
                    >
                    </b-form-input>
                    <img src="/images/peso.svg" class="img-icon" alt="peso" />
                    <span>Kg</span>
                  </label>
                </div>

                <div class="col-12 col-sm-3">
                  <label class="d-flex align-items-center">
                    <b-form-input
                      v-model="lclShipmentVolume"
                      placeholder="Total Volume"
                      class="s-input-form mr-1"
                      type="number"
                      @input="setChargeableWeight()"
                    >
                    </b-form-input>
                    <img
                      src="/images/espacio-de-trabajo.svg"
                      class="img-icon"
                      alt="volumen"
                    />
                    <span>m<sup>3</sup></span>
                  </label>
                </div>

                <div class="col-12 col-sm-3" style="text-align: center">
                  <h6><b>CHARGEABLE WEIGHT</b></h6>
                  <p>{{ lclShipmentChargeableWeight }} W/M</p>
                </div>
              </div>
            </b-tab>

            <b-tab title="CALCULATE BY PACKAGING">
              <div class="row">
                <div v-if="invalidPackagingCalculation" class="col-12 mb-3">
                  <h6 class="invalid-data" style="color:red;">
                    <b-icon icon="exclamation-circle" class="mr-2"></b-icon
                    >Values cannot be empty or zero
                  </h6>
                </div>

                <div id="surcharges-list" class="col-12">
                  <div class="row surcharge-content">
                    <div class="col-12 col-sm-1 pr-0">
                      <div
                        class="type-packages"
                        style="width: initial!important;"
                      >
                        <multiselect
                          v-model="addPackagingBar.cargoType"
                          :multiple="false"
                          :close-on-select="true"
                          :clear-on-select="false"
                          :show-labels="false"
                          :allow-empty="false"
                          label="name"
                          track-by="name"
                          :options="datalists.cargo_types"
                          placeholder="Select"
                          class="s-input no-select-style"
                        >
                        </multiselect>
                      </div>
                    </div>

                    <div class="col-12 col-sm-1 pr-0">
                      <label class="d-flex align-items-center">
                        <b-form-input
                          v-model="addPackagingBar.quantity"
                          type="number"
                          placeholder="Quantity"
                          class="s-input-form input-quantity"
                        >
                        </b-form-input>
                      </label>
                    </div>

                    <div class="col-12 col-sm-2 pr-0">
                      <label class="d-flex align-items-center">
                        <b-form-input
                          v-model="addPackagingBar.height"
                          placeholder="Height (cm)"
                          type="number"
                          class="s-input-form"
                        >
                        </b-form-input>
                        <img
                          src="/images/espacio-de-trabajo.svg"
                          class="img-icon"
                          alt="paquete"
                        />
                      </label>
                    </div>

                    <div class="col-12 col-sm-2 pr-0">
                      <label class="d-flex align-items-center">
                        <b-form-input
                          v-model="addPackagingBar.width"
                          placeholder="Width (cm)"
                          class="s-input-form"
                          type="number"
                        >
                        </b-form-input>
                        <img
                          src="/images/espacio-de-trabajo.svg"
                          class="img-icon"
                          alt="paquete"
                        />
                      </label>
                    </div>

                    <div class="col-12 col-sm-2 pr-0">
                      <label>
                        <b-form-input
                          v-model="addPackagingBar.depth"
                          placeholder="Length (cm)"
                          class="s-input-form"
                          type="number"
                        ></b-form-input>
                        <img
                          src="/images/espacio-de-trabajo.svg"
                          class="img-icon"
                          alt="paquete"
                        />
                      </label>
                    </div>

                    <div class="col-12 col-sm-2 pr-0">
                      <label>
                        <b-form-input
                          v-model="addPackagingBar.weight"
                          placeholder="Weight"
                          type="number"
                          class="s-input-form"
                        ></b-form-input>
                        <img
                          src="/images/peso.svg"
                          class="img-icon"
                          alt="peso"
                        />
                      </label>
                    </div>

                    <div
                      class="col-12 col-sm-1 pr-0 d-flex justify-content-center align-items-center"
                    >
                      <span @click="addLclPackaging" class="btn-add-surch"
                        ><b-icon icon="check-circle"></b-icon
                      ></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div
                  class="row col-12 mt-3 mb-3 mr-0 ml-0 pr-0 pl-0 data-surcharges"
                  v-for="(pack, index) in lclPackaging"
                  :key="index"
                >
                  <div class="col-12 col-sm-1 pr-0">
                    <p>{{ pack.cargoType.name }}</p>
                  </div>
                  <div class="col-12 col-sm-1 pr-0">
                    <p>{{ pack.quantity }}</p>
                  </div>
                  <div class="col-12 col-sm-2 pr-0">
                    <p>{{ pack.height }}</p>
                  </div>
                  <div class="col-12 col-sm-2 pr-0">
                    <p>{{ pack.width }}</p>
                  </div>
                  <div class="col-12 col-sm-2 pr-0">
                    <p>{{ pack.depth }}</p>
                  </div>
                  <div class="col-12 col-sm-2 pr-0">
                    <p>{{ pack.weight }}</p>
                  </div>
                  <div class="col-12 col-sm-1 pr-0">
                    <span v-on:click="deleteLclPackaging(index)"
                      ><b-icon icon="x-circle"></b-icon
                    ></span>
                  </div>
                </div>
              </div>

              <div class="row c-gap-20">
                <h6><b>TOTAL PACKAGES: </b></h6>
                {{ lclPackagingQuantity }} units {{ lclPackagingVolume }} m3
                {{ lclPackagingWeight }} Kg
                <h6 class="ml-10px"><b>CHARGEABLE WEIGHT: </b></h6>
                <p>{{ lclPackagingChargeableWeight }} W/M</p>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>
        <!-- End Tabs Section -->
      </div>
      <!-- FIN LCL FORM INPUTS -->

      <div v-if="!searching" class="col-lg-8">
        <div
          v-if="((searchRequest.type == 'FCL' &&
              Array.isArray(foundRates) &&
              foundRates.length == 0) ||
              (searchRequest.type == 'LCL' &&
                Array.isArray(foundRatesLcl) &&
                foundRatesLcl.length == 0)) &&
              !foundApiRates"
          class="alert alert-danger"
          role="alert"
        >
          No results for this particular route. Create an express contract or
          try another search
        </div>
      </div>

      <!-- BOTON SEARCH Y ADD CONTRAT -->
      <div class="row justify-content-center mr-0 ml-0">
        <div class="col-2 d-flex justify-content-center">
          <button
            v-if="!searching"
            class="btn-search"
            @click="searchButtonPressed"
          >
            SEARCH
          </button>

          <button v-else class="btn-search">
            <div class="spinner-border text-light" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </button>
          <b-button
            v-b-modal.add-contract
            v-if="searchRequest.type == 'FCL'"
            class="btn-add-contract ml-4"
            >Add Contract</b-button
          >
        </div>
      </div>
      <!-- FIN BOTON SEARCH Y ADD CONTRAT -->

      <!-- MODAL ADD CONTRACT -->
      <b-modal
        id="add-contract"
        size="lg"
        centered
        title="Created Contract"
        ref="my-modal"
        hide-footer
      >
        <!-- STEPS -->
        <div id="add-contract-form-steps" class="row pt-5 pb-5 custom-step-box">
          <div
            class="col-2 d-flex flex-column justify-content-center align-items-center step-add-contract"
            v-bind:class="{ stepComplete: isCompleteOne }"
          >
            <div class="add-contract-step">1</div>
            <span>Contract</span>
          </div>

          <div
            class="col-2 d-flex flex-column justify-content-center align-items-center step-add-contract"
            v-bind:class="{ stepComplete: isCompleteTwo }"
          >
            <div class="add-contract-step">2</div>
            <span>Ocean Freight</span>
          </div>

          <div
            class="col-2 d-flex flex-column justify-content-center align-items-center step-add-contract"
            v-bind:class="{ stepComplete: isCompleteThree }"
          >
            <div class="add-contract-step">3</div>
            <span>Remarks</span>
          </div>

          <div
            class="col-2 d-flex flex-column justify-content-center align-items-center step-add-contract"
            v-bind:class="{ stepComplete: isCompleteFour }"
          >
            <div class="add-contract-step">4</div>
            <span>Surcharges</span>
          </div>

          <div
            class="col-2 d-flex flex-column justify-content-center align-items-center step-add-contract"
            v-bind:class="{ stepComplete: isCompleteFive }"
          >
            <div class="add-contract-step">5</div>
            <span>Files</span>
          </div>
        </div>
        <!-- FIN STEPS -->

        <!-- FORMULARIO -->
        <form
          id="add-contract-form"
          action="/action_page.php"
          class="add-contract-form"
        >
          <!-- CONTRACT -->
          <fieldset v-if="stepOne">
            <div class="row">
              <div v-if="invalidInput" class="col-12 mt-3 mb-3">
                <h5 class="invalid-data">
                  <b-icon icon="exclamation-circle" class="mr-2"></b-icon>Please
                  complete all the fields.
                </h5>
              </div>

              <div class="col-12 mb-3">
                <label>
                  <b-form-input
                    v-model="reference"
                    placeholder="Reference"
                    class="input-modal"
                  ></b-form-input>
                  <img
                    src="/images/investigacion.svg"
                    alt="reference"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label>
                  <date-range-picker
                    :opens="'center'"
                    :locale-data="{
                      firstDay: 1,
                      format: 'yyyy/mm/dd',
                    }"
                    :singleDatePicker="false"
                    :autoApply="true"
                    :timePicker="false"
                    v-model="dateRange"
                    :linkedCalendars="true"
                    class="input-h"
                  ></date-range-picker>
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="carrier">
                  <multiselect
                    v-model="carrier"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.carriers"
                    label="name"
                    track-by="name"
                    placeholder="Carrier"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/carrier.svg"
                    alt="carrier"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="valueq">
                  <multiselect
                    v-model="valueEq"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.container_groups"
                    placeholder="Equipment"
                    label="name"
                    track-by="name"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/container.svg"
                    alt="container"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="direction">
                  <multiselect
                    v-model="direction"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.directions"
                    label="name"
                    track-by="name"
                    placeholder="Direction"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/entrega.svg"
                    alt="entrega"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
            </div>
          </fieldset>

          <!-- OCEAN FREIGHT -->
          <fieldset v-if="stepTwo">
            <div v-if="invalidInput" class="col-12 mt-3 mb-3">
              <h5 class="invalid-data">
                <b-icon icon="exclamation-circle" class="mr-2"></b-icon>Please
                complete all the fields.
              </h5>
            </div>

            <div class="row">
              <div class="col-12 col-sm-6 mb-3">
                <label for="origin">
                  <multiselect
                    v-model="origin"
                    :multiple="true"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.harbors"
                    label="display_name"
                    track-by="display_name"
                    placeholder="Origin"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/port.svg"
                    alt="origen"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="destination">
                  <multiselect
                    v-model="destination"
                    :multiple="true"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.harbors"
                    label="display_name"
                    track-by="display_name"
                    placeholder="Destination"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/port.svg"
                    alt="destination"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="carrier">
                  <multiselect
                    v-model="carrier"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.carriers"
                    label="name"
                    track-by="name"
                    placeholder="Carrier"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/carrier.svg"
                    alt="carrier"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                <label for="currency">
                  <multiselect
                    v-model="currency"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :show-labels="false"
                    :options="datalists.currency"
                    label="alphacode"
                    track-by="alphacode"
                    placeholder="Currency"
                    class="input-modal"
                  >
                  </multiselect>
                  <img
                    src="/images/dinero.svg"
                    alt="currency"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>

              <div
                v-for="(item, index) in items"
                :key="index"
                class="col-12 col-sm-6"
              >
                <label>
                  <b-form-input
                    :name="item.name"
                    :placeholder="item.placeholder"
                    class="input-modal mb-3"
                    v-model="equipType[item.name]"
                    required
                  ></b-form-input>
                  <img
                    src="/images/ordenar.svg"
                    alt="ordenar"
                    width="25px"
                    height="25px"
                  />
                </label>
              </div>
            </div>
          </fieldset>

          <!-- REMARKS  -->
          <fieldset v-if="stepThree">
            <h5 class="q-title">Remarks</h5>
            <br />
            <ckeditor
              id="inline-form-input-name"
              type="classic"
              v-model="remarks"
            ></ckeditor>

            <br /><br />
          </fieldset>

          <!-- SURCHARGES -->
          <fieldset v-if="stepFour">
            <div class="row">
              <div v-if="invalidSurcharge" class="col-12 mb-3">
                <h5 class="invalid-data">
                  <b-icon icon="exclamation-circle" class="mr-2"></b-icon
                  >Complete all the fileds
                </h5>
              </div>
              <div id="surcharges-list" class="col-12">
                <div class="row surcharge-content">
                  <div class="col-12 col-sm-3">
                    <label for="surcharge">
                      <multiselect
                        v-model="typeContract"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="datalists.surcharges"
                        label="name"
                        track-by="name"
                        placeholder="Type"
                        class="input-modal surcharge-input"
                      >
                      </multiselect>
                    </label>
                  </div>
                  <div class="col-12 col-sm-3">
                    <label for="calculationT">
                      <multiselect
                        v-model="calculationType"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="datalists.calculation_type"
                        label="name"
                        track-by="name"
                        placeholder="Calculation Type"
                        class="input-modal surcharge-input"
                      >
                      </multiselect>
                    </label>
                  </div>
                  <div class="col-12 col-sm-3">
                    <label for="currencyS">
                      <multiselect
                        v-model="currencySurcharge"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="datalists.currency"
                        label="alphacode"
                        track-by="alphacode"
                        placeholder="Currency"
                        class="input-modal surcharge-input"
                      >
                      </multiselect>
                    </label>
                  </div>
                  <div class="col-12 col-sm-2">
                    <label>
                      <b-form-input
                        v-model="amount"
                        placeholder="Amount"
                        class="input-modal surcharge-input"
                        style="padding: 21px 11px !important"
                        @keypress="isNumber($event)"
                      ></b-form-input>
                    </label>
                  </div>
                  <div
                    class="col-1 d-flex justify-content-center align-items-center"
                  >
                    <span v-on:click="addSurchargeModal" class="btn-add-surch"
                      ><b-icon icon="check-circle"></b-icon
                    ></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div
                class="row col-12 mt-3 mb-3 mr-0 ml-0 pr-0 pl-0 data-surcharges"
                v-for="(item, index) in dataSurcharge"
                :key="index"
              >
                <div class="col-12 col-sm-3">
                  <p>{{ item.type.name }}</p>
                </div>
                <div class="col-12 col-sm-3">
                  <p>{{ item.calculation.name }}</p>
                </div>
                <div class="col-12 col-sm-3">
                  <p>{{ item.currency.alphacode }}</p>
                </div>
                <div class="col-12 col-sm-2">
                  <p>{{ item.amount }}</p>
                </div>
                <div class="col-12 col-sm-1">
                  <span v-on:click="deleteSurchargeModal(index)"
                    ><b-icon icon="x-circle"></b-icon
                  ></span>
                </div>
              </div>
            </div>
          </fieldset>

          <!-- FILES -->
          <fieldset v-if="stepFive">
            <vue-dropzone
              ref="myVueDropzone"
              :useCustomSlot="true"
              id="dropzone"
              :options="dropzoneOptions"
              v-on:vdropzone-removed-file="removeThisFile"
              v-on:vdropzone-success="success"
            >
              <div class="dropzone-container">
                <div class="file-selector">
                  <h6 class="title-dropzone">Upload</h6>
                  <figure>
                    <svg
                      width="104px"
                      height="104px"
                      viewBox="0 0 104 104"
                      version="1.1"
                      xmlns="http://www.w3.org/2000/svg"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                    >
                      <defs>
                        <circle id="path-1" cx="36" cy="36" r="36"></circle>
                        <filter
                          x="-37.5%"
                          y="-29.2%"
                          width="175.0%"
                          height="175.0%"
                          filterUnits="objectBoundingBox"
                          id="filter-2"
                        >
                          <feOffset
                            dx="0"
                            dy="6"
                            in="SourceAlpha"
                            result="shadowOffsetOuter1"
                          ></feOffset>
                          <feGaussianBlur
                            stdDeviation="8"
                            in="shadowOffsetOuter1"
                            result="shadowBlurOuter1"
                          ></feGaussianBlur>
                          <feColorMatrix
                            values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.08 0"
                            type="matrix"
                            in="shadowBlurOuter1"
                            result="shadowMatrixOuter1"
                          ></feColorMatrix>
                          <feOffset
                            dx="0"
                            dy="1"
                            in="SourceAlpha"
                            result="shadowOffsetOuter2"
                          ></feOffset>
                          <feGaussianBlur
                            stdDeviation="1"
                            in="shadowOffsetOuter2"
                            result="shadowBlurOuter2"
                          ></feGaussianBlur>
                          <feColorMatrix
                            values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.11 0"
                            type="matrix"
                            in="shadowBlurOuter2"
                            result="shadowMatrixOuter2"
                          ></feColorMatrix>
                          <feMerge>
                            <feMergeNode in="shadowMatrixOuter1"></feMergeNode>
                            <feMergeNode in="shadowMatrixOuter2"></feMergeNode>
                          </feMerge>
                        </filter>
                      </defs>
                      <g
                        id="Page-1"
                        stroke="none"
                        stroke-width="1"
                        fill="none"
                        fill-rule="evenodd"
                      >
                        <g
                          id="Artboard"
                          transform="translate(-460.000000, -125.000000)"
                        >
                          <g
                            id="Group-4"
                            transform="translate(412.000000, 129.000000)"
                          >
                            <g
                              id="Group-2"
                              transform="translate(58.000000, 0.000000)"
                            >
                              <circle
                                id="Oval"
                                fill="#3560FF"
                                opacity="0.100000001"
                                cx="42"
                                cy="42"
                                r="42"
                              ></circle>
                              <g
                                id="Group"
                                transform="translate(6.000000, 6.000000)"
                              >
                                <g id="Oval">
                                  <use
                                    fill="black"
                                    fill-opacity="1"
                                    filter="url(#filter-2)"
                                    xlink:href="#path-1"
                                  ></use>
                                  <use
                                    fill="#FFFFFF"
                                    fill-rule="evenodd"
                                    xlink:href="#path-1"
                                  ></use>
                                </g>
                                <g
                                  id="upload-cloud"
                                  transform="translate(21.818182, 24.000000)"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                >
                                  <polyline
                                    id="Path"
                                    stroke="#000000"
                                    points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
                                  ></polyline>
                                  <path
                                    d="M14.3565525,12.0897285 L14.3565525,24.1794569"
                                    id="Path"
                                    stroke="#3560FF"
                                  ></path>
                                  <path
                                    d="M25.6438239,20.7792208 C28.2965835,19.3021499 29.6312816,16.1761528 28.8860265,13.1856562 C28.1407715,10.1951596 25.5052337,8.10125672 22.4838689,8.09921935 L20.8179512,8.09921935 C19.7219904,3.76967373 16.1275086,0.577339516 11.7773112,0.0700384831 C7.42711383,-0.43726255 3.22057026,1.84535014 1.19724759,5.81113853 C-0.826075091,9.77692693 -0.247870665,14.6059952 2.6515151,17.9569414"
                                    id="Path"
                                    stroke="#3560FF"
                                  ></path>
                                  <polyline
                                    id="Path"
                                    stroke="#3560FF"
                                    points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
                                  ></polyline>
                                </g>
                              </g>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </figure>
                  Drop Or Add Files Here
                  <p><span> or </span></p>
                  <button type="button" class="btn btn-primary btn-bg">
                    Choose file
                  </button>
                </div>
              </div>
            </vue-dropzone>
            <div v-if="contractAdded" class="alert alert-success" role="alert">
              Contract saved successfully!
            </div>
            <div
              v-if="contractAddedFailed"
              class="alert alert-danger"
              role="alert"
            >
              Failed creating contract, complete all the fields!
            </div>
          </fieldset>

          <div class="footer-add-contract-modal pl-4 pr-4">
            <b-button
              v-if="stepTwo || stepThree || stepFour || stepFive"
              v-on:click="backStep"
              variant="link"
              style="color: red"
              class="mr-3"
              >Back</b-button
            >
            <b-button
              v-on:click="nextStep"
              v-if="!stepFive"
              class="btn-create-quote"
              >Save & Continue</b-button
            >
            <b-button
              v-if="stepFive"
              class="btn-create-quote"
              @click="contracButtonPressed"
            >
              Create Contract</b-button
            >
          </div>
        </form>
        <!-- FIN FORMULARIO -->
      </b-modal>
      <!-- FIN MODAL ADD CONTRACT -->
    </div>
  </div>
</template>

<script>
import Search from "./Search";
import vue2Dropzone from "vue2-dropzone";
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
import "vue-multiselect/dist/vue-multiselect.min.css";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import actions from "../../actions";

export default {
  components: {
    Search,
    Multiselect,
    DateRangePicker,
    vueDropzone: vue2Dropzone,
  },
  data() {
    return {
      loaded: false,
      additionalVisible: false,
      searching: false,
      actions: actions,
      searchActions: {},
      datalists: {},
      IDRequest: {},
      searchRequest: {
        direction: 2,
        type: "FCL",
        destinationCharges: false,
        originCharges: false,
        showRateCurrency: false,
        deliveryType: {},
        selectedContainerGroup: {},
        containers: [],
        originPorts: [],
        destinationPorts: [],
        company: "",
        contact: "",
        pricelevel: "",
        carriers: [],
        carriersApi: [],
        originAddress: "",
        destinationAddress: "",
        dateRange: {
          startDate: new Date().toISOString(),
          endDate: new Date().toISOString(),
        },
        requestData: {},
        //LCL
        packaging: [],
        volume: 0,
        weight: 0,
        quantity: 0,
        chargeableWeight: 0,
        cargoType: "",
      },
      //LCL
      lclShipmentCargoType: "",
      lclShipmentChargeableWeight: 1,
      lclPackaging: [],
      lclPackagingVolume: "",
      lclPackagingWeight: "",
      lclPackagingQuantity: "",
      lclPackagingChargeableWeight: "",
      lclShipmentVolume: 1,
      lclShipmentWeight: 1,
      lclShipmentQuantity: 1,
      lclTypeIndex: 0,
      dropzoneOptions: {
        url: "/",
        thumbnailWidth: 150,
        maxFilesize: 10,
        headers: {
          "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]")
            .content,
        },
        addRemoveLinks: true,
        autoProcessQueue: false,
      },
      addPackagingBar: {
        cargoType: "",
        weight: "",
        width: "",
        depth: "",
        height: "",
        quantity: "",
      },
      selectedContainerGroup: {},
      containers: [],
      carriers: [],
      carriersApiOptions: [],
      containerOptions: [],
      typeOptions: ["FCL", "LCL"],
      deliveryTypeOptions: [],
      directionOptions: {},
      originPortOptions: {},
      destinationPortOptions: {},
      originAddressOptions: [],
      destinationAddressOptions: [],
      companyOptions: {},
      contactOptions: [],
      priceLevelOptions: {},
      carrierOptions: [],
      containerText: [],
      allCarriers: false,
      carrierText: "All Carriers Selected",
      errorsExist: false,
      responseErrors: {},
      foundRates: {},
      foundRatesLcl: {},
      foundApiRates: false,
      companyChosen: false,
      quoteData: {},
      originDistance: true,
      destinationDistance: true,
      originAutocompleteValue: null,
      destinationAutocompleteValue: null,
      originAddressPlaceholder: "Select an address",
      destinationAddressPlaceholder: "Select an address",
      invalidPackagingCalculation: false,
      invalidShipmentCalculation: false,
      //Gene defined
      ptdActive: false,
      dtpActive: false,
      dtdActive: false,
      indeterminate: false,
      selected: "radio1",

      //DATEPICKER
      locale: "en-US",
      dateFormat: { year: "numeric", month: "long", day: "numeric" },
      dateRange: {
        startDate: "",
        endDate: "",
      },

      //modal
      checked1: false,
      checked2: false,
      isActive: false,
      stepOne: true,
      stepTwo: false,
      stepThree: false,
      stepFour: false,
      stepFive: false,
      invalidInput: false,
      invalidSurcharge: false,
      valueEq: "",
      amount: "",
      currency: "",
      currencySurcharge: "",
      origin: "",
      destination: "",
      carrier: "",
      reference: "",
      equipType: {},
      direction: "",
      typeContract: "",
      calculationType: "",
      dataSurcharge: [],
      filterBy: "LOWEST PRICE",
      carrierSearchQuery: "",
      items: [],
      isCompleteOne: true,
      isCompleteTwo: false,
      isCompleteThree: false,
      isCompleteFour: false,
      isCompleteFour: false,
      isCompleteFive: false,
      contractAdded: false,
      contractAddedFailed: false,
      creatingContract: false,
    };
  },
  mounted() {
    api.getData({}, "/api/search/data", (err, data) => {
      this.setDropdownLists(err, data.data);
      this.getQuery();
    });
  },
  methods: {    
    //modal
    nextStep() {
      if (this.stepOne) {
        if (
          this.reference == "" ||
          this.carrier == "" ||
          this.valueEq == "" ||
          this.direction == "" ||
          this.vdata == ""
        ) {
          this.invalidInput = true;
          return;
        }

        this.invalidInput = false;
        this.stepOne = false;
        this.stepTwo = !this.stepTwo;
        this.isCompleteTwo = !this.isCompleteTwo;
        return;
      } else if (this.stepTwo) {
        if (
          this.origin == "" ||
          this.destination == "" ||
          this.carrier == "" ||
          this.currency == "" ||
          this.equipType == ""
        ) {
          this.invalidInput = true;
          return;
        }

        this.invalidInput = false;
        this.stepTwo = false;
        this.stepThree = !this.stepThree;
        this.isCompleteThree = !this.isCompleteThree;
        return;
      } else if (this.stepThree) {
        this.stepThree = false;
        this.stepFour = !this.stepFour;
        this.isCompleteFour = !this.isCompleteFour;
        return;
      } else if (this.stepFour) {
        this.invalidInput = false;
        this.stepFour = false;
        this.stepFive = !this.stepFive;
        this.isCompleteFive = !this.isCompleteFive;
      }
    },

    backStep() {
      if (this.stepFive) {
        this.invalidInput = false;
        this.stepFive = false;
        this.stepFour = !this.stepFour;
        this.isCompleteFour = !this.isCompleteFour;
        return;
      } else if (this.stepFour) {
        this.invalidInput = false;
        this.stepFour = false;
        this.stepThree = !this.stepThree;
        this.isCompleteFour = !this.isCompleteFour;
        return;
      } else if (this.stepThree) {
        this.stepThree = false;
        this.stepTwo = !this.stepTwo;
        this.isCompleteThree = !this.isCompleteThree;
        return;
      } else if (this.stepTwo) {
        this.invalidInput = false;
        this.stepTwo = false;
        this.stepOne = !this.stepOne;
        this.isCompleteTwo = !this.isCompleteTwo;
        return;
      }
    },

    addSurchargeModal() {
      if (
        this.typeContract == "" ||
        this.calculationType == "" ||
        this.currencySurcharge == ""
      ) {
        this.invalidSurcharge = true;
        return;
      }

      this.invalidSurcharge = false;

      var surcharge = {
        type: this.typeContract,
        calculation: this.calculationType,
        currency: this.currencySurcharge,
        amount: this.amount,
      };

      this.dataSurcharge.push(surcharge);

      this.typeContract = "";
      this.calculationType = "";
      this.currencySurcharge = "";
      this.amount = "";
    },

    //FILES OPTIONS Modal
    setFiles(data) {
      let file = {};
      let url = "";
      let vcomponent = this;
      let i = 0;

      let url_tags = document.getElementsByClassName("img-link");

      data.forEach(function(media) {
        vcomponent.$refs.myVueDropzone.manuallyAddFile(media, media.url);
        url_tags[i].setAttribute("href", media.url);
        i += 1;
      });
    },

    removeThisFile(file) {
      let id = this.$route.params.id;

      this.actions
        .removefile(id, { id: file.id })
        .then((response) => {})
        .catch((data) => {});
    },

    //Set lists of data
    setDropdownLists(err, data) {
      this.datalists = data;
      this.$emit("initialDataLoaded", this.datalists);
    },

    getQuery() {
      this.searchRequest.requestData = this.$route.query;

      if (Object.keys(this.searchRequest.requestData).length != 0) {
        if (this.searchRequest.requestData.requested == 0) {
          this.getSearchData(this.searchRequest.requestData.model_id);
        } else if (this.searchRequest.requestData.requested == 1) {
          this.getQuoteToDuplicate(this.searchRequest.requestData.model_id);
        }
      } else {
        this.$emit("searchTypeChanged", "code");
        this.setSearchDisplay(null);
      }
    },

    getSearchData(id) {
      let component = this;

      component.actions.search
        .retrieve(id)
        .then((response) => {
          component.searchData = response.data.data;
          component.setSearchDisplay(
            component.searchRequest.requestData.requested
          );
        })
        .catch((error) => {
          if (error.status === 422) {
            this.responseErrors = error.data.errors;
          }
        });
    },

    getQuoteToDuplicate(id) {
      actions.quotes
        .retrieve(id)
        .then((response) => {
          this.quoteData = response.data.data;
          this.$emit("quoteLoaded", this.quoteData);
          this.setSearchDisplay(this.searchRequest.requestData.requested);
        })
        .catch((error) => {
          if (error.status === 422) {
            this.responseErrors = error.data.errors;
          }
        });
    },

    //set UI elements
    setSearchDisplay(requestType) {
      let component = this;

      component.originPortOptions = component.datalists.harbors;
      component.destinationPortOptions = component.datalists.harbors;
      component.directionOptions = [
        {
          text: component.datalists.directions[1].name,
          value: component.datalists.directions[1].id,
        },
        {
          text: component.datalists.directions[0].name,
          value: component.datalists.directions[0].id,
        },
      ];
      component.containerGroupOptions = [
        {
          text: component.datalists.container_groups[0].name,
          value: component.datalists.container_groups[0],
        },
        {
          text: component.datalists.container_groups[1].name,
          value: component.datalists.container_groups[1],
        },
        {
          text: component.datalists.container_groups[2].name,
          value: component.datalists.container_groups[2],
        },
        {
          text: component.datalists.container_groups[3].name,
          value: component.datalists.container_groups[3],
        },
      ];
      if (component.carrierOptions.length == 0) {
        component.datalists.carriers.forEach(function(carrier) {
          component.carrierOptions.push({
            text: carrier.name,
            value: carrier,
          });
        });
      }
      if (component.carriersApiOptions.length == 0) {
        component.datalists.carriers_api.forEach(function(carrier_api) {
          component.carriersApiOptions.push({
            text: carrier_api.name,
            value: carrier_api,
          });
        });
      }
      component.contactOptions = component.datalists.contacts;
      component.priceLevelOptions = component.datalists.price_levels;
      component.deliveryTypeOptions = component.datalists.delivery_types.filter(
        function byGroup(dtype) {
          return !dtype.name.includes("Door");
        }
      );
      component.searchRequest.deliveryType = component.deliveryTypeOptions[0];
      component.allCarriers = true;
      component.searchRequest.originCharges =
        component.datalists.company_user.origincharge == null ? false : true;
      component.searchRequest.destinationCharges =
        component.datalists.company_user.destinationcharge == null
          ? false
          : true;

      this.fillInitialFields(requestType);
    },

    fillInitialFields(requestType) {
      let component = this;
      let origPortNames = [];
      let destPortNames = [];

      if (requestType == null) {
        this.selectedContainerGroup = this.datalists.container_groups[0];
        this.searchRequest.carriersApi = this.datalists.carriers_api;
        this.deliveryType = this.deliveryTypeOptions[0];
      } else if (requestType == 0) {
        this.searchRequest.type = this.searchData.type;
        this.$emit("searchTypeChanged", "code");
        this.searchRequest.direction = this.searchData.direction_id;
        //this.deliveryType = this.searchData.delivery_type;
        this.searchRequest.deliveryType = this.searchData.delivery_type;
        this.searchRequest.originPorts = [];
        component.searchData.origin_ports.forEach(function(origPort) {
          if (!origPortNames.includes(origPort.name)) {
            origPortNames.push(origPort.name);
            component.searchRequest.originPorts.push(origPort);
          }
        });
        this.searchRequest.destinationPorts = [];
        component.searchData.destination_ports.forEach(function(destPort) {
          if (!destPortNames.includes(destPort.name)) {
            destPortNames.push(destPort.name);
            component.searchRequest.destinationPorts.push(destPort);
          }
        });
        if (this.searchData.type == "FCL") {
          this.selectedContainerGroup = this.searchData.container_group;
          this.searchRequest.selectedContainerGroup = this.searchData.container_group;
          this.containers = this.searchData.containers;
          this.searchRequest.containers = this.searchData.containers;
        } else if (this.searchData.type == "LCL") {
          this.selectedContainerGroup = this.datalists.container_groups[0];
          this.containers = this.datalists.containers.filter(function byGroup(
            container
          ) {
            return container.gp_container_id == 1;
          });
          let equipLcl = JSON.parse(this.searchData.equipment);
          this.searchRequest.lclTypeIndex = equipLcl.type;
          if (equipLcl.type == 0) {
            this.lclShipmentCargoType = equipLcl.cargo_type;
            this.lclShipmentVolume = equipLcl.volume;
            this.lclShipmentWeight = equipLcl.weight;
            this.lclShipmentQuantity = equipLcl.quantity;
            this.lclShipmentChargeableWeight = equipLcl.chargeable_weight;
          } else if (equipLcl.type == 1) {
            this.lclPackaging = equipLcl.packaging;
            this.setChargeableWeight();
          }
          this.setSearchParameters();
        }
        this.searchRequest.dateRange.startDate =
          this.searchData.start_date + "T01:00:00";
        this.searchRequest.dateRange.endDate =
          this.searchData.end_date + "T01:00:00";
        this.searchRequest.company = this.searchData.company;
        this.unlockContacts();
        this.searchRequest.contact = this.searchData.contact;
        this.searchRequest.pricelevel = this.searchData.price_level;
        this.searchRequest.carriersApi = this.searchData.carriers_api;
        if (this.searchData.carriers.length != this.datalists.carriers.length) {
          component.carriers = [];
          this.searchRequest.carriers = this.searchData.carriers;
          component.searchData.carriers.forEach(function(carrier) {
            component.carriers.push(carrier);
          });
        } else {
          this.searchRequest.carriers = this.datalists.carriers;
        }
        this.searchRequest.originCharges =
          this.searchData.origin_charges == 0 ? false : true;
        this.searchRequest.destinationCharges =
          this.searchData.destination_charges == 0 ? false : true;
        this.searchRequest.showRateCurrency =
          this.datalists.company_user.options.totals_in_freight_currency == 1
            ? true
            : false;
        this.requestSearch();
      } else if (requestType == 1) {
        if (this.quoteData.search_options != null) {
          this.searchRequest.company = this.quoteData.search_options.company;
          this.unlockContacts();
          this.searchRequest.contact = this.quoteData.search_options.contact;
          this.searchRequest.pricelevel = this.quoteData.search_options.price_level;
          this.searchRequest.originCharges = this.quoteData.search_options.origin_charges;
          this.searchRequest.showRateCurrency = this.quoteData.search_options.show_rate_currency;
          this.searchRequest.destinationCharges = this.quoteData.search_options.destination_charges;
          this.searchRequest.originPorts = this.quoteData.search_options.origin_ports;
          this.searchRequest.destinationPorts = this.quoteData.search_options.destination_ports;
          this.searchRequest.dateRange.startDate =
            this.quoteData.search_options.start_date + "T01:00:00";
          this.searchRequest.dateRange.endDate =
            this.quoteData.search_options.end_date + "T01:00:00";
        } else {
          this.searchRequest.company = this.quoteData.company_id;
          this.unlockContacts();
          this.searchRequest.contact = this.quoteData.contact;
          this.searchRequest.pricelevel = this.quoteData.price_level;
          this.searchRequest.originPorts = this.quoteData.origin_ports_duplicate;
          this.searchRequest.destinationPorts = this.quoteData.destiny_ports_duplicate;
        }
        if (this.quoteData.direction_id != null) {
          this.searchRequest.direction = this.quoteData.direction_id;
        }
        this.searchRequest.type = this.quoteData.type;
        this.$emit("searchTypeChanged", "code");
        //this.deliveryType = this.quoteData.delivery_type;
        this.searchRequest.deliveryType = this.quoteData.delivery_type;
        this.selectedContainerGroup = this.quoteData.gp_container;
        this.searchRequest.selectedContainerGroup = this.quoteData.gp_container;
        this.containers = this.quoteData.containers;
        this.searchRequest.containers = this.quoteData.containers;
        this.searchRequest.carriers = this.datalists.carriers;
        this.searchRequest.carriersApi = this.datalists.carriers_api;
        this.searchRequest.harbors = this.datalists.harbors;
        this.searchRequest.currency = this.datalists.currency;
        this.searchRequest.calculation_type = this.datalists.calculation_type;
        this.searchRequest.surcharges = this.datalists.surcharges;
        this.requestSearch();
      }

      if (
        (this.searchRequest.company != null &&
          this.searchRequest.company != "") ||
        (this.searchRequest.contact != null &&
          this.searchRequest.contact != "") ||
        (this.searchRequest.pricelevel != null &&
          this.searchRequest.pricelevel != "")
      ) {
        this.additionalVisible = true;
      }

      this.setPriceLevels();
      this.loaded = true;
    },

    //Send Search Request to Controller
    searchButtonPressed() {
      let component = this;

      this.setSearchParameters();

      this.carrierSearchQuery = "";

      if (
        this.lclTypeIndex == 0 &&
        (this.lclShipmentVolume == "" ||
          this.lclShipmentWeight == "" ||
          this.lclShipmentQuantity == "")
      ) {
        this.invalidShipmentCalculation = true;

        setTimeout(function() {
          component.invalidShipmentCalculation = false;
          return;
        }, 1500);
      } else if (this.lclTypeIndex == 1 && this.lclPackaging.length == 0) {
        this.invalidPackagingCalculation = true;

        setTimeout(function() {
          component.invalidPackagingCalculation = false;
          return;
        }, 1500);
      }

      if (
        this.searchRequest.requestData.requested == undefined ||
        this.searchRequest.requestData.requested == 0
      ) {
        component.searchActions
          .create(this.searchRequest)
          .then((response) => {
            this.$router.push({
              path: `search`,
              query: {
                requested: 0,
                model_id: response.data.data.id,
              },
            });
            this.getQuery();
          })
          .catch((error) => {
            this.errorsExist = true;
            this.searching = false;
            if (error.status === 422) {
              this.responseErrors = error.data.errors;
              if (
                this.responseErrors.quantity ||
                this.responseErrors.volume ||
                this.responseErrors.weight
              ) {
                this.invalidShipmentCalculation = true;
                this.invalidPackagingCalculation = true;

                setTimeout(function() {
                  component.invalidShipmentCalculation = false;
                  component.invalidPackagingCalculation = false;
                  return;
                }, 1500);
              }
            }
          });
      } else if (this.searchRequest.requestData.requested == 1) {
        this.getQuery();
      }
    },

    setSearchParameters() {
      this.searching = true;
      if (this.searchRequest.type == "FCL") {
        this.searchRequest.selectedContainerGroup = this.selectedContainerGroup;
        this.searchRequest.containers = this.containers;
      } else if (this.searchRequest.type == "LCL") {
        if (this.searchRequest.lclTypeIndex == 0) {
          this.searchRequest.volume = this.lclShipmentVolume;
          this.searchRequest.weight = this.lclShipmentWeight;
          this.searchRequest.quantity = this.lclShipmentQuantity;
          this.searchRequest.chargeableWeight = this.lclShipmentChargeableWeight;
          this.searchRequest.cargoType = this.lclShipmentCargoType;
        } else if (this.searchRequest.lclTypeIndex == 1) {
          this.searchRequest.packaging = this.lclPackaging;
          this.searchRequest.volume = this.lclPackagingVolume;
          this.searchRequest.weight = this.lclPackagingWeight;
          this.searchRequest.quantity = this.lclPackagingQuantity;
          this.searchRequest.chargeableWeight = this.lclPackagingChargeableWeight;
        }
      }
      //this.searchRequest.deliveryType = this.deliveryType;
      this.searchRequest.carriers = this.carriers;
      this.errorsExist = false;
    },

    alert(msg, type) {
      this.$toast.open({
        message: msg,
        type: type,
        duration: 5000,
        dismissible: true,
      });
    },

    contracButtonPressed() {
      let component = this;
      let data = {
        //stepOne contract
        reference: this.reference,
        datarange: this.dateRange,
        carrier: this.carrier,
        direction: this.direction,
        valueEq: this.valueEq,
        //stepTwo Ocean
        origin: this.origin,
        destination: this.destination,
        carrier: this.carrier,
        currency: this.currency,
        rates: this.equipType,
        //stepthree remarks
        remarks: this.remarks,
        //stepFour Surcharges
        dataSurcharge: this.dataSurcharge,
        //stepFive
      };
      let vcomponent = this;
      
      if(!this.creatingContract){

        vcomponent.creatingContract = true;

        component.searchActions
          .createContract(data)
          .then((response) => {
            vcomponent.$refs.myVueDropzone.dropzone.options.url = `/api/v2/contracts/${response.data.id}/storeMedia`;
            vcomponent.$refs.myVueDropzone.processQueue();
            vcomponent.contractAdded = true;
  
            setTimeout(function() {
              vcomponent.contractAdded = false;
              vcomponent.$refs["my-modal"].hide();
              vcomponent.creatingContract = false;
              vcomponent.$router.go();
            }, 3000);
          })
          .catch((error) => {
            if (error.status === 422) {
              vcomponent.contractAddedFailed = true;
              this.responseErrors = error.data.errors;
              setTimeout(function() {
                vcomponent.contractAddedFailed = false;
              }, 5000);
            }
          });
      }
    },

    requestSearch() {
      let component = this;
      this.$emit("clearResults",'searchStarted');
      this.searching = true;
      this.$emit("searchRequested", this.searchRequest);

      component.searchActions
        .process(this.searchRequest)
        .then((response) => {
          response.data.data.forEach(function(rate) {
            if (
              component.searchRequest.type == "FCL" &&
              typeof rate.containers == "string"
            ) {
              rate.containers = JSON.parse(rate.containers);
            }
            if (rate.search == undefined) {
              rate.search = response.data.data[0].search;
            }
          });
          if (this.searchRequest.type == "FCL") {
            this.foundRates = response.data.data;
          } else if (this.searchRequest.type == "LCL") {
            this.foundRatesLcl = response.data.data;
          }
          this.$emit("searchSuccess", response.data.data);
        })
        .catch((error) => {
          this.errorsExist = true;
          if (error.status === 422) {
            this.responseErrors = error.data.errors;
          }
        });
    },

    unlockContacts() {
      let component = this;
      let dlist = this.datalists;

      if (
        component.searchRequest.company != null &&
        component.searchRequest.company != ""
      ) {
        component.contactOptions = [];

        dlist.contacts.forEach(function(contact) {
          if (contact.company_id == component.searchRequest.company.id) {
            component.contactOptions.push(contact);
          }
        });
        component.companyChosen = true;
      } else {
        component.companyChosen = false;
      }
    },

    setPriceLevels() {
      let component = this;
      let dlist = this.datalists;
      let prices = [];

      component.priceLevelOptions = [];

      if (component.searchRequest.company != null) {
        dlist.company_prices.forEach(function(comprice) {
          prices.push(comprice.price_id);
          if (component.searchRequest.company.id == comprice.company_id) {
            dlist.price_levels.forEach(function(price) {
              if (
                price.id == comprice.price_id &&
                !component.priceLevelOptions.includes(price)
              ) {
                component.priceLevelOptions.push(price);
              }
            });
          }
        });

        dlist.price_levels.forEach(function(price) {
          if (
            !prices.includes(price.id) &&
            !component.priceLevelOptions.includes(price)
          ) {
            component.priceLevelOptions.push(price);
          }
        });
      } else {
        let prices = [];

        dlist.company_prices.forEach(function(comprice) {
          prices.push(comprice.price_id);
        });

        dlist.price_levels.forEach(function(price) {
          if (!prices.includes(price.id)) {
            component.priceLevelOptions.push(price);
          }
        });
      }
    },

    updateQuoteSearchOptions() {
      let component = this;
      
      if (this.searchRequest.requestData.requested == 1) {
        component.actions.quotes
          .updateSearch(
            this.searchRequest.requestData.model_id,
            this.searchRequest
          )
          .then((response) => {
            console.log("Quote updated!");
          })
          .catch((error) => {
            this.errorsExist = true;
            if (error.status === 422) {
              this.responseErrors = error.data.errors;
            }
          });
      }
    },

    setOriginAddressMode() {
      let component = this;

      if (component.searchRequest.originPorts.length > 1) {
        component.originAddressPlaceholder =
          "Please select only one Origin Port";
      } else {
        component.originAddressPlaceholder = "Select an address";
        if (component.searchRequest.originPorts.length == 1) {
          component.datalists.inland_distances.forEach(function(distance) {
            if (
              distance.harbor_id == component.searchRequest.originPorts[0].id
            ) {
              component.originAddressOptions.push(distance);
            }
          });

          if (component.originAddressOptions.length == 0) {
            component.originDistance = false;
          } else {
            component.originDistance = true;
          }
        }
      }
    },

    setDestinationAddressMode() {
      let component = this;

      if (component.searchRequest.destinationPorts.length > 1) {
        component.destinationAddressPlaceholder =
          "Please select only one Origin Port";
      } else {
        component.destinationAddressPlaceholder = "Select an address";
        if (component.searchRequest.destinationPorts.length == 1) {
          component.datalists.inland_distances.forEach(function(distance) {
            if (
              distance.harbor_id ==
              component.searchRequest.destinationPorts[0].id
            ) {
              component.destinationAddressOptions.push(distance);
            }
          });

          if (component.destinationAddressOptions.length == 0) {
            component.destinationDistance = false;
          } else {
            component.destinationDistance = true;
          }
        }
      }
    },

    setOriginPlace(place) {
      this.searchRequest.originAddress = place.formatted_address;
    },

    setDestinationPlace(place) {
      this.searchRequest.destinationAddress = place.formatted_address;
    },

    commitOriginAutocomplete() {
      this.originAutocompleteValue = this.searchRequest.originAddresses;
    },

    commitDestinationAutocomplete() {
      this.destinationAutocompleteValue = this.searchRequest.destinationAddresses;
    },

    setSearchType() {
      if (this.searchRequest.type == "LCL") {
        if (this.lclShipmentCargoType == "") {
          this.lclShipmentCargoType = this.datalists.cargo_types[0];
        }
        if (this.addPackagingBar.cargoType == "") {
          this.addPackagingBar.cargoType = this.datalists.cargo_types[0];
        }
      }
      this.$emit("searchTypeChanged", "dd");
    },

    setActions() {
      if (this.searchRequest.type == "FCL") {
        this.searchActions = this.actions.search;
      } else if (this.searchRequest.type == "LCL") {
        this.searchActions = this.actions.searchlcl;
      }
    },

    addLclPackaging() {
      let component = this;

      if (
        this.addPackagingBar.weight &&
        this.addPackagingBar.width &&
        this.addPackagingBar.depth &&
        this.addPackagingBar.height &&
        this.addPackagingBar.quantity &&
        this.addPackagingBar.cargoType
      ) {
        if (
          this.addPackagingBar.weight > 0 &&
          this.addPackagingBar.width > 0 &&
          this.addPackagingBar.depth > 0 &&
          this.addPackagingBar.height > 0 &&
          this.addPackagingBar.quantity > 0
        ) {
          let newPackaging = _.cloneDeep(this.addPackagingBar);
          this.lclPackaging.push(newPackaging);
          this.setChargeableWeight();
          this.clearAddPackagingBar();
        } else {
          this.invalidPackagingCalculation = true;

          setTimeout(function() {
            component.invalidPackagingCalculation = false;
          }, 1500);
        }
      } else {
        this.invalidPackagingCalculation = true;

        setTimeout(function() {
          component.invalidPackagingCalculation = false;
        }, 1500);
      }
    },

    clearAddPackagingBar() {
      this.addPackagingBar.weight = null;
      this.addPackagingBar.width = null;
      this.addPackagingBar.depth = null;
      this.addPackagingBar.height = null;
      this.addPackagingBar.quantity = null;
      this.addPackagingBar.cargoType = null;
    },

    deleteLclPackaging(index) {
      this.lclPackaging.splice(index, 1);
      this.setChargeableWeight();
    },

    deleteSurchargeModal(index) {
      this.dataSurcharge.splice(index, 1);
    },

    //upload files
    success(file, response) {
      let url_tags = $(".img-link").last();
      url_tags.attr("href", response.url);
    },

    isNumber: function(evt) {
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

    setChargeableWeight() {
      if (this.searchRequest.lclTypeIndex == 0) {
        if (this.lclShipmentVolume > this.lclShipmentWeight / 1000) {
          this.lclShipmentChargeableWeight =
            this.lclShipmentVolume ;
        } else {
          this.lclShipmentChargeableWeight =
            (this.lclShipmentWeight / 1000) ;
        }
      } else if (this.searchRequest.lclTypeIndex == 1) {
        let component = this;

        component.lclPackagingQuantity = 0;
        component.lclPackagingVolume = 0;
        component.lclPackagingWeight = 0;

        component.lclPackaging.forEach(function(pack) {
          component.lclPackagingQuantity += parseFloat(pack.quantity);
          component.lclPackagingVolume +=
            (parseFloat(pack.depth) *
            parseFloat(pack.height) *
            parseFloat(pack.width)) / 1000000;
          component.lclPackagingWeight += parseFloat(pack.weight);
        });

        if (this.lclPackagingVolume > this.lclPackagingWeight / 1000) {
          this.lclPackagingChargeableWeight =
            this.lclPackagingVolume ;
        } else {
          this.lclPackagingChargeableWeight =
            (this.lclPackagingWeight / 1000);
        }
      }
    },
  },
  watch: {

    /**deliveryType: function () {
            if (this.deliveryType.id == 1) {
                this.ptdActive = false;
                this.dtpActive = false;
                this.dtdActive = false;
                return;
            } else if (this.deliveryType.id == 2) {
                this.dtpActive = false;
                this.dtdActive = false;

                this.ptdActive = !this.ptdActive;

                this.setDestinationAddressMode();
                return;
            } else if (this.deliveryType.id == 3) {
                this.ptdActive = false;
                this.dtdActive = false;

                this.dtpActive = !this.dtpActive;

                this.setOriginAddressMode();
                return;
            } else if (this.deliveryType.id == 4) {
                this.ptdActive = false;
                this.dtpActive = false;

                this.dtdActive = !this.dtdActive;

                this.setDestinationAddressMode();
                this.setOriginAddressMode();
                return;
            }
        },**/

    selectedContainerGroup: function() {
      let component = this;
      let fullContainersByGroup = [];
      let selectedContainersByGroup = [];

      component.containerOptions = [];

      component.datalists.containers.forEach(function(container) {
        if (component.selectedContainerGroup.id == container.gp_container_id) {
          selectedContainersByGroup.push(container);
          fullContainersByGroup.push(container);
        }
      });

      fullContainersByGroup.forEach(function(cont) {
        component.containerOptions.push({
          text: cont.code,
          value: cont,
        });
      });

      if (
        component.searchRequest.type == "FCL" &&
        Object.keys(component.searchRequest.requestData).length != 0 &&
        component.searchRequest.requestData.requested == 0 &&
        component.searchData.container_group.id ==
          component.selectedContainerGroup.id
      ) {
        selectedContainersByGroup = component.searchData.containers;
      } else {
        if (selectedContainersByGroup.length > 3) {
          selectedContainersByGroup.splice(3, 2);
        }
      }

      component.containers = selectedContainersByGroup;
    },

    containers: function() {
      let component = this;

      //Invocamos un computed method que ordena la propiedad containers
      this.sortedContainers;

      component.containerText = [];

      component.containers.forEach(function(container) {
        component.containerText.push(container.code);
      });
      if (this.containers == []) {
        this.containerText = ["Select Containers"];
      }
    },

    carriers() {
      let component = this;

      if (component.carriers.length == component.datalists.carriers.length) {
        component.carrierText = "All Carriers Selected";
      } else if (component.carriers.length >= 5) {
        component.carrierText =
          component.carriers.length + " Carriers Selected";
      } else if (component.carriers.length == 0) {
        if (component.searchRequest.carriersApi.length == 0) {
          component.carrierText = "Select a Carrier";
        } else {
          component.carrierText = "SPOT Providers selected";
        }
      } else {
        let selectedCarriers = [];

        component.carriers.forEach(function(carrier) {
          selectedCarriers.push(carrier.name);
        });

        component.carrierText = selectedCarriers.join(", ");
      }
    },

    allCarriers() {
      let component = this;

      if (component.allCarriers) {
        component.carriers = [];
        // Check all
        component.datalists.carriers.forEach(function(carrier) {
          component.carriers.push(carrier);
        });
      } else {
        component.carriers = [];
      }
    },

    valueEq: function(newValue, oldValue) {
      let component = this;

      this.items.splice({});
      if (newValue && newValue != "") {
        this.datalists.containers.forEach(function(container) {
          if (container.gp_container_id == newValue.id) {
            component.items.push({
              name: "C" + container.code,
              placeholder: container.code,
              value: 0,
            });
          }
        });
        return;
      }
    },
  },
  computed: {
    carrierOptionsSearch() {
      return this.carrierOptions.filter((c) =>
        c.text.toLowerCase().includes(this.carrierSearchQuery.toLowerCase())
      );
    },
    sortedContainers(){
      return this.containers.sort((a,b) => a.id-b.id);
    }
  },
};
</script>

<style scoped>
.c-gap-20 {
  column-gap: 20px;
}

.ml-10px {
  margin-left: 10px;
}
</style>
