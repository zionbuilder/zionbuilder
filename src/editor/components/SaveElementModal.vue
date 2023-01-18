<template>
	<Modal
		v-if="activeSaveElement.type"
		:title="__('Save to library', 'zionbuilder')"
		append-to="body"
		:width="560"
		:show-maximize="false"
		class="znpb-modal-save-element"
		:show="true"
		@close-modal="hideSaveElement"
	>
		<div class="znpb-modal-save-element-wrapper">
			<OptionsForm v-model="computedFormModel" :schema="optionsSchema" />

			<div class="znpb-modal-content-save-buttons">
				<Button class="znpb-button--secondary" @click="saveElement">
					<span>{{ __('Save', 'zionbuilder') }}</span>
				</Button>
				<Button class="znpb-button--line" @click="downloadElement">
					{{ __('Download', 'zionbuilder') }}
				</Button>
			</div>
			<p
				v-if="loadingMessage || errorMessage.length > 0"
				class="znpb-modal-save-element-wrapper__message"
				:class="{ 'znpb-modal-save-element-wrapper__message--error': errorMessage.length > 0 }"
				v-html="loadingMessage ? loadingMessage : errorMessage"
			></p>
			<Loader v-if="loading" :size="16" class="znpb-modal-save-element-wrapper__loading" />
		</div>
	</Modal>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed, onBeforeUnmount } from 'vue';
import { saveAs } from 'file-saver';

import { useSaveTemplate } from '../composables';
import { useContentStore } from '/@/editor/store';

// Common API
const { useLibrary } = window.zb.composables;
const { exportTemplate } = window.zb.api;

const { activeSaveElement, hideSaveElement } = useSaveTemplate();
const contentStore = useContentStore();

const formModel = ref({});
const computedFormModel = computed({
	get() {
		return formModel.value;
	},
	set(newValue) {
		formModel.value = null !== newValue ? newValue : {};
	},
});

// Refs
const loading = ref(false);
const loadingMessage = ref('');
const errorMessage = ref('');

// Computed
const optionsSchema = computed(() => {
	return {
		title: {
			type: 'text',
			title: __('Choose a title', 'zionbuilder'),
			description: __('Write a suggestive name for your element', 'zionbuilder'),
		},
	};
});

// Lifecycle hooks
onBeforeUnmount(() => {
	loadingMessage.value = '';
	errorMessage.value = '';
});

// Methods
function saveElement() {
	const { getSource } = useLibrary();
	const { element, type } = activeSaveElement.value;
	const compiledElementData =
		type === 'template' ? contentStore.getAreaContentAsJSON(window.ZnPbInitialData.page_id) : [element.toJSON()];
	const templateType = type === 'template' ? 'template' : 'block';

	const localLibrary = getSource('local_library');

	if (!localLibrary) {
		console.warn('Local library was not registered. It may be possible that a plugin is removing the default library.');
		return;
	}

	// save template
	loading.value = true;
	loadingMessage.value = '';
	errorMessage.value = '';

	localLibrary
		.createItem({
			title: formModel.value.title,
			template_type: templateType,
			template_data: compiledElementData,
		})
		.then(response => {
			loadingMessage.value = __('The template was successfully added to library', 'zionbuilder');
		})
		.catch(error => {
			if (error.response !== undefined) {
				if (typeof error.response.data === 'string') {
					errorMessage.value = error.response.data;
				} else errorMessage.value = arrayBufferToString(error.response.data);
			} else {
				// eslint-disable-next-line
						console.error(error);
				errorMessage.value = error;
			}
		})
		.finally(() => {
			loading.value = false;
			formModel.value = {};

			setTimeout(() => {
				loadingMessage.value = false;
				errorMessage.value = false;
			}, 3500);
		});
}

function decode_utf8(s: string) {
	return decodeURIComponent(escape(s));
}

function arrayBufferToString(buffer) {
	const s = String.fromCharCode.apply(null, new Uint8Array(buffer));

	return decode_utf8(s);
}

function downloadElement() {
	const { element, type } = activeSaveElement.value;
	const compiledElementData =
		type === 'template' ? contentStore.getAreaContentAsJSON(window.ZnPbInitialData.page_id) : [element.toJSON()];
	const templateType = type === 'template' ? 'template' : 'block';

	loading.value = true;
	loadingMessage.value = '';
	errorMessage.value = '';

	exportTemplate({
		title: formModel.value.title,
		template_type: templateType,
		template_data: compiledElementData,
	})
		.then(response => {
			const fileName = formModel.value.title && formModel.value.title.length > 0 ? formModel.value.title : 'export';

			const blob = new Blob([response.data], { type: 'application/zip' });
			saveAs(blob, `${fileName}.zip`);
			loadingMessage.value = '';
			hideSaveElement();
		})
		.catch(error => {
			if (typeof error.response.data === 'string') {
				errorMessage.value = error.response.data;
			} else errorMessage.value = arrayBufferToString(error.response.data);
		})
		.finally(() => {
			loading.value = false;
			formModel.value = {};
		});
}
</script>
<style lang="scss">
.znpb-modal-content-save-buttons {
	display: flex;
	padding: 0 5px;
	text-align: center;

	.znpb-button {
		position: relative;
		display: block;
		flex: 1;

		&--secondary {
			margin-right: 5px;
		}

		.znpb-admin-small-loader {
			top: 0;
		}
	}
}

.znpb-modal-save-element-wrapper {
	padding: 20px 15px;

	.znpb-options-form-wrapper {
		padding: 0;
	}
}

.znpb-modal-save-element-wrapper__message {
	padding: 0 30px;
	margin-top: 20px;
	margin-bottom: 0;
	color: var(--zb-success-color);
	font-family: var(--zb-font-stack);
	font-size: 12px;
	font-weight: 500;
	text-align: center;

	&--error {
		color: var(--zb-error-color);
	}
}
.znpb-modal-save-element {
	.znpb-modal__content {
		overflow: visible;
	}
}
.znpb-modal-save-element-wrapper__loading {
	position: relative;
	position: relative;
	top: 0;
	margin: 0 auto;
	text-align: center;
	.znpb-loader {
		margin-top: 20px;
	}
}
</style>
