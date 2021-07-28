<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">
			<div class="col-12 mb-2" style="padding: 0px 35px">
				<a href="/api/sale_terms/" class="p-light quote-link">
					<i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back
				</a>
			</div>
				<!-- Form Contract Inline -->
				<FormInlineView
				v-if="loaded"
				:data="currentData" 
				:fields="form_fields"
				:datalists="datalists"
				:actions="actions.sale_terms"
				:update="true"
				@success="onSuccess"
				>
			</FormInlineView>
			<!-- End Form Contract Inline -->

			<!-- Tabs Section -->
			<b-card no-body class="card-tabs">
				<b-tabs card>
					<b-tab title="Details">
						<SaleCharges
							:datalists="datalists"
							:equipment="equipment"
							:actions="actions.sale_charges">
						</SaleCharges>
					</b-tab>
				</b-tabs>
			</b-card>

			<!-- End Tabs Section -->

		</div>

	</div>

</div>

</template>
<script>
	import FormInlineView from '../../components/views/FormInlineView.vue';
    import actions from '../../actions';
    import SaleCharges from './SaleCharges';

	export default {
		components: {
            FormInlineView,
            SaleCharges
		},
		data() {
			return {
				actions: actions,
				equipment: {},
				loaded: false,
                datalists: {},
                currentData: {},

				/* Form Inline Fields */
	            form_fields: {
	                name: { 
                        label: 'Name', 
                        type: 'text', 
                        rules: 'required', 
                        placeholder: 'Name',
                    },
                    type: { 
                        label: 'Type', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'name', 
                        placeholder: 'Select option', 
                        options: 'types' 
                    },
                    port: { 
                        label: 'Port', 
                        searchable: true, 
                        type: 'select', 
                        rules: 'required', 
                        trackby: 'display_name', 
                        placeholder: 'Select options', 
                        options: 'harbors',
                        initial: []
                    },
                    group_container: { 
	                    label: 'Equipment', 
	                    searchable: true, 
	                    type: 'select', 
	                    rules: 'required', 
	                    trackby: 'name', 
	                    placeholder: 'Select option', 
	                    options: 'equipments',
						disabled: true,
	                },
				}
			}
		},
		created() {

			let id = this.$route.params.id;

			/* Return the lists data for dropdowns */
			api.getData({}, '/api/v2/sale_terms/data', (err, data) => {
				this.setDropdownLists(err, data.data);
				this.loaded = true;
			});

			actions.sale_terms.retrieve(id)
			.then( ( response ) => {
                console.log(response.data.data);
				this.currentData = response.data.data;
				this.onSuccess(this.currentData);
			})
			.catch(( data ) => {
				this.$refs.observer.setErrors(data.data.errors);
			});

		},
		methods: {
			/* Execute when inline form updated */
			onSuccess(data){
                this.equipment = data.group_container;
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