<template>
	<Modal
		v-if="activeSaveElement.type"
		:title="$translate('save_to_library')"
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
					<span>{{ $translate('save') }}</span>
				</Button>
				<Button class="znpb-button--line" @click="downloadElement">
					{{ $translate('download') }}
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
import { ref, computed } from 'vue';
import { saveAs } from 'file-saver';

import { useElements, useTemplateParts, useSaveTemplate } from '../composables';
import { useLibrary } from '@common/composables';
import { exportTemplate } from '@common/api';

export default {
	name: 'SaveElementModal',
	setup() {
		const { activeSaveElement, hideSaveElement } = useSaveTemplate();
		const { getElement } = useElements();
		const { getActivePostTemplatePart } = useTemplateParts();

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
			getElement,
			getActivePostTemplatePart,
			activeSaveElement,
			hideSaveElement,
			formModel,
			computedFormModel,
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
					title: this.$translate('choose_title'),
					description: this.$translate('save_title_desc'),
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
			const compiledElementData = type === 'template' ? this.getActivePostTemplatePart().toJSON() : [element.toJSON()];
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
					this.loadingMessage = this.$translate('template_was_added');
				})
				.catch(error => {
					if (error.response !== undefined) {
						if (typeof error.response.data === 'string') {
							this.errorMessage = error.response.data;
						} else this.errorMessage = this.arrayBufferToString(error.response.data);
					} else {
						// eslint-disable-next-line
						console.error(error)
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
			const compiledElementData = type === 'template' ? this.getActivePostTemplatePart().toJSON() : element.toJSON();
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
