<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head"><h3>Add Integration</h3></div>
      <form action="" class="create-form" autocomplete="off">
        <CustomInput
          label="Client"
          name="name"
          ref="name"
          v-model="GET_COMPANY_USER.name"
          :disabled="true"
          :rules="{
            required: true            
          }"
        />
        <SorteableDropdown 
          @reset="selected = ''" 
          :error="selectable_error" 
          label="Available Integrations" 
          @selected="setSelected($event)" 
          :itemList="apiProvidersOptions"
        />
        <CustomInput 
          v-for="(item, index) in credential_keys" 
          :key="index"
          :label="item"
          :name="item"
          :ref="item"
          :placeholder="item"
          @input="updateCredentialValues(item, $event)"
          :rules="{
            required: true,
          }"
        />
      </form>
      <div class="controls-container">
        <p @click="$emit('cancel')">Cancel</p>
        <MainButton @click="postData()" text="Add Integration" :add="true" />
      </div>
    </div>
  </section>
</template>

<script>
import MainButton from "../../common/MainButton.vue";
import CustomInput from "../../common/CustomInput.vue";
import SorteableDropdown from '../../common/SorteableDropdown.vue';
import { mapGetters } from "vuex";

export default {
  components: { MainButton, CustomInput, SorteableDropdown },
  props: {
  },
  data: () => ({
    selected: {},
    selectable_error: false,
    credential_keys: [],
    credential_values: {}
  }),
  mounted() {    
    this.$store.dispatch("getAvailableApiProviders", {
      body: {
        company_user_id: this.GET_COMPANY_USER.id,
      }
    })
  },
  computed: {
    ...mapGetters([
      "GET_COMPANY_USER",
      "GET_AVAILABLE_API_PROVIDERS"
    ]),
    apiProvidersOptions() {
      return this.GET_AVAILABLE_API_PROVIDERS.map(provider => {
        return {
          id: provider.id,
          alphacode: provider.name,
          credential_keys: provider.credential_keys     
        };
      });
    }
  },
  methods: {
    postData() {
      this.$store.dispatch("createApiCredentials", {
        body: {
          model_id: this.GET_COMPANY_USER.id,
          api_provider_id: this.selected.id,
          credentials: this.credential_values,
        },
      }).then(() => {
        this.$emit('cancel');
        this.$store.dispatch("getApiProvidersByCompanyUser", {
          id: this.$route.params.id
        });
      });
    },
    updateCredentialValues(prop, value) {
      Vue.set(this.credential_values, prop, value);
    },
    setSelected(option) {
      this.selected = option;
      this.credential_values = {};
      this.credential_keys = JSON.parse(option.credential_keys); 
      for(let i=0; i<this.credential_keys.length; i++){
        let item = this.credential_keys[i];
        this.credential_values[item] = "";
      } 
    },
  },
};
</script>

<style lang="scss" scoped>
section {
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: transparent;
}

.layer {
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.397);
  z-index: 5000;
  position: absolute;
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
  top: 50%;
  transform: translate(-50%, -50%);
  z-index: 5001;
}

.create-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 20px;
  row-gap: 30px;
  padding: 40px 40px 20px 40px;
}

.controls-container {
  display: flex;
  justify-content: flex-end;
  padding: 20px;
  align-items: center;

  & > p {
    margin: 0;
    margin-right: 20px;
    color: #ff4c61;
    cursor: pointer;
  }
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
</style>
