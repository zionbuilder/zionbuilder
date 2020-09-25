<template>
	<div class="znpb-baseselect-overwrite">
		<Tooltip
			:trigger="null"
			:show-arrows="false"
			:placement="placement"
			append-to="element"
			:show="expanded"
			ref="tooltip"
			positionFixed="true"
			:modifiers="{
				preventOverflow: {
					enabled: false
				},
				hide: {
					enabled: false
				}
			}"
			tooltip-class="znpb-baseselect-list-wrapper"
		>
			<ul
				ref="dropdown"
				slot="content"
				class="znpb-baseselect-list hg-popper-list znpb-fancy-scrollbar"
				:style="{'min-width': `${inputWidth}px`}"
			>
				<li
					v-for="(option, i) in filteredItems"
					v-bind:key="i"
					:class="{'znpb-select-option-selected': isSelected(option.id), 'znpb-baseselect-list__option--active': optionIndex === i}"
					@click.stop="onOptionSelect(option.id)"
					class="znpb-baseselect-list__option hg-popper-list__item"
					ref="filteredOptions"
					:style="getStyle(option.name)"
				>
					{{option.name}}
				</li>

				<li
					v-if="filteredItems.length === 0 && !addable"
					class="znpb-not-found-message"
				>
					{{$translate('no_result')}}
				</li>
				<li
					class="znpb-baseselect-list__option znpb-baseselect-list__option--addable hg-popper-list__item"
					v-if="addable && filteredItems.length === 0 && searchKeyword.length > 0"
					@click.stop="addNewItem"
				>
					{{searchKeyword}}

					<BaseIcon
						class="znpb-baseselect-list__option-add-icon"
						icon="plus"
						:size="10"
						:bg-size="22"
						:rounded="true"
						color="#959595"
					/>

				</li>
				<li
					v-if="addable && filteredItems.length === 0 && searchKeyword.length === 0"
					class="znpb-not-found-message"
				>{{$translate('no_items')}}</li>

			</ul>
			<div
				class="znpb-baseselect__trigger"
				@click.prevent="toggleDropdown"
			>
				<!-- if select multiple -->
				<div
					class="znpb-multiple-options"
					v-if="this.multiple"
				>
					<div class="znpb-multiple-options-list">

						<div
							v-for="(savedOption,i) in valueModel"
							v-bind:key="i"
							class="znpb-multiple-options-list__item"
						>
							<span>{{getNameFromOptionId(savedOption)}}</span>
							<BaseIcon
								icon="close"
								:data-index="i"
								class="close-icon"
								@click.native.capture="onOptionSelect(savedOption)"
							/>
						</div>
						<BaseInput
							type="text"
							:clearable="true"
							:filterable="filterable"
							:placeholder="getPlaceholder"
							v-model="searchKeyword"
							@keydown.native.prevent="handleKeydown"
							:readonly="!filterable"
							ref="input"
						>
							<div
								class="znpb-baseselect__trigger-icon"
								slot="suffix"
							>
								<BaseIcon
									icon="select"
									:rotate="expanded ? '180' : false"
								/>
							</div>
						</BaseInput>
					</div>
				</div>
				<!-- if select simple -->
				<BaseInput
					v-else
					type="text"
					:clearable="selected !== searchKeyword"
					:placeholder="getPlaceholder"
					v-model="searchKeyword"
					:filterable="filterable"
					:readonly="!filterable"
					ref="input"
					@keydown.native="handleKeydown"
					:font-family="style_type === 'font-select' ? selected : null"
				>
					<div
						class="znpb-baseselect__trigger-icon"
						slot="suffix"
					>
						<BaseIcon
							icon="select"
							:rotate="expanded ? '180' : false"
						/>
					</div>
				</BaseInput>

			</div>

		</Tooltip>
	</div>
</template>

<script>
import BaseIcon from '../../../BaseIcon.vue'
import BaseInput from '../input/BaseInput.vue'
import { Tooltip } from '../../../tooltip'

