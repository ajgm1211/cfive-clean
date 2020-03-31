<template>
  <div class="container bg-container">
    <div class="row mt-5">
      <div class="col-12">
        <b-card>
          <div class="row">
            <div class="col-6">
              <b-card-title>Quote's details</b-card-title>
            </div>
          </div>

          

          
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isBusy: true, // Loader
      data: null,

      fields: [
        { key: "name", label: "Reference", sortable: true },
        {
          key: "carriers",
          label: "Carriers",
          formatter: value => {
            let $carriers = [];

            value.forEach(function(val) {
              $carriers.push(val.name);
            });
            return $carriers.join(", ");
          }
        },
        {
          key: "direction",
          label: "Direction",
          formatter: value => {
            return value.name;
          }
        }
      ]
    };
  },
  created() {
    api.getData({}, "/api/v2/contracts", (err, data) => {
      this.setData(err, data);
    });
  },
  methods: {
    setData(err, { data: records, links, meta }) {
      this.isBusy = false;

      if (err) {
        this.error = err.toString();
      } else {
        this.data = records;
      }
    }
  }
};
</script>