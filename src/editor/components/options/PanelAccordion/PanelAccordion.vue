<template>
	<div class="znpb-panel-accordion">
		<div class="znpb-panel-accordion__header" @click="toggle">
			<div class="znpb-panel-accordion__header-title">
				{{ title }}
				<ChangesBullet
					v-if="hasChanges"
					:content="i18n.__('Discard changes', 'zionbuilder')"
					@remove-styles="emit('discard-changes')"
				/>
			</div>

			<Icon class="znpb-option-group-selector__clone-icon" :icon="expanded ? 'minus' : 'plus'"></Icon>
		</div>
		<OptionsForm
			v-if="child_options && expanded"
			ref="accordionOption"
			v-model="valueModel"
			class="znpb-option__type-option-accordion"
			:schema="child_options"
		/>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';

import { ref, computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue: Record<string, any>;
		child_options: Record<string, any>;
		title?: string;
		collapsed?: boolean;
		hasChanges?: boolean;
	}>(),
	{
		collapsed: false,
		hasChanges: false,
		title: '',
	},
);

const emit = defineEmits(['update:modelValue', 'discard-changes']);

const expanded = ref(!props.collapsed);

const valueModel = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

function toggle() {
	expanded.value = !expanded.value;
}
</script>

<style lang="scss">
.znpb-panel-accordion__header {
	top: 0;
	display: flex;
	justify-content: space-between;
	width: calc(100% + 40px);
	padding: 9px 20px;
	margin: 0 -20px 10px -20px;
	background-color: var(--zb-surface-lighter-color);
	cursor: pointer;
}

.znpb-panel-accordion {
	position: relative;
	width: 100%;
	.znpb-panel-accordion__header-title {
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		display: flex;
		align-items: center;
	}
}

.znpb-options-form-wrapper.znpb-option__type-option-accordion {
	padding: 10px 0 0;
	transition: all 0.3s ease-in-out;
	.znpb-input-type--dimensions {
		padding-bottom: 10px;
	}
}
</style>
