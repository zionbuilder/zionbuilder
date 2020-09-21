<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				slot="prepend"
				class="znpb-add-elements__filter-category"
				:options="getSelectOptions"
				:placeholder="getSelectOptions[0].name"
				v-model="categoryValue"
			/>
			<BaseInput
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				v-model="searchKeyword"
				:placeholder="$translate('search_elements')"
				:clearable="true"
				icon="search"
				@keyup.native.stop="onSearchKeyword"
				autocomplete="off"
				ref="searchInput"
			>

			</BaseInput>
		</div>
		<div
			class="znpb-fancy-scrollbar znpb-wrapper-category"
		>
			<ElementList
				:elements="visibleElements"
				tag="div"
			/>

			<div
				v-if="searchKeyword.length > 2 && visibleElements.length === 0"
			>{{$translate('no_elements_found')}}</div>
		</div>

	</div>
</template>
<script>
import { mapActions } from 'vuex'
import { BaseInput, InputSelect } from '@zb/components/forms'
import { generateUID } from '@zb/utils'
import ElementList from '@/editor/components/addElements/ElementList.vue'
import { getElement, getElements, getCategories } from '@zb/editor/elements'

export default {
	name: 'AddElementsPopup',
	components: {
		ElementList,
		BaseInput,
		InputSelect
	},
	props: {
		parentUid: {
			type: String,
			required: true
		},
		insertIndex: {
			type: Number,
			required: false
		},
		data: {
			type: Object
		}
	},

	data () {
		return {
			searchKeyword: '',
			categVal: null
		}
	},
	computed: {
		visibleElements () {
			const allElements = getElements()
			const keyword = this.searchKeyword
			const category = this.categVal

			if (keyword.length === 0 && !category) {
				return allElements
			}

			return allElements.filter(element => {
				const isInCategory = category ? element.category !== undefined && element.category === category : true
				const hasKeyword = keyword.length > 0 ? element.name.toLowerCase().indexOf(keyword.toLowerCase()) !== -1 || element.keywords.join().toLowerCase().indexOf(keyword.toLowerCase()) !== -1 : true

				return isInCategory && hasKeyword
			})
		},

		getSelectOptions () {
			let options = getCategories()
			let allElement = {
				id: null,
				name: 'All'
			}
			const newArray = [allElement].concat(options)
			return newArray
		},
		categoryValue: {
			get () {
				return this.categVal || this.getSelectOptions[0].name
			},
			set (newVal) {
				this.categVal = newVal
			}
		}
	},
	created () {
		window.zb.on('add-element', this.onElementAdded)
	},
	beforeDestroy () {
		window.zb.off('add-element', this.onElementAdded)
	},
	methods: {
		...mapActions([
			'addElement',
			'setShouldOpenAddElementsPopup',
			'setActiveElement',
			'openPanel'
		]),
		onElementAdded (event) {
			const uid = generateUID()

			let elementData = {
				element_type: event.detail.element_type,
				uid,
				...event.detail.extra_data
			}

			this.addElement({
				parentUid: this.parentUid,
				index: this.insertIndex,
				data: elementData
			})

			// Open the popup
			const elementModel = getElement(event.detail.element_type)
			if (elementModel.wrapper) {
				this.setShouldOpenAddElementsPopup(true)
			}
			this.$emit('close-popper', true)

			// Check if we need to open the element options
			if (elementModel.auto_open) {
				this.setActiveElement(uid)
				this.openPanel('PanelElementOptions')
			}
		},

		onSearchKeyword (event) {
			this.searchKeyword = event.target.value
		}
	},
	mounted () {
		setTimeout(() => {
			if (this.$refs.searchInput) {
				this.searchKeyword = ''
				this.$refs.searchInput.focus()
			}
		}, 100)
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
