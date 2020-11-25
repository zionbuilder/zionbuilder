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
				<Loader v-if="loading" />
				<TemplateList
					v-else
					:templates="getFilteredTemplates"
				/>

			</Tab>
		</Tabs>

		<div class="znpb-admin-templates-actions">
			<Button
				@click="showModal=true"
				type="line"
			>
				<span class="znpb-add-element-icon"></span>
				{{$translate('add_new_template')}}
			</Button>
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
				@save-template="onAddNewTemplate"
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
import { computed, inject, ref, reactive, watchEffect } from 'vue'
import { useLocalLibrary } from '@zionbuilder/composables'

// Components
import ModalAddNewTemplate from './ModalAddNewTemplate.vue'
import TemplateList from './TemplateList.vue'

export default {
	name: 'TemplatePage',
	components: {
		ModalAddNewTemplate,
		TemplateList
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
	setup (props) {
		const {
			fetchTemplates,
			libaryItems,
			loading,
			addTemplate
		} = useLocalLibrary()

		const showModalConfirm = ref(false)
		const showModal = ref(false)
		const activeTemplate = ref(null)
		const activeFilter = ref('publish')

		const tabs = ref([
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
		])

		// Load all templates
		fetchTemplates()

		const getFilteredTemplates = computed(() => {
			return libaryItems.value.filter((template) => {
				return template.post_status === activeFilter.value && template.template_type && template.template_type === props.templateType
			})
		})

		function onAddNewTemplate(template) {
			addTemplate(template).finally(() => {
				showModal.value = false
			})
		}

		function onTabChange (tabId) {
			activeFilter.value = tabId
		}

		return {
			showModalConfirm,
			showModal,
			activeTemplate,
			activeFilter,
			tabs,
			loading,
			getFilteredTemplates,
			onAddNewTemplate,
			onTabChange
		}
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
