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
		>
			<OptionsForm
				:schema="child_options[tabId].child_options"
				v-model="valueModel"
			/>

		</Tab>
	</Tabs>

</template>
<script>
import { ref, computed } from 'vue'
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

		let activeTab = ref(null)

		const keys = Object.keys(props.child_options)
		activeTab.value = keys[0]

		const valueModel = computed({
			get: () => {
				return typeof (props.modelValue || {})[activeTab.value] !== 'undefined' ? (props.modelValue || {})[activeTab.value] || {} : {}
			},
			set: (newValue) => {
				// Check if we actually need to delete the option
				const newValues = {
					...props.modelValue,
					[activeTab.value]: newValue
				}

				if (null === newValue) {
					delete newValues[activeTab.value]
				}

				emit('update:modelValue', newValues)
			}
		})

		return {
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
