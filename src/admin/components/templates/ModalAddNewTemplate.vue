<template>
	<ModalTemplateSaveButton :disabled="!canAdd" @save-modal="emit('save-template', localTemplate)">
		<div class="znpb-admin-title-block znpb-admin-title-block--heading">
			<h4 class="znpb-admin-modal-title-block__title">{{ __('Shortcode', 'zionbuilder') }}</h4>
			<p class="znpb-admin-modal-title-block__desc">
				{{ __('Create a new template by choosing the template type and adding a name', 'zionbuilder') }}
			</p>
		</div>
		<ModalTwoColTemplate :title="__('Template type', 'zionbuilder')" :desc="__('Select a template', 'zionbuilder')">
			<InputSelect
				v-model="localTemplate.template_type"
				:placeholder="__('Select type', 'zionbuilder')"
				:options="templates"
				class="znpb-admin-add-template-select"
			/>
		</ModalTwoColTemplate>
		<ModalTwoColTemplate
			:title="__('Template Name', 'zionbuilder')"
			:desc="__('Type a name for the new template', 'zionbuilder')"
		>
			<BaseInput
				v-model="localTemplate.title"
				:placeholder="__('Enter a name for this template', 'zionbuilder')"
				class="znpb-admin-add-template-input"
			/>
		</ModalTwoColTemplate>
	</ModalTemplateSaveButton>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';

const props = withDefaults(
	defineProps<{
		templateType: string;
	}>(),
	{
		templateType: 'templates',
	},
);

const emit = defineEmits(['save-template']);

const localTemplate = ref({
	title: '',
	template_type: props.templateType,
});

const templates = computed(() => {
	const templateTypes: {
		id: string;
		name: string;
	}[] = [];
	window.ZnPbAdminPageData.template_types.forEach(element => {
		templateTypes.push({
			id: element.id,
			name: element.singular_name,
		});
	});

	return templateTypes;
});

const canAdd = computed(() => {
	const { template_type: templateType, title } = localTemplate.value;
	return templateType && title;
});
</script>

<style lang="scss">
.znpb-admin-title-block--heading {
	padding: 35px 30px;
	text-align: center;
	border-bottom: 1px solid var(--zb-surface-border-color);
	h4 {
		font-size: 20px;
	}
	.znpb-admin-modal-title-block__desc {
		width: 60%;
		margin: 0 auto;
		color: var(--zb-surface-text-color);
		font-size: 14px;
		line-height: 26px;
	}
}
</style>
