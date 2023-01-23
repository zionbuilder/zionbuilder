<template>
	<PageTemplate class="znpb-admin-templates-wrapper">
		<h3>{{ templateName }}</h3>
		<Tabs tab-style="minimal" @changed-tab="onTabChange">
			<Tab v-for="tab in tabs" :id="tab.id" :key="tab.id" :name="tab.title">
				<Loader v-if="localLibrary.loading" />
				<TemplateList v-else :templates="getFilteredTemplates" />
			</Tab>
		</Tabs>

		<div class="znpb-admin-templates-actions">
			<Button type="line" @click="showModal = true">
				<span class="znpb-add-element-icon"></span>
				{{ i18n.__('Add new template', 'zionbuilder') }}
			</Button>
		</div>
		<Modal
			v-model:show="showModal"
			:show-maximize="false"
			:title="i18n.__('Add new template', 'zionbuilder')"
			:width="560"
			append-to="#znpb-admin"
		>
			<ModalAddNewTemplate :template-type="templateType" @save-template="onAddNewTemplate" />
		</Modal>

		<template #right>
			<p class="znpb-admin-info-p">
				{{ i18n.__('Templates allow you to easily build a WordPress page', 'zionbuilder') }}
			</p>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, ref } from 'vue';

// Components
import ModalAddNewTemplate from './ModalAddNewTemplate.vue';
import TemplateList from './TemplateList.vue';

const props = defineProps<{
	templateType: string;
	templateName: string;
}>();

const showModal = ref(false);
const activeFilter = ref('publish');

const { getSource } = window.zb.composables.useLibrary();

const localLibrary = getSource('local_library');
localLibrary.getData();

const tabs = ref([
	{
		title: 'Published',
		id: 'publish',
	},
	{
		title: 'Drafts',
		id: 'draft',
	},
	{
		title: 'Trashed',
		id: 'trash',
	},
]);

const getFilteredTemplates = computed(() => {
	return localLibrary.items.filter(template => {
		return template.status === activeFilter.value && template.type && template.category.includes(props.templateType);
	});
});

function onAddNewTemplate(template) {
	localLibrary.createItem(template).finally(() => {
		showModal.value = false;
	});
}

function onTabChange(tabId) {
	activeFilter.value = tabId;
}
</script>
<style lang="scss">
.znpb-admin-templates-wrapper {
	.znpb-tabs__header {
		margin: 0 auto;
		&-item {
			padding: 15px 20px 30px 0;

			&--active,
			&:hover {
				color: var(--zb-primary-color);
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
