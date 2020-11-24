<template>
	<div class="znpb-gradient-wrapper">

		<GradientBoard
			v-if="!saveToLibrary"
			:config="computedValue"
			:activegrad="activeGradient"
			@change-active-gradient="changeActive($event)"
			@position-changed="changePosition($event)"
		/>

		<ActionsOverlay v-else>
			<GradientBoard
				:config="computedValue"
				:activegrad="activeGradient"
				@change-active-gradient="changeActive($event)"
				@position-changed="changePosition($event)"
			/>

			<template v-slot:actions>
				<span
					v-if="!showPresetInput"
					class="znpb-gradient__show-preset"
					@click="showPresetInput=true"
				>{{$translate('save_to_library')}}</span>

				<PresetInput
					v-else
					@save-preset="addGlobalPattern"
					@cancel="showPresetInput=false"
				/>

				<Icon
					v-if="!showPresetInput"
					icon="delete"
					:bg-size="30"
					bg-color="#fff"
					@click.stop="deleteGradientValue"
					class="znpb-gradient-wrapper__delete-gradient"
				/>
			</template>
		</ActionsOverlay>

		<div class="znpb-gradient-elements-wrapper">
			<Sortable
				class="znpb-admin-colors__container"
				v-model="computedValue"
				:handle="null"
				:drag-delay="0"
				:drag-treshold="10"
				:disabled="false"
				:revert="true"
				axis="horizontal"
			>
				<GradientElement
					class="znpb-gradient-elements__delete-button"
					v-for="(gradient, i) in computedValue"
					:key="i"
					:config="gradient"
					:show-remove="computedValue.length > 1"
					:is-active="activeGradientIndex === i"
					@change-active-gradient="changeActive(i)"
					@delete-gradient="deleteGradient(gradient)"
				/>
			</Sortable>
			<Icon
				icon="plus"
				class="znpb-colorpicker-add-grad"
				@click="addGradientConfig"
			/>
		</div>

		<GradientOptions v-model="activeGradient" />

	</div>
</template>
<script>
import { inject } from 'vue'
import { applyFilters } from '@zb/hooks'
import { getDefaultGradient } from '../../utils/'

// Components
import Icon from '../Icon/Icon.vue'
import GradientBoard from './GradientBoard.vue'
import GradientOptions from './GradientOptions.vue'
import GradientElement from './GradientElement.vue'
import PresetInput from './PresetInput.vue'
import { Sortable } from '@zionbuilder/sortable'
import { ActionsOverlay } from '../ActionsOverlay'

