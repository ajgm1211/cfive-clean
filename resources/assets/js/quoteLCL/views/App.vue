<template>
  <div class="quote-header">
    <div class="container-fluid">

      <div class="row">

        <div class="col-12" style="padding: 0px 50px">

          <a href="#" class="p-light quote-link">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back
          </a>

          <div class="d-flex justify-content-between align-items-center">

            <h4 class="card-title mt-3">
              <b>LCL Quote</b>
            </h4>
            <div>
              <a href="#" class="btn btn-primary btn-bg">+ PDF</a>
            </div>
          </div>

        </div>

        <!-- Tabs Section -->
        <b-card no-body class="card-tabs" style="width: 100%">

          <b-tabs card class="quote-content-tab">

            <b-tab title="Quote Info" active>
              <Quote></Quote>
            </b-tab>

            <b-tab title="Ocean Freight">
              <Ocean></Ocean>
            </b-tab>

            <b-tab title="Local Charges">
              <Local></Local>
            </b-tab>

            <b-tab title="Inland">
              <Inland></Inland>
            </b-tab>

            <b-tab title="Totals">Totales</b-tab>

          </b-tabs>

        </b-card>
        <!-- End Tabs Section -->

      </div>

    </div>
  </div>
</template>


<script>
import Quote from "./Quote";
import Inland from "./Inland";
import Ocean from "./Ocean";
import Local from "./Local";
export default {
  components: {
    Quote,
    Inland,
    Ocean,
    Local
  },
  data() {
    return {
      activeOcean: false
    };
  }
};
</script>