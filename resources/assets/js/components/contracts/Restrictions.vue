<template>
    <b-card>
        <div class="row">
            <div class="col-12">
                <h6 class="title-card">Restrictions</h6>
            </div>
            <div class="col-12">
                <form ref="form" @submit.stop.prevent="handleSubmit" class="modal-input">
                    <b-form-group
                                  :state="nameState"
                                  label="Company"
                                  label-for="company"
                                  >
                        <multiselect v-model="company" :options="options" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select an option"></multiselect>

                    </b-form-group>
                    <b-form-group
                                  :state="nameState"
                                  label="Users"
                                  label-for="users"
                                  >
                        <multiselect v-model="users" :options="options" :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select an option"></multiselect>

                    </b-form-group>
                </form>
            </div>
        </div>
    </b-card>
</template>


<script>
    import Multiselect from 'vue-multiselect';

    export default {
        components: { 
            Multiselect,
     
        },
        data() {
            return {
                isBusy:true, // Loader
                data: null,
                users: '',
                company: '',
                options: [
                    'opcion 1',
                    'opcion 2',
                    'opcion 3'
                ],
            }
        },
        
       
    }
</script>