export default {
	name: 'GradientGenerator',
	components: {
		GradientOptions,
		GradientBoard,
		GradientElement,
		PresetInput,
		ActionsOverlay,
		Sortable,
		Icon
	},
	props: {
		modelValue: {
			type: Array,
			required: false,
			default () { }
		},
		saveToLibrary: {
			type: Boolean,
			required: false,
			default: true
		}
	},

	setup() {
		// This should be provided by Apps that are using this component
		const useBuilderOptions = inject('builderOptions')
		const {
			addLocalGradient,
			addGlobalGradient,
			getOptionValue
		} = useBuilderOptions()

		return {
			addLocalGradient,
			addGlobalGradient,
			getOptionValue
		}
	},

	data () {
		return {
			showPresetInput: false,
			activeGradientIndex: 0
		}
	},

	computed: {
		computedValue: {
			get () {
				const clonedValue = this.modelValue === undefined || this.modelValue === null ? getDefaultGradient() : this.modelValue
				return applyFilters('zionbuilder/options/model', JSON.parse(JSON.stringify(clonedValue)))
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		activeGradient: {
			get () {
				return this.computedValue[this.activeGradientIndex]
			},
			set (newValue) {
				const valueToSend = [...this.computedValue]
				valueToSend[this.activeGradientIndex] = newValue

				this.computedValue = valueToSend
			}
		}
	},
	methods: {
		getValue () {
			return this.computedValue
		},
		addGlobalPattern (name, type) {
			this.showPresetInput = false

			const defaultGradient = {
				name: name,
				config: this.computedValue
			}

			if (type === 'local') {
				this.addLocalGradient(defaultGradient)
			} else {
				this.addGlobalGradient(defaultGradient)
			}
		},
		deleteGradient (gradientConfig) {
			const deletedGradientIndex = this.computedValue.indexOf(gradientConfig)

			// Set the previous color as active
			if (this.activeGradient === gradientConfig) {
				if (deletedGradientIndex > 0) {
					this.activeGradientIndex = deletedGradientIndex - 1
				} else {
					this.activeGradientIndex = deletedGradientIndex + 1
				}
			} else {
				// check if the deleted gradient had an index lower than the active gradient
				if (deletedGradientIndex < this.activeGradientIndex) {
					this.activeGradientIndex = this.activeGradientIndex - 1
				}
			}

			const updatedValues = this.computedValue.slice(0)
			updatedValues.splice(deletedGradientIndex, 1)

			// Send the new value to parent
			this.computedValue = updatedValues
		},
		addGradientConfig () {
			const defaultConfig = getDefaultGradient()

			this.computedValue = [
				...this.computedValue,
				defaultConfig[0]
			]

			// Change the active gradient
			this.$nextTick(() => {
				const newGradientIndex = this.computedValue.length - 1
				this.changeActive(newGradientIndex)
			})
		},
		changeActive (index) {
			this.activeGradientIndex = index
			this.showOptions = true
		},
		changePosition (position) {
			this.activeGradient = {
				...this.activeGradient,
				position: position
			}
		},
		deleteGradientValue () {
			this.$emit('update:modelValue', null)
		}
	}
}
</script>
<style lang="scss">
.znpb-admin__wrapper {
	.znpb-admin__gradient-modal-wrapper {
		padding: 20px 10px;
	}

	.znpb-gradient-wrapper {
		padding: 0 10px;
	}

	.znpb-gradient-elements-wrapper {
		padding: 0;
	}
}
.znpb-gradient-wrapper {
	.znpb-gradient-elements-wrapper {
		padding: 10px 15px 0;
	}
	.znpb-gradient-options-wrapper {
		padding: 20px 15px;

		.znpb-input-wrapper {
			width: 100%;
			padding: 0;
		}
	}
	.znpb-gradient-wrapper__board {
		margin-bottom: 0;
	}

	.znpb-form__input-title {
		margin-bottom: 10px;
		color: $font-color;
		font-weight: 500;
	}

	.znpb-colorpicker-add-grad {
		display: inline-flex;
		justify-content: center;
		align-items: center;
		width: 46px;
		height: 46px;
		margin-bottom: 10px;
		background: none;
		border: 2px dashed $border-color;
		border-radius: 3px;
		cursor: pointer;
		.zion-icon.zion-svg-inline {
			width: 14px;
			margin: 0 auto;
			color: $font-color;
		}
	}
	.znpb-gradient__type {
		margin-bottom: 20px;

		.znpb-tabs--minimal .znpb-tabs__header {
			padding: 3px;
			margin-bottom: 20px;
			background: $surface-variant;
			border-radius: 3px;

			& > .znpb-tabs__header-item {
				flex-grow: 1;
				padding: 10px 25px;
				border-radius: 2px;

				&:hover {
					color: $font-color;
					background-color: darken($surface-variant, 5%);
				}
			}

			& > .znpb-tabs__header-item--active {
				color: $surface;
				background-color: $secondary;

				&:hover {
					color: $surface;
					background-color: $secondary;
				}
			}
		}
	}
}

.znpb-radial-postion-wrapper {
	display: flex;

	.znpb-forms-input-wrapper {
		margin-right: 20px;

		&:last-child {
			margin-right: 0;
		}
		.znpb-forms-form__input-title {
			margin-bottom: 0;
		}
	}

	& > .znpb-input-wrapper {
		flex-direction: column;
		flex-grow: 1;
		margin-right: 10px;
		margin-bottom: 0;

		&:last-child {
			margin-right: 0;
		}
	}
}

.znpb-gradient-elements-wrapper {
	display: flex;
	flex-wrap: wrap;
	padding: 0 20px;
}

.znpb-gradient-wrapper {
	.znpb-gradient__angle {
		& > .znpb-form__input-title {
			padding: 0;
		}
	}

	.znpb-actions-overlay__actions {
		display: flex;
		align-items: center;
		width: 100%;
		padding: 8px;
		margin: 0 auto;
		font-weight: 500;
		text-align: center;
		cursor: pointer;

		span {
			&:first-child {
				margin-right: 15px;
				margin-left: 10px;
			}
		}

		.znpb-gradient__show-preset, .znpb-gradient__show-preset-input {
			transition: color .15s;

			&:hover {
				color: $surface-active-color;
			}
		}
	}
}

.znpb-gradient-wrapper__delete-gradient {
	flex: 1 0 30%;
	max-width: 30px;
	margin-left: auto;
	border: 2px solid $border-color;
	border-radius: 3px;
	transition: opacity .15s ease;

	&:hover {
		opacity: .7;
	}
}

.znpb-admin-colors__container {
	display: flex;
	flex-wrap: wrap;
}
</style>
