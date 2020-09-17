<template>
	<div class="znpb-input-border-radius-tabs-wrapper">
		<Tabs
			tab-style="group"
			class="znpb-input-border-radius-tabs"
		>
			<Tab
				:name="tab.name"
				v-for="(tab, index) in borderRadiusTabs"
				:key='index'
			>
				<!-- @slot title for Radius -->
				<div slot="title">
					<BaseIcon
						slot="reference"
						:icon="tab.icon"
					></BaseIcon>
				</div>
				<InputBorderRadius
					:title="tab.name"
					:value="valueModel[tab.id] || null"
					@input="onValueUpdated(tab.id, $event)"
				>
				</InputBorderRadius>
			</Tab>
		</Tabs>
	</div>
</template>
<script>
import BaseIcon from '@/common/components/BaseIcon.vue'
import InputBorderRadius from './InputBorderRadius'

export default {
	name: 'InputBorderRadiusTabs',
	props: {
		/**
		 * v-model/value for border radius
		 */
		value: {
			default () {
				return {}
			},
			type: Object,
			required: false
		}
	},
	components: {
		InputBorderRadius,
		BaseIcon
	},
	data () {
		return {
			borderRadiusTabs: {
				all: {
					name: 'all borders',
					icon: 'all-corners',
					id: 'all-borders-radius',
					description: 'All borders'
				},
				topLeft: {
					name: 'top left',
					icon: 't-l-corner',
					id: 'border-top-left-radius',
					description: 'Top Left Border'
				},
				topRight: {
					name: 'top right',
					icon: 't-r-corner',
					id: 'border-top-right-radius',
					description: 'Top Right Border'
				},
				bottomRight: {
					name: 'bottom right',
					icon: 'b-r-corner',
					id: 'border-bottom-right-radius',
					description: 'Bottom Right Border'
				},
				bottomLeft: {
					name: 'bottom left',
					icon: 't-l-corner',
					id: 'border-bottom-left-radius',
					description: 'Bottom Left Border'
				}
			}
		}
	},

	computed: {
		valueModel () {
			return this.value || {}
		}
	},
	methods: {
		onValueUpdated (position, newValue) {
			/**
			 * emits new object with new value of borders
			 */
			this.$emit('input', {
				...this.valueModel,
				[position]: newValue
			})
		}
	}
}
</script>
<style lang="scss">
.znpb-input-border-radius-tabs > .znpb-tabs__content {
	padding-top: 15px;
}
</style>
