<template>
    <div class="container-fluid">
      <HelpDropdown
        :options="helpOptions"
      ></HelpDropdown>
      <div class="row mt-5">
        <div class="col-12">
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
import DataTable from "../../components/DataTableV2s";
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
        { 
          key: "id", 
          label: "ID", 
          filterisOpen: true, 
          isObject: false },
        { 
          key: "quote_id", 
          label: "Quote ID", 
          filterisOpen: true, 
          isObject: false },
        { 
          key: "custom_quote_id", 
          label: "Custom ID", 
          filterisOpen: true, 
          isObject: false},
        {
          key: "status",
          label: "status",
          filterisOpen: true,
          isObject: false
        },
        {
          key: "company_id",
          label: "Client",
          filterisOpen: true,
          isObject: true
        },
        { 
          key: "type", 
          label: "Type", 
          filterisOpen:true, 
          isObject: false },
        {
          key: "origin",
          label: "Origin",
          collapse: "Show origins",
          filterisOpen:true,
          isObject: true
        },
        {
          key: "destiny",
          label: "Destiny",
          collapse: "Show destinations",
          filterisOpen:true,
          isObject: true,
        },
        {
          key: "user_id",
          label: "User",
          filterisOpen:true,
          isObject: true,
        },
        { 
          key: "created_at", 
          label: "Created at", 
          filterisOpen: true ,
          isObject: false
        }
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
