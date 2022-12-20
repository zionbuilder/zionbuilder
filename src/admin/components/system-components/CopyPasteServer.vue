<template>
	<div class="znpb-system-list-wrapper">
		<h2 class="znpb-system-title">{{ __('Copy and paste info', 'zionbuilder') }}</h2>
		<h5 class="znpb-system-subtitle">
			{{ __('You can copy the below info as simple text with Ctrl+C / Ctrl+V:', 'zionbuilder') }}
		</h5>

		<BaseInput
			:modelValue="getCategoryData"
			type="textarea"
			class="znpb-system-textarea"
			readonly
			spellcheck="false"
			autocomplete="false"
		/>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed } from 'vue';

type ServerCategory = {
	category_name: string;
	values: {
		[key: string]: {
			name: string;
			value?: string;
		};
	};
};

const props = defineProps<{
	categoryData: ServerCategory[];
}>();

const getCategoryData = computed(() => {
	const result: string[] = [];

	props.categoryData.forEach((category: ServerCategory) => {
		result.push(`==${category.category_name}==\n`);

		Object.keys(category.values).forEach(function (key) {
			result.push(`\t${category.values[key].name}`);
			// statement for plugins which don't have value
			if (category.values[key].value !== undefined) {
				result.push(`: ${category.values[key].value}`);
			}
			result.push('\n');
		});
	});

	return result.join('');
});
</script>
<style lang="scss">
.znpb-system-title {
	font-size: 24px;
	font-weight: 500;
}
.znpb-system-subtitle {
	color: var(--zb-surface-text-color);
	font-size: 13px;
	font-weight: 400;
	text-transform: capitalize;
}

.znpb-system-textarea {
	min-width: 480px;
	min-height: 320px;
	margin-bottom: 30px;
	line-height: 27px;
	text-transform: capitalize;
	white-space: pre-wrap;
	border-radius: 3px;

	@media (max-width: 767px) {
		min-width: auto;
		max-width: 100%;
	}
}
</style>
