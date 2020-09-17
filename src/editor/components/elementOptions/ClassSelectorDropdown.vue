<template>
	<div class="znpb-class-selector">
		<Tooltip
			trigger="null"
			:show="dropdownState"
			placement="bottom-start"
			tooltip-class="znpb-class-selector__popper"
			:show-arrows="false"
		>
			<div
				class="znpb-class-selector-body"
				slot="content"
				tabindex="0"
				ref="dropDownWrapper"
				@keydown.down="onKeyDown"
				@keydown.up="onKeyUp"
				@keydown.enter="onKeyEnter"
				@keydown.esc.stop="dropdownState = false"
			>
				<div class="znpb-search-wrapper">
					<BaseInput
						:value="keyword"
						:filterable="true"
						:clearable="true"
						:placeholder="$translate('enter_class_name')"
						ref="input"
						@input="handleClassInput"
						@keydown.native.enter.stop="addNewCssClass"
					></BaseInput>
					<BaseButton
						@click.native="addNewCssClass"
						type="secondary"
						class="znpb-class-selector__add-class-button"
					>
						{{$translate('add_class')}}
					</BaseButton>
				</div>

				<template v-if="filteredClasses.length > 0">
					<CssSelector
						v-for="(cssClassItem) in filteredClasses"
						:key="cssClassItem.selector"
						:name="cssClassItem.name"
						:type="cssClassItem.type"
						:is-selected="cssClassItem.selector === activeClass"
						:show-delete="cssClassItem.deletable"
						@remove-class="removeClass"
						@click.native="selectClass(cssClassItem.selector), dropdownState = false"
					/>
				</template>
				<div
					v-if="!errorMessage && filteredClasses.length === 0"
					class="znpb-class-selector-noclass"
				>{{$translate('no_class_found')}}
				</div>
				<div
					class="znpb-class-selector-validator"
					v-if="invalidClass"
				>{{ errorMessage }}
				</div>
			</div>

			<CssSelector
				v-bind="activeClassConfig"
				:show-delete="false"
				@click.native="dropdownState = !dropdownState"
				class="znpb-class-selector-trigger"
			/>

		</Tooltip>

	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import CssSelector from './CssSelector.vue'
import {
	Tooltip,
	BaseInput
} from '@zb/components'

