<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				class="znpb-add-elements__filter-category"
				:options="elementCategories"
				:placeholder="elementCategories[0].name"
				v-model="categoryValue"
				:placement="isRtl ? 'bottom-end' : 'bottom-start'"
			/>

			<BaseInput
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				v-model="computedSearchKeyword"
				:placeholder="$translate('search_elements')"
				:clearable="true"
				icon="search"
				autocomplete="off"
				ref="searchInputEl"
			/>
		</div>

		<div class="znpb-fancy-scrollbar">
			<div class="znpb-wrapper-category">

				<ElementList
					v-for="(category,i) in computedRuleCategories"
					:key="i"
					:elements="getElements(category.id)"
					:element="element"
					:category="category.name"
					@add-element="onAddElement"
				/>

			</div>
		</div>
	</div>
</template>
<script>
import { ref, computed, onMounted, nextTick } from 'vue'

import { useElementTypes, useElementTypeCategories, useAddElementsPopup, useHistory, useEditorData } from '@composables'
import { on, off } from '@zb/hooks'

// Components
import ElementList from './ElementList.vue'

export default {
	name: 'ElementsTab',
	components: {
		ElementList
	},
	props: {
		element: {
			type: Object
		},
		searchKeyword: String
	},
	setup (props) {
		const { getVisibleElements } = useElementTypes()
		const { categories } = useElementTypeCategories()
		const { editorData } = useEditorData()
		// Refs
		const localSearchKeyword = ref(null)
		const computedSearchKeyword = computed(
			{
				get: () => {
					return localSearchKeyword.value !== null ? localSearchKeyword.value : props.searchKeyword
				},
				set: (newValue) => {
					localSearchKeyword.value = newValue
				}
			}
		)
		const categoryValue = ref('all')
		const searchInputEl = ref(null)

		const sortedCategories = computed(() => {
			return categories.value.sort((a, b) => {
				return a.priority < b.priority ? -1 : 1
			})
		})

		// Normal data
		const elementCategories = [{
			id: 'all',
			name: 'All'
		}].concat(sortedCategories.value)

		// Computed
		const computedRuleCategories = computed(() => {
			let categoriesArray = []
			let category = categoryValue.value

			if (category !== 'all') {
				categoriesArray = categories.value.filter(cat => cat.id === category)

			} else {
				categoriesArray = sortedCategories.value
			}

			return categoriesArray

		})


		// Methods
		const onAddElement = (element) => {
			const { hideAddElementsPopup, insertElement } = useAddElementsPopup()

			const config = {
				element_type: element.element_type,
				...element.extra_data
			}

			insertElement(config)

			const { getElementType } = useElementTypes()
			const { addToHistory } = useHistory()
			const elementType = getElementType(config.element_type)
			addToHistory(`Added ${elementType.name}`)

			hideAddElementsPopup()
		}

		function getElements (category) {
			let elements = getVisibleElements.value

			const keyword = computedSearchKeyword.value

			// Check if we have a specific category selected

			elements = elements.filter((element) => {
				return element.category.includes(category)
			})


			// Check if we have a keyword
			if (keyword.length > 0) {
				elements = elements.filter((element) => {
					return (
						element.name.toLowerCase().indexOf(keyword.toLowerCase()) !== -1 ||
						element.keywords
							.join()
							.toLowerCase()
							.indexOf(keyword.toLowerCase()) !== -1
					)
				})
			}

			return elements
		}


		// Lifecycle
		onMounted(() => {
			setTimeout(() => {
				searchInputEl.value.focus()
			}, 0)
		})

		return {
			// Normal values
			elementCategories,
			getElements,
			// Refs
			computedSearchKeyword,
			categoryValue,
			searchInputEl,
			// Computed
			computedRuleCategories,
			// Methods
			onAddElement,
			// rtl
			isRtl: editorData.value.rtl
		}
	}
}
</script>

<style lang="scss">
.znpb-columns-templates-wrapper {
	.znpb-tab__wrapper {
		&--columns-template-elements {
			display: flex;
			flex-direction: column;
			min-height: 385px;
			.znpb-wrapper-category {
				align-items: center;
				max-height: 321px;
				padding-top: 20px;
				& > div {
					width: 100%;
				}
			}
		}

		.znpb-fancy-scrollbar {
			flex-grow: 1;
			padding: 0 6px 0 10px;
		}

		.hg-popper-list {
			padding: 0;
		}
	}
	.zion-input__prepend {
		padding: 0;
		background: transparent;
		border-right: 2px solid $border-color;

		.znpb-baseselect__trigger > .zion-input {
			border: none;
		}
	}

	.zion-input input, .zion-input input::placeholder {
		color: $surface-headings-color;
	}

	.znpb-element-category-list {
		.znpb-element-box {
			padding: 0;
		}
	}
}

.znpb-add-elements__filter {
	display: flex;
	padding: 0 10px;

	&-category {
		margin-right: 10px;
	}
}

.znpb-add-elements__search-results-wrapper {
	flex-grow: 1;
	height: 100%;
	max-height: 100%;
}
.znpb-columns-templates {
	display: grid;
	color: $surface-variant;

	grid-column-gap: 20px;
	grid-row-gap: 20px;
	grid-template-columns: 1fr 1fr 1fr;

	&__search-wrapper.zion-input {
		// width: calc(100% - 20px);
		// padding: 0 10px;
		margin-bottom: 20px;

// margin-left: 10px;
		background: transparent;
	}
}
</style>
