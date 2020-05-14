<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">

				<!-- Form Contract Inline -->
				<FormInlineView
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

						<b-tab title="Ocean Freight" active>
							<ocean-freight v-if="freight"
							:equipment="equipment" 
							:datalists="datalists"
							:actions="actions.oceanfreights"
							></ocean-freight>
						</b-tab>

						<b-tab title="Surcharges">
							<surcharges
								  :datalists="datalists"
								  :actions="actions.surcharges"
								  >
							</surcharges>
						</b-tab>

						<b-tab title="Restrictions">
							<!--<restrictions></restrictions>-->
						</b-tab>

						<b-tab title="Remarks">
							<!--<remarks></remarks>-->
						</b-tab>

						<b-tab title="Files">
							<!--<files></files>-->
						</b-tab>

					</b-tabs>
				</b-card>
				<!-- End Tabs Section -->

			</div>

		</div>

	</div>

</template>
<script>
	import Multiselect from 'vue-multiselect';
	import DateRangePicker from 'vue2-daterange-picker';
	import OceanFreight from './Freight';
	import Surcharges from './Surcharges';
	import Restrictions from './Restrictions';
	import Remarks from './Remarks';
	import Files from './Files';
	import actions from '../../actions';
	import FormInlineView from '../views/FormInlineView.vue';

	import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
	import 'vue-multiselect/dist/vue-multiselect.min.css';

	export default {
		components: { 
			DateRangePicker,
			Multiselect,
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
				/* Inline Form */
				equipment: null,
				freight: false,
				currentData: {
					daterange: { startDate: null, endDate: null }
				},
				
				// Dropdown Lists
				datalists: {
				  'carriers': [],
				  'equipments': [],
				  'directions': [],
				  'containers': [],
				  'harbors': [],
				  'currencies': [],
				  'surcharges': [],
				  'route_types': [],
				  'destination_types': [],
				  'calculation_types': []
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
				this.freight = true;
			},

			/* Set the Dropdown lists to use in form */
			setDropdownLists(err, data){
				this.datalists = {
				  'carriers': data.carriers,
				  'equipments': data.equipments,
				  'directions': data.directions,
				  'containers': data.containers,
				  'harbors': data.harbors,
				  'currencies': data.currencies,
				  'surcharges': data.surcharges,
				  'countries': data.countries,
				  'route_types': [
						{ id: 'port', name: 'Port', vselected: 'harbors' }, 
						{ id: 'country', name: 'Country', vselected: 'countries' }
					  ],
				  'destination_types': data.destination_types,
				  'calculation_types': data.calculation_types
				}
			},
		},
		watch: {
		}
	}
</script>