<template>
  <div class="container-fluid bg-container">
    <div class="row mt-5">
      <div class="container-fluid">
        <b-card-group deck>
          <b-card
            border-variant="secondary"
            header="Quote's details"
            header-border-variant="secondary"
            header-bg-variant="transparent"
            align="justify"
          >
          
            
          </b-card>
        </b-card-group>
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
    let id = 1;
    api.getData({}, "/api/v2/quotes/"+id, (err, data) => {
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
        console.log(this.data);
      }
    }
  }
};
</script>