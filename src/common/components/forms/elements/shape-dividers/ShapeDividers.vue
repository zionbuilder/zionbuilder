<template>

	<div>
		<InputWrapper :title="$translate('mask_position')">
			<CustomSelector
				v-model="activeMaskPosition"
				:options="maskPosOptions"
				:columns="2"
			>

			</CustomSelector>
		</InputWrapper>

		<ShapeConfig
			:config="shapeConfigValue"
			@input="onShapeConfigUpdate($event)"
			:position="activeMaskPosition"
		/>
	</div>

</template>

<script>
import ShapeConfig from './ShapeConfig.vue'
import CustomSelector from '../custom-selector/CustomSelector'
import { InputWrapper } from '../inputWrapper'
import { mapGetters } from 'vuex'
export default {
	name: 'ShapeDividers',
	inject: ['inputWrapper'],
	components: {
		CustomSelector,
		InputWrapper,
		ShapeConfig
	},
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: Object
		}
	},
	data () {
		return {
			maskPosOptions: [
				{
					id: 'top',
					name: 'Top masks'
				},
				{
					id: 'bottom',
					name: 'Bottom masks'
				}
			],

			activeMaskPosition: 'top',
			defaultValue: {
				'top': {},
				'bottom': {}
			}
		}
	},
	computed: {
		computedValue () {
			return this.value || this.defaultValue
		},
		shapeConfigValue () {
			return this.computedValue[this.activeMaskPosition] !== undefined ? this.computedValue[this.activeMaskPosition] : {}
		}
	},
	methods: {
		onShapeConfigUpdate (newValue) {
			let clonedMasks = { ...this.computedValue }
			for (const [key, maskObject] of Object.entries(clonedMasks)) {
				if (key === this.activeMaskPosition) {
					Object.assign(clonedMasks[key], newValue)
				}
			}
			this.$emit('input', clonedMasks)
		}
	}
}
</script>
<style lang="scss">
.znpb-shape-list {
	display: flex;
	flex-direction: column;
	max-height: 400px;
	padding: 20px;
	margin: 0 -20px;
	background-color: #f1f1f1;
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-active, .slide-fade-leave-active {
	transition: all .1s;
}

.slide-fade-enter, .slide-fade-leave-to {
	opacity: 0;
}
</style>
