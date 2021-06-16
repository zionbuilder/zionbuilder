<template>
	<div class="znpb-link-optionsAttribute">
		<div class="znpb-link-optionsAttributeInput znpb-link-optionsAttributeField">
			<BaseInput
				type="text"
				:placeholder="$translate('attribute_key')"
				:modelValue="attributeConfig.key"
				@update:modelValue="updateValue('key', $event)"
			/>
		</div>
		<div class="znpb-link-optionsAttributeInput znpb-link-optionsAttributeField">
			<BaseInput
				type="text"
				:placeholder="$translate('attribute_value')"
				:modelValue="attributeConfig.value"
				@update:modelValue="updateValue('value', $event)"
			/>
		</div>
		<div class="znpb-link-optionsAttributeDelete znpb-link-optionsAttributeField">
			<Icon
				icon="delete"
				@click="$emit('delete', attributeConfig)"
			/>
		</div>
	</div>
</template>

<script>
import { BaseInput } from '@zb/components'

export default {
	name: 'LinkAttributeForm',
	components: {
		BaseInput
	},
	props: {
		attributeConfig: {
			type: Object
		}
	},
	setup (props, { emit }) {
		function updateValue (key, value) {
			emit('update-attribute', {
				...props.attributeConfig,
				[key]: value
			})
		}

		return {
			updateValue
		}
	}
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

.znpb-link-optionsAttribute {
	margin-bottom: 10px;
}
</style>