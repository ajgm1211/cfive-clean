<template>
  <div class=" custom-card-container bg-white">
    <div class="custom-card bg-white ">
      <!-- COMPANY LOGO  -->
      <img
        class="company-logo"
        :src="
          'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
            rate.carrier.image
        "
        alt="company logo"
      />

      <!-- SEARCH RESULT INFO  -->
      <div class="card-info">
        <h6 class="card-title">{{ rate.contract.name }}</h6>

        <div class="card-main-info  align-items-center">
          <!-- INFO CONTRACT -->
          <div class="origin_destination">
            <!-- ORIGEN -->
            <div class="route">
              <b>origin</b>
              <p>
                {{ rate.port_origin.display_name }}
              </p>
            </div>

            <!-- TT -->
            <div
              style="margin: 0 20px;"
              class="via d-flex flex-column justify-content-center align-items-center"
            >
              <div class="direction-form d-none d-md-flex">
                <img
                  src="/images/logo-ship-blue.svg"
                  alt="bote"
                  style="top: -30px"
                />

                <div class="route-indirect d-flex align-items-center">
                  <div class="circle mr-2"></div>
                  <div class="line"></div>
                  <div class="circle fill-circle-gray mr-2 ml-2"></div>
                  <div class="line line-blue"></div>
                  <div class="circle fill-circle ml-2"></div>
                </div>
              </div>
            </div>

            <!-- DESTINATION -->
            <div class="route">
              <b>destination</b>
              <p>
                {{ rate.port_destiny.display_name }}
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-center align-items-center">
            <div
              v-if="
                rate.charges.Origin == undefined &&
                  rate.charges.Destination == undefined
              "
            >
              <b style="margin-right:5px; font-size: 15px">{{
                rate.total_with_markups_freight_currency
                  ? rate.total_with_markups_freight_currency
                  : rate.total_freight_currency
              }}</b>

              <span>
                <b>{{ rate.currency.alphacode }}</b></span
              >
            </div>
            <div v-else>
              <b style="margin-right:5px; font-size: 15px">{{
                rate.total_with_markups ? rate.total_with_markups : rate.total
              }}</b>

              <span>
                <b>{{ rate.client_currency.alphacode }}</b></span
              >
            </div>
          </div>
        </div>

        <div class="info-details align-items-center">
          <div class="d-flex align-items-end" style="justify-self: baseline;">
            <b>Validity:</b>
            <p style="margin-left:10px">
              {{ rate.contract.validity }} / {{ rate.contract.expire }}
            </p>
          </div>

          <div class="d-flex align-items-center">
            <div
              class="detailsbtn"
              style="margin-right: 20px"
              :aria-controls="'remarks_' + +String(rate.id)"
              v-if="rate.remarks != '<br><br>' && rate.remarks != '<br>'"
              @click="rate.remarksCollapse = !rate.remarksCollapse"
            >
              <b style="margin-right:10px">Remarks</b>
              <b-icon icon="caret-down-fill"></b-icon>
            </div>
            <div
              :aria-controls="'remarks_' + +String(rate.id)"
              @click="rate.detailCollapse = !rate.detailCollapse"
              class="detailsbtn"
            >
              <b style="margin-right:10px">detailed cost</b>
              <b-icon icon="caret-down-fill"></b-icon>
            </div>
          </div>
        </div>
      </div>

      <!--  ADD TO QUOTE BTN  -->
      <div class="d-flex align-items-center justify-content-center">
        <b-form-checkbox
          v-model="rate.addToQuote"
          class="btn-add-quote"
          name="check-button"
          button
          @change="sendQuote(rate)"
        >
          <b>add to quote</b>
        </b-form-checkbox>
      </div>
    </div>

    <b-collapse
      v-model="rate.detailCollapse"
      :id="'details_' + String(rate.id)"
      style="background:white;"
    >
      <CustomTable
        v-for="(charge, index) in rate.charges"
        :key="index"
        :charge="index"
        :thead="fields"
        :data="charge"
        :total_by_type="rate.charge_totals_by_type"
        :search_pricelevel="rate.search.pricelevel"
        :total_markups="rate.total_markups"
        :currency="rate.currency"
      />
    </b-collapse>

    <b-collapse
      :id="'remarks_' + String(rate.id)"
      class="pt-5 pb-5 pl-5 pr-5 col-12"
      v-model="rate.remarksCollapse"
    >
      <h5><b>Remarks</b></h5>

      <b-card>
        <p v-html="rate.remarks"></p>
      </b-card>
    </b-collapse>
  </div>
