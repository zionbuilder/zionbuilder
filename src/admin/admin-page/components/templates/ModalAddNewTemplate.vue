<template>
	<ModalTemplateSaveButton
		@save-modal="$emit('save-template', localTemplate)"
		:disabled="!canAdd"
	>
		<div class="znpb-admin-title-block znpb-admin-title-block--heading">
			<h4  class="znpb-admin-modal-title-block__title">{{$translate('add_new_template')}}</h4>
			<p class="znpb-admin-modal-title-block__desc">{{$translate('create_new_modal_template')}}</p>
		</div>
		<ModalTwoColTemplate
			:title="$translate('template_type')"
			desc="Select a template"
		>
			<InputSelect
				v-model="localTemplate.template_type"
				:multiple="false"
				placeholder="Select type"
				:options="templates"
				class="znpb-admin-add-template-select"
			/>
		</ModalTwoColTemplate>
		<ModalTwoColTemplate :title="$translate('template_name')" desc="Type a name for the new template">
			<BaseInput
				v-model="localTemplate.title"
				placeholder="Enter a name for this template"
				class="znpb-admin-add-template-input"
			/>
		</ModalTwoColTemplate>

		<ModalTwoColTemplate
			:title="$translate('add_to_categ')"
			:desc="$translate('add_to_categ_desc')"
		>
			<InputSelect
				v-model="localTemplate.category"
				:options="categories"
				:addable="true"
				:filterable="true"
			/>
		</ModalTwoColTemplate>

	</ModalTemplateSaveButton>
</template>

<script>
import ModalTemplateSaveButton from '../ModalTemplateSaveButton.vue'
import { InputSelect } from '@/common/components/forms'

export default {
	name: 'ModalAddNewTemplate',
	components: {
		ModalTemplateSaveButton,
		InputSelect
	},
	props: {
		templateType: {
			type: String,
			required: false,
			default: 'templates'
		}
	},
	data: function () {
		return {
			localTemplate: {
				title: '',
				template_type: this.templateType,
				category: null
			}
		}
	},
	computed: {
		templates () {
			const templateTypes = []
			window.ZnPbAdminPageData.template_types.forEach(element => {
				templateTypes.push({
					id: element.id,
					name: element.singular_name
				})
			})

			return templateTypes
		},
		categories () {
			const categories = []

			window.ZnPbAdminPageData.template_categories.forEach(element => {
				categories.push({
					id: element.term_id,
					name: element.name
				})
			})

			return categories
		},
		canAdd () {
			const { template_type: templateType, title } = this.localTemplate
			return templateType.length > 0 && title.length > 0
		}
	}

}
</script>

<style lang="scss" >

.znpb-admin-title-block--heading {
	padding: 35px 30px;
	text-align: center;
	border-bottom: 1px solid $surface-variant;
	h4 {
		font-size: 20px;
	}
	.znpb-admin-modal-title-block__desc {
		width: 60%;
		margin: 0 auto;
		color: $font-color;
		font-size: 14px;
		line-height: 26px;
	}
}
</style>
