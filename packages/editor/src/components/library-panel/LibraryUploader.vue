<template>
	<form
		enctype="multipart/form-data"
		novalidate
		id="znpb-upload-form-library"
	>
		<div
			class="znpb-empty-list__container znpb-editor-library-upload"
			:class="{'znpb-editor-library-upload--dragging': !isInitial}"
		>
			<div class="znpb-empty-list__border-top-bottom"></div>
			<div class="znpb-empty-list__border-left-right"></div>
			<div class="znpb-empty-list__content">

				<template v-if="isInitial && !isSaving">
					<Icon icon="import-big-icon" />
					<p class="znpb-editor-library-upload__text">
						{{$translate('drag_drop')}}
						<span class="">{{$translate('browse')}}</span>
						{{$translate('for_files')}}
					</p>
				</template>
				<div
					v-if="!isInitial && !isSaving"
					class="znpb-library-uploading-wrapper"
				>

					<Icon
						icon="long-arrow-right"
						:bg-size="68"
						bg-color="#06bee1"
						:rounded="true"
						color="#fff"
						:size="21"
					/>
					<p class="znpb-library-uploading-wrapper__text">
						<b>{{$translate('drop_files')}}</b><br />
						{{$translate('to_upload')}}

					</p>
				</div>
				<input
					ref="formupload"
					type="file"
					accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed"
					multiple
					:name="uploadFieldName"
					:disabled="isSaving"
					@change="uploadFiles($event.target.name, $event.target.files); fileCount = $event.target.files.length"
					class="znpb-library-input-file"
				/>
				<Loader v-if="isSaving" />
				<span v-if="errorMessage.length > 0">{{errorMessage}}</span>
			</div>
		</div>
	</form>
</template>

<script>
import { importTemplateLibrary, addTemplate } from '@zb/rest'
import { inject } from 'vue'

export default {
	name: 'LibraryUploader',
	props: {
		noMargin: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {
			isInitial: true,
			isSaving: false,
			fileCount: 0,
			uploadFieldName: 'file',
			errorMessage: ''
		}
	},
	mounted () {
		let dropArea = this.$refs.formupload
		dropArea.addEventListener('dragenter', this.highlightForm)
		dropArea.addEventListener('dragleave', this.dragOut)
		dropArea.addEventListener('dragover', this.highlightForm)
		dropArea.addEventListener('drop', this.dragDropped)
	},
	setup () {
		const $zb = inject('$zb')

		function addLocalTemplate (template) {
			$zb.templates.addTemplate(template)
		}
		return {
			addLocalTemplate
		}
	},
	methods: {
		highlightForm () {
			this.isInitial = false
		},
		dragOut () {
			this.isInitial = true
		},
		dragDropped () {
			this.isInitial = true
		},

		uploadFiles (fieldName, fileList) {
			const formData = new FormData()

			if (!fileList.length) return

			// append the files to FormData
			Array.from(fileList).forEach(file => {
				formData.append(fieldName, file, file.name)
			})

			// send it to axios
			this.saveFile(formData)
		},
		saveFile (formData) {
			this.isSaving = true
			this.errorMessage = ''
			importTemplateLibrary(formData).then((result) => {
				this.isInitial = false
				addTemplate(result.data).then((value) => {
					showModal.value = false
					loading.value = true
					this.addLocalTemplate(value.data)

				})
			}).catch(error => {
				console.log('error', error)
				if (typeof error.response.data === 'string') {
					this.errorMessage = error.response.data
				} else this.errorMessage = this.arrayBufferToString(error.response.data)
			})
				.finally(() => {
					this.isSaving = false
					this.isInitial = true
					this.$emit('file-uploaded', true)
				})
		},
		decode_utf8 (s) {
			let obj = JSON.parse(s)
			return decodeURIComponent(escape(obj.message))
		},
		arrayBufferToString (buffer) {
			var s = String.fromCharCode.apply(null, new Uint8Array(buffer))

			return this.decode_utf8(s)
		}
	},
	beforeUnmount () {
		let dropArea = this.$refs.formupload
		dropArea.removeEventListener('dragenter', this.highlightForm)
		dropArea.removeEventListener('dragleave', this.dragOut)
		dropArea.removeEventListener('dragover', this.highlightForm)
		dropArea.removeEventListener('drop', this.dragDropped)
	}
}
</script>

<style lang="scss">
#znpb-upload-form-library {
	display: flex;
	width: 100%;
	height: 100%;
}

.znpb-library-uploading-wrapper {
	position: relative;
	display: flex;
	align-items: center;

	& > .znpb-editor-icon-wrapper {
		margin-bottom: -20px;
		transform-origin: center;
		animation-duration: 2s;
		animation-iteration-count: infinite;
		animation-name: BOUNCE;
		animation-timing-function: ease;
	}

	& > p.znpb-library-uploading-wrapper__text {
		margin-left: 20px;
		text-align: left;
	}
}

@keyframes BOUNCE {
	0% {
		transform: translateY(0) rotate(-90deg);
	}
	50% {
		transform: translateY(-20px) rotate(-90deg);
	}
	100% {
		transform: translateY(0) rotate(-90deg);
	}
}

input.znpb-library-input-file {
	position: absolute;
	width: 100%;
	height: 100%;
	padding: 0;
	margin: 0;
	cursor: pointer;
	opacity: 0;
}
.znpb-editor-library-upload {
	width: 100%;
	max-height: 100%;
	margin: 30px;
	transition: all .2s;

	&--dragging {
		.znpb-empty-list__border-top-bottom {
			&:after, &:before {
				background-image: linear-gradient(
				to right,
				$secondary 77%,
				rgba(255, 255, 255, 0) 0%
				);
			}
		}

		.znpb-empty-list__border-left-right {
			&:after, &:before {
				background-image: linear-gradient(
				to top,
				$secondary 77%,
				rgba(255, 255, 255, 0) 0%
				);
			}
		}
	}

	&__text {
		font-size: 16px;
		span {
			color: $secondary;
			font-weight: 500;
		}
	}

	& > .znpb-empty-list__content {
		position: relative;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;

		& > .znpb-editor-icon-wrapper {
			margin-bottom: 30px;
			color: #06bee1;
			font-size: 160px;
		}
	}
}
</style>
