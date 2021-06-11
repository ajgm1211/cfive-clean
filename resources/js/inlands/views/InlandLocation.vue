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
			<div class="row mb-3">
				<div class="col-12">
					<div class="float-right">
						<button class="btn btn-primary btn-bg" v-b-modal.addRange>Import File</button>
					</div>
				</div>
			</div>

			<!-- Tabs Section -->
			<b-card no-body class="card-tabs">
				
				<per-location
					v-if="range"
					:equipment="equipment" 
					:datalists="datalists"
					:classTable="classTable"
					:actions="actions.ranges">
				</per-location>
				
			</b-card>

			<!-- End Tabs Section -->

		</div>

	</div>

</div>

</template>
<script>
	import PerLocation from './PerLocation';
	import actions from '../../actions';
	import FormInlineView from '../../components/views/FormInlineView.vue';

	export default {
		components: { 
			PerLocation,
			FormInlineView
		},
		data() {
			return {
				actions: actions,
				equipment: {},
				inland: false,
				loaded: false,
				currentData: { daterange: { startDate: null, endDate: null } },
				classTable: 'table-inland-per-location',

				/* Form Inline Fields */
	            form_fields: {
	                reference: { 
	                    label: 'Provider', 
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
	                    placeholder: 'Select', 
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
	                    colClass: 'col-lg-2 col-pr-5 col-pl-5 input-h'
	                },
	                type: { 
	                    label: 'Calculation type', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select', 
	                    options: 'types',
	                    colClass: 'col-lg-2'
	                },
                    restrictions: { 
                        label:'Company restriction', 
                        searchable: true, 
                        type: 'multiselect', 
                        trackby: 'business_name', 
                        placeholder: 'Select options', 
                        options: 'companies' 
					},
                    providers: { 
                        label: 'Carriers', 
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

			/* Set the Dropdown lists to use in form */
			setDropdownLists(err, data){
				this.datalists = data;
			},
		}
	}
</script>