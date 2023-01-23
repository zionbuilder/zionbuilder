<template>
	<Tabs v-model:activeTab="activeTab" tab-style="group" class="znpb-options__tab">
		<Tab v-for="(tabConfig, tabId) in child_options" :id="tabId" :key="tabId" :name="tabConfig.title">
			<OptionsForm v-model="valueModel" :schema="child_options[tabId].child_options" />
		</Tab>
	</Tabs>
</template>

<script lang="ts" setup>
import { ref, computed, Ref } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: Record<string, unknown>;
		// eslint-disable-next-line @typescript-eslint/no-explicit-any, vue/prop-name-casing
		child_options: Record<string, any>;
	}>(),
	{
		modelValue: () => {
			return {};
		},
	},
);

const emit = defineEmits(['update:modelValue']);

const activeTab: Ref<string> = ref('');

const keys = Object.keys(props.child_options);
activeTab.value = keys[0];

const valueModel = computed({
	get: () => {
		return typeof (props.modelValue || {})[activeTab.value] !== 'undefined'
			? (props.modelValue || {})[activeTab.value] || {}
			: {};
	},
	set: newValue => {
		// Check if we actually need to delete the option
		const newValues = {
			...props.modelValue,
			[activeTab.value]: newValue,
		};

		if (null === newValue) {
			delete newValues[activeTab.value];
		}

		emit('update:modelValue', newValues);
	},
});
</script>
<style lang="scss">
.znpb-options__tab {
	& > .znpb-tabs__content > .znpb-tab__wrapper > .znpb-options-form-wrapper {
		padding: 0;
	}

	& > .znpb-tabs__header > .znpb-tabs__header-item {
		font-size: 11px !important;
	}
}
</style>
