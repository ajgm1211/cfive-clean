<template>
    <div class="row">
        <div v-for="(provider, index) in providers" :key="index" class="col-12 col-sm-3">
          <div class="card text-center mb-2">
              <img class="card-img-top" :src="provider.image" alt="Card image cap">
              <div class="card-body">
                  <h5 class="card-title">{{ provider.name }}</h5>

                  <button v-if="provider.api_credential && !provider.api_credential.status" class="btn btn-success" @click="changeStatus(provider, true)">
                    Activar Integración
                  </button> 
                  <button v-else class="btn btn-danger" @click="changeStatus(provider, false)">
                    Desactivar Integración
                  </button>

                  <p><a @click="showModalSeeApiCredentials(provider)" class="col-12 ">Ver credenciales</a></p>
              </div>
          </div>
        </div>        
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  props: {
    providers: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  computed: {
    ...mapGetters([
      "GET_COMPANY_USER",
    ]),
  },
  methods: {   
    showModalSeeApiCredentials(apiProvider) {
      this.$emit('show', apiProvider);
    },
    changeStatus(provider, newStatus) {
      if (!provider.api_credential) {
        this.$store.dispatch("deleteApiProviderOfCompanyUser", {
          id: this.GET_COMPANY_USER.id,
          body: {
            api_provider_id: provider.id
          },
        }).then(() => {
          this.reloadApiProviders();
        });
      } else {
        this.$store.dispatch("updateApiCredentialsStatus", {
          id: provider.api_credential.id,
          body: {
            status: newStatus
          },
        }).then(() => {
          this.$store.dispatch("getApiProvidersByCompanyUser", {
            id: this.$route.params.id
          });
        });
      }
    },
    reloadApiProviders() {
      this.$store.dispatch("getApiProvidersByCompanyUser", {
        id: this.$route.params.id
      });
    }
  }
};
</script>

<style lang="scss" scoped>
  .card-img-top{
    max-height: 150px;
    object-fit: contain;
  }
  .card-title{
    margin-bottom: 20px;;
  }
  .btn{
    margin-bottom: 20px;
  }
</style>
