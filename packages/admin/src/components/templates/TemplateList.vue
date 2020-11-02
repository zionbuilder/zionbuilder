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
			:loading="getLoadingItem === template"
		/>

		<EmptyList v-if="templates.length === 0">{{$translate('no_template')}}</EmptyList>

		<Modal
			v-model:show="showModalPreview"
			:title="`${templateTitle} ${$translate('preview')}`"
			append-to="#znpb-admin"
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
import TemplateItem from './TemplateItem.vue'
import ModalTemplatePreview from './ModalTemplatePreview.vue'
import { ModalConfirm, Modal, EmptyList } from '@zb/components'
import { deleteTemplate } from '@zionbuilder/rest'

export default {
	name: 'TemplateList',
	props: ['templates', 'showInsert', 'activeItem', 'loadingItem'],
	data () {
		return {
			showModalConfirm: false,
			activeTemplate: null,
			showModalPreview: false,
			templateTitle: null,
			templatePreview: null,
			localLoadingItem: null
		}
	},
	components: {
		TemplateItem,
		ModalTemplatePreview,
		EmptyList,
		Modal,
		ModalConfirm
	},
	computed: {
		getLoadingItem () {
			return this.loadingItem ? this.loadingItem : this.localLoadingItem ? this.localLoadingItem : {}
		},
		sortedTemplates () {
			let a = [...this.templates].sort((a, b) => (a.post_modified < b.post_modified) ? 1 : -1)
			return a
		}
	},
	methods: {
		showConfirmDelete (template) {
			this.showModalConfirm = true
			this.activeTemplate = template
		},
		activateModalPreview (template) {
			this.showModalPreview = true
			this.templateTitle = template.post_title
			this.templatePreview = template.preview_url
		},
		onTemplateDelete () {
			this.localLoadingItem = this.activeTemplate

			deleteTemplate(this.activeTemplate.ID).then(() => {
				this.localLoadingItem = false
				this.showModalConfirm = false
				this.$zb.templates.remove(this.activeTemplate)
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-admin-templates-titles {
	display: flex;
	padding: 12px 0;

	h5.znpb-admin-templates-titles__heading {
		flex: 1;
		padding: 0 14px;
		margin-bottom: 0;
		color: $font-color;
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