export default {
	/**
	 * this type of element supports light class
	 *
	 * required properties received:
	 *   options - array of objects
	 * other properties received:
	 * 	 colorscheme - String
	 *   placeholder - String
	 *   multiple - Boolean
	 *   model - Array | String
	 */
	name: 'InputSelect',
	components: {
		BaseInput,
		Tooltip,
		BaseIcon
	},
	props: {
		/**
		* v-model or value
		*/
		value: {
			required: false
		},
		/**
		* Placeholder text for the input
		*/
		placeholder: {
			type: String,
			required: false
		},
		/**
		* If select multiple values
		*/
		multiple: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		* Array of object containing the options value
		*/
		options: {
			type: Array,
			required: true
		},
		/**
		* If the user can enter text to filter the options
		*/
		filterable: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		* If the user can enter a style type
		*/
		style_type: {
			type: String,
			required: false
		},
		/**
		* If the user can add a new option
		*/
		addable: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		* Set dropdown placement
		*/
		placement: {
			type: String,
			required: false,
			default: 'bottom'
		}

	},
	data () {
		return {
			searchKeyword: '',
			selected: this.value,
			expanded: false,
			filteredOptions: this.options,
			optionIndex: null,
			inputWidth: null,
			containerHeight: null
		}
	},
	computed: {
		valueModel () {
			if (this.multiple) {
				if (Array.isArray(this.value)) {
					return this.value
				} else if (this.value) {
					return [this.value]
				}
				return []
			} else {
				return this.value
			}
		},
		getPlaceholder () {
			if (this.multiple && this.valueModel && this.valueModel.length > 0) {
				return ''
			}
			return this.placeholder
		},
		filteredItems () {
			if (this.searchKeyword !== this.selected) {
				return this.options.filter(item => {
					return item.name.toLowerCase().indexOf(this.searchKeyword.toLowerCase()) > -1
				})
			}
			return this.options
		}

	},
	watch: {
		expanded: function (newValue, oldValue) {
			if (newValue) {
				if (!this.multiple) {
					this.$nextTick(() => {
						if (this.optionIndex) {
							this.$refs.dropdown.scrollTop = this.$refs.filteredOptions[this.optionIndex].offsetTop
						}
					})
				}

				this.$refs.input.focus()
				this.$el.ownerDocument.addEventListener('click', this.closePanel)
			} else {
				this.$refs.input.blur()
				this.$el.ownerDocument.removeEventListener('click', this.closePanel)

				if (this.addable) {
					this.addNewItem()
				}
			}
		},

		value () {
			this.setSelected()
		}

	},
	methods: {
		getStyle (font) {
			if (this.style_type === 'font-select') {
				let style = {
					fontFamily: font
				}
				return style
			} else return null
		},
		toggleDropdown (event) {
			this.expanded = !this.expanded
			const inputOption = this.$refs.input
			if (inputOption && inputOption.$el && !this.inputWidth) {
				this.inputWidth = inputOption.$el.clientWidth
			}
		},
		handleKeydown (event) {
			const optionsElements = this.$refs.filteredOptions
			const optionsContainer = this.$refs.dropdown

			if (optionsContainer && this.containerHeight === null) {
				this.containerHeight = optionsContainer.getBoundingClientRect().height
			}
			if (this.expanded) {
				if (event.key === 'ArrowDown') {
					if (this.optionIndex === null) {
						this.optionIndex = 0
					} else {
						this.optionIndex++
					}
					if (this.optionIndex > this.options.length - 1) {
						this.optionIndex = 0
						optionsContainer.scrollTop = 0
					}
					if (optionsElements[this.optionIndex].offsetTop > optionsContainer.scrollTop + this.containerHeight - 39) {
						optionsContainer.scrollTop += 39
					}
				}
				if (event.key === 'ArrowUp') {
					if (this.optionIndex === null) {
						this.optionIndex = this.options.length - 1
						optionsContainer.scrollTop = optionsElements[this.optionIndex].offsetTop
					} else {
						this.optionIndex--
					}
					if (this.optionIndex < 0) {
						this.optionIndex = this.options.length - 1
						optionsContainer.scrollTop = optionsElements[this.optionIndex].offsetTop + 39
					}
					if (optionsElements[this.optionIndex].offsetTop < optionsContainer.scrollTop) {
						optionsContainer.scrollTop -= 39
					}
				}
				if (!this.multiple) {
					if (this.optionIndex !== null) {
						this.$emit('input', this.options[this.optionIndex].id)
					}
				}
				if (event.key === 'Escape') {
					this.expanded = false
				}
			}

			if (event.key === 'Enter' && !this.multiple) {
				this.expanded = !this.expanded
			}
			if (event.key === 'Enter' && this.multiple) {
				if (this.optionIndex || this.optionIndex === 0) {
					if (this.expanded) {
						this.onOptionSelect(this.options[this.optionIndex].id)
					}
				}
				if (!this.expanded) {
					this.expanded = true
				}
			}
			if (event.key === 'Enter' && this.addable && this.filteredItems.length === 0) {
				this.addNewItem()
			}
		},

		// show the name of the selected option
		getNameFromOptionId (optionid) {
			let optionConfig = this.options.find((optionConfig) => {
				return optionConfig.id === optionid
			})
			if (typeof optionConfig !== 'undefined' && typeof optionConfig.id !== 'undefined') {
				return optionConfig.name
			}
		},
		/**
		 * Close panel if clicked outside of selector
		 */
		closePanel (event) {
			if (this.$el.contains(event.target) || this.$refs.dropdown.contains(event.target)) {

			} else {
				this.expanded = false
			}
		},
		isSelected (optionId) {
			if (this.multiple) {
				return this.valueModel.includes(optionId)
			} else {
				return this.valueModel === optionId
			}
		},
		onOptionSelect (selectedOption) {
			if (this.$refs.tooltip) {
				this.$refs.tooltip.preventOutsideClickPropagation()
			}

			if (this.multiple) {
				const value = this.valueModel.slice()
				const existingValueIndex = value.indexOf(selectedOption)
				// Check to see if we need to remove the item
				if (existingValueIndex > -1) {
					value.splice(existingValueIndex, 1)
				} else {
					value.push(selectedOption)
				}
				Object.values(this.options).forEach((option, index) => {
					if (option.id === selectedOption) {
						this.optionIndex = index
					}
				})
				this.$refs.input.focus()

				/**
				* Will emit when the select value is updated
				*/
				this.$emit('input', value)
			} else {
				setTimeout(() => {
					this.expanded = false
				}, 50)

				/**
				* Will emit when the select value is updated */

				this.$emit('input', selectedOption)
			}
			this.setSelected()
		},
		setSelected () {
			if (!this.multiple) {
				const selectedOptionName = this.getNameFromOptionId(this.valueModel) || this.valueModel || ''
				this.searchKeyword = selectedOptionName
				this.selected = selectedOptionName
				Object.values(this.options).forEach((option, index) => {
					if (option.id === this.valueModel) {
						this.optionIndex = index
					}
				})
			}
		},
		addNewItem () {
			// emit new option to be added
			this.$emit('input', this.searchKeyword)
			this.selected = this.searchKeyword

			// close the list
			this.expanded = false
		}
	},
	mounted () {
		this.setSelected()

		// set width
	},
	beforeDestroy () {
		this.$el.ownerDocument.removeEventListener('click', this.closePanel)
	}
}

