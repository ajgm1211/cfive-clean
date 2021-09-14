<template>
  <section class="custom-table">
    <h5>
      <b>{{ charge }}</b>
    </h5>

    <b-table
      striped
      hover
      responsive
      :items="tbody"
      :fields="thead"
    >
    </b-table>
  </section>
</template>

<script>
export default {
  props: {
    charge: {
      required: true,
    },
    thead: {
      required: true,
    },
    data: {
      required: true,
    },
    total_by_type: {
      required: true,
    },
  },
  data() {
    return {
      tbody: [],
      currency: "",
    };
  },
  mounted() {
    // console.log("DATA", this.data);
    // console.log("total byt type", this.total_by_type[this.charge]);

    // console.log("markups?????????", this.thead.includes("Markups"));

    this.data.forEach((charge) => {
      let object = {
        Charge: null,
        Detail: null,
        Amount: null,
        Units: null,
        Total: null,
      };

      this.currency =
        this.charge == "Freight"
          ? charge.currency.alphacode
          : charge.client_currency.alphacode;

      if (this.thead.includes("Markups") == true) {
        // this.tbody = [];
        let object = {
          Charge: null,
          Detail: null,
          Amount: null,
          Units: null,
          Markups: null,
          Total: null,
        };

        object.Charge = charge.surcharge.name;
        object.Detail = charge.calculationtypelcl.name;
        object.Amount = charge.ammount;
        object.Units = charge.units;

        if (charge.total_markups != undefined) {
          object.Markups =
            charge.joint_as == "client_currency"
              ? charge.total_markups_client_currency
              : charge.total_markups;
        }

        object.Markups = charge.units;
        object.Total = charge.total;

        this.tbody.push(object);
      } else {
        object.Charge = charge.surcharge.name;
        object.Detail = charge.calculationtypelcl.name;
        object.Amount = charge.ammount;
        object.Units = charge.units;
        object.Total = charge.total;

        this.tbody.push(object);
      }

      //   console.log("object", object);
    });
    // console.log("tbody", this.tbody);

    let total = `Total ${this.charge}  ${this.currency} ${
      this.total_by_type[this.charge]
    }`;

    let fixedTotal = {
      Total: total,
    };

    this.tbody.push(fixedTotal);

    // console.log("TOTAL", total);
    // console.log("CURRENCY", this.currency);
  },
};
</script>

<style scoped>
.custom-table {
  padding: 20px;
}
</style>
