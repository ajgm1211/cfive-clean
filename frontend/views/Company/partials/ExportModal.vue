<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div class="modal-content-create">
        <b-form @submit.prevent="exportCompanies">
          <b-container>
              <b-row>
                  <b-col cols="12" md="12" class="mb-2">
                      <p>In what format do you want to export the companies?</p>
                  </b-col>
                  <b-col cols="12" md="12" class="mb-2">
                    <b-form-select
                        name="select-format"
                        class="input-v2" 
                        v-model="format" 
                        :options="options_formats" 
                        >
                    </b-form-select>
                  </b-col>
              </b-row>
              <b-row class="modal-footer-create">
                  <b-col cols="12" md="12">
                      <div>
                        <!-- Alerts Go HERE!-->
                      </div>
                      <div class="modal-footer-create-container">
                        <div class="modal-footer-content-wl"></div>
                        <div class="modal-footer-create-container-btns">
                          <p @click="$emit('cancel')">Cancel</p>
                          <b-link class="btn-form" :href="'/companies/v2/export-companies/' + format" @click="exportCompanies()">Export</b-link>
                        </div>
                      </div>
                  </b-col>
              </b-row>
          </b-container>
        </b-form>
      </div>
    </div>
  </section>
</template>

<script>

import toastr from "toastr"

export default {
  props: {
    title: {
      type: String,
    },
    action:{
      type:String
    }
  },
  data(){
    return {
        format:'xlsx',
        options_formats: [
        { text: 'csv', value: 'csv' },
        { text: 'xls', value: 'xls' },
        { text: 'xlsx', value: 'xlsx' }
        ]
    }
  },
  methods: {
    async exportCompanies(){
        toastr.warning("Export in progress...")
        this.$emit('cancel')
    }
  },
};
</script>

