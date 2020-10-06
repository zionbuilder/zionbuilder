<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
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
				@keyup.stop="onSearchKeyword"
				autocomplete="off"
				ref="searchInput"
			>

			</BaseInput>
		</div>
		<div
			v-if="searchKeyword.length > 2"
			class="znpb-fancy-scrollbar"
		>
			<div class="znpb-wrapper-category">
				<ElementList
					v-if="getVisibleElements.length > 0"
					:elements="getVisibleElements"
					tag="div"
				/>
				<div v-else>{{$translate('no_elements_found')}}</div>
			</div>
		</div>
		<div
			v-else
			class="znpb-fancy-scrollbar"
		>
			<CategoriesElements
				:category="getActiveCat"
				tag="div"
			/>
		</div>
	</div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { BaseInput, InputSelect } from '@zb/components'
import { generateUID } from '@zb/utils'
import CategoriesElements from '../components/addElements/CategoriesElements.vue'
import ElementList from '../components/addElements/ElementList.vue'
import { on, off } from '@zb/hooks'

export default {
	name: 'AddElementsPopup',
	components: {
		CategoriesElements,
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
			categVal: 'all'

		}
	},
	computed: {
		...mapGetters([
			'getElementCategories',
			'getVisibleElements',
			'getElementById',
			'getElementData'
		]),

		getSelectOptions () {
			let options = this.getElementCategories
			let allElement = {
				id: 'all',
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
		},
		getActiveCat () {
			let activeCat = null
			this.getSelectOptions.forEach((cat) => {
				if (cat.id === this.categVal) {
					activeCat = cat
				}
			})
			return activeCat || this.getSelectOptions[0]
		}
	},
	created () {
		on('add-element', this.onElementAdded)
	},
	beforeUnmount () {
		off('add-element', this.onElementAdded)
	},
	methods: {
		...mapActions([
			'addElement',
			'filterElementsBySearch',
			'setShouldOpenAddElementsPopup',
			'setActiveElement',
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
			const elementModel = this.getElementById(event.detail.element_type)
			if (elementModel.wrapper) {
				this.setShouldOpenAddElementsPopup(true)
			}
			this.$emit('close-popper', true)

			// Check if we need to open the element options
			if (elementModel.auto_open) {
				this.setActiveElement(uid)
				this.$zb.panels.openPanel('PanelElementOptions')
			}
		},

		onSearchKeyword (event) {
			this.searchKeyword = event.target.value
			if (this.searchKeyword.length >= 2) {
				this.filterElementsBySearch(this.searchKeyword)
			} else {
				this.filterElementsBySearch(null)
			}
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
