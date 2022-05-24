<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head">
        <h3>{{ action + " " + title }}</h3>
      </div>
      <div v-if="create" class="modal-content-create">
        <div>
          <form on="" class="create-form" autocomplete="off">
            <div>
              <div style="padding-bottom:10px">
                  <b-form-select class="s-input" v-model="segmentConfiguration.quote_segment_type_id" ref="option_selected" required>
                      <b-form-select-option :value="null" selected disabled>Select a Segment Type</b-form-select-option>
                      <b-form-select-option v-for="(segmentType, index) in segmentTypes" :key="index" :value="segmentType.id">{{segmentType.name}}</b-form-select-option>
                  </b-form-select>
                  <span v-if="errorSelectSegmentType">{{errorSelectSegmentType}}</span>
              </div>
              <div style="padding-bottom:10px">
                  <b-form-input class="s-input" type="number" v-model="segmentConfiguration.segment_id" placeholder="Enter your new segment ID"  required/>
                  <span v-if="errorSegmentId">{{errorSegmentId}}</span>
              </div>
            </div>
          </form>
          <div class="modal-footer-create-container">
            <div class="modal-footer-content-wl input-box">

            </div>
            <div class="modal-footer-create-container-btns">
              <p @click="$emit('cancel')">Cancel</p>
              <MainButton
                @click="createSegmentConfiguration()"
                :text="action + ' ' + title"
                :add="true"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import toastr from "toastr"
import MainButton from "../../../../components/common/MainButton.vue"

export default {
  components:{MainButton},
  props: {
    title: {
      type: String,
    },
    action: {
      type: String,
    },
    create:{
      type:Boolean,
      default() {
        return false;
      }
    },
    segmentTypes:{
      type: Array,
      default() {
        return [];
      }
    },
    ids:{
      type: Array,
      default() {
        return [];
      }
    },
    page:{
      type:Number,
      default(){
        return 0;
      }
    }
  },
  data() {
    return{
      segmentConfiguration:{
        segment_id:null,
        quote_segment_type_id:null
      }
    }
  },
  computed: {
    errorSegmentId(){
      if (!this.segmentConfiguration.segment_id) {
        return 'Segment ID is required.'
      }else{
        return null
      }
    },
    errorSelectSegmentType(){
      if (!this.segmentConfiguration.quote_segment_type_id) {
        return 'Segment Type is required.'
      }else{
        return null
      }
    }
  },
  methods: {
    async createSegmentConfiguration(){
      if(this.checkInputs()){
        try {
          await this.$emit('updateIds', this.$refs.option_selected.value)
          await this.$store.dispatch("postSegmentConfiguration", {segment:this.segmentConfiguration, ids: this.ids, page: this.page });
          toastr.success("Created successfully")
          this.$emit('cancel')
        } catch (error) {
          toastr.success("Not created successfully")
        }
      }
    },
    checkInputs(){
      if (this.segmentConfiguration.segment_id && this.segmentConfiguration.quote_segment_type_id) {
        return true;
      }else{
        return false;
      }

    }
  }
};
</script>


<style lang="scss" scoped>
.layer {
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.397);
  z-index: 5000;
  position: fixed;
  top: 0;
  left: 0;
}

.create-modal {
  background: #f9f9f9;
  border-radius: 15px;
  width: 600px;
  //   height: 350px;
  position: absolute;
  left: 50%;
  top: 30%;
  transform: translate(-50%, -50%);
  z-index: 50010;
}

.create-form {
  display: grid;
  grid-template-columns: 1fr;
  column-gap: 20px;
  row-gap: 30px;
  padding: 40px 40px 20px 40px;
}

.controls-container {
  display: flex;
  justify-content: flex-end;
  padding: 20px;
  align-items: center;
}

.modal-head {
  background-color: white;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  padding: 10px 20px;

  & > h3 {
    margin: 0;
    text-transform: capitalize;
    font-size: 17px;
    color: #071c4b;
    letter-spacing: 0.05em;
    font-weight: 500;
  }
}

.hidden {
  display: none;
}

.modal-content-create{
  padding: 10px 25px;
}
.modal-footer-create-container{
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.modal-footer-create-container-btns{
  display: flex;
  align-items: center;
  justify-content: right;
  & > p {
    margin: 0;
    margin-right: 20px;
    color: #ff4c61;
    cursor: pointer;
  }
}

.modal-footer-content-wl{
    align-items: center;
    display: flex;
    justify-content: left;
    padding-top: 4px;

      & > p {
        margin: 0;
        margin-right: 20px;
        color: #ff4c61;
        cursor: pointer;
      }
      label{
        width: 100%;
        margin: 0;
      }
      .input-v2{
        width: auto;
        height: auto;
        margin-top:0;
      }
}

.input-box{
  align-items: center;
  display: flex;
}

#checkbox-create{

  .custom-control-label{
    font-size: 14px;
    line-height: 21px;
    letter-spacing: 0.05em;
    padding: 3px 0px 0px 5px;
    &:before{
      left: -1.5rem !important;
    }
    &:after{
      left: -1.5rem !important;
    }
  }
}
</style>