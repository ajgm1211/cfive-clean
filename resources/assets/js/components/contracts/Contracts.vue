<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">

				<!-- Form Contract Inline -->
				<FormInlineView v-if="loaded"
		            :data="currentData" 
		            :fields="form_fields"
		            :datalists="datalists"
		            :actions="actions.contracts"
		            :update="true"
		            @success="onSuccess"
		            >
		        </FormInlineView>
		        <!-- End Form Contract Inline -->

				<!-- Tabs Section -->
				<b-card no-body class="card-tabs">
					<b-tabs card>

						<b-tab title="Ocean Freight" active @click="changeView('freight')">
							<ocean-freight v-if="freight"
							:equipment="equipment" 
							:datalists="datalists"
							:actions="actions.oceanfreights"
							></ocean-freight>
						</b-tab>

						<b-tab title="Surcharges" @click="changeView('surcharges')">
							<surcharges
								  v-if="surcharges"
								  :datalists="datalists"
								  :actions="actions.surcharges"
								  >
							</surcharges>
						</b-tab>

						<b-tab title="Restrictions">
							<restrictions v-if="loaded"
								:datalists="datalists"
								:actions="actions.restrictions"
								:data="currentData['restrictions']"
							></restrictions>
						</b-tab>

						<b-tab title="Remarks">
							<remarks v-if="loaded"
								:actions="actions.remarks"
								:data="currentData"
							></remarks>
						</b-tab>

						<b-tab title="Files">
							<files
								:actions="actions.contracts"
							></files>
						</b-tab>

					</b-tabs>
				</b-card>
				<!-- End Tabs Section -->

			</div>

		</div>

	</div>

</template>
<script>
	import OceanFreight from './Freight';
	import Surcharges from './Surcharges';
	import Restrictions from './Restrictions';
	import Remarks from './Remarks';
	import Files from './Files';
	import actions from '../../actions';
	import FormInlineView from '../views/FormInlineView';

	export default {
		components: { 
			OceanFreight,
			Surcharges,
			Restrictions,
			Remarks,
			Files,
			FormInlineView

		},
		data() {
			return {
				actions: actions,
				equipment: {},
				freight: false,
				surcharges: false,
				loaded: false,
				currentData: {
					daterange: { startDate: null, endDate: null }
				},

				/* Form Inline Fields */
                form_fields: {
                    name: { 
                        label: 'Reference', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Reference'
                    },
                    daterange: { 
                        label: 'Validity', 
                        rules: 'required', 
                        type:"daterange", 
                        sdName: 'validity', 
                        edName: 'expire'
                    },
                    carriers: { 
                        label:'Carriers', 
                        searchable: true, 
                        type: 'multiselect', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select options', 
                        options: 'carriers' 
                    },
                    gp_container: { 
                        label: 'Equipment', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'equipments' 
                    },
                    direction: { 
                        label:'Direction', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'directions' 
                    },
                    status: {
                    	label: 'Status',
                    	type: 'status'
                    }
                }
			}
		},
		created() {

			let id = this.$route.params.id;

			/* Return the lists data for dropdowns */
			api.getData({}, '/api/v2/contracts/data', (err, data) => {
				this.setDropdownLists(err, data.data);
				this.freight = true;
				this.loaded = true;
			});

			actions.contracts.retrieve(id)
			.then( ( response ) => {
				this.currentData = response.data.data;
				this.onSuccess(this.currentData);
				this.currentData['daterange'] = { startDate: this.currentData.validity, endDate: this.currentData.expire };
			})
			.catch(( data ) => {
				this.$refs.observer.setErrors(data.data.errors);
			});

		},
		methods: {
			/* Execute when inline form updated */
			onSuccess(data){
				this.equipment = data.gp_container;
			},
			changeView(val){

				if(val == 'freight'){
					this.freight = true;
					this.surcharges = false;
				}

				if(val == 'surcharges'){
					this.freight = false;
					this.surcharges = true;
				}
			},
			/* Set the Dropdown lists to use in form */
			setDropdownLists(err, data){
				this.datalists = data;
				
				this.datalists['route_types'] = [
						{ id: 'port', name: 'Port', vselected: 'harbors' }, 
						{ id: 'country', name: 'Country', vselected: 'countries' }
					];
			},
		},
		watch: {
		}
	}
</script>