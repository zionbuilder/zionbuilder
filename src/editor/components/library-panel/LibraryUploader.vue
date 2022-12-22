<template>
	<form id="znpb-upload-form-library" enctype="multipart/form-data" novalidate>
		<div
			class="znpb-empty-list__container znpb-editor-library-upload"
			:class="{ 'znpb-editor-library-upload--dragging': !isInitial }"
		>
			<div class="znpb-empty-list__border-top-bottom"></div>
			<div class="znpb-empty-list__border-left-right"></div>
			<div class="znpb-empty-list__content">
				<template v-if="isInitial && !isSaving">
					<Icon icon="import-big-icon" />
					<p class="znpb-editor-library-upload__text">
						{{ __('Drag and drop your exported item here or just click to ', 'zionbuilder') }}
						<span class="">{{ __('browse', 'zionbuilder') }}</span>
						{{ __('for files', 'zionbuilder') }}
					</p>
				</template>
				<div v-if="!isInitial && !isSaving" class="znpb-library-uploading-wrapper">
					<Icon icon="long-arrow-right" :bg-size="68" bg-color="#06bee1" :rounded="true" color="#fff" :size="21" />
					<p class="znpb-library-uploading-wrapper__text">
						<b>{{ __('Drop your files', 'zionbuilder') }}</b
						><br />
						{{ __('to upload', 'zionbuilder') }}
					</p>
				</div>

				<input
					type="file"
					accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed"
					multiple
					name="file"
					:disabled="isSaving"
					class="znpb-library-input-file"
					@change="uploadFiles"
				/>
				<Loader v-if="isSaving" />
				<span v-if="errorMessage.length > 0">{{ errorMessage }}</span>
			</div>
		</div>
	</form>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref } from 'vue';
import { onBeforeUnmount, onMounted } from 'vue';
const { useLibrary } = window.zb.composables;

withDefaults(defineProps<{ noMargin?: boolean }>(), {
	noMargin: false,
});

const emit = defineEmits(['file-uploaded']);

const isInitial = ref(true);
const isSaving = ref(false);
const errorMessage = ref('');

onMounted(() => {
	const dropArea = document.getElementById('znpb-upload-form-library');

	if (!dropArea) {
		return
	}

	dropArea.addEventListener('dragenter', highlightForm);
	dropArea.addEventListener('dragleave', dragOut);
	dropArea.addEventListener('dragover', highlightForm);
	dropArea.addEventListener('drop', dragDropped);
});

onBeforeUnmount(() => {
	const dropArea = document.getElementById('znpb-upload-form-library');

	if (!dropArea) {
		return
	}

	dropArea.removeEventListener('dragenter', highlightForm);
	dropArea.removeEventListener('dragleave', dragOut);
	dropArea.removeEventListener('dragover', highlightForm);
	dropArea.removeEventListener('drop', dragDropped);
});

function highlightForm() {
	isInitial.value = false;
}
function dragOut() {
	isInitial.value = true;
}

function dragDropped() {
	isInitial.value = true;
}

function uploadFiles(event: Event) {
	const {
		files: fileList,
		name: fieldName,
	} = <HTMLInputElement>event.target;

	const formData = new FormData();

	if (!fileList || !fileList.length) return;

	// append the files to FormData
	Array.from(fileList).forEach((file) => {
		formData.append(fieldName, file, file.name);
	});

	// send it to axios
	saveFile(formData);
}

function saveFile(formData: FormData) {
	const { getSource } = useLibrary();

	const localLibrary = getSource('local_library');

	if (!localLibrary) {
		console.warn('Local library was not registered. It may be possible that a plugin is removing the default library.');
		return;
	}

	isSaving.value = true;
	errorMessage.value = '';

	localLibrary
		.importItem(formData)
		.catch(error => {
			console.error(error);

			if (typeof error.response.data === 'string') {
				errorMessage.value = error.response.data;
			} else errorMessage.value = arrayBufferToString(error.response.data);
		})
		.finally(() => {
			isSaving.value = false;
			isInitial.value = true;
			emit('file-uploaded', true);
		});
}

function arrayBufferToString(buffer: ArrayBuffer) {
	const arr = new Uint8Array(buffer);
	const str = String.fromCharCode.apply(String, arr);
	if (/[\u0080-\uffff]/.test(str)) {
		throw new Error('this string seems to contain (still encoded) multi bytes');
	}
	return str;
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
	transition: all 0.2s;

	&--dragging {
		.znpb-empty-list__border-top-bottom {
			&:after,
			&:before {
				background-image: linear-gradient(to right, var(--zb-secondary-color) 77%, rgba(255, 255, 255, 0) 0%);
			}
		}

		.znpb-empty-list__border-left-right {
			&:after,
			&:before {
				background-image: linear-gradient(to top, var(--zb-secondary-color) 77%, rgba(255, 255, 255, 0) 0%);
			}
		}
	}

	&__text {
		font-size: 16px;
		span {
			color: var(--zb-secondary-color);
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

			svg > circle:first-of-type {
				fill: var(--zb-surface-lighter-color);
			}
		}
	}
}
</style>
