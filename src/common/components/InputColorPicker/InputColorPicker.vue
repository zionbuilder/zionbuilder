<template>
	<div class="znpb-form-colorpicker">
		<Color
			v-if="type === 'simple'"
			ref="color"
			v-model="colorModel"
			:show-library="showLibrary"
			:placeholder="placeholder"
			class="znpb-colorpicker-circle znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity"
			@open="$emit('open')"
			@close="$emit('close')"
		>
			<template #trigger>
				<span
					:style="{ backgroundColor: modelValue }"
					class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
				></span>

				<Icon
					v-if="dynamicContentConfig"
					icon="globe"
					:rounded="true"
					bg-color="#fff"
					:bg-size="16"
					:size="12"
					class="znpb-colorpicker-circle__global-icon"
				/>

				<span
					v-if="!modelValue"
					v-znpb-tooltip="__('No color chosen', 'zionbuilder')"
					class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle znpb-colorpicker-circle--no-color"
				></span>
			</template>
		</Color>

		<BaseInput v-else v-model="colorModel" :placeholder="computedPlaceholder">
			<template #prepend>
				<Color
					v-model="colorModel"
					:show-library="showLibrary"
					class="znpb-colorpicker-circle znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity"
					:placeholder="placeholder"
					@open="$emit('open')"
					@close="$emit('close')"
				>
					<template #trigger>
						<span>
							<Tooltip
								v-if="!modelValue || modelValue === undefined"
								:content="__('No color chosen', 'zionbuilder')"
								tag="span"
							>
								<span
									:style="{ backgroundColor: modelValue || placeholder }"
									class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
								></span>
							</Tooltip>
							<span
								v-else
								:style="{ backgroundColor: modelValue || placeholder }"
								class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
							></span>

							<Icon
								v-if="dynamicContentConfig"
								icon="globe"
								:rounded="true"
								bg-color="#fff"
								:bg-size="16"
								:size="12"
								class="znpb-colorpicker-circle__global-icon"
							/>
						</span>
					</template>
				</Color>
			</template>
		</BaseInput>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputColorPicker',
	inheritAttrs: true,
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';
import Color from './Color.vue';
import { Tooltip } from '../tooltip';
import { Icon } from '../Icon';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		type?: null;
		dynamicContentConfig?: object;
		showLibrary?: boolean;
		placeholder?: string | null;
	}>(),
	{
		showLibrary: true,
		placeholder: null,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
	(e: 'open'): void;
	(e: 'close'): void;
}>();

const color = ref<InstanceType<typeof Color> | null>(null);

const computedPlaceholder = computed(() => {
	return props.placeholder || __('Color', 'zionbuilder');
});

const colorModel = computed({
	get() {
		return props.modelValue || '';
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});
</script>
<style lang="scss">
.znpb-form-colorpicker {
	position: relative;
	display: flex;
	.zion-input__prepend {
		padding: 0 0 0 10px;
	}
	&__color-trigger {
		position: absolute;
		top: 0;
		left: 0;
		cursor: pointer;
	}
	.znpb-colorpicker-circle {
		// box-shadow: 0 0 0 2px var(--zb-surface-border-color);
		&--no-color {
			background: var(--zb-surface-color);
		}
		&__global-icon {
			position: absolute;
			top: -2px;
			right: 0;
			color: var(--zb-surface-icon-color);
		}
	}
}
span.znpb-form-colorpicker__color-trigger:not(.znpb-colorpicker-circle--no-color) {
	display: block;
}

.znpb-input-wrapper--inline.znpb-input-type--colorpicker > .znpb-input-content {
	display: flex;
	justify-content: flex-end;
}
</style>
