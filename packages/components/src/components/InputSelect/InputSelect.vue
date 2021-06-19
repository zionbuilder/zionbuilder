<template>
	<Tooltip
		v-model:show="showDropdown"
		append-to="element"
		:placement="placement"
		trigger="click"
		:close-on-outside-click="true"
		tooltip-class="znpb-option-selectTooltip"
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
			<Icon
				icon="select"
				class="znpb-inputDropdownIcon"
				:rotate="showDropdown ? '180' : false"
			/>
		</div>

		<template #content>
			<div class="znpbpro-dynamicSourceTypeSelectorListWrapper">
				<BaseInput
					v-if="filterable || addable"
					class="znpb-dynamicSourceTypeSearch"
					v-model="searchKeyword"
					:placeholder="$translate('search')"
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
						/>
					</template>
				</BaseInput>

				<ListScroll
					@scroll-end="onScrollEnd"
					v-model:loading="loading"
					class="znpbpro-dynamicSourceListScroll"
				>
					<div
						class="znpbpro-dynamicSourceTypeSelectorListItem"
						:class="{'znpbpro-dynamicSourceTypeSelectorListItem--active': modelValue === option.id}"
						@click.stop="onOptionSelect(option.id)"
						v-for="option in visibleItems"
						:key="option.id"
						:style="getStyle(option.name)"
					>
						{{option.name}}
					</div>
				</ListScroll>

				<div
					v-if="stopSearch"
					class="znpb-optionWPPageSelectorItemNoMore"
				>{{$translate('no_more_items')}}</div>

			</div>
		</template>

	</Tooltip>
</template>

<script>
import { ref, computed, watch, watchEffect } from 'vue'
import { debounce } from 'lodash-es'

import { useSelectServerData } from './useSelectServerData.js'

export default {
	name: 'InputSelect',
	props: {
		modelValue: {
			type: [String, Number]
		},
		options: {
			type: Array,
			required: true,
			default: []
		},
		filterable: {
			type: Boolean
		},
		server_callback_method: {
			type: String
		},
		server_callback_name_method: {
			type: String
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

		let page = 1
		const { fetch, getItems, getItem } = useSelectServerData({
			method: props.server_callback_method
		})

		const items = computed(() => {
			// Add local items
			let options = [...props.options]

			// Check if we need to get items from server
			if (props.server_callback_method) {
				// Add the server items
				const serverOptions = getItems(props.server_callback_method)
				if (serverOptions.length > 0) {
					options.push(...serverOptions)
				}
			}

			// Check if the addable option was set
			if (props.addable && props.modelValue && !options.find(option => option.id === props.modelValue)) {
				options.push({
					name: props.modelValue,
					id: props.modelValue
				})
			}

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

			loading.value = true

			const include = props.modelValue

			fetch({
				server_callback_method: props.server_callback_method,
				page,
				searchKeyword: searchKeyword.value,
				include
			}).then((response) => {
				// Check to see if all posts were found
				if (response.length < 25) {
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
			if (typeof props.modelValue === 'undefined') {
				return props.placeholder
			} else {
				const activeTitle = items.value.find(option => option.id === props.modelValue)
				if (activeTitle) {
					return activeTitle.name
				} else if (props.addable) {
					return props.modelValue
				}
				return null
			}
		})

		watchEffect(() => {
			if (dropdownPlaceholder.value === null) {
				loadingTitle.value = true
			}
		})

		function onOptionSelect (option) {
			emit('update:modelValue', option)
			showDropdown.value = false
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
			emit('update:modelValue', searchKeyword.value)
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
.znpbpro-dynamicSourceDropdownWrapper {
	margin-bottom: 25px;
}

.znpbpro-dynamicSourceListScroll {
	max-height: 240px;
	margin: 0 -10px;
}

.znpb-dynamicSourceTypeSearch {
	margin-bottom: 10px;
}

.znpb-optionWPPageSelectorItemNoMore {
	padding: 10px 6px 5px;
	text-align: center;
	opacity: .8;
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
	border: 2px solid var(--zion-border-color);
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
	color: $font-color;
}

.znpb-inputAddableIcon {
	padding: 11px;
	cursor: pointer;
}

.znpb-option-selectTooltip {
	padding: 0;
}
</style>