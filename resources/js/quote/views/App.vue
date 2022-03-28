<template>
    <div class="container-fluid">
      <div class="row mt-5">
        <div class="col-12">
          <HelpDropdown
            :options="helpOptions"
          ></HelpDropdown>
          <b-card>
            <div class="row">
              <div class="col-6">
                <b-card-title>Quotes</b-card-title>
              </div>
              <div class="col-6">
                <div class="float-right">
                  <a href="/api/search" class="btn btn-primary btn-bg"
                    ><i class="fa fa-search"></i> Search Rates</a
                  >
                </div>
              </div>
            </div>

            <DataTable
              :fields="fields"
              :actions="actions.quotes"
              :filter="true"
              :singleActions="['edit', 'duplicate', 'delete', 'specialduplicate', 'generatePDF']"
              @onEdit="onEdit"
              @onGeneratePDF="onGeneratePDF"
              :totalResults="totalResults"
            ></DataTable>
          </b-card>
        </div>
      </div>
    </div>
</template>


<script>
import Quote from "./Quote";
import Inland from "./Inland";
import Ocean from "./Ocean";
import actions from "../../actions";
import Local from "./Local";
import DataTable from "../../components/DataTable";
import HelpDropdown from "../../components/HelpDropdown";

export default {
  components: {
    DataTable,
    Quote,
    Inland,
    Ocean,
    Local,
    HelpDropdown,
  },
  data() {
    return {
      totalResults: true,
      activeOcean: false,
      actions: actions,
      fields: [
        { key: "id", label: "ID", filterIsOpen:false },
        { key: "quote_id", label: "Quote ID", filterIsOpen:false },
        { key: "custom_quote_id", label: "Custom ID", filterIsOpen:false },
        {
          key: "status",
          label: "status",
          formatter: (value) => {
            return value.name;
          },
          filterIsOpen:false,
          filterTrackBy: "name",
          trackLabel: "name",
        },
        {
          key: "company_id",
          label: "Client",
          formatter: (value) => {
            return this.setClient(value);
          },
          filterIsOpen:false,
          filterTrackBy: "business_name",
          trackLabel: "business_name",
        },
        { key: "type", label: "Type", filterIsOpen:false },
        {
          key: "origin",
          label: "Origin",
          filterIsOpen:false,
          collapse: "Show origins",
        },
        {
          key: "destiny",
          label: "Destiny",
          filterIsOpen:false,
          collapse: "Show destinations",
        },
        {
          key: "user_id",
          label: "User",
          formatter: (value) => {
            return value.fullname;
          },
          filterIsOpen:false,
          filterTrackBy: "fullname",
          trackLabel: "fullname",
        },
        { key: "created_at", label: "Created at", filterIsOpen:false },
      ],
      helpOptions: [
        {
          title: "How to manage your quotes",
          link: "https://support.cargofive.com/how-to-manage-your-quotes/"
        },
        {
          title: "How to generate an FCL Quote",
          link: "https://support.cargofive.com/how-to-generate-an-fcl-quote-new/"
        }
      ]
    };
  },
  created() {},
  methods: {

    onEdit(data) {
      window.location = `/api/quote/${data.id}/edit`;
    },
    onGeneratePDF(data) { 
      window.open(`/api/quote/pdf/${data.id}`, '_blank');
    },

    setClient(value) {
      if (value == null) {
        return "--";
      } else {
        return value.business_name;
      }
    },

  },
};
</script>
