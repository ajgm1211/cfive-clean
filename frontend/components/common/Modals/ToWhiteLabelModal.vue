<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div class="modal-content-create">
        <b-form @submit.prevent="AddToWhiteLabel">
          <b-container>
              <b-row>
                  <b-col cols="12" md="12" class="mb-2">
                      <p>Are you sure to transfer these {{title}} to WhiteLabel?</p>
                  </b-col>
                  <b-col cols="12" md="12" class="mb-2">
                    <b-list-group v-for="(company, companyKey) in selected" :key="companyKey">
                      <b-list-group-item>{{company.business_name}}</b-list-group-item>
                    </b-list-group>
                    <p>selected {{title}}: {{selected.length}}</p>
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
                          <b-button type="submit" class="btn-form" variant="primary">Add To WhiteLabel</b-button>
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

import MainButton from "../../../components/common/MainButton"
import toastr from "toastr"

export default {
  components: {MainButton},
  props: {
    title: {
      type: String,
    },
    action:{
      type:String
    },
    selected:{
      type:Array,
      default(){
        return []
      }
    }
  },
  methods: {
    async AddToWhiteLabel(){
      try {
        await this.$emit('transferTWhiteLabel')
        toastr.success("successful create")
        this.$emit('cancel')
      } catch (error) {
        toastr.error("unsuccessful create.")
      }
    }
  },
};
</script>

