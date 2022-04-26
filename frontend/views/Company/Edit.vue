
<template>
  <div class="col-12">
    <section>
      <EditInputs :company="company" :user="user" />
    </section>
    <section>
      <div>
        <b-tabs content-class="mt-3">
          <b-tab title="Contacts" active>
            <Contacts/>
          </b-tab>
        </b-tabs>
      </div>
    </section>
  </div>
</template>

<script>

import { mapState } from 'vuex'
import EditInputs from './partials/EditInputs'
import actions from '../../store/modules/company/actions'
import Contacts from './partials/Contacts'

export default {
  components: {EditInputs, Contacts},
  data() {
    return {
        actions:actions,
        company:{}
    }
  },
  async created(){
    const {data} =  await this.actions.retrieve(this.$route.params.id)
    this.company  = data.data
  },
  computed:{
    ...mapState('auth', ['user'])
  }
}
</script>

<style lang="scss" scoped>
</style>
