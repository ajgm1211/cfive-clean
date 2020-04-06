<template>
  <div class="container">
    <div class="row mt-5">
  <div class="col-12">
    <b-card>
    <div class="row">
      <div class="col-6"><b-card-title>FCL Contracts</b-card-title></div>
      <div class="col-6"><div class="float-right"><button class="btn btn-link">+ Add Contract</button>
        <button class="btn btn-primary btn-bg">+ Import Contract</button></div></div>
    </div>

    <div class="row my-3">
      <div class="col-4">
         <b-form inline>
             <i class="fa fa-search" aria-hidden="true"></i>
            <b-input
              id="inline-form-input-name"
              class="mb-2 mr-sm-2 mb-sm-0"
              placeholder="Search"
            ></b-input>
          </b-form>
      </div>
    </div>
      <b-table borderless hover :fields="fields" :items="data" :current-page="currentPage"></b-table>
      <b-button id="popover-button-variant" class="action-app" href="#" tabindex="0"><i class="fa fa-ellipsis-h" aria-hidden="true"></i>
</b-button>
  <b-popover target="popover-button-variant" class="btns-action" variant="" triggers="focus" placement="bottomleft">
<button class="btn-action">Edit</button>
      <button class="btn-action">Duplicate</button>
      <button class="btn-action">Delete</button>
       </b-popover>
       <span class="status-st published">published</span>
        <span class="status-st expired">expired</span>
        <span class="status-st incompleted">incompleted</span>
        <input type="checkbox" class="input-check" id="check">
        <label  for="check"></label>
      <b-pagination v-model="currentPage" :total-rows="rows" align="right"></b-pagination>
  </b-card>
    
  </div>
</div>
    
  </div>

</template>

<script>
  export default {
    data() {
      return {
        isBusy:true, // Loader
        data: null,

        fields: [
            { key: 'name', label: 'Reference', sortable: true },
            { key: 'carriers', label: 'Carriers', 
                formatter: value => {
                    let $carriers = [];

                    value.forEach(function(val){
                        $carriers.push(val.name);
                    });
                    return $carriers.join(', ');
                } 
            },
            { key: 'direction', label: 'Direction', formatter: value => { return value.name } 
            }
        ]
      }
    },
    created() {

        api.getData({}, '/api/v2/contracts', (err, data) => {
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
        },

    }
  }
</script>