<template>
	<div class="znpb-input-border-tabs-wrapper">
		<Tabs
			tab-style="group"
			class="znpb-input-border-tabs"
		>
			<Tab
				:name="tab.name"
				v-for="(tab, index) in positions"
				:key='index'
				class="znpb-input-border-tabs__tab"
			>
				<template v-slot:title>
					<div>
						<Icon :icon="tab.icon"></Icon>
					</div>
				</template>

				<InputBorderControl
					:modelValue="computedValue[tab.id] || {}"
					@update:modelValue="onValueUpdated(tab.id, $event)"
				></InputBorderControl>
			</Tab>
		</Tabs>
	</div>
</template>
<script>
import { cloneDeep, set, unset } from 'lodash-es'

import { Icon } from '../Icon'
import InputBorderControl from './InputBorderControl.vue'

export default {
	name: 'InputBorderTabs',
	props: {
		/**
		 * v-model/modelValue for borders
		 */
		modelValue: {
			default () {
				return {}
			},
			type: Object,
			required: false
		}
	},
	components: {
		InputBorderControl,
		Icon
	},
	data () {
		return {
			positions: {
				all: {
					name: 'all',
					icon: 'all-sides',
					id: 'all'
				},
				top: {
					name: 'Top',
					icon: 'border-top',
					id: 'top'
				},
				right: {
					name: 'right',
					icon: 'border-right',
					id: 'right'
				},
				bottom: {
					name: 'bottom',
					icon: 'border-bottom',
					id: 'bottom'
				},
				left: {
					name: 'left',
					icon: 'border-left',
					id: 'left'
				}
			}
		}
	},
	computed: {
		computedValue () {
			return this.modelValue || {}
		}
	},
	methods: {
		onValueUpdated (position, newValue) {
			const clonedValue = cloneDeep(this.modelValue)
			if (null === newValue) {
				unset(clonedValue, position)
			} else {
				set(clonedValue, position, newValue)
			}

			if (Object.keys(clonedValue).length > 0) {
				this.$emit('update:modelValue', clonedValue)
			} else {
				this.$emit('update:modelValue', null)
			}
		}
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
