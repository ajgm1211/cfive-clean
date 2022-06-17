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
                      <p>Are you sure to transfer these {{moduleTitle}} to WhiteLabel?</p>
                  </b-col>
                  <b-col cols="12" md="12" class="mb-2">
                    <b-list-group v-for="(item, itemKey) in selected" :key="itemKey">
                      <b-list-group-item >
                        <div class="d-flex justify-content-between">
                          <label class="item-label">
                            {{item.name}}
                          </label>    
                          <label class="item-label">
                            <slot name="entity_whitelabel" v-bind:entity="item"></slot>
                          </label>
                        </div>
                      </b-list-group-item>
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
                        <div class="modal-footer-content-wl input-box">
                          <slot name="action_whitelabel"></slot>
                        </div>
                        <div class="modal-footer-create-container-btns">
                          <p @click="$emit('cancel')">Cancel</p>
                          <b-button type="submit" class="main-btn" variant="primary">Add To WhiteLabel</b-button>
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
    },
    moduleTitle:{
      type:String
    }
  },
  methods: {
    async AddToWhiteLabel(){
      await this.$emit('transferToWhiteLabel')
      this.$emit('cancel')
    }
  },
};
</script>

