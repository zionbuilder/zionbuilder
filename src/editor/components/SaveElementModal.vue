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

<script>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';
import { saveAs } from 'file-saver';

import { useSaveTemplate } from '../composables';
import { useContentStore } from '/@/editor/store';

// Common API
const { useLibrary } = window.zb.composables;
const { exportTemplate } = window.zb.api;

export default {
	name: 'SaveElementModal',
	setup() {
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

		return {
			activeSaveElement,
			hideSaveElement,
			formModel,
			computedFormModel,
			contentStore,
		};
	},

	data() {
		return {
			loading: false,
			loadingMessage: false,
			errorMessage: '',
		};
	},

	computed: {
		optionsSchema() {
			return {
				title: {
					type: 'text',
					title: __('Choose a title', 'zionbuilder'),
					description: __('Write a suggestive name for your element', 'zionbuilder'),
				},
			};
		},
	},
	beforeUnmount() {
		this.loadingMessage = '';
		this.errorMessage = '';
	},
	methods: {
		saveElement() {
			const { getSource } = useLibrary();
			const { element, type } = this.activeSaveElement;
			const compiledElementData =
				type === 'template'
					? this.contentStore.getAreaContentAsJSON(window.ZnPbInitialData.page_id)
					: [element.toJSON()];
			const templateType = type === 'template' ? 'template' : 'block';

			const localLibrary = getSource('local_library');

			if (!localLibrary) {
				console.warn(
					'Local library was not registered. It may be possible that a plugin is removing the default library.',
				);
				return;
			}

			// save template
			this.loading = true;
			this.loadingMessage = '';
			this.errorMessage = '';

			localLibrary
				.createItem({
					title: this.formModel.title,
					template_type: templateType,
					template_data: compiledElementData,
				})
				.then(response => {
					this.loadingMessage = __('The template was successfully added to library', 'zionbuilder');
				})
				.catch(error => {
					if (error.response !== undefined) {
						if (typeof error.response.data === 'string') {
							this.errorMessage = error.response.data;
						} else this.errorMessage = this.arrayBufferToString(error.response.data);
					} else {
						// eslint-disable-next-line
						console.error(error);
						this.errorMessage = error;
					}
				})
				.finally(() => {
					this.loading = false;
					this.formModel = {};

					setTimeout(() => {
						this.loadingMessage = false;
						this.errorMessage = false;
					}, 3500);
				});
		},

		downloadElement() {
			const { element, type } = this.activeSaveElement;
			const compiledElementData =
				type === 'template'
					? this.contentStore.getAreaContentAsJSON(window.ZnPbInitialData.page_id)
					: [element.toJSON()];
			const templateType = type === 'template' ? 'template' : 'block';

			this.loading = true;
			this.loadingMessage = '';
			this.errorMessage = '';

			exportTemplate({
				title: this.formModel.title,
				template_type: templateType,
				template_data: compiledElementData,
			})
				.then(response => {
					const fileName = this.formModel.title && this.formModel.title.length > 0 ? this.formModel.title : 'export';

					var blob = new Blob([response.data], { type: 'application/zip' });
					saveAs(blob, `${fileName}.zip`);
					this.loadingMessage = '';
					this.hideSaveElement();
				})
				.catch(error => {
					if (typeof error.response.data === 'string') {
						this.errorMessage = error.response.data;
					} else this.errorMessage = this.arrayBufferToString(error.response.data);
				})
				.finally(() => {
					this.loading = false;
					this.formModel = {};
				});
		},
		decode_utf8(s) {
			let obj = JSON.parse(s);
			return decodeURIComponent(escape(obj.message));
		},
		arrayBufferToString(buffer) {
			var s = String.fromCharCode.apply(null, new Uint8Array(buffer));

			return this.decode_utf8(s);
		},
	},
};
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
