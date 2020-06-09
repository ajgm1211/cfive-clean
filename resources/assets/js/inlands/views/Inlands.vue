<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">

				<!-- Form Contract Inline -->
				<FormInlineView
				v-if="loaded"
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
					<b-tab title="Per Ranges" @click="changeView('range')">
						<inland-ranges
							v-if="range"
							:equipment="equipment" 
							:datalists="datalists"
							:actions="actions.ranges">
						</inland-ranges>
					</b-tab>
					<b-tab title="Per Km" @click="changeView('km')">
						<inland-km
							v-if="km"
							:equipment="equipment" 
							:datalists="datalists"
							:actions="actions.kms">
						</inland-km>
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
	import FormInlineView from '../../components/views/FormInlineView.vue';

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
				inland: false,
				loaded: false,
				km: false,
				range: false,
				currentData: { daterange: { startDate: null, endDate: null } },

				/* Form Inline Fields */
	            form_fields: {
	                reference: { 
	                    label: 'Reference', 
	                    type: 'text', 
	                    rules: 'required', 
	                    placeholder: 'Reference',
	                    colClass: 'col-lg-2 col-pr-5'
	                },
	                direction: { 
	                    label:'Direction', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select option', 
	                    options: 'directions',
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5'
	                },
	                daterange: { 
	                    label: 'Validity', 
	                    rules: 'required', 
	                    type: "daterange", 
	                    sdName: 'validity', 
	                    edName: 'expire',
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5'
	                },
	                gp_container: { 
	                    label: 'Equipment', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select option', 
	                    options: 'equipments',
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5'
	                },
	                type: { 
	                    label: 'Calculation Type', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select option', 
	                    options: 'types',
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5'
	                },
                    restrictions: { 
                        label:'Restrictions', 
                        searchable: true, 
                        type: 'multiselect', 
                        trackby: 'business_name', 
                        placeholder: 'Select options', 
                        options: 'companies' 
                    },
				}
			}
		},
		created() {

			let id = this.$route.params.id;

			/* Return the lists data for dropdowns */
			api.getData({}, '/api/v2/inland/data', (err, data) => {
				this.setDropdownLists(err, data.data);
				this.range = true;
				this.loaded = true;
			});

			actions.inlands.retrieve(id)
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

				if(val == 'range'){
					this.range = true;
					this.km = false;
				}

				if(val == 'km'){
					this.range = false;
					this.km = true;
				}
			},

			/* Set the Dropdown lists to use in form */
			setDropdownLists(err, data){
				this.datalists = data;
			},
		}
	}
</script>