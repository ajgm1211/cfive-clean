<template>
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col-12">
			<!-- Tabs Section -->
			<b-card no-body class="card-tabs">
				<b-tabs card>
					<b-tab title="Sale Templates" @click="changeView('saleterms')">
						<Index v-if="saleterms"></Index>
					</b-tab>
					<b-tab title="Sale Codes" @click="changeView('codes')">
						<Codes v-if="codes"></Codes>
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
    import Index from './App';
    import Codes from './Codes';

	export default {
		components: {
            FormInlineView,
            Index,
            Codes
		},
		data() {
			return {
				actions: actions,
				saleterms: true,
				codes: false,
			}
		},
		methods: {

			changeView(val){

				if(val == 'saleterms'){
					this.saleterms = true;
					this.codes = false;
				}

				if(val == 'codes'){
					this.saleterms = false;
					this.codes = true;
				}
			},
		}
	}
</script>
