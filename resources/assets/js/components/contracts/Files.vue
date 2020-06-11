<template>
	  <b-card class="no-padding no-scroll">
		<div class="row">
			<div class="col-12">
				<vue-dropzone
				ref="myVueDropzone"
				:useCustomSlot="true"
				id="dropzone"
				:options="dropzoneOptions"
				v-on:vdropzone-removed-file="removeThisFile"
				v-on:vdropzone-success="success"
				>
				<div class="dropzone-container">
					    <div class="file-selector">
					    <h6 class="title-dropzone">Upload</h6>
						<figure>
						  <svg
							width="104px"
							height="104px"
							viewBox="0 0 104 104"
							version="1.1"
							xmlns="http://www.w3.org/2000/svg"
							xmlns:xlink="http://www.w3.org/1999/xlink"
						  >
							<defs>
							  <circle id="path-1" cx="36" cy="36" r="36"></circle>
							  <filter
								x="-37.5%"
								y="-29.2%"
								width="175.0%"
								height="175.0%"
								filterUnits="objectBoundingBox"
								id="filter-2"
							  >
								<feOffset
								  dx="0"
								  dy="6"
								  in="SourceAlpha"
								  result="shadowOffsetOuter1"
								></feOffset>
								<feGaussianBlur
								  stdDeviation="8"
								  in="shadowOffsetOuter1"
								  result="shadowBlurOuter1"
								></feGaussianBlur>
								<feColorMatrix
								  values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.08 0"
								  type="matrix"
								  in="shadowBlurOuter1"
								  result="shadowMatrixOuter1"
								></feColorMatrix>
								<feOffset
								  dx="0"
								  dy="1"
								  in="SourceAlpha"
								  result="shadowOffsetOuter2"
								></feOffset>
								<feGaussianBlur
								  stdDeviation="1"
								  in="shadowOffsetOuter2"
								  result="shadowBlurOuter2"
								></feGaussianBlur>
								<feColorMatrix
								  values="0 0 0 0 0.0117647059   0 0 0 0 0.0862745098   0 0 0 0 0.160784314  0 0 0 0.11 0"
								  type="matrix"
								  in="shadowBlurOuter2"
								  result="shadowMatrixOuter2"
								></feColorMatrix>
								<feMerge>
								  <feMergeNode in="shadowMatrixOuter1"></feMergeNode>
								  <feMergeNode in="shadowMatrixOuter2"></feMergeNode>
								</feMerge>
							  </filter>
							</defs>
							<g
							  id="Page-1"
							  stroke="none"
							  stroke-width="1"
							  fill="none"
							  fill-rule="evenodd"
							>
							  <g
								id="Artboard"
								transform="translate(-460.000000, -125.000000)"
							  >
								<g id="Group-4" transform="translate(412.000000, 129.000000)">
								  <g id="Group-2" transform="translate(58.000000, 0.000000)">
									<circle
									  id="Oval"
									  fill="#3560FF"
									  opacity="0.100000001"
									  cx="42"
									  cy="42"
									  r="42"
									></circle>
									<g id="Group" transform="translate(6.000000, 6.000000)">
									  <g id="Oval">
										<use
										  fill="black"
										  fill-opacity="1"
										  filter="url(#filter-2)"
										  xlink:href="#path-1"
										></use>
										<use
										  fill="#FFFFFF"
										  fill-rule="evenodd"
										  xlink:href="#path-1"
										></use>
									  </g>
									  <g
										id="upload-cloud"
										transform="translate(21.818182, 24.000000)"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
									  >
										<polyline
										  id="Path"
										  stroke="#000000"
										  points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
										></polyline>
										<path
										  d="M14.3565525,12.0897285 L14.3565525,24.1794569"
										  id="Path"
										  stroke="#3560FF"
										></path>
										<path
										  d="M25.6438239,20.7792208 C28.2965835,19.3021499 29.6312816,16.1761528 28.8860265,13.1856562 C28.1407715,10.1951596 25.5052337,8.10125672 22.4838689,8.09921935 L20.8179512,8.09921935 C19.7219904,3.76967373 16.1275086,0.577339516 11.7773112,0.0700384831 C7.42711383,-0.43726255 3.22057026,1.84535014 1.19724759,5.81113853 C-0.826075091,9.77692693 -0.247870665,14.6059952 2.6515151,17.9569414"
										  id="Path"
										  stroke="#3560FF"
										></path>
										<polyline
										  id="Path"
										  stroke="#3560FF"
										  points="19.6458087 17.3789847 14.3565525 12.0897285 9.06729634 17.3789847"
										></polyline>
									  </g>
									</g>
								  </g>
								</g>
							  </g>
							</g>
						  </svg>
						</figure>
						Drop Or Add Files Here
						<p><span> or </span></p>
						<button type="button" class="btn btn-primary btn-bg">Choose file</button>
					  </div>
				  </div>
				</vue-dropzone>
			</div>
		</div>
	</b-card>
</template>



<script>
	import vue2Dropzone from 'vue2-dropzone';
	import 'vue2-dropzone/dist/vue2Dropzone.min.css';

	export default {
		components: {
			vueDropzone: vue2Dropzone
		},
		props: {
			actions: Object,
		},
		data: function () {
			return {
				dropzoneOptions: {
					url: `/api/v2/contracts/${this.$route.params.id}/storeMedia`,
					thumbnailWidth: 150,
					maxFilesize: 0.5,
					headers: { "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content },
					addRemoveLinks: true,
					previewTemplate: this.template()
				}
			}
		},
		methods: {
			setFiles(data){
				let file = {};
				let url = '';
				let vcomponent = this;
				let i = 0;

				let url_tags = document.getElementsByClassName("img-link");

				data.forEach(function(media){
					vcomponent.$refs.myVueDropzone.manuallyAddFile(media, media.url);
					url_tags[i].setAttribute('href', media.url);
					i+=1;
				});	
			},
			removeThisFile(file){
				let id = this.$route.params.id;
				
				this.actions.removefile(id, { 'id': file.id })
				.then( ( response ) => {
				})
				.catch(( data ) => {

				});
			},
			success(file, response){
				let url_tags = $(".img-link").last();
				url_tags.attr('href', response.url);
			},
			template: function () {
				return `<div class="dz-preview dz-complete dz-image-preview"><a href="" class="img-link" target="_blank">
							<div class="dz-image"><img data-dz-thumbnail /></div>
							<div class="dz-details">
								<div class="dz-filename"><span data-dz-name></span></div>
								<div class="dz-size" data-dz-size></div>
								
							</div>
							<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
							<div class="dz-success-mark"><span>✔</span></div>
							<div class="dz-error-mark"><span>✘</span></div>
							<div class="dz-error-message"><span data-dz-errormessage></span></div></a>
						</div>
				`;
			},	
		},
		created(){
			let id = this.$route.params.id;

			this.actions.getfiles(id)
			.then( ( response ) => {
				this.setFiles(response.data.data);
			})
			.catch(( data ) => {

			});

		}

	}
</script>

