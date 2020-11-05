<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				class="znpb-add-elements__filter-category"
				:options="elementCategories"
				:placeholder="elementCategories[0].name"
				v-model="categoryValue"
			/>

			<BaseInput
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				v-model="searchKeyword"
				:placeholder="$translate('search_elements')"
				:clearable="true"
				icon="search"
				autocomplete="off"
				ref="searhInputEl"
			/>
		</div>

		<div
			class="znpb-fancy-scrollbar"
		>
			<div class="znpb-wrapper-category">
				<ElementList
					v-if="visibleElements.length > 0"
					:elements="visibleElements"
					:element="element"
					@add-element="onAddElement"
				/>

				<div v-else>{{$translate('no_elements_found')}}</div>
			</div>
		</div>
	</div>
</template>
<script>
import { ref, computed, onMounted } from 'vue'
import { generateUID } from '@zb/utils'
import { on, off } from '@zb/hooks'
import ElementList from './ElementList.vue'
import { useElementTypes, useElementTypeCategories, useAddElementsPopup } from '@data'

export default {
	name: 'ElementsTab',
	components: {
		ElementList
	},
	props: {
		element: {
			type: Object
		}
	},
	setup (props) {
		const { getVisibleElements } = useElementTypes()
		const { categories } = useElementTypeCategories()

		// Refs
		const searchKeyword = ref('')
		const categoryValue = ref('all')
		const searhInputEl = ref(null)

		// Normal data
		const elementCategories = [{
			id: 'all',
			name: 'All'
		}].concat(categories.value)

		// Computed
 		const visibleElements = computed(() => {
			let elements = getVisibleElements.value
			const category = categoryValue.value
			const keyword = searchKeyword.value

			// Check if we have a specific category selected
			if (category !== 'all') {
				elements = elements.filter((element) => {
					return element.category.includes(category)
				})
			}

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
		})

		// Methods
		const onAddElement = (element) => {
			const { hideAddElementsPopup } = useAddElementsPopup()

			const config = {
				element_type: element.element_type,
				...element.extra_data
			}

			// If it's a wrapper, it means that it can have childs
			const elementParent = props.element.isWrapper || props.element.element_type === 'contentRoot' ? props.element : props.element.parent
			elementParent.addChild(config)

			hideAddElementsPopup()

		}

		// Lifecycle
		onMounted(() => {
			searhInputEl.value.focus()
		})

		return {
			// Normal values
			elementCategories,
			// Refs
			searchKeyword,
			categoryValue,
			searhInputEl,
			// Computed
			visibleElements,
			// Methods
			onAddElement
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
