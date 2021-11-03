<template>
    <div>
        <div v-for="(item, index) in companyUsers" :key="index" class="card">
            <img class="card-img-top" src="" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">{{item.name}}</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
</template>

<script>

import MainButton from "../../common/MainButton.vue";
import CustomInput from "../../common/CustomInput.vue";
import { mapGetters } from "vuex";
import SorteableDropdown from "../../common/SorteableDropdown.vue";

export default {
  props: {
  },
  components: {

  },
  data: () => ({

  }),
  mounted() {
    
  },
  methods: {
    
  },
  computed: {
     //...mapGetters(["GET_PRICE_LEVEL_DATA"]),
  },
};
</script>

<style lang="scss" scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>
