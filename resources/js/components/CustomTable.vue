<template>
  <section class="custom-table">
    <h5 v-show="(searchData.originCharges && charge=='Origin') || (searchData.destinationCharges && charge=='Destination') || charge=='Freight'">
      <b>{{ charge }}</b>
    </h5>

    <b-table 
      v-show="(searchData.originCharges && charge=='Origin') || (searchData.destinationCharges && charge=='Destination') || charge=='Freight'"
      striped hover responsive :items="tbody" :fields="thead">
      <template slot="custom-foot">
        <td></td>
        <td></td>
        <td></td>
        <td v-if="thead.includes('Markups')"></td>
        <td style="text-align:right">
          <b>Total {{ charge }}</b>
        </td>
        <td>
          <b>{{ alphacode }}</b> <b>{{ total_by_type[charge] }}</b>
        </td>
      </template>
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
      type: Array,
    },
    data: {
      required: true,
    },
    total_by_type: {
      required: true,
    },
    search_pricelevel: {
      required: true,
    },
    currency: {
      required: true,
    },
    searchData: {
      required: true,
    },
  },
  data() {
    return {
      tbody: [],
      alphacode: "",
      total: "",
    };
  },
  mounted() {
    this.data.forEach((charge) => {
      let object = {
        Charge: null,
        Detail: null,
        Amount: null,
        Units: null,
        Total: null,
      };

      this.alphacode =
        this.charge == "Freight"
          ? charge.currency.alphacode
          : charge.client_currency.alphacode;

      if (this.thead.includes("Markups")) {
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

        // MARKUP VALUE
        if (this.search_pricelevel == null || charge.total_markups == undefined) {
          object.Markups = "+ 0";
        }else if (charge.total_markups != undefined) {
          object.Markups = "+" +
            charge.joint_as == "client_currency"
              ? charge.total_markups_client_currency
              : charge.total_markups;
        }

        let alphacode2;

        // TOTAL VALUE
        if (this.charge == "Freight") {
          alphacode2 =
            charge.joint_as == "client_currency"
              ? this.currency.alphacode
              : charge.currency.alphacode;
        } else if (charge.joint_as == "client_currency") {
          alphacode2 = charge.client_currency.alphacode;
        } else if (charge.joint_as != "client_currency") {
          alphacode2 = charge.currency.alphacode;
        }

        var total;

        if (charge.total_markups != undefined) {
          total =
            charge.joint_as == "client_currency"
              ? charge.total_with_markups_client_currency
              : charge.total_with_markups;
        } else {
          total =
            charge.joint_as == "client_currency"
              ? charge.total_client_currency
              : charge.total;
        }

        object.Total = `${alphacode2} ${total}`;

        this.tbody.push(object);
      } else {
        object.Charge = charge.surcharge.name;
        object.Detail = charge.calculationtypelcl.name;
        object.Amount = charge.ammount;
        object.Units = charge.units;
        object.Total = charge.total;

        this.tbody.push(object);
      }
    });
  },
};
</script>

<style lang="scss" scoped>
.custom-table {
  padding: 20px;
  padding-bottom: 0px;

  &:last-of-type{
    padding-bottom:20px ;
  }

}

.table thead {
    background-color: transparent !important;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}
</style>
