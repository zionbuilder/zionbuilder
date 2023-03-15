<template>
	<div class="znpb-link-optionsAttribute">
		<OptionsForm
			v-model="computedModel"
			class="znpb-link--optionsForm"
			:schema="schema"
			:enable-dynamic-data="true"
			:no-space="true"
		/>
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
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';

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

const computedModel = computed({
	get() {
		return props.attributeConfig;
	},
	set(newValue) {
		emit('update-attribute', newValue);
	},
});

const schema = {
	key: {
		type: 'text',
		placeholder: i18n.__('Attribute key', 'zionbuilder'),
		width: 50,
		id: 'key',
	},
	value: {
		type: 'text',
		placeholder: i18n.__('Attribute value', 'zionbuilder'),
		width: 50,
		id: 'value',
	},
};
</script>

<style lang="scss">
.znpb-link-optionsAttribute {
	display: flex;
	align-items: center;
}

.znpb-link-optionsAttributeField {
	margin-bottom: 20px;
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
