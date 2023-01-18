<template>
	<Color v-model="colorModel" @open="emit('open')" @close="emit('close')">
		<template #trigger>
			<div class="znpb-style-background-color">
				<EmptyList v-if="!modelValue && !placeholder" class="znpb-input-background-image__empty" :no-margin="true">
					{{ __('Select background color', 'zionbuilder') }}
				</EmptyList>
				<ActionsOverlay v-else>
					<div class="znpb-style-background-color__holder" :style="getColorStyle"></div>
					<template v-if="modelValue" #actions>
						<div>
							<Icon :rounded="true" icon="delete" :bg-size="30" @click.stop="deleteColor" />
						</div>
					</template>
				</ActionsOverlay>
			</div>
		</template>
	</Color>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string | null;
		placeholder?: string | null;
	}>(),
	{
		modelValue: '',
		placeholder: null,
	},
);

const emit = defineEmits(['update:modelValue', 'open', 'close']);

const showColorPicker = ref(false);
const preventNextClick = ref(false);
const isDragging = ref(false);

// Computed
const colorModel = computed({
	get() {
		let computedValue = null;
		if (props.modelValue !== undefined) {
			if (typeof props.modelValue === 'string') {
				computedValue = props.modelValue;
			} else computedValue = props.modelValue.value;
		}

		return computedValue !== null ? computedValue : props.placeholder;
	},
	set(newColor) {
		emit('update:modelValue', newColor);
	},
});

const getColorStyle = computed(() => {
	return {
		'background-color': colorModel.value || props.placeholder,
	};
});

const deleteColor = () => {
	emit('update:modelValue', null);
};
</script>

<style lang="scss">
.znpb-style-background-color {
	margin-bottom: 20px;
	cursor: pointer;

	&__holder {
		display: block;
		width: 100%;
		height: 200px;
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);
		border-radius: 3px;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
