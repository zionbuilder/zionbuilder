<template>
	<div class="znpb-template-list__wrapper">
		<div class="znpb-admin-templates-titles">
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--title">{{$translate('title')}}</h5>
			<h5 class="znpb-admin-templates-titles__heading">{{$translate('author')}}</h5>
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--shortcode">{{$translate('shortcode')}}</h5>
			<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--actions">{{$translate('actions')}}</h5>
		</div>

		<TemplateItem
			ref="singleTemplate"
			v-for="(template, index) in sortedTemplates"
			:key="index"
			:template="template"
			@delete-template="showConfirmDelete"
			@show-modal-preview="activateModalPreview(template)"
			@insert="$emit('insert', $event)"
			:show-insert="showInsert"
			:active="activeItem === template.ID"
		/>

		<EmptyList v-if="templates.length === 0">{{$translate('no_template')}}</EmptyList>

		<Modal
			v-model:show="showModalPreview"
			:title="`${templateTitle} ${$translate('preview')}`"
			append-to="body"
			class="znpb-admin-preview-template-modal"
		>
			<ModalTemplatePreview :frameUrl="templatePreview" />
		</Modal>
		<ModalConfirm
			v-if="showModalConfirm"
			:width="530"
			:confirm-text="$translate('template_delete_confirm')"
			:cancel-text="$translate('template_delete_cancel')"
			@confirm="onTemplateDelete"
			@cancel="showModalConfirm = false"
		>
			{{$translate('are_you_sure_template_delete')}}
		</ModalConfirm>
	</div>
</template>

<script>
import { ref, computed } from 'vue'

// Components
import TemplateItem from './TemplateItem.vue'
import ModalTemplatePreview from './ModalTemplatePreview.vue'
import { useLocalLibrary } from '@zionbuilder/composables'

export default {
	name: 'TemplateList',
	components: {
		TemplateItem,
		ModalTemplatePreview
	},
	props: ['templates', 'showInsert', 'activeItem', 'loadingItem'],
	setup (props) {
		const showModalConfirm = ref(false)
		const activeTemplate = ref(null)
		const showModalPreview = ref(false)
		const templateTitle = ref(null)
		const templatePreview = ref(null)

		// Computed
		const sortedTemplates = computed(() => [...props.templates].sort((a, b) => (a.date < b.date) ? 1 : -1))

		// Methods
		function showConfirmDelete (template) {
			showModalConfirm.value = true
			activeTemplate.value = template
		}

		function activateModalPreview (template) {
			showModalPreview.value = true
			templateTitle.value = template.post_title
			templatePreview.value = template.preview_url
		}

		function onTemplateDelete () {
			showModalConfirm.value = false
			activeTemplate.value.delete()
		}

		return {
			// Data
			showModalPreview,
			templateTitle,
			templatePreview,
			showModalConfirm,

			// Computed
			sortedTemplates,

			// methods
			showConfirmDelete,
			activateModalPreview,
			onTemplateDelete
		}
	}
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
		letter-spacing: .5px;
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
