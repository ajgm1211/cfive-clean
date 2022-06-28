<template>
  <div>
    <Search
      @initialDataLoaded="setDatalists"
      @searchRequested="setSearchStatus"
      @searchSuccess="setSearchData"
      @clearResults="clearDisplay"
      @quoteLoaded="setQuoteData"
      @searchTypeChanged="setActions"
      ref="searchComponent"
    ></Search>

    <!-- RESULTS HEADER -->
    <div v-if="resultsTotal > 0" class="container-cards">
      <!-- FILTERS -->
      <div class="row mb-3" style="margin-top: 80px">
        <div class="col-12 col-sm-6 d-flex align-items-center result-and-filter">
          <h2 class="mr-5 t-recent">
            results found: <b>{{ resultsTotal }}</b>
          </h2>
        </div>

        <div class="col-12 col-sm-6 addcontract-createquote">
          <!--<b-button v-b-modal.add-contract class="add-contract mr-4">+ Add Contract</b-button>-->

          <b-button v-if="!creatingQuote" b-button variant="primary" @click="createQuote">
            Create Quote
          </b-button>

          <b-button v-else b-button variant="primary">
            <div class="spinner-border text-light" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </b-button>
        </div>
      </div>
      <!-- FIN FILTERS -->

      <div v-if="noRatesAdded" class="alert alert-warning" role="alert">
        Please select at least one Rate to add
      </div>

      <!-- HEADER FCL -->
      <div class="row mt-4 mb-4 result-header" v-if="searchType == 'FCL'">
        <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center">
          <b>carrier</b>
        </div>
        <div class="row col-12 " :class="[searchRequest.selectedContainerGroup.id === 2 ? ['col-sm-5'] : ['col-sm-4']]"></div>
        <div
          style="column-gap:10px"
          :class="[searchRequest.selectedContainerGroup.id === 2 ? ['justify-content-start', 'pl-47px'] : ['justify-content-start', 'row ', 'col-12', 'col-sm-4']]"
          class=" d-flex align-items-center"
        >
          <div
            class="d-flex justify-content-start"
            :class="[searchRequest.selectedContainerGroup.id === 2 ? ['mr-8'] : countContainersClass(), container.code == '40HCRF' ? 'mr-36px' : '']"
            v-for="(container, requestKey) in searchRequest.containers"
            :key="requestKey"
          >
            <b>
              {{ container.code }}
            </b>
          </div>
        </div>
      </div>
      <!-- FIN HEADER FCL -->

      <!-- HEADER LCL -->
      <div class="row mt-4 mb-4 result-header" v-else-if="searchType == 'LCL'">
        <div class="col-12 col-sm-2 d-flex justify-content-center align-items-center">
          <b>carrier</b>
        </div>
        <div class="row col-12 col-sm-8 d-flex align-items-center justify-content-between">
          <div class="col-12 col-sm-3"><b>ORIGEN</b></div>
          <div class="col-12 col-sm-3 d-flex justify-content-end">
            <b>DESTINO</b>
          </div>
          <div class="col-12 col-sm-6 d-flex justify-content-center">
            <b>PRICE</b>
          </div>
        </div>
      </div>
      <!-- FIN HEADER LCL -->
    </div>
    <!-- FIN RESULTS HEADER -->

    <Recent v-show="resultsTotal == 0 && !searching" :searchType="searchType" @recentSearch="quickSearch" ref="recentComponent"></Recent>

    <APIResults
      v-if="searchRequest.length != 0"
      :request="searchRequest"
      :datalists="datalists"
      :searchData="searchData"
      @apiSearchStarted="clearDisplay"
      @apiSearchDone="addApiResults"
      @addedToQuote="setResultsForQuote"
      ref="resultsAPIComponent"
    ></APIResults>

    <Result
      v-if="foundRates.length != 0 || foundRatesLcl.length != 0"
      :searchType="searchType"
      :rates="searchType == 'FCL' ? foundRates : foundRatesLcl"
      :request="searchRequest"
      :datalists="datalists"
      :searchData="searchData"
      @createQuote="createQuote"
      @addedToQuote="setRatesForQuote"
      @resultsCreated="setActions"
      ref="resultsComponent"
    ></Result>
  </div>
