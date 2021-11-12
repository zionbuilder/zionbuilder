<template>

	<div class="znpb-global-css-classes__wrapper">

		<div class="znpb-global-css-classes__search">
			<BaseInput
				v-model="keyword"
				:filterable="true"
				icon="search"
				:clearable="true"
				placeholder="Search for a class"
				ref="input"
			/>
		</div>

		<template v-if="filteredClasses.length">
			<OptionsForm
				:schema="schema"
				v-model="value"
				class="znpb-globalCSSClassesOptionsForm"
			/>
		</template>
		<div
			v-else
			class="znpb-class-selector-noclass"
		>{{$translate('no_global_class_found')}}
		</div>

		<AddSelector
			@add-selector="onSelectorAdd"
			type="class"
		>
			<template v-slot="{actions}">
				<Button
					type="line"
					class="znpb-class-selectorAddButton"
					@click="actions.toggleModal()"
				>
					{{$translate('add_css_class')}}
				</Button>

			</template>
		</AddSelector>
	</div>

</template>
<script>
import { ref, computed, inject, onBeforeUnmount } from 'vue'
import { useCSSClasses } from '@composables'

import AddSelector from '../common/AddSelector.vue'

export default {
	name: 'GlobalClasses',
	components: {
		AddSelector
	},
	setup (props) {
		const { CSSClasses, addCSSClass, getClassesByFilter, removeCSSClass, setCSSClasses, removeAllCssClasses } = useCSSClasses()
		const keyword = ref('')
		const activeClass = ref(null)
		const breadCrumbConfig = ref({
			title: null,
			previousCallback: closeAccordion
		})
		const horizontalAccordion = ref([])

		const parentAccordion = inject('parentAccordion')

		const filteredClasses = computed(() => {
			if (keyword.value.length === 0) {
				return CSSClasses.value
			} else {

				return getClassesByFilter(keyword.value)
			}
		})

		const schema = computed(() => {
			const schema = {}
			const selectors = filteredClasses.value || []

			selectors.forEach(cssClassConfig => {
				const { id, title } = cssClassConfig
				schema[id] = {
					type: 'css_selector',
					title: title,
					allow_class_assignments: false,
					show_changes: false
				}
			});

			return schema
		})

		const value = computed({
			get () {
				const modelValue = {}
				const existingCSSClasses = CSSClasses.value
				existingCSSClasses.forEach(cssClassConfig => {
					const { id } = cssClassConfig
					modelValue[id] = cssClassConfig
				})

				return modelValue
			},
			set (newValue) {
				if (null === newValue) {
					removeAllCssClasses()
				} else {
					const classes = []

					Object.keys(newValue).forEach(selectorId => {
						const selectorValue = newValue[selectorId]
						classes.push(selectorValue)
					})
					setCSSClasses(classes)
				}
			}
		})

		function onItemSelected () {
			breadCrumbConfig.value.title = activeClass.value.name
			parentAccordion.addBreadcrumb(breadCrumbConfig.value)
		}

		function onItemCollapsed () {
			breadCrumbConfig.value.title = null
			parentAccordion.removeBreadcrumb(breadCrumbConfig.value)
		}

		function deleteClass (classItem) {
			removeCSSClass(classItem)
		}

		function closeAccordion () {
			// Find the expanded accordion from ref
			const activeAccordion = horizontalAccordion.value.find((accordion) => {
				return accordion.localCollapsed
			})

			if (activeAccordion) {
				activeAccordion.closeAccordion()
			}
		}

		function onSelectorAdd (config) {
			addCSSClass({
				id: config.selector,
				name: config.title
			})
		}

		// Lifecycle
		onBeforeUnmount(() => {
			if (activeClass.value) {
				parentAccordion.removeBreadcrumb(breadCrumbConfig.value)
			}
		})

		return {
			// Computed
			filteredClasses,
			keyword,
			activeClass,
			breadCrumbConfig,
			CSSClasses,
			removeCSSClass,
			getClassesByFilter,
			horizontalAccordion,
			// Methods
			onItemSelected,
			onItemCollapsed,
			deleteClass,
			schema,
			value,
			onSelectorAdd
		}
	}
}
</script>
<style lang="scss">
.znpb-global-css-classes {
	&__search {
		margin-bottom: 20px;
	}
}

.znpb-globalCSSClassesOptionsForm {
	padding: 0;

	& > .znpb-input-type--css_selector {
		padding: 0;
	}
}

.znpb-input-type--css_selector {
	padding-bottom: 0;
}

.znpb-class-selector-noclass {
	text-align: center;
}

.znpb-class-selectorAddButton {
	width: 100%;
	margin-top: 10px;
}
</style>
