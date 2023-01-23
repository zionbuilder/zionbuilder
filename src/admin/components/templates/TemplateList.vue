<template>
	<div class="znpb-template-list__wrapper">
		<div class="znpb-admin-templates-titles">
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--title">
				{{ i18n.__('Title', 'zionbuilder') }}
			</h5>
			<h5 class="znpb-admin-templates-titles__heading">{{ i18n.__('Author', 'zionbuilder') }}</h5>
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--shortcode">
				{{ i18n.__('Shortcode', 'zionbuilder') }}
			</h5>
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--actions">
				{{ i18n.__('actions', 'zionbuilder') }}
			</h5>
		</div>

		<TemplateItem
			v-for="(template, index) in sortedTemplates"
			ref="singleTemplate"
			:key="index"
			:template="template"
			:active="activeItem === template.ID"
			:show-insert="showInsert"
			@delete-template="showConfirmDelete"
			@show-modal-preview="activateModalPreview(template)"
			@insert="emit('insert', $event)"
		/>

		<EmptyList v-if="templates.length === 0">{{ i18n.__('No template', 'zionbuilder') }}</EmptyList>

		<Modal
			v-model:show="showModalPreview"
			:title="`${templateTitle} ${i18n.__('preview', 'zionbuilder')}`"
			append-to="body"
			class="znpb-admin-preview-template-modal"
		>
			<ModalTemplatePreview :frame-url="templatePreview" />
		</Modal>
		<ModalConfirm
			v-if="showModalConfirm"
			:width="530"
			:confirm-text="i18n.__('Yes, delete template', 'zionbuilder')"
			:cancel-text="i18n.__('No, keep template', 'zionbuilder')"
			@confirm="onTemplateDelete"
			@cancel="showModalConfirm = false"
		>
			{{ i18n.__('Are you sure you want to delete this template?', 'zionbuilder') }}
		</ModalConfirm>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed } from 'vue';

// Components
import TemplateItem from './TemplateItem.vue';
import ModalTemplatePreview from './ModalTemplatePreview.vue';

const props = withDefaults(
	defineProps<{
		templates: [];
		showInsert?: boolean;
		activeItem?: number;
		loadingItem?: boolean;
	}>(),
	{
		templates: () => [],
		showInsert: false,
		activeItem: 0,
		loadingItem: false,
	},
);

const emit = defineEmits(['insert']);

const showModalConfirm = ref(false);
const activeTemplate = ref(null);
const showModalPreview = ref(false);
const templateTitle = ref(null);
const templatePreview = ref('');

// Computed
const sortedTemplates = computed(() => [...props.templates].sort((a, b) => (a.date < b.date ? 1 : -1)));

// Methods
function showConfirmDelete(template) {
	showModalConfirm.value = true;
	activeTemplate.value = template;
}

function activateModalPreview(template) {
	showModalPreview.value = true;
	templateTitle.value = template.name;
	templatePreview.value = template.urls.preview_url;
}

function onTemplateDelete() {
	showModalConfirm.value = false;
	activeTemplate.value.delete();
}
</script>

<style lang="scss">
.znpb-admin-preview-template-modal {
	& > .znpb-modal__wrapper--full-size {
		width: 100%;
		height: 90%;
	}

	& > .znpb-modal__wrapper {
		width: calc(100% - 40px);
	}
}

.znpb-admin-templates-titles {
	display: flex;
	padding: 12px 0;

	@media (max-width: 767px) {
		display: none;
	}

	h5.znpb-admin-templates-titles__heading {
		flex: 1;
		padding: 0 14px;
		margin-bottom: 0;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.5px;
		text-transform: uppercase;

		&--title {
			flex-grow: 2;
			padding: 0 20px;
		}

		&--actions {
			padding: 0 20px;
			text-align: right;
		}

		&--shortcode {
			flex-basis: 160px;
		}
	}
}
</style>
