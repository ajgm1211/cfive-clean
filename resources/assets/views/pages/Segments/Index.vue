<template>
  <section>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <b-card>
                    <div class="row">
                        <div class="col-6">
                            <b-card-title>Segments Configuration</b-card-title>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <button @click="toogleModal()" class="btn btn-primary btn-bg" :disabled="activeAddModal">+ Add Segment ID</button>
                            </div>
                        </div>
                    </div>
                    <b-table 
                      v-if="loaded"
                      striped hover
                      :items="segmentConfigurations"
                      :fields="fields"
                      :per-page="perPage"
                      :current-page="currentPage"
                      small
                      show-empty
                    >
                      <template #empty="scope">
                        <b-card-title class="justify-content-md-center">{{ scope.emptyText }}</b-card-title>
                      </template>
                      <template #[gomycell(field)]="row" v-for="(field, key) in fields" >
                        <b-form-input v-model="row.item[field]" :key="key" readonly v-if="field == 'type' " />
                        <b-form-input v-model="row.item[field]" :key="key" v-else type="number" @blur="update()" />
                      </template>
                    </b-table>
                    <b-pagination
                      v-model="currentPage"
                      :total-rows="rows"
                      :per-page="perPage"
                      aria-controls="my-table"
                    ></b-pagination>
                </b-card>
            </div>
        </div>
    </div>
    <div>
        <AddModal
          :create="create"
          v-if="create"
          :title="'Segment ID'"
          :action="'Add'"
          :ids="this.segmentConfigurationIds"
          :page="this.currentPage"
          :segmentTypes="segmentTypes"
          @cancel="create = false"
          @updateIds= updateIds
        />
    </div>
  </section>
</template>

<script>

import toastr from "toastr"
import { mapGetters } from "vuex"
import AddModal from './partials/Add.vue'

export default {
  components:{AddModal},
  data(){
    return{
      loaded: false,
      perPage: 4,
      currentPage: 1,
      create: false,
      fields:['segment_id', 'type']
    }
  },
  computed: {
    ...mapGetters(["GET_SEGMENT_CONFIGURATION","GET_SEGMENT_TYPES"]),
    rows() {
      return this.GET_SEGMENT_CONFIGURATION.length
    },
    segmentConfigurationIds(){
      return this.GET_SEGMENT_CONFIGURATION.map(item =>{
        return item.quote_segment_type.id
      })
    },
    segmentConfigurations(){
      return this.GET_SEGMENT_CONFIGURATION.map(item =>{
        return {
          id:item.id,
          quote_segment_type_id:item.quote_segment_type.id,
          segment_id: item.segment_id,
          type: item.quote_segment_type.name,
          company_user_id: item.company_user_id
        }
      })
    },
    segmentTypes(){
      return this.GET_SEGMENT_TYPES
    },
    activeAddModal(){
      return this.segmentTypes.length > 0 ? false: true
    }
  },
  async created(){
    await this.$store.dispatch("getSegmentConfiguration", { page: this.currentPage });
    await this.$store.dispatch("getSegmentTypes", { segmentConfigurationIds: this.segmentConfigurationIds });
    this.loaded = true
  },
  methods:{
    gomycell(item) {
      return `cell(${item})`;
    },
    toogleModal(){
      this.create = true
    },
    updateIds(value){
      this.segmentConfigurationIds.push(value)
    },
    async update(){
      try {
        await this.$store.dispatch("putSegmentConfiguration",{segments:this.segmentConfigurations, ids: this.segmentConfigurationIds, page: this.currentPage});
        toastr.success("successful update");
      } catch (error) {
        toastr.error("unsuccessful update.");
      }
    }
  }
};
</script>