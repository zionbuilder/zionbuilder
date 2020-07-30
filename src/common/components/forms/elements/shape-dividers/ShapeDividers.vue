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
		<InputWrapper :title="$translate('select_mask')">
			<ShapeDividerComponent
				:mask-position="activeMaskPosition"
				v-model="shapeValue"
				@remove-shape="deleteShape"
			/>
		</InputWrapper>
	</div>

</template>

<script>

import ShapeDividerComponent from './ShapeDividerComponent.vue'
import CustomSelector from '../custom-selector/CustomSelector'
import { InputWrapper } from '../inputWrapper'
import { mapGetters } from 'vuex'
export default {
	name: 'ShapeDividers',
	inject: ['inputWrapper', 'optionsForm'],
	components: {
		ShapeDividerComponent,
		CustomSelector,
		InputWrapper
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
				'top': {

				},
				'bottom': {

				}
			}
		}
	},
	computed: {
		computedValue () {
			return this.value || this.defaultValue
		},
		shapeValue: {
			get () {
				return this.computedValue[this.activeMaskPosition] !== undefined ? this.computedValue[this.activeMaskPosition]['shape_type'] : ''
			},
			set (newValue) {
				let clonedMasks = this.computedValue

				for (const [key, maskObject] of Object.entries(clonedMasks)) {
					if (key === this.activeMaskPosition) {
						maskObject['shape_type'] = newValue
					}
				}
				this.$emit('input', clonedMasks)
			}
		}
	},
	methods: {
		deleteShape () {
			console.log('delete shape')
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
