
<template>
  <div class="col-12">
    <section>
      <EditInputs :company="company" :user="user" @confirmation="ShowConfirm"/>
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
    <ConfirmTransfer
      :create="true"
      v-if="create"
      :title="'Transfer'"
      :action="'Confirm'"
      @cancel="create = false"
      :company="company.id"
    />
  </div>
</template>

<script>

import { mapState } from 'vuex'
import EditInputs from './Partials/EditInputs'
import actions from '../../store/modules/company/actions'
import Contacts from './Partials/Contacts'
import ConfirmTransfer from './Modals/ConfirmTransfer'

export default {
  components: {EditInputs, Contacts, ConfirmTransfer},
  data() {
    return {
        actions:actions,
        company:{},
        create:false
    }
  },
  async created(){
    const {data} =  await this.actions.retrieve(this.$route.params.id)
    this.company  = data.data
  },
  computed:{
    ...mapState('auth', ['user'])
  },
  methods:{
    ShowConfirm(){
      this.create = true
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
