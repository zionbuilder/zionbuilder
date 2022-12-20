<template>
	<div>
		<Shape
			class="znpb-active-shape-preview"
			:shape-path="modelValue"
			:class="[{ 'mask-active': modelValue }]"
			:position="position"
		>
			<EmptyList v-if="!modelValue" class="znpb-style-shape__empty" :no-margin="true">
				{{ __('Select shape divider', 'zionbuilder') }}
			</EmptyList>
			<span
				v-else
				class="znpb-active-shape-preview__action"
				@mouseover="showDelete = true"
				@mouseleave="showDelete = false"
			>
				<transition name="slide-fade" mode="out-in">
					<Icon v-if="!showDelete" key="1" icon="check" :size="10" />

					<Icon
						v-else
						key="2"
						icon="close"
						:size="10"
						@click.stop="$emit('update:modelValue', null), (showDelete = false)"
					/>
				</transition>
			</span>
		</Shape>
		<div class="znpb-shape-list znpb-fancy-scrollbar">
			<Shape
				v-for="(shape, shapeID) in masks"
				:key="shapeID"
				:shape-path="shapeID"
				:position="position"
				@click="$emit('update:modelValue', shapeID)"
			></Shape>

			<UpgradeToPro
				v-if="!isPro"
				:message_title="__('More shape dividers', 'zionbuilder')"
				:message_description="__('The shape you were looking for is not listed above ?', 'zionbuilder')"
				:info_text="__('Click here to learn more about PRO.', 'zionbuilder')"
			/>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'ShapeDividerComponent',
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, inject } from 'vue';
import { Icon } from '../Icon';
import Shape from './Shape.vue';
import { EmptyList } from '../EmptyList';
import { UpgradeToPro } from '../UpgradeToPro';

defineProps<{
	position?: 'top' | 'bottom';
	modelValue?: string;
}>();

defineEmits<{
	(e: 'update:modelValue', value: string | null): void;
}>();

const showDelete = ref(false);

const masks = inject('masks') as Record<string, { path: string; url: string }>;

const isPro = window.ZBCommonData.is_pro_active;
</script>

<style lang="scss">
.znpb-shape-list {
	display: flex;
	flex-direction: column;
	max-height: 400px;
	padding: 20px;
	margin: 0 -20px;
	background-color: var(--zb-surface-light-color);
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-to,
.slide-fade-leave-from {
	transition: all 0.1s;
}

.slide-fade-enter-to,
.slide-fade-leave-to {
	opacity: 0;
}
</style>
