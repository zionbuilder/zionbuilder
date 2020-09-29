<template>
	<div class="znpb-element-styles__wrapper">
		<div class="znpb-element-styles__media-wrapper">
			<ClassSelectorDropdown
				v-model="computedClasses"
				:selector="selector"
				:title="title"
				:activeClass.sync="activeClass"
			/>

			<PseudoSelectors v-model="computedStyles" />
		</div>

		<OptionsForm
			:schema="getElementStyleOptionsSchema"
			v-model="computedStyles"
			class="znpb-element-styles-option__options-wrapper"
		/>

	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import PseudoSelectors from '../../../components/elementOptions/PseudoSelectors.vue'
import ClassSelectorDropdown from '../../../components/elementOptions/ClassSelectorDropdown.vue'

export default {
	name: 'ElementStyles',
	props: ['modelValue', 'title', 'selector'],
	data () {
		return {
			activeClass: null
		}
	},
	components: {
		PseudoSelectors,
		ClassSelectorDropdown
	},
	computed: {
		...mapGetters([
			'getElementStyleOptionsSchema',
			'getClassConfig'
		]),
		computedClasses: {
			get () {
				return this.modelValue.classes || []
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					classes: newValue
				})
			}
		},
		computedStyles: {
			get () {
				if (this.activeClass !== this.selector) {
					const activeClassConfig = this.getClassConfig(this.activeClass)
					if (activeClassConfig) {
						return activeClassConfig.style
					}

					// eslint-disable-next-line
					console.warn(`Class with id ${this.activeClass} not found`)
					return {}
				} else {
					return this.modelValue.styles
				}
			},
			set (newValues) {
				if (this.activeClass !== this.selector) {
					this.updateClassSettings({
						classId: this.activeClass,
						newValues: {
							style: newValues
						}
					})
				} else {
					this.updateValues('styles', newValues)
				}
			}
		}
	},
	methods: {
		...mapActions([
			'updateClassSettings'
		]),
		updateValues (type, newValue) {
			const clonedValue = { ...this.modelValue }
			if (newValue === null && typeof clonedValue[type]) {
				// If this is used as layout, we need to delete the active pseudo selector
				delete clonedValue[type]
			} else {
				clonedValue[type] = newValue
			}

			this.$emit('update:modelValue', clonedValue)
		}
	},
	created () {
		this.activeClass = this.selector
	}
}
</script>
<style lang="scss">
.znpb-element-styles {
	&__wrapper {
		display: flex;
		flex-direction: column;
		height: 100%;
	}

	&__media-wrapper {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-grow: 1;
		margin: 0 5px;
	}
}
.znpb-options-form-wrapper.znpb-element-styles-option__options-wrapper {
	padding: 20px 0 0;
}
</style>
