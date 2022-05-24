
<template>
  <div class="col-12">
    <section>
      <EditInputs :contact="contact" :user="user" />
    </section>
  </div>
</template>

<script>

import { mapState } from 'vuex'
import EditInputs from './partials/EditInputs'
import actions from '../../store/modules/contact/actions'

export default {
  components: {EditInputs},
  data() {
    return {
        actions:actions,
        contact:{}
    }
  },
  async created(){
    const {data} =  await this.actions.retrieve(this.$route.params.id)
    this.contact  = data.data
  },
  computed:{
    ...mapState('auth', ['user'])
  }
}
</script>

<style lang="scss" scoped>
</style>
