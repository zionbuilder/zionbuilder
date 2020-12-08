<template>

	<Tabs
		tab-style="group"
		class="znpb-options__tab"
		v-model:activeTab="activeTab"
	>
		<Tab
			:name="tabConfig.title"
			v-for="(tabConfig, tabId) in child_options"
			:key="tabId"
			:id="tabId"
			:ref="setItemRef"
		>
			<OptionsForm
				:schema="child_options[tabId].child_options"
				v-model="valueModel"
			/>

		</Tab>
	</Tabs>

</template>
<script>
import { ref, computed, watch } from 'vue'
export default {
	name: 'TabGroup',
	props: {
		/**
		 * v-model/value
		 */
		modelValue: {
			type: Object,
			required: false
		},
		child_options: {
			type: Object
		}
	},
	setup (props, { emit }) {
		let itemRefs = ref([])
		let activeTab = ref(null)

		const setItemRef = el => {
			itemRefs.value.push(el)
		}

		watch(itemRefs, (newValue, prevCount) => {
			activeTab.value = itemRefs.value[0].id
		})

		const valueModel = computed({
			get: () => {
				return typeof (props.modelValue || {})[activeTab.value] !== 'undefined' ? (props.modelValue || {})[activeTab.value] : {}
			},
			set: (newValue) => {
				// Check if we actually need to delete the option
				const newValues = {
					...props.modelValue,
					[activeTab.value]: newValue
				}
				emit('update:modelValue', newValues)
			}
		})
		return {
			itemRefs,
			setItemRef,
			activeTab,
			valueModel
		}
	}
}
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
