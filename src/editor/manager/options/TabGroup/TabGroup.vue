<template>

	<Tabs
		tab-style="group"
		class="znpb-options__tab"
		@changed-tab="changeTab"
		:active-tab="activeTab"
	>
		<Tab
			:name="tabConfig.title"
			v-for="(tabConfig, tabId) in child_options"
			:key='tabId'
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
export default {
	name: 'TabGroup',
	props: {
		/**
		 * v-model/value
		 */
		value: {
			type: Object,
			required: false
		},
		child_options: {
			type: Object
		}
	},
	data () {
		return {
			activeTab: null
		}
	},
	computed: {
		valueModel: {
			get () {
				return typeof (this.value || {})[this.activeTab] !== 'undefined' ? (this.value || {})[this.activeTab] : {}
			},
			set (newValue) {
				// Check if we actually need to delete the option
				const newValues = {
					...this.value,
					[this.activeTab]: newValue
				}
				this.$emit('input', newValues)
			}
		}
	},

	methods: {
		changeTab (activeTab) {
			this.activeTab = activeTab
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