</template>

<script>
import Search from "./Search";
import Recent from "./Recent";
import Result from "./Result";
import APIResults from "./APIResults";
import actions from "../../actions";

export default {
  components: {
    Search,
    Recent,
    Result,
    APIResults,
  },
  data() {
    return {
      searching: false,
      searchRequested: false,
      foundRates: [],
      foundRatesLcl: [],
      foundCharges: {},
      searchRequest: [],
      datalists: {},
      requestData: {},
      resultsTotal: 0,
      creatingQuote: false,
      noRatesAdded: false,
      ratesForQuote: {
        rates: [],
        results: [],
      },
      actions: actions,
      apiSearchDone: true,
      searchDone: true,
      quoteData: {},
      searchType: "FCL",
      searchLoaded: false,
      searchData: {},
    };
  },
  created() {
    this.requestData = this.$route.query;
  },
  methods: {
    setActions(origin) {
      let component = this;

      this.searchType = this.$refs.searchComponent.searchRequest.type;

      for (var child in component.$refs) {
        if (component.$refs[child] && component.$refs[child].searchActions) {
          if (component.$refs[child].searchType) {
            component.$refs[child].searchType = component.searchType;
          }
          if (child != "resultsAPIComponent") {
            component.$refs[child].setActions();
          }
        }
      }

      if (origin == "dd") {
        this.clearDisplay("switch");
      }
    },

    countContainersClass() {
      if (this.searchRequest.containers.length == 5 || this.searchRequest.containers.length == 4) {
        return "col-2";
      }

      if (this.searchRequest.containers.length == 3) {
        return "col-3";
      }
      if (this.searchRequest.containers.length == 2) {
        return "col-4";
      }
    },
    createQuote() {
      let component = this;
      component.creatingQuote = true;

      if (component.ratesForQuote.rates.length == 0 && component.ratesForQuote.results.length == 0) {
        component.noRatesAdded = true;
        component.creatingQuote = false;
        setTimeout(function() {
          component.noRatesAdded = false;
        }, 2000);
      } else {
        component.ratesForQuote.results.forEach(function(result) {
          if (result.activeTab != undefined) {
            result.routingDetails[0] = result.routingDetails[result.activeTab];
          }
        });

        if (Object.keys(component.quoteData).length != 0) {
          var duplicateMatch = this.checkLocalOrInlandDuplicates();
        } else {
          var duplicateMatch = false;
        }

        if (component.requestData.requested == 0 || !duplicateMatch) {
          component.actions.quotes
            .create(component.ratesForQuote, component.$route)
            .then((response) => {
              window.location.href = "/api/quote/" + response.data.data.id + "/edit";
              component.creatingQuote = false;
            })
            .catch((error) => {
              if (error.status === 422) {
                component.responseErrors = error.data.errors;
                component.creatingQuote = false;
              }
            });
        } else if (component.requestData.requested == 1 || duplicateMatch) {
          if (component.ratesForQuote.rates.length > 0) {
            component.ratesForQuote.rates[0].search.requestData = component.requestData;
          }

          if (component.ratesForQuote.results.length > 0) {
            component.ratesForQuote.results[0].search.requestData = component.requestData;
          }
          component.actions.quotes
            .specialduplicate(component.ratesForQuote)
            .then((response) => {
              window.location.href = "/api/quote/" + response.data.data.id + "/edit";
              component.creatingQuote = false;
            })
            .catch((error) => {
              if (error.status === 422) {
                component.responseErrors = error.data.errors;
                component.creatingQuote = false;
              }
            });
        }
      }
    },

    setDatalists(initialData) {
      this.datalists = initialData;
    },

    setSearchStatus(searchRequest) {
      this.searching = true;
      this.searchRequest = searchRequest;
      this.searchData = _.cloneDeep(searchRequest);
      this.requestData = this.$route.query;
      if (this.searchType == "FCL") {
        this.$nextTick(() => {
          this.$refs.resultsAPIComponent.callAPIs();
        });
      }
    },

    setSearchData(rateData) {
      this.searching = false;
      if (this.searchType == "FCL") {
        this.foundRates = rateData;
        this.resultsTotal += this.foundRates.length;
      } else if (this.searchType == "LCL") {
        this.foundRatesLcl = rateData;
        this.resultsTotal += this.foundRatesLcl.length;
      }
      this.searchDone = true;
      if (this.apiSearchDone || this.$refs.searchComponent.searchRequest.type == "LCL") {
        this.$refs.searchComponent.searching = false;
      }
    },

    clearDisplay(trigEvent) {
      this.foundRates = [];
      this.foundRatesLcl = [];
      this.ratesForQuote = {
        rates: [],
        results: [],
      };
      if (this.$refs.resultsAPIComponent) {
        this.$refs.searchComponent.foundApiRates = false;
        this.$refs.resultsAPIComponent.results = {
          "maersk": [],
          "cmacgm": [],
          "evergreen": [],
          "hapag-lloyd": [],
        };
      }
      this.resultsTotal = 0;
      this.apiSearchDone = false;
      this.searching = false;
      if (trigEvent == "switch") {
        this.$refs.searchComponent.searching = false;
      }
    },

    quickSearch() {
      this.$refs.searchComponent.getQuery();
    },

    addApiResults(resultsFound) {
      this.resultsTotal += resultsFound;
      if (resultsFound == 0) {
        this.$refs.searchComponent.foundApiRates = false;
      } else {
        this.$refs.searchComponent.foundApiRates = true;
      }

      this.apiSearchDone = true;
      if (this.searchDone) {
        this.$refs.searchComponent.searching = false;
      }
    },

    setRatesForQuote(rates) {
      this.ratesForQuote["rates"] = rates;
    },

    setResultsForQuote(results) {
      this.ratesForQuote["results"] = results;
    },

    setQuoteData(quoteData) {
      this.quoteData = quoteData;
    },

    checkLocalOrInlandDuplicates() {
      let component = this;
      let duplicateMatch = false;

      //RATES FROM CONTRACTS
      component.ratesForQuote.rates.forEach(function(rate) {
        component.quoteData.local_ports.origin.forEach(function(originPort) {
          if (originPort.id == rate.origin_port) {
            duplicateMatch = true;
          }
        });

        component.quoteData.local_ports.destination.forEach(function(destinationPort) {
          if (destinationPort.id == rate.destiny_port) {
            duplicateMatch = true;
          }
        });

        component.quoteData.inland_ports.origin.forEach(function(originPort) {
          if (originPort.id == rate.origin_port) {
            duplicateMatch = true;
          }
        });

        component.quoteData.inland_ports.destination.forEach(function(destinationPort) {
          if (destinationPort.id == rate.destiny_port) {
            duplicateMatch = true;
          }
        });
      });

      //API RESULTS
      component.ratesForQuote.results.forEach(function(result) {
        component.quoteData.local_ports.origin.forEach(function(originPort) {
          if (originPort.code == result.originPort) {
            duplicateMatch = true;
          }
        });

        component.quoteData.local_ports.destination.forEach(function(destinationPort) {
          if (destinationPort.code == result.destinationPort) {
            duplicateMatch = true;
          }
        });

        component.quoteData.inland_ports.origin.forEach(function(originPort) {
          if (originPort.code == result.originPort) {
            duplicateMatch = true;
          }
        });

        component.quoteData.inland_ports.destination.forEach(function(destinationPort) {
          if (destinationPort.code == result.destinationPort) {
            duplicateMatch = true;
          }
        });
      });

      return duplicateMatch;
    },
  },
};
</script>

<style lang="scss" scoped>
.mr-8 {
  margin-right: 61px;
}

.mr-36px {
  margin-right: 36px !important;
}
</style>
