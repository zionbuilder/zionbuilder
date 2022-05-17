<template>
	<Tooltip
		v-model:show="showDropdown"
		append-to="element"
		:placement="placement"
		trigger="click"
		:close-on-outside-click="true"
		tooltip-class="znpb-option-selectTooltip hg-popper--no-padding"
		class="znpb-option-selectWrapper"
		@show="onModalShow"
		:tooltip-style="{'width': tooltipWidth + 'px'}"
		:show-arrows="false"
		strategy="fixed"
		:modifiers="[
				{
					name: 'preventOverflow',
					enabled: true
				},
				{
					name: 'hide',
					enabled: true
				},
				{
					name: 'flip',
					options: {
						fallbackPlacements: ['bottom', 'top', 'right','left'],
					},
				},
			]"
	>

		<div
			class="znpb-option-selectOptionPlaceholder"
			ref="optionWrapper"
		>
			<Loader
				v-if="loadingTitle"
				:size="14"
			/>

			<span
				v-else
				class="znpb-option-selectOptionPlaceholderText"
			>{{dropdownPlaceholder}}</span>
			<span class="znpb-inputDropdownIcon-wrapper">
				<Icon
					icon="select"
					class="znpb-inputDropdownIcon"
					:rotate="showDropdown ? '180' : false"
				/>
			</span>
		</div>

		<template #content>
			<div class="znpb-option-selectOptionListWrapper">
				<BaseInput
					v-if="filterable || addable"
					class="znpb-option-selectOptionListSearchInput"
					v-model="searchKeyword"
					:placeholder="addable ? $translate('search_or_add') : $translate('search')"
					:clearable="true"
					icon="search"
					autocomplete="off"
					ref="searchInput"
					@keydown="onInputKeydown"
				>
					<template
						#after-input
						v-if="addable && searchKeyword.length > 0"
					>
						<Icon
							icon="plus"
							class="znpb-inputAddableIcon"
							@click.stop.prevent="addItem"
							v-znpb-tooltip="$translate('add_new_item')"
						/>
					</template>
				</BaseInput>

				<ListScroll
					@scroll-end="onScrollEnd"
					v-model:loading="loading"
					class="znpb-menuList znpb-mh-200"
				>
					<div
						class="znpb-menuListItem"
						v-for="option in visibleItems"
						:key="option.id"
						:class="{
							'znpb-menuListItem--selected': !option.is_label && option.isSelected,
							'znpb-menuListItem--is-label': option.is_label,
							'znpb-menuListItem--is-group_item': option.is_group_item
						}"
						@click.stop="onOptionSelect(option)"
						:style="getStyle(option.name)"
					>
						{{option.name}}
					</div>
				</ListScroll>

				<div
					v-if="stopSearch"
					class="znpb-option-selectOptionListNoMoreText"
				>{{$translate('no_more_items')}}</div>

			</div>
		</template>

	</Tooltip>
</template>

<script>
import { ref, computed, watch, watchEffect, inject, unref } from 'vue'
import { debounce } from 'lodash-es'
import { applyFilters } from '@zb/hooks'


import { useSelectServerData } from './useSelectServerData.js'

