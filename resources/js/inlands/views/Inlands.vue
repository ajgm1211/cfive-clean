<template>
	<div class="container-fluid">
		<div class="row mt-5" >
			<div class="col-12 mb-2" style="padding: 0px 50px">
				<a href="/api/inlands/" class="p-light quote-link">
					<i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back
				</a>
			</div>
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
				<b-tabs card v-if="this.currentData.type.id == 1">
					<b-tab title="Per Ranges" @click="changeView('range')">
						<inland-ranges
							v-if="range"
							:equipment="equipment" 
							:datalists="datalists"
							:classTable="classTable"
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
				<b-tabs card v-if="this.currentData.type.id == 2">
					<b-tab title="Per Location">
						<per-location
							v-if="range"
							:equipment="equipment" 
							:datalists="datalists"
							:classTable="classTable"
							:actions="actions.ranges">
						</per-location>
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
	import PerLocation from './PerLocation';
	import actions from '../../actions';
	import FormInlineView from '../../components/views/FormInlineView.vue';

	export default {
		components: { 
			InlandRanges,
			InlandKm,
			PerLocation,
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
				classTable: 'table-contract',

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
                    ports: { 
                        label: 'Ports', 
                        searchable: true, 
                        type: 'multiselect', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select options', 
                        options: 'harbors',
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
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5 input-h'
	                },
	                type: { 
	                    label: 'Calculation', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select', 
	                    options: 'types',
	                    colClass: 'col-lg-1'
	                },
                    restrictions: { 
                        label:'Restrictions', 
                        searchable: true, 
                        type: 'multiselect', 
                        trackby: 'business_name', 
                        placeholder: 'Select options', 
                        options: 'companies' 
					},
                    providers: { 
                        label: 'Provider', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select', 
						options: 'providers',
						colClass: 'col-lg-1'
                        
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
                console.log(response.data.data);
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