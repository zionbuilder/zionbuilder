<template>
	<div class="znpb-input-border-tabs-wrapper">
		<Tabs tab-style="group" class="znpb-input-border-tabs">
			<Tab v-for="tab in positions" :key="tab.id" :name="tab.name" class="znpb-input-border-tabs__tab">
				<template #title>
					<div>
						<Icon :icon="tab.icon"></Icon>
					</div>
				</template>

				<InputBorderControl
					:modelValue="computedValue[tab.id] || {}"
					:placeholder="placeholder ? placeholder[tab.id] : null"
					@update:modelValue="onValueUpdated(tab.id as PositionIds, $event)"
				></InputBorderControl>
			</Tab>
		</Tabs>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputBorderTabs',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { cloneDeep, set, unset } from 'lodash-es';
import { Icon } from '../Icon';
import InputBorderControl, { BorderValue } from './InputBorderControl.vue';

type PositionIds = 'all' | 'top' | 'right' | 'bottom' | 'left';
type ModelValue = Partial<Record<PositionIds, BorderValue>>;

const props = defineProps<{
	modelValue?: ModelValue;
	placeholder?: ModelValue;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: ModelValue | null): void;
}>();

const positions: {
	name: string;
	icon: string;
	id: PositionIds;
}[] = [
	{
		name: 'all',
		icon: 'all-sides',
		id: 'all',
	},
	{
		name: 'Top',
		icon: 'border-top',
		id: 'top',
	},
	{
		name: 'right',
		icon: 'border-right',
		id: 'right',
	},
	{
		name: 'bottom',
		icon: 'border-bottom',
		id: 'bottom',
	},
	{
		name: 'left',
		icon: 'border-left',
		id: 'left',
	},
];

const computedValue = computed(() => props.modelValue || {});

function onValueUpdated(position: PositionIds, newValue: BorderValue) {
	const clonedValue: ModelValue = cloneDeep(props.modelValue || {});
	if (newValue === null) {
		unset(clonedValue, position);
	} else {
		set(clonedValue, position, newValue);
	}

	if (Object.keys(clonedValue).length > 0) {
		emit('update:modelValue', clonedValue);
	} else {
		emit('update:modelValue', null);
	}
}
</script>

<style lang="scss">
.znpb-input-border-tabs-wrapper {
	.znpb-options-form-wrapper {
		padding: 0;
		margin: 0 -5px;
	}
}

.znpb-input-border-tabs__tab {
	overflow: visible !important;
}
</style>
