<template>
  <div v-if="loaded" style="padding: 0px 25px;">
    <div v-if="freights.length == 0">
      <h4>
        Nothing to totalize. Start by adding a new freight at the Ocean Freight
        Tab
      </h4>
    </div>
    <div v-else>
      <div class="card" style="width: 100%">
        <div class="card-body row" style="overflow: inherit">
          <div class="col-lg-8">
            <div v-if="errorsExist" class="alert alert-danger" role="alert">
              Exchange rates can't be zero!
            </div>
          </div>
          <div class="col-lg-12">
            <!-- Show Totals Checkbox-->
            <div
              class="col-12 d-flex align-items-center justify-content-start flex-wrap mt-4 mb-4"
            >
              <ul class="exchange-rates" style="margin-bottom: 0px !important">
                <li class="mr-3">
                  <b class="mr-3">Select PDF:</b>
                  <multiselect
                    v-model="selectPDF"
                    :multiple="false"
                    :options="selectPDFOptions"
                    :searchable="false"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :show-labels="false"
                    :hide-selected="true"
                    :allow-empty="false"
                    label="name"
                    track-by="name"
                    style="width: 180px"
                    @input="updatePdfOptions('typePDF')"
                  >
                  </multiselect>
                </li>
              </ul>

              <span><b class="mr-3">Show totals in:</b></span
              >
              <multiselect
                v-model="totalsCurrency"
                :multiple="false"
                :options="datalists['filtered_currencies']"
                :searchable="true"
                :close-on-select="true"
                :clear-on-select="false"
                :show-labels="false"
                :hide-selected="true"
                :allow-empty="false"
                label="alphacode"
                track-by="alphacode"
                @input="updatePdfOptions('showAs')"
                style="width: 90px"
              >
              </multiselect>

              <ul
                class="exchange-rates"
                style="margin-bottom: 0px !important"
              >
                <li
                  v-for="(currency, key) in exchangeRates"
                  v-show="totalsCurrency.alphacode != currency.alphacode"
                  :key="key"
                  class="mr-3"
                >
                  <b class="mr-3"
                    >{{ currency.alphacode }} to
                    {{ totalsCurrency.alphacode }}:</b
                  >
                  <b-form-input
                    v-if="totalsCurrency.alphacode == 'USD'"
                    v-model="currency.exchangeUSD"
                    type="number"
                    @blur="updatePdfOptions('exchangeRates')"
                    style="width: 90px"
                  ></b-form-input>
                  <b-form-input
                    v-else-if="totalsCurrency.alphacode == 'EUR'"
                    v-model="currency.exchangeEUR"
                    type="number"
                    @blur="updatePdfOptions('exchangeRates')"
                    style="width: 90px"
                  ></b-form-input>
                </li>
              </ul>
            </div>
            <!-- Show Totals Checkbox End-->
          </div>
        </div>

        <!--  -->
        <!-- NEW SECTION: COST SHEET -->
        <!--  -->

        <section>
          <menu>
            <ul>
              <li v-for="(item, index) in checks" :key="index">
                <input type="checkbox" name="item1" id="" v-model="item.show" />
                <label for="item1"> {{ item.name }} </label>
              </li>
            </ul>

            <!--  -->
            <!-- FILTER  -->
            <!--  -->

            <div>
              <button type="button" @click="openedFilter = !openedFilter">
                <strong>Filter <settings-icon></settings-icon> </strong>
              </button>

              <div v-if="openedFilter" class="filter-opened">
                <div class="circle-x" @click="openedFilter = false">
                  <strong>X</strong>
                </div>

                <div class="mb-5">
                  <p class="mr-3 mb-3 text-left">Route:</p>

                  <multiselect
                    v-model="filteredRouteSelected"
                    :multiple="false"
                    :options="filteredRoutesOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :show-labels="false"
                    :allow-empty="false"
                    @input="addOptionSelectedCarriers"
                    label="route"
                    track-by="route"
                    style="min-width: 200px"
                  >
                  </multiselect>
                </div>
                <div>
                  <p class="mr-3 mb-3 text-left">Shipping Company:</p>

                  <multiselect
                    v-model="filteredCostSheetSelected"
                    :multiple="false"
                    :options="filteredCarrierOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :show-labels="false"
                    :allow-empty="false"
                    @input="setCostSheet"
                    label="carrier_name"
                    track-by="rate_id"
                    placeholder="Seleccione ruta"
                    style="min-width: 200px"
                  >
                  </multiselect>
                </div>
              </div>
            </div>
          </menu>
          
          <div class="mx-5">
            <template v-if="dataFeatCostSheet.type == 'LCL'">
              <h4 v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateHeader'+i">
                <strong>{{rate.carrier.name}}</strong>
                | {{rate.POL.name}} - {{rate.POD.name}}
              </h4> 
              <div
                :class="item.show ? 'opened' : ''"
                class="cost-card"
                v-for="(item, i) in checks"
                :key="'item'+i"
              >
                <table>
                  <thead>
                    <tr>
                      <th>{{ item.name }}</th>
                      <th></th>
                      <th>{{dataFeatCostSheet.containers_head.name}}</th>
                    </tr>
                  </thead>
                  <tbody v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateBody'+i">
                    <template v-if="item.name == 'Buying rates'">
                      <tr v-for="(freight, index) in rate.buying[0].freight" :key="'FreightBuying'+index">
                        <td>{{freight.surcharge}}</td>
                        <td>{{freight.type}}</td>
                        <td>{{freight.amount}} {{freight.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(local, index) in rate.buying[0].locales" :key="'localBuying'+index">
                        <td>{{local.surcharge}}</td>
                        <td>Local - {{local.type}}</td>
                        <td>{{local.amount}} {{local.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(inland, index) in rate.buying[0].inlands" :key="'inlandBuying'+index">
                        <td>{{inland.charge}}</td>
                        <td>Inland - {{inland.type}}</td>
                        <td>{{inland.rate}} {{inland.currency.alphacode}}</td>
                      </tr>                    
                    </template>
                    <template v-if="item.name == 'Selling rates'">
                      <tr>
                        <td>Ocean Freight</td>
                        <td>Total Freights</td>
                        <td>{{rate.selling[0].total_freight}}  {{rate.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(local, index) in rate.selling[0].locales" :key="'localSelling'+index">
                        <td>{{local.surcharge}}</td>
                        <td>Local - {{local.type}}</td>
                        <td>{{local.amount}} {{local.currency.alphacode}}</td>
                      </tr>
                      <tr v-if="rate.selling[0].inlands !== 0">
                        <td>Inlands</td>
                        <td>Total Inlands</td>
                        <td>{{rate.selling[0].inlands}}  {{rate.currency.alphacode}}</td>
                      </tr>
                    </template>
                    <template v-if="item.name == 'Total Profits'">
                      <tr>
                        <td class="total-in-table">Profit</td>
                        <td>Total Profit</td>
                        <td>{{rate.profit[0].profit}} {{rate.currency.alphacode}}</td>
                      </tr>
                      <tr>
                        <td>Profit %</td>
                        <td>% profit</td>
                        <td>{{rate.profit[0].profit_percentage}}</td>
                      </tr>
                    </template>
                  </tbody>
                  <thead v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateTotal'+i">
                    <template v-if="item.name == 'Buying rates'">
                      <tr>
                        <th class="total-in-table">Total {{ item.name }}</th>
                        <th></th>
                        <th><p>{{rate.buying[0].totals}} {{rate.currency.alphacode}}</p></th>
                      </tr>
                    </template>
                    <template v-if="item.name == 'Selling rates'">
                      <tr>
                        <th class="total-in-table">Total {{ item.name }}</th>
                        <th></th>
                        <th><p>{{rate.selling[0].totals}} {{rate.currency.alphacode}}</p></th>
                      </tr>
                    </template>
                  </thead>
                </table>
              </div>
            </template>
            <template v-if="dataFeatCostSheet.type == 'FCL'">
              <h4 v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateHeader'+i">
                <strong>{{rate.carrier.name}}</strong>
                | {{rate.POL.name}} - {{rate.POD.name}}
              </h4> 
              <div
                :class="item.show ? 'opened' : ''"
                class="cost-card"
                v-for="(item, i) in checks"
                :key="'item'+i"
              >
                <table>
                  <thead>
                    <tr>
                      <th>{{ item.name }}</th>
                      <th></th>
                      <th v-for="(container, i) in dataFeatCostSheet.containers_head" :key="'containerName'+i">{{container.name}}</th>
                    </tr>
                  </thead>

                  <tbody v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateBody'+i">
                    <template v-if="item.name == 'Buying rates'">
                      <tr v-for="(freight, index) in rate.buying[0].freight" :key="'FreightBuying'+index">
                        <td>{{freight.surcharge}}</td>
                        <td>{{freight.type}}</td>
                        <td v-for="(container, i) in freight.amount" :key="'container'+i">{{container.amount}} {{freight.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(local, index) in rate.buying[0].locales" :key="'localBuying'+index">
                        <td>{{local.surcharge}}</td>
                        <td>Local - {{local.type}}</td>
                        <td v-for="(container, i) in local.amount" :key="'localContainer'+i">{{container.amount}} {{local.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(inland, index) in rate.buying[0].inlands" :key="'inlandBuying'+index">
                        <td>{{inland.charge}}</td>
                        <td>Inland - {{inland.type}}</td>
                        <td v-for="(container, i) in inland.rate" :key="'inlandContainer'+i">{{container.amount}} {{inland.currency.alphacode}}</td>
                      </tr>                    
                    </template>
                    <template v-if="item.name == 'Selling rates'">
                      <tr>
                        <td>Ocean Freight</td>
                        <td>Total Freights</td>
                        <td v-for="(freight, i) in rate.selling[0].total_freight" :key="'totalFreigth'+i">{{freight.amount}}  {{rate.currency.alphacode}}</td>
                      </tr>
                      <tr v-for="(local, index) in rate.selling[0].locales" :key="'localSelling'+index">
                        <td>{{local.surcharge}}</td>
                        <td>Local - {{local.type}}</td>
                        <td v-for="(container, i) in local.amount" :key="'localContainer'+i">{{container.amount}} {{local.currency.alphacode}}</td>
                      </tr>
                      <tr v-if="rate.selling[0].inlands[0]">
                        <td>Inlands</td>
                        <td>Total Inlands</td>
                        <td v-for="(inland, i) in rate.selling[0].inlands[0]" :key="'inlandContainer'+i">{{inland.amount}} {{rate.currency.alphacode}}</td>
                      </tr>
                    </template>
                    <template v-if="item.name == 'Total Profits'">
                      <tr>
                        <td class="total-in-table">Profit</td>
                        <td>Total Profit</td>
                        <td v-for="(profit, i) in rate.profit[0].profit" :key="'profit'+i" class="total-in-table">{{profit.amount}} {{rate.currency.alphacode}}</td>
                      </tr>
                      <tr>
                        <td>Profit %</td>
                        <td>% profit</td>
                        <td v-for="(profitPercentage, i) in rate.profit[0].profit_percentage" :key="'profitPercentage'+i" class="total-in-table">{{profitPercentage.amount}}</td>
                      </tr>
                    </template>
                  </tbody>

                  <thead v-for="(rate, i) in dataFeatCostSheet.rates" :key="'rateTotal'+i">
                    <template v-if="item.name == 'Buying rates'">
                      <tr>
                        <th class="total-in-table">Total {{ item.name }}</th>
                        <th></th>
                        <th v-for="(totalContainers, i) in rate.buying[0].totals" :key="'totalContainers'+i"><p>{{totalContainers.amount}} {{rate.currency.alphacode}}</p></th>
                      </tr>
                    </template>
                    <template v-if="item.name == 'Selling rates'">
                      <tr>
                        <th class="total-in-table">Total {{ item.name }}</th>
                        <th></th>
                        <th v-for="(totalContainers, i) in rate.selling[0].totals" :key="'totalContainers'+i"><p>{{totalContainers.amount}} {{rate.currency.alphacode}}</p></th>
                      </tr>
                    </template>
                  </thead>
                </table>
              </div>
            </template>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Selectable from "../../../assets/components/common/Selectable.vue";
import SorteableDropdown from "../../../assets/components/common/SorteableDropdown.vue";
import SettingsIcon from "../../../assets/components/Icons/SettingsIcon.vue";

export default {
  props: {
    freights: Array,
    datalists: {},
    actions: Object,
  },
  components: {
    Multiselect,
    Selectable,
    SorteableDropdown,
    SettingsIcon,
  },
  data() {
    return {
      showTotals: false,
      totalsCurrency: {},
      exchangeRates: null,
      loaded: false,
      pdfOptions: {},
      currentQuoteData: {},
      selectPDF: {},
      selectPDFOptions: [
        { id: 1, name: "PDF totals only" },
        { id: 2, name: "PDF totals + detailed costs" },
        { id: 3, name: "PDF detailed costs only" },
      ],
      errorsExist: false,

      //  NEW SECTION: COST SHEET
      openedFilter: false,
      buying_rates: false,
      selling_rates: false,
      total_profit: false,
      checks: [
        { name: "Buying rates", show: true },
        { name: "Selling rates", show: true },
        { name: "Total Profits", show: true },
      ],
      data: [],
      filteredRoutesOptions: [],
      filteredCarrierOptions: [],
      filteredRouteSelected: [],
      filteredCostSheetSelected: [],
      dataFeatCostSheet: []
    };
  },
  created() {
    let id = this.$route.params.id;
    this.$emit("totalsLoaded", id);
  },
  methods: {
    setInitialOptions(quoteData) {
      
      // Asigar data de cotización para disponer de el 
      this.currentQuoteData = quoteData;

      // Obtener primer rate de la cotización
      this.filteredCostSheetSelected['rate_id'] = this.currentQuoteData.rates[0].id;

      //cargar hoja de costo de rate por defecto
      this.setCostSheet();

      //obtener opciones de filtro para generar hoja de costos
      this.getFilteredRoutesOptions(this.currentQuoteData); 

      if (typeof this.currentQuoteData.pdf_options == "string") {
        this.pdfOptions = JSON.parse(this.currentQuoteData.pdf_options);
      } else {
        this.pdfOptions = this.currentQuoteData.pdf_options;
      }

      this.showTotals = this.pdfOptions["showTotals"];

      this.totalsCurrency = this.pdfOptions["totalsCurrency"];

      this.exchangeRates = this.pdfOptions["exchangeRates"];

      this.selectPDF = this.pdfOptions["selectPDF"];

      this.loaded = true;
    },
    getFilteredRoutesOptions(data) {
      let originPorts = data.origin_ports;
      let destinyPorts = data.destiny_ports;
      let response = [];      

      for(let i = 0; i < originPorts.length; i++) {

        let routeExist = false;
        
        response.forEach(r => {
          if (r['key'] == `${originPorts[i]['id']}${destinyPorts[i]['id']}`) { 
            routeExist = true;
          }
        });
        if(!routeExist) {
          response.push({
            'key': `${originPorts[i]['id']}${destinyPorts[i]['id']}`,
            'origin_id': originPorts[i]['id'], 
            'destiny_id': destinyPorts[i]['id'], 
            'route': `${originPorts[i]['display_name']} --> ${destinyPorts[i]['display_name']}`
          });
        }
      }
      this.filteredRoutesOptions = response; 
    },
    addOptionSelectedCarriers() {
      this.filteredCarrierOptions = [];
      
      let origin_id = this.filteredRouteSelected['origin_id'];
      let destiny_id = this.filteredRouteSelected['destiny_id'];
      let rates = this.currentQuoteData['rates'];
      let carriers = this.currentQuoteData['carriers'];
      let response = [];

      rates.forEach((rate) => {
        if(rate['origin_port_id'] === origin_id && rate['destination_port_id'] === destiny_id) {
          let carrier = carriers.find(c => c.id === rate['carrier_id']);
          response.push({
            'rate_id': rate['id'],
            'carrier_name': carrier['name'],
          });
        }
      })
      this.filteredCarrierOptions = response;

    },
    updatePdfOptions(updateType) {
      let component = this;
      let newExchangeRates = [];

      if (updateType == "exchangeRates") {
        component.pdfOptions["exchangeRates"].forEach(function(exRate) {
          exRate["custom"] = true;
          newExchangeRates.push(exRate);
        });
        component.exchangeRates = newExchangeRates;
      }

      if (updateType == "typePDF") {
        component.showTotals = false;
        if (component.selectPDF["id"] == 1 || component.selectPDF["id"] == 2) {
          component.showTotals = true;
        }
      }

      let pdfOptions = {
        pdf_options: {
          allIn: component.pdfOptions["allIn"],
          showCarrier: component.pdfOptions["showCarrier"],
          showTotals: component.showTotals,
          totalsCurrency: component.totalsCurrency,
          exchangeRates: component.exchangeRates,
          selectPDF: component.selectPDF,
        },
      };

      component.actions.quotes
        .update(component.currentQuoteData["id"], pdfOptions)
        .then((response) => {
          let id = component.$route.params.id;

          component.$emit("freightAdded", id);
        })
        .catch((data) => {
          component.errorsExist = true;
          setTimeout(() => {
            component.errorsExist = false;
          }, 2000);
          component.$refs.observer.setErrors(data.data.errors);
        });
    },
    setCostSheet(event){
      // Petición para obtener data para mostrar en hoja de costos
      this.actions.quotes
      .setCostSheet(this.filteredCostSheetSelected['rate_id'], this.$route)
      .then((response) => {
        this.dataFeatCostSheet = response.data.data;
      })
      .catch((data) => {
        this.errorsExist = true;
        this.$refs.observer.setErrors(data.data.errors);
      });
    }
  },
};
</script>

<style lang="scss" scoped>
section {
  margin: 0 65px;
}

p {
  margin: 0;
  padding: 0;
}

menu {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0;
  padding: 0 0 1rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
  position: relative;

  & > ul {
    display: flex;
    margin: 0;
    padding: 0;

    & > li {
      display: flex;
      margin-right: 4rem;
      align-items: center;

      &:last-of-type {
        margin-right: 0;
      }

      & > label {
        margin: 0;
      }

      & > input {
        margin-right: 1rem;

        &:checked {
          outline-color: darkslateblue;
          border-color: darkseagreen;
        }
      }
    }
  }

  & div > button {
    background-color: #031b4e;
    outline: none;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 4px 8px;
    position: relative;

    &:hover {
      background-color: #011339;
    }
  }
}

.filter-opened {
  background-color: #031b4e;
  outline: none;
  border: none;
  color: white;
  border-radius: 4px;
  display: grid;
  padding: 20px 30px;

  position: absolute;
  top: 0;
  right: 0;
  width: fit-content;
  min-width: 300px;
  height: fit-content;
  z-index: 10000;
}

h4 {
  margin: 2rem 0;
}

 .opened {
    padding: 2.5rem 1rem;
    max-height: fit-content !important;
    transition: max-height 0.25s ease-in;
    transition: padding 0.25s ease-in;
  }

.cost-card {
  background: #f8f8fc;
  margin-bottom: 3rem;
  border-radius: 10px;

  // test
  max-height: 0;
  transition: max-height 0.15s ease-out;
  transition: padding 0.25s ease-out;
  overflow: hidden;



  & > table {
    width: 100%;

    & tr {
      & th,
      & td {
        padding: 8px 0;
      }

      // & th{

      // }
    }

    & thead {
      border-bottom: 1px solid #e5e5e5;
      &:first-of-type {
        border-top: none;
      }
      &:last-of-type {
        border-bottom: none;
      }
    }

    & > tbody {
      width: 100%;
    }

    & > thead {
      width: 100%;
    }
  }
}

.total-in-table {
  color: #006bf9;
  font-weight: bold;
}

.circle-x {
  padding: 6px 12px;
  background-color: #0b2761;
  border-radius: 50%;
  width: fit-content;
  cursor: pointer;
  justify-self: right;
  margin-bottom: -15px;
}

section {
  width: auto;
}

table {
  table-layout: fixed;
}
</style>