</template>

<script>
import CustomTable from "./CustomTable.vue";
export default {
  props: {
    rate: {
      required: true,
    },
  },
  components: {
    CustomTable,
  },
  data() {
    return {
      expanded: true,
      pricelevel: true,
      fields: ["Charge", "Detail", "Amount", "Units", "Markups", "Total"],
    };
  },
  mounted() {
    console.log("rate 1", this.rate);
    this.rate.search.pricelevel != null
      ? (this.fields = [
          "Charge",
          "Detail",
          "Amount",
          "Units",
          "Markups",
          "Total",
        ])
      : (this.fields = ["Charge", "Detail", "Amount", "Units", "Total"]);
  },
  methods: {
    sendQuote(rate) {
      this.$emit("QuoteToAdd", rate);
    },
  },
};
</script>

<style scoped>
p {
  margin: 0;
}

.custom-card {
  display: grid;
  grid-template-columns: 1fr;
  justify-items: center;
  grid-template-rows: 1fr 1fr 1fr;
  height: 650px;
}

@media (min-width: 992px) {
  .custom-card {
    display: grid;
    /* grid-template-columns: 250px auto 285px; */
    grid-template-columns: 1fr 5fr 1fr;
    grid-template-rows: initial;
    height: 195px;
  }
}

.card-info {
  display: grid;
  grid-template-rows: 40px auto 40px;
  /* padding: 10px 20px; */
  justify-items: center;
  align-items: center;
  width: 100%;
}

@media (min-width: 992px) {
  .card-info {
    border-right: 1px solid #f3f3f3;
    border-left: 1px solid #f3f3f3;
    justify-items: initial;
  }
}

.origin_destination {
  display: grid;
  position: relative;
  grid-template-rows: 1fr 1fr;
}

@media (min-width: 720px) {
  .origin_destination {
    display: flex;
    justify-self: center;
  }
}

.custom-card-container {
  width: 90%;
  margin: 0 auto;
  margin-bottom: 20px;
  border-radius: 4px;
  box-shadow: 0px 0px 15px 3px #eaeaea;
}

@media (min-width: 992px) {
  .custom-card-container {
    width: 100%;
    margin-bottom: 20px;
  }
}

.card-main-info {
  display: grid;
  grid-template-columns: 1fr;
  justify-items: center;
  grid-template-rows: 1fr 1fr;
  padding: 0 20px;
}

@media (min-width: 992px) {
  .card-main-info {
    border-bottom: 1px solid #f3f3f3;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: initial;
    justify-items: initial;
    height: 100%;
  }
}

.company-logo {
  width: 115px;
  height: 115px;
  margin: 40px;
  object-fit: contain;
}

.card-title {
  text-transform: uppercase;
  font-weight: 900;
  color: #071c4b;
  margin: 0;
  font-size: 14px;
  padding: 15px 10px 0 10px;
}

.info-details {
  display: grid;
  grid-template-columns: 1fr;
  font-size: 12px;
  grid-template-rows: 1fr 1fr 1fr;
  justify-items: center;
  row-gap: 10px;
  height: fit-content;
  padding: 10px;
}

@media (min-width: 992px) {
  .info-details {
    display: flex;
    justify-content: space-between;
    /* grid-template-columns: 1fr 1fr;
    grid-template-rows: initial;
    row-gap: initial; */
  }
}

.add-quote-button {
  border: 2px solid #6c757d;
  color: #6c757d;
  padding: 10px 20px;
  text-transform: uppercase;
  font-weight: 600;
  font-size: 13px;
  border-radius: 100px;
  cursor: pointer;
  line-height: 1;
}
.add-quote-button:hover {
  color: #ffffff;
  background: #006afa !important;
  border-color: #006afa !important;
}

.detailsbtn {
  justify-self: center;
  color: #006afa;
  text-transform: uppercase;
  font-size: 10px;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  cursor: pointer;
}

@media (min-width: 992px) {
  .detailsbtn {
    justify-self: end;
  }
}

.expand {
  height: 10000px;
}

.route {
  font-size: 10px;
  color: #80888b;
  display: flex;
  flex-direction: column;
  text-transform: uppercase;
}

.route > p {
  color: #071c4b;
  font-weight: bold;
}
</style>
