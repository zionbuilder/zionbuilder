<template>
	<div class="znpb-link-optionsAttribute">
		<div class="znpb-link-optionsAttributeInput znpb-link-optionsAttributeField">
			<BaseInput
				type="text"
				:placeholder="__('Attribute key', 'zionbuilder')"
				:modelValue="attributeConfig.key"
				:spellcheck="false"
				@update:modelValue="updateValue('key', $event)"
			/>
		</div>
		<div class="znpb-link-optionsAttributeInput znpb-link-optionsAttributeField">
			<BaseInput
				type="text"
				:placeholder="__('Attribute value', 'zionbuilder')"
				:modelValue="attributeConfig.value"
				:spellcheck="false"
				@update:modelValue="updateValue('value', $event)"
			/>
		</div>
		<div class="znpb-link-optionsAttributeDelete znpb-link-optionsAttributeField">
			<Icon
				icon="delete"
				:class="{ 'znpb-link-optionsAttributeDelete--disabled': !canDelete }"
				@click="emit('delete', attributeConfig)"
			/>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';

const props = withDefaults(
	defineProps<{
		attributeConfig: {
			key: string;
			value: string;
		};
		canDelete?: boolean;
	}>(),
	{
		canDelete: true,
	},
);

const emit = defineEmits(['update-attribute', 'delete']);

function updateValue(key: string, value: string) {
	emit('update-attribute', {
		...props.attributeConfig,
		[key]: value,
	});
}
</script>

<style lang="scss">
.znpb-link-optionsAttribute {
	display: flex;
	align-items: center;
}

.znpb-link-optionsAttributeField {
	margin-bottom: 5px;
	margin-left: 5px;

	&:first-child {
		margin-left: 0;
	}
}

.znpb-link-optionsAttributeDelete {
	.znpb-editor-icon-wrapper {
		width: 40px;
		height: 40px;
		border: 2px solid var(--zb-surface-border-color);
		border-radius: 3px;
		transition: opacity 0.15s ease;
		cursor: pointer;

		&:hover {
			opacity: 0.7;
		}
	}
}

.znpb-link-optionsAttributeDelete--disabled {
	cursor: default;
	opacity: 0.5;
	pointer-events: none;

	&:hover {
		opacity: 0.5;
	}
}
</style>
