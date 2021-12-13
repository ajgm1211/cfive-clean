<template>
  <section>
    <div class="layer" @click="$emit('cancel')"></div>
    <div class="create-modal">
      <div class="modal-head"><h3>Editar Api Credentials</h3></div>
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
        <CustomInput
          label="Api Provider"
          name="name"
          ref="name"
          v-model="provider.name"
          :disabled="true"
          :rules="{
            required: true            
          }"
        />
        <template v-if="apiCredentials"> 
        <CustomInput 
          v-for="(value, key) in apiCredentials" 
          :key="key"
          :label="key"
          :name="key"
          :ref="key"
          :placeholder="key"
          v-model="apiCredentials[key]"
          :rules="{
            required: true,
          }"
        />
        </template>
        <p v-else>No credentials</p>

      </form>
      <div class="controls-container">
        <p @click="$emit('cancel')">Cancel</p>
        <MainButton v-if="apiCredentials" @click="postData()" text="Guardar cambios" :add="false" />
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
  props: ['provider'],
  data: () => ({
    apiCredentials: {}
  }),
  mounted() {    
    if (this.provider.api_credential) {
      this.apiCredentials = JSON.parse(this.provider.api_credential.credentials);
    } else {
      this.apiCredentials = null;
    }
  },
  computed: {
    ...mapGetters([
      "GET_COMPANY_USER",
    ])
  },
  methods: {
    postData() {
      if (!this.provider.api_credential) {
        this.$emit('cancel');
        return;
      }

      this.$store.dispatch("updateApiCredentials", {
        id: this.provider.api_credential.id,
        body: {
          credentials: this.apiCredentials,
        },
      }).then(() => {
        this.$emit('cancel');
        this.$store.dispatch("getApiProvidersByCompanyUser", {
          id: this.$route.params.id
        });
      });
    }
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
