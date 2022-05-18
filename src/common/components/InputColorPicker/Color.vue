<template>
	<Tooltip
		ref="popper"
		tooltip-class="hg-popper--no-padding"
		trigger="click"
		:close-on-outside-click="true"
		append-to="body"
		:modifiers="[
			{
				name: 'preventOverflow',
				options: {
					rootBoundary: 'viewport',
				},
			},
			{
				name: 'offset',
				options: {
					offset: [0, 15],
				},
			},
			{
				name: 'flip',
				options: {
					fallbackPlacements: ['left', 'right', 'bottom', 'top'],
				},
			},
		]"
		strategy="fixed"
		@show="openColorPicker"
		@hide="closeColorPicker"
	>
		<template #content>
			<ColorPicker
				ref="colorpickerHolder"
				:model="modelValue"
				@color-changed="updateColor"
				@click.stop="onColorPickerClick"
				@mousedown.stop="onColorPickerMousedown"
			>
				<template #end>
					<PatternContainer
						v-if="showLibrary"
						:model="modelValue"
						:active-tab="dynamicContentConfig ? 'global' : 'local'"
						@color-updated="onLibraryUpdate"
					/>
				</template>
			</ColorPicker>
		</template>

		<slot name="trigger" />
	</Tooltip>
</template>

<script lang="ts">
export default {
	name: 'Color',
};
</script>

<script lang="ts" setup>
import { ref, onBeforeUnmount } from 'vue';
import PatternContainer from './PatternContainer.vue';
import { Tooltip } from '../tooltip';
import { ColorPicker } from '../Colorpicker';

withDefaults(
	defineProps<{
		modelValue?: string;
		appendTo?: string;
		showLibrary?: boolean;
		dynamicContentConfig?: any;
	}>(),
	{
		modelValue: '',
		showLibrary: true,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
	(e: 'option-updated', value: string): void;
	(e: 'open'): void;
	(e: 'close'): void;
}>();

const popper = ref<InstanceType<typeof Tooltip> | null>(null);
const colorpickerHolder = ref<InstanceType<typeof ColorPicker> | null>(null);
const isDragging = ref(false);

let backdrop: HTMLDivElement;

function onLibraryUpdate(newValue: string) {
	emit('update:modelValue', newValue);
}

function onColorPickerClick() {
	isDragging.value = false;
}

function onColorPickerMousedown() {
	isDragging.value = true;
}

function updateColor(color: string) {
	/**
	 * emits new color when color changed
	 */
	emit('option-updated', color);
	/**
	 * emits new color when inputcolor changed
	 */
	emit('update:modelValue', color);
}

function openColorPicker() {
	emit('open');
	document.addEventListener('click', closePanelOnOutsideClick, true);

	if (popper.value) {
		backdrop = document.createElement('div');
		backdrop.classList.add('znpb-tooltip-backdrop');
		const parent = popper.value.$el.parentNode;
		parent.insertBefore(backdrop, popper.value.$el);
	}
}

function closeColorPicker() {
	emit('close');
	document.removeEventListener('click', closePanelOnOutsideClick);

	if (backdrop) {
		document.body.appendChild(backdrop);
		backdrop.parentNode?.removeChild(backdrop);
	}
}

function closePanelOnOutsideClick(event: MouseEvent) {
	if (popper.value?.$el.contains(event.target) || colorpickerHolder.value?.$refs.colorPicker.contains(event.target)) {
		return;
	}

	if (!isDragging.value && popper.value) {
		popper.value.hidePopper();
	}
	isDragging.value = false;
}

onBeforeUnmount(() => {
	document.removeEventListener('click', closePanelOnOutsideClick);

	if (backdrop) {
		document.body.appendChild(backdrop);
		backdrop.parentNode?.removeChild(backdrop);
	}
});
</script>

<style lang="scss">
.znpb-tooltip-backdrop {
	width: 100%;
	height: 100%;
	position: fixed;
	top: 0;
	left: 0;
}
</style>
