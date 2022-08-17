<template>
	<div class="znpb-custom-selector">
		<ul class="znpb-custom-selector__list-wrapper">
			<li
				v-for="(option, index) in options"
				:key="index"
				class="znpb-custom-selector__item"
				:title="option.icon ? option.name : ''"
				:class="{
					['znpb-custom-selector__item--activePlaceholder']: !modelValue && placeholder === option.id,
					['znpb-custom-selector__item--active']: modelValue === option.id,
					[`znpb-custom-selector__columns-${columns}`]: columns,
				}"
				@click="changeValue(option.id)"
			>
				<span v-if="!option.icon" class="znpb-custom-selector__item-name">
					{{ option.name }}
				</span>
				<Icon v-if="!textIcon && option.icon" :icon="option.icon" />
				<div v-if="textIcon" class="znpb-custom-selector__icon-text-content">
					<Icon v-if="option.icon" :icon="option.icon" />
					<span v-if="option.name" class="znpb-custom-selector__item-name">
						{{ option.name }}
					</span>
				</div>
			</li>
		</ul>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputCustomSelector',
};
</script>

<script lang="ts" setup>
import { Icon } from '../Icon';

type SelectValue = string | number | boolean | null;

const props = defineProps<{
	options: { id: string; name: string; icon?: string }[];
	columns?: 1 | 2 | 3 | 4;
	modelValue?: SelectValue;
	textIcon?: boolean;
	placeholder?: SelectValue;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: SelectValue): void;
}>();

function changeValue(newValue: SelectValue) {
	let valueToSend = newValue;
	// If the same value was selected, we need to delete it
	if (props.modelValue === newValue) {
		valueToSend = null;
	}

	emit('update:modelValue', valueToSend);
}
</script>

<style lang="scss">
.znpb-custom-selector {
	overflow: hidden;
	padding: 3px;
	background-color: var(--zb-surface-lighter-color);
	border-radius: 3px;

	&__list-wrapper {
		display: flex;
		flex-wrap: wrap;
		margin: 0;
	}

	&__item {
		display: flex;
		justify-content: center;
		align-items: center;
		flex: 1 1 auto;
		color: var(--zb-surface-text-color);
		padding: 10px 5px;
		margin: 0;
		font-size: 13px;
		font-weight: 500;
		border-radius: 2px;
		cursor: pointer;

		&:hover,
		&--activePlaceholder {
			color: var(--zb-surface-text-active-color);
			background-color: var(--zb-surface-lightest-color);
		}

		&--active {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);
			&:hover {
				color: var(--zb-secondary-text-color);
				background-color: var(--zb-secondary-color);
			}
		}
	}

	&__columns-1 {
		width: 100%;
	}
	&__columns-2 {
		width: 50%;
	}
	&__columns-3 {
		flex-basis: 33%;
		width: calc(100% / 3);
	}
	&__columns-4 {
		width: 25%;
	}
	&__icon-text-content {
		display: flex;
		flex-direction: column;
	}
}
</style>