export default {
	name: 'InputSelect',
	props: {
		modelValue: {
			type: [String, Number, Array, Boolean],
		},
		options: {
			type: Array,
			default: []
		},
		filterable: {
			type: Boolean
		},
		server_callback_method: {
			type: String
		},
		server_callback_args: {},
		server_callback_per_page: {
			type: Number,
			default: 25
		},
		placeholder: {
			type: String
		},
		/**
		* Set dropdown placement
		*/
		placement: {
			type: String,
			required: false,
			default: 'bottom'
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
		* If the user can add a new option
		*/
		multiple: {
			type: Boolean,
			required: false,
			default: false
		},
		local_callback_method: {
			type: String,
			required: false
		},
		filter_id: {
			type: String,
			required: false
		}
	},
	setup (props, { emit }) {
		const optionWrapper = ref(null)
		const searchInput = ref(null)
		const searchKeyword = ref('')
		const showDropdown = ref(false)
		const loading = ref(false)
		const loadingTitle = ref(false)
		const stopSearch = ref(false)
		const tooltipWidth = ref(null)

		// Add element info
		const elementInfo = inject('elementInfo', null)

		let page = 1
		const { fetch, getItems } = useSelectServerData({})

		const computedModelValue = computed(() => {
			if (props.modelValue && props.multiple && !Array.isArray(props.modelValue)) {
				return [props.modelValue]
			}

			if (!props.modelValue && props.multiple) {
				return []
			}

			return props.modelValue
		})

		const items = computed(() => {
			// Add local items
			let options = [...props.options]

			// Check if we need to get items from server
			if (props.server_callback_method) {
				// Add the server items
				const serverOptions = getItems({
					server_callback_method: props.server_callback_method,
					server_callback_args: props.server_callback_args,
				})
				if (serverOptions.length > 0) {
					options.push(...serverOptions)
				}
			}

			// Check if the addable option was set
			if (props.addable && props.modelValue) {
				if (props.multiple) {
					computedModelValue.value.forEach(savedValue => {
						if (!options.find(option => option.id === savedValue)) {
							options.push({
								name: savedValue,
								id: savedValue,
							})
						}
					})
				} else if (!options.find(option => option.id === computedModelValue.value)) {
					options.push({
						name: props.modelValue,
						id: props.modelValue,
					})
				}
			}

			// Check if we need to populate the data
			if (props.local_callback_method) {
				const localOptions = window[props.local_callback_method]
				if (typeof localOptions === 'function') {
					// Pass in options so we can modify them
					options.push(...localOptions(options, elementInfo))
				}
			}

			if (props.filter_id) {
				options = applyFilters(props.filter_id, options, unref(elementInfo))
			}

			// set active tag
			options = options.map(option => {
				let isSelected = false
				if (props.multiple) {
					isSelected = computedModelValue.value.includes(option.id)
				} else {
					isSelected = computedModelValue.value === option.id
				}

				// create a copy so we do not modify the initial data
				return {
					...option,
					isSelected
				}
			})

			return options
		})

		const visibleItems = computed(() => {
			let options = items.value

			if (props.filterable || props.addable) {
				if (searchKeyword.value.length > 0) {
					options = options.filter(optionConfig => {
						return optionConfig.name.toLowerCase().indexOf(searchKeyword.value.toLowerCase()) !== -1
					})
				}
			}

			// If this is set to multiple, sort them
			if (props.multiple) {
				options.sort((item) => item.isSelected ? -1 : 1)
			}

			return options
		})

		watch(searchKeyword, () => {
			// Reset the search end flag
			stopSearch.value = false
			debouncedGetItems()
		})

		// Clear the search keyword when the dropdown is closed
		watch(showDropdown, (newValue) => {
			if (!newValue) {
				searchKeyword.value = ''
			}
		})

		const debouncedGetItems = debounce(() => {
			loadNext()
		}, 300)

		function loadNext () {
			if (!props.server_callback_method) {
				return
			}

			if (loading.value) {
				return
			}

			loading.value = true

			const include = props.modelValue

			fetch({
				server_callback_method: props.server_callback_method,
				server_callback_args: props.server_callback_args,
				page,
				searchKeyword: searchKeyword.value,
				include
			}).then((response) => {
				// Check to see if all posts were found
				if (props.server_callback_per_page === -1) {
					stopSearch.value = true
				} else if (response.length < props.server_callback_per_page) {
					stopSearch.value = true
				}

				loading.value = false
				loadingTitle.value = false
			})
		}

		function onScrollEnd () {
			if (!props.server_callback_method) {
				return
			}

			// Don't search if we need to show all results
			if (props.server_callback_per_page === -1) {
				return
			}

			if (!stopSearch.value) {
				page++
				loadNext()
			}
		}

		// Load initial data
		if (props.server_callback_method) {
			// load inital posts
			loadNext()
		}

		const dropdownPlaceholder = computed(() => {
			if (typeof props.modelValue === 'undefined' || (props.multiple && computedModelValue.value.length === 0)) {
				return props.placeholder
			} else {
				if (props.multiple) {
					const activeTitles = items.value.filter(option => computedModelValue.value.includes(option.id))
					if (activeTitles) {
						return activeTitles.map(item => item.name).join(', ')
					} else if (props.addable) {
						return computedModelValue.value.join(',')
					}
				} else {
					const activeTitle = items.value.find(option => option.id === computedModelValue.value)
					if (activeTitle) {
						return activeTitle.name
					} else if (props.addable) {
						return props.modelValue
					}
				}

				return null
			}
		})

		watchEffect(() => {
			if (dropdownPlaceholder.value === null && props.server_callback_method) {
				loadingTitle.value = true
			}
		})

		function onOptionSelect (option) {
			if (props.multiple) {
				const oldValues = [...computedModelValue.value]
				if (option.isSelected) {
					const selectedOptionIndex = oldValues.indexOf(option.id)
					oldValues.splice(selectedOptionIndex, 1)
					emit('update:modelValue', oldValues)
				} else {
					oldValues.push(option.id)
					emit('update:modelValue', oldValues)
				}
			} else {
				emit('update:modelValue', option.id)
				showDropdown.value = false
			}
		}

		function onModalShow () {
			// Set the tooltip width
			if (optionWrapper.value) {
				tooltipWidth.value = optionWrapper.value.getBoundingClientRect().width
			}



			if ((props.filterable || props.addable) && searchInput.value) {
				searchInput.value.focus()
			}
		}

		function getStyle (font) {
			if (props.style_type === 'font-select') {
				return {
					fontFamily: font
				}
			} else return null
		}

		function addItem () {
			onOptionSelect({
				name: searchKeyword.value,
				id: searchKeyword.value
			})
			showDropdown.value = false
		}

		function onInputKeydown (event) {
			// if addable and enter is pressed
			if (props.addable && event.keyCode === 13) {
				addItem()
			}
		}

		return {
			optionWrapper,
			tooltipWidth,
			searchInput,
			searchKeyword,
			dropdownPlaceholder,
			onOptionSelect,
			onScrollEnd,
			onModalShow,
			getStyle,
			addItem,
			onInputKeydown,
			loading,
			showDropdown,
			stopSearch,
			items,
			loadingTitle,
			visibleItems
		}
	}
}
</script>

<style lang="scss">
.znpb-option-selectOptionListNoMoreText {
	padding: 10px 6px 5px;
	text-align: center;
	opacity: .8;
}

.znpb-inputDropdownIcon-wrapper {
	border-left: var(--zb-input-separator-width) solid
	var(--zb-input-separator-color);
}

.znpb-inputDropdownIcon {
	padding: 11px;
}

.znpb-option-selectOptionPlaceholder {
	display: flex;
	justify-content: space-between;
	align-items: center;
	width: 100%;
	font-weight: 500;
	background: var(--zb-input-bg-color);
	border: 2px solid var(--zb-input-border-color);
	border-radius: 3px;
	cursor: pointer;
}

.znpb-option-selectOptionPlaceholderText {
	overflow: hidden;
	padding: 11px;
	line-height: 1;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.znpb-option-selectWrapper {
	width: 100%;
	margin-right: 10px;
	color: var(--zb-surface-text-color);
}

.znpb-inputAddableIcon {
	padding: 11px;
	cursor: pointer;
}

.znpb-option-selectOptionListSearchInput {
	width: auto !important;
	margin: 15px;
}

.znpb-menuListItem--is-label {
	font-weight: bold;
	pointer-events: none;
	color: var(--zb-surface-text-active-color);
}

.znpb-menuListItem--is-label ~ .znpb-menuListItem:not(.znpb-menuListItem--is-label) {
	padding-left:25px;
}

.znpb-menuListItem--is-group_item {
	padding-left: 30px;
}
</style>