export default {
	name: 'ClassSelectorDropdown',
	props: {
		selector: {
			type: String,
			required: true
		},
		value: {
			type: Array
		},
		title: {
			type: String
		},
		activeClass: {
			type: String,
			required: true
		}
	},
	data () {
		return {
			dropdownState: false,
			errorMessage: null,
			keyword: '',
			invalidClass: false,
			focusClassIndex: 0
		}
	},
	components: {
		CssSelector,
		BaseInput,
		Tooltip
	},
	computed: {
		...mapGetters([
			'getClassesByFilter',
			'getClasses'
		]),
		computedValue: {
			get () {
				return this.value
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		computedSelectorConfig () {
			return {
				type: 'selector',
				selector: this.selector,
				name: this.title,
				deletable: false
			}
		},
		invalidClassReset () {
			return this.keyword.length > 0
		},
		activeClassConfig () {
			if (this.activeClass !== this.selector) {
				return {
					type: 'class',
					name: this.activeClass
				}
			} else {
				return this.computedSelectorConfig
			}
		},
		filteredClasses () {
			if (this.keyword.length === 0) {
				const extraClasses = this.computedValue.map(selector => {
					return {
						type: 'class',
						selector,
						name: selector,
						deletable: true
					}
				})

				return [
					this.computedSelectorConfig,
					...extraClasses
				]
			} else {
				return this.getClassesByFilter(this.keyword).map(selectorConfig => {
					return {
						type: 'class',
						selector: selectorConfig.id,
						name: selectorConfig.name
					}
				})
			}
		}
	},
	watch: {
		dropdownState: function (newState, oldState) {
			if (newState) {
				document.addEventListener('click', this.closePanel)
				this.$nextTick(() => {
					// Element not focused on nect tick alone
					setTimeout(() => {
						this.$refs.input.focus()
					}, 0)
				})
				this.keyword = ''
			} else {
				document.removeEventListener('click', this.closePanel)
				this.errorMessage = null
				this.focusClassIndex = 0
			}
		}
	},
	methods: {
		...mapActions([
			'addClass'
		]),
		onKeyDown (event) {
			let nextClass

			if (this.filteredClasses.length !== 0) {
				this.$refs.input.blur()
				this.$refs.dropDownWrapper.focus()

				if (this.filteredClasses[this.focusClassIndex + 1]) {
					nextClass = this.filteredClasses[this.focusClassIndex + 1].selector
					this.selectClass(nextClass)
					this.focusClassIndex += 1
				}
			}
		},
		onKeyUp (event) {
			let previousClass

			if (this.filteredClasses.length !== 0) {
				this.$refs.input.blur()
				this.$refs.dropDownWrapper.focus()

				if (this.filteredClasses[this.focusClassIndex - 1]) {
					previousClass = this.filteredClasses[this.focusClassIndex - 1].selector
					this.selectClass(previousClass)
					this.focusClassIndex -= 1
				}
			}
		},
		onKeyEnter (event) {
			this.dropdownState = false
		},

		closePanel (event) {
			if (event.target === document) {
				this.dropdownState = false
				return
			}
			if (!this.$el.contains(event.target) && event.target.tagName !== 'INPUT' && !event.target.classList.contains('znpb-class-selector__add-class-button') && !this.dragOutside) {
				this.dropdownState = false
			}
		},

		removeClass (selector) {
			const classIndex = this.computedValue.indexOf(selector)
			const clonedValue = [...this.computedValue]
			const previousClassIndex = classIndex - 1
			const previousClassSelector = this.computedValue[previousClassIndex]

			clonedValue.splice(classIndex, 1)

			// Update the value
			this.computedValue = clonedValue

			// Change active index if this was deleted
			if (selector === this.activeClass) {
				const newSelector = previousClassSelector || this.selector
				const newSelectorIndex = clonedValue.indexOf(newSelector)

				this.selectClass(newSelector)

				// Add +1 as the main selector is not inside the selectors array
				this.focusClassIndex = newSelectorIndex !== -1 ? newSelectorIndex + 1 : 0
			}

			// clear the keyword
			this.keyword = ''
			this.errorMessage = null
		},
		handleClassInput (event) {
			this.keyword = event

			// Add new class to store
			if (!/^[a-z_-][a-z\d_-]*$/i.test(this.keyword) || this.keyword.split('')[0] === '-') {
				this.errorMessage = 'Invalid class name, classes must not start with numbers and cannot contain spaces'
				this.invalidClass = true
			} else {
				this.invalidClass = false
				this.errorMessage = null
			}

			if (!this.keyword.length) {
				this.errorMessage = null
			}
		},
		addNewCssClass (event) {
			if (!this.invalidClass && this.keyword.length) {
				this.dropdownState = false

				// check if the class already exists
				const existingClass = Object.values(this.getClasses).find(classItem => {
					return classItem.name.toLowerCase() === this.keyword
				})

				if (!existingClass) {
					this.addClass(this.keyword)
				}

				// Add css class to element options
				this.selectClass(this.keyword)

				// clear the keyword
				this.keyword = ''
			}
		},
		selectClass (selector) {
			// Check to see if we need to add the class to the element
			if (selector && selector !== this.selector && this.computedValue.indexOf(selector) === -1) {
				// Make a clone as computed properties with setters are not
				// triggered by array methods
				const clonedValue = [...this.computedValue]
				clonedValue.push(selector)
				this.computedValue = clonedValue
			}

			this.$emit('update:activeClass', selector)
		}
	},
	beforeDestroy () {
		document.removeEventListener('click', this.closePanel)
	}
}
</script>

<style lang="scss">
.znpb-button.znpb-class-selector__add-class-button {
	padding: 15px;
	margin-left: 5px;
	white-space: nowrap;
}

.znpb-class-selector {
	flex: 6;
	margin-right: 10px;
	border: 2px solid #e5e5e5;
	border-radius: 3px;
	.selected-class {
		.znpb-item {
			padding-top: 0;
		}
	}
	.znpb-css-class-selector__item-type {
		display: inline-block;
	}

	&-noclass {
		padding: 0 15px;
		line-height: 20px;
	}
	&-validator {
		color: $red;
		line-height: 18px;
	}
}

.znpb-class-selector-body {
	width: 288px;

	&:focus {
		outline: none;
	}

	.znpb-search-wrapper {
		display: flex;
		margin-bottom: 10px;
	}
}
</style>
