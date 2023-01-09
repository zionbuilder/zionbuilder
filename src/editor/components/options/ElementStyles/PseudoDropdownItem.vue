<template>
	<div class="znpb-element-options__media-class-pseudo-selector" @click.stop="onSelectorSelected">
		{{ selector.name }}

		<div class="znpb-element-options__pseudo-actions">
			<Tooltip v-if="clearable" content="Delete Pseudo Selector" tag="span">
				<Icon icon="delete" @click.stop="onDeleteSelector"></Icon>
			</Tooltip>
			<ChangesBullet v-if="hasChanges" @remove-styles="emit('remove-styles', selector.id)" />
		</div>

		<ZionLabel v-if="selector.label" :text="selector.label.text" :type="selector.label.type" class="znpb-label--pro" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import ZionLabel from '/@/editor/common/Label.vue';

const props = withDefaults(
	defineProps<{
		selector: Record<string, unknown>;
		selectorsModel?: Record<string, unknown>;
		clearable?: boolean;
	}>(),
	{
		selectorsModel: () => ({}),
		clearable: false,
	},
);

const emit = defineEmits(['delete-selector', 'selector-selected', 'remove-styles']);

const selectorsModelComputed = computed(() => props.selectorsModel || {});
const hasChanges = computed(() => Object.keys(selectorsModelComputed.value[props.selector.id] || {}).length > 0);

function onDeleteSelector() {
	emit('delete-selector', props.selector);
	emit('selector-selected', null);
}

function onSelectorSelected() {
	emit('selector-selected', props.selector);
}
</script>

<style lang="scss">
.znpb-element-options__pseudo-actions {
	display: flex;
	align-items: center;
	color: var(--zb-surface-icon-color);
	font-size: 14px;

	&:hover {
		color: var(--zb-surface-text-hover-color);
	}
}

.znpb-element-options__media-class-pseudo {
	&-selector {
		display: flex;
		justify-content: space-between;
		align-items: center;
		min-width: 120px;
		cursor: pointer;

		&--active {
			color: var(--zb-surface-text-active-color);
		}
		&:hover {
			color: var(--zb-surface-text-active-color);
		}
	}
}
</style>
