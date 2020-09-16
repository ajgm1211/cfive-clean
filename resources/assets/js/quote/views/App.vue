<template>
  <div class="quote-header">
    <div class="container-fluid">
      <div class="row mt-5">
          <div class="col-12">
              <b-card>
                  <div class="row">
                      <div class="col-6">
                          <b-card-title>FCL Quotes</b-card-title>
                      </div>
                      <div class="col-6">
                          <div class="float-right">
                              <button class="btn btn-link" v-b-modal.addContract>+ Add Contract</button>
                              <a href="/RequestFcl/NewRqFcl" class="btn btn-primary btn-bg" >+ Import Contracts</a>
                          </div>
                      </div>
                  </div>

                  <DataTable
                      :fields="fields"
                      :actions="actions.quotes"
                      @onEdit="onEdit"
                      ></DataTable>
              </b-card>
          </div>
      </div>
    </div>
  </div>
</template>


<script>
import Quote from "./Quote";
import Inland from "./Inland";
import Ocean from "./Ocean";
import actions from '../../actions';
import Local from "./Local";
import DataTable from '../../components/DataTable';

export default {
  components: {
    DataTable,
    Quote,
    Inland,
    Ocean,
    Local
  },
  data() {
    return {
      activeOcean: false,
      actions:actions,
      fields: [
      { key: 'quote_id', label: 'Id' },
      { key: 'company_id', label: 'Client Company',formatter: (value)=> { return this.setClient(value) } },
      { key: 'user_id', label: 'User',formatter: (value)=> { return value.name } },
      { key: 'validity_start', label: 'created at' },
      { key: 'origin_address', label: 'Origin' },
      { key: 'destination_address', label: 'Destination' },
      { key: 'type', label: 'type' },
      ],
    };
  },
  created() {


  },
  methods: {
      onEdit(data){
          window.location = `/api/quote/${data.id}/edit`;
      },
      setClient(value){
        if(value == null){
          return '-';
        } else {
          return value.business_name;
        }
      }
    }
};
</script>