</script>
<style lang="scss">
.znpb-baseselect {
	&-overwrite {
		position: relative;
		appearance: none;
		border-top-right-radius: 3px;
		border-top-left-radius: 3px;
	}

	&__trigger {
		display: flex;
		justify-content: space-between;
		align-items: center;
		color: $surface-headings-color;
		border-radius: 3px;
		cursor: pointer;

		input:read-only {
			cursor: pointer;
		}

		.znpb-editor-icon {
			transition: 0s;
		}

		&-icon {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 36px;
			height: 100%;
			padding: 10px;
			color: darken($surface-variant, 30%);
			border-left: 2px solid #e3e3e3;

			.znpb-editor-icon-wrapper {
				font-size: 14px;
			}
		}
	}

	&__placeholder {
		padding: 16px 0;
	}
}
.znpb-baseselect-list {
	overflow: auto;
	max-height: 200px;
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;

	&-wrapper {
		padding: 0;
	}

	&__option {
		&--active {
			color: $surface-active-color;
		}

		&:hover {
			color: $surface-active-color;
		}

		&.znpb-select-option-selected {
			color: $surface-active-color;
		}

		&--addable {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		&-add-icon {
			font-size: 10px;
			text-align: center;
			border: 2px solid $border-color;
		}
	}
}
.znpb-multiple-options {
	display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	flex: 1;

	&-list {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		padding: 8px 0 8px 0;

		&__item {
			padding: 3px 12px;
			margin: 0 5px 5px 0;
			color: $font-color;
			background: $surface-headings-color;
			border-radius: 15px;

			&:last-child {
				margin-right: 0;
			}

			.zion-icon:hover {
				cursor: pointer;
			}
			&:hover {
				cursor: pointer;
			}
		}
	}
}

.znpb-not-found-message {
	padding: 5px 14px;
}
</style>
