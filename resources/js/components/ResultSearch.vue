<template>
  <div class=" custom-card-container bg-white">
    <div class="custom-card bg-white ">
      <!-- COMPANY LOGO  -->
      <img
        class="company-logo"
        :src="
          'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' +
            info.carrier.image
        "
        alt="company logo"
      />

      <!-- SEARCH RESULT INFO  -->
      <div class="card-info">
        <h6 class="card-title">{{ info.contract.name }}</h6>

        <div class="card-main-info  align-items-center">
          <!-- INFO CONTRACT -->
          <div class="origin_destination">
            <!-- ORIGEN -->
            <div class="route">
              <b>origin</b>
              <p>
                {{ info.port_origin.display_name }}
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
                {{ info.port_destiny.display_name }}
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-center align-items-center">
            <b style="margin-right:5px; font-size: 15px">160</b>
            <b>EUR</b>
          </div>
        </div>

        <div class="info-details align-items-center">
          <div class="d-flex align-items-end" style="justify-self: baseline;">
            <b>Validity:</b>
            <p style="margin-left:10px">
              {{ info.contract.validity }} / {{ info.contract.expire }}
            </p>
          </div>

          <b class="download-contract">download contract</b>

          <div class="detailsbtn" v-b-toggle.collapse-table>
            <b style="margin-right:10px">detailed cost</b>
            <b-icon icon="caret-down-fill"></b-icon>
          </div>
        </div>
      </div>

      <!--  ADD TO QUOTE BTN  -->
      <div
        class="d-flex align-items-center justify-content-center"
        style="height: fit-content; margin-top: 100px;"
      >
        <div class="add-quote-button">add to quote</div>
      </div>
    </div>

    <b-collapse style="background:white;" id="collapse-table">
      <CustomTable
        v-for="(charge, index) in info.charges"
        :key="index"
        :charge="index"
        :thead="fields"
        :data="charge"
        :total_by_type="info.charge_totals_by_type"
      />
    </b-collapse>
  </div>
</template>

<script>
import CustomTable from "./CustomTable.vue";
export default {
  props: {
    info: {
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
      fields: ["Charge", "Detail", "Amount", "Units", "Total"],
    };
  },
  mounted() {
    if (this.info.search.pricelevel != null) {
      this.fields.splice(4, 0, "Markups");
    }
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
    height: 230px;
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
    margin: 0 15px;
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
}

.card-title {
  text-transform: uppercase;
  font-weight: 900;
  color: #071c4b;
  margin: 0;
  font-size: 14px;
  padding: 10px;
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
    grid-template-columns: 1fr 2fr 1fr;
    grid-template-rows: initial;
    row-gap: initial;
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
}

.download-contract {
  color: #fff;
  background-color: #6c757d;
  text-transform: uppercase;
  padding: 3px 10px;
  border-radius: 4px;
}
</style>
