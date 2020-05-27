<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">

				<!-- Form Contract Inline -->
				<FormInlineView
				:data="currentData" 
				:fields="form_fields"
				:datalists="datalists"
				:actions="actions.inlands"
				:update="true"
				@success="onSuccess"
				>
			</FormInlineView>
			<!-- End Form Contract Inline -->

			<!-- Tabs Section -->
			<b-card no-body class="card-tabs">
				<b-tabs card>
					<b-tab title="Per Ranges">
						<inland-ranges></inland-ranges>
					</b-tab>
					<b-tab title="Per Km">
						<inland-km></inland-km>
					</b-tab>
				</b-tabs>
			</b-card>

			<!-- End Tabs Section -->

		</div>

	</div>

</div>

</template>
<script>
import InlandRanges from './InlandRanges';
import InlandKm from './InlandKm';
import actions from '../../actions';
import FormInlineView from '../views/FormInlineView';

export default {
	components: { 
		InlandRanges,
		InlandKm,
		FormInlineView
	},
	data() {
		return {
			actions: actions,
			equipment: {},
			freight: false,
			loaded: false,
			currentData: { daterange: { startDate: null, endDate: null } },

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
			this.loaded = true;
			this.equipment = data.gp_container;
		},

		/* Set the Dropdown lists to use in form */
		setDropdownLists(err, data){
			this.datalists = data;

			this.datalists['route_types'] = [
			{ id: 'port', name: 'Port', vselected: 'harbors' }, 
			{ id: 'country', name: 'Country', vselected: 'countries' }
			];
		},
	}
}
</script>