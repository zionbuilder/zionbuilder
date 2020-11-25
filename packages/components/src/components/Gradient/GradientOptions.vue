<template>
	<div class="znpb-gradient-options-wrapper">
		<InputWrapper
			:title="$translate('gradient_type')"
			class="znpb-gradient__type"
		>

			<Tabs
				tab-style="minimal"
				@changed-tab="onTabChange"
				:activeTab="computedValue.type"
			>
				<Tab name="Linear">
					<InputWrapper
						:title="$translate('gradient_angle')"
						class="znpb-gradient__angle"
					>
						<InputRange
							v-model="computedAngle"
							:min="0"
							:max="360"
							:step="1"
						>deg</InputRange>
					</InputWrapper>
				</Tab>
				<Tab name="Radial">
					<div class="znpb-radial-postion-wrapper">
						<InputWrapper
							title="Position X"
							layout="inline"
						>
							<InputNumber
								v-model="computedPositionX"
								:min="0"
								:max="100"
								:step="1"
							>
								%
							</InputNumber>
						</InputWrapper>
						<InputWrapper
							title="Position Y"
							layout="inline"
						>
							<InputNumber
								v-model="computedPositionY"
								:min="0"
								:max="100"
								:step="1"
							>
								%
							</InputNumber>
						</InputWrapper>
					</div>
				</Tab>
			</Tabs>
		</InputWrapper>

		<InputWrapper :title="$translate('gradient_bar')">
			<GradientBar
				v-model="computedValue"
				class="znpb-gradient__bar"
			/>
		</InputWrapper>
	</div>
</template>
<script>
import GradientBar from './GradientBar.vue'
import { InputWrapper, InputNumber, InputRange } from '../index.ts'
import { Tabs, Tab } from "../Tabs/";

export default {
	name: 'GradientOptions',
	components: {
		GradientBar,
		InputWrapper,
		InputNumber,
		InputRange,
		Tabs,
		Tab
	},
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			typeSwitch: [
				{
					name: 'linear',
					id: 'linear'
				},
				{
					name: 'radial',
					id: 'radial'
				}

			]
		}
	},
	computed: {
		computedValue: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},

		computedAngle: {
			get () {
				return this.computedValue.angle
			},
			set (newValue) {
				this.computedValue = {
					...this.computedValue,
					angle: newValue
				}
			}
		},
		computedPosition: {
			get () {
				return this.computedValue.position || {}
			},
			set (newValue) {
				this.computedValue = {
					...this.computedValue,
					position: newValue
				}
			}
		},
		computedPositionX: {
			get () {
				return (this.computedValue.position || {}).x || 50
			},
			set (newValue) {
				this.computedPosition = {
					...this.computedPosition,
					x: newValue
				}
			}
		},
		computedPositionY: {
			get () {
				return (this.computedValue.position || {}).y || 50
			},
			set (newValue) {
				this.computedPosition = {
					...this.computedPosition,
					y: newValue
				}
			}
		}
	},
	methods: {
		onTabChange (tabId) {
			this.computedValue = {
				...this.computedValue,
				type: tabId
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-admin__wrapper {
	.znpb-gradient-options-wrapper {
		padding: 20px 0 0;
	}
}
.znpb-gradient-options-wrapper {
	padding: 20px 20px 0 20px;

	.znpb-forms-input-wrapper {
		padding-right: 0;
		padding-left: 0;
	}
	& > .znpb-forms-input-wrapper > .znpb-forms-form__input-title {
		margin-bottom: 0;
	}

	.znpb-tabs > .znpb-tabs__content {
		background: transparent;
	}
}
</style>
