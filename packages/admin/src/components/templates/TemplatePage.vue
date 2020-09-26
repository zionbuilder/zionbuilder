<template>
	<PageTemplate class="znpb-admin-templates-wrapper">
		<h3>{{templateName}}</h3>
		<Tabs
			@changed-tab="onTabChange"
			tab-style="minimal"
		>
			<Tab
				v-for="tab in tabs"
				:key="tab.id"
				:id="tab.id"
				:name="tab.title"
			>
				<TemplateList
					:templates="getFilteredTemplates"
				/>

			</Tab>
		</Tabs>

		<div class="znpb-admin-templates-actions">
			<BaseButton
				@click="showModal=true"
				type="line"
			>
				<span class="znpb-add-element-icon"></span>
				{{$translate('add_new_template')}}
			</BaseButton>
		</div>

		<Modal
			v-model:show="showModal"
			:show-maximize="false"
			:title="$translate('add_new_template')"
			:width="560"
			append-to="#znpb-admin"
		>
			<ModalAddNewTemplate
				:template-type="templateType"
				@save-template="addTemplate($event), showModal=false"
			/>
		</Modal>

		<template v-slot:right>
			<p class="znpb-admin-info-p">
				{{$translate('templates_description')}}
			</p>
		</template>
	</PageTemplate>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'

import ModalAddNewTemplate from './ModalAddNewTemplate.vue'
import TemplateList from './TemplateList.vue'
import { BaseButton, Modal, Tabs, Tab } from '@zb/components'

export default {
	name: 'TemplatePage',
	components: {
		Modal,
		ModalAddNewTemplate,
		TemplateList,
		BaseButton,
		Tabs,
		Tab
	},
	props: {
		templateType: {
			type: String,
			required: true,
			default: 'template'
		},
		templateName: {
			type: String,
			required: true,
			default: 'Template'
		}
	},
	data () {
		return {
			showModalConfirm: false,
			showModal: false,
			activeTemplate: null,
			templatePreview: '',
			activeFilter: 'publish',
			tabs: [
				{
					title: 'Published',
					id: 'publish'
				},
				{
					title: 'Drafts',
					id: 'draft'
				},
				{
					title: 'Trashed',
					id: 'trash'
				}
			]
		}
	},
	computed: {
		...mapGetters(['getTemplates']),
		getFilteredTemplates () {
			return this.getTemplates.filter((template) => {
				return template.post_status === this.activeFilter && template.template_type && template.template_type === this.templateType
			})
		}
	},
	methods: {
		...mapActions([
			'fetchTemplates',
			'addTemplate'
		]),
		onTabChange (tabId) {
			this.activeFilter = tabId
		}
	},
	created () {
		this.fetchTemplates()
	}
}
</script>
<style lang="scss" >
.znpb-admin-templates-wrapper {
	.znpb-tabs__header {
		margin: 0 auto;
		&-item {
			padding: 15px 20px 30px 0;

			&--active, &:hover {
				color: $primary-color;
			}
		}
	}
}

.znpb-admin-templates-actions {
	.znpb-button {
		float: right;
		padding: 13.5px 20px;
		margin-top: 10px;
		margin-bottom: 30px;
	}
	.znpb-add-element-icon {
		display: inline-block;
		width: 8px;
		height: 8px;
		margin-right: 5px;
		&:before {
			height: 8px;
		}
		&:after {
			width: 8px;
		}
	}
}
</style>
