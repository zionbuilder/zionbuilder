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
			<HorizontalAccordion
				v-for="(classItem, index) in filteredClasses"
				v-bind:key="index"
				:show-trigger-arrow="false"
				:has-breadcrumbs="false"
				@expand="onItemSelected"
				@collapse="onItemCollapsed"
				class="znpb-global-css-classes__accordion-wrapper"
				:ref="el => { if (el) horizontalAccordion[index] = el }"
			>
				<template v-slot:header>
					<SingleClass
						:class-item="classItem"
						@click="activeClass=classItem"
						@delete-class="deleteClass($event)"
						@edit-class="activeClass=classItem"
					/>
				</template>
				<SingleClassOptions
					:class-item="classItem"
					@update:modelValue="saveClass"
					@update:modelValue-classname="saveClass"
				/>

			</HorizontalAccordion>
		</template>
		<div
			v-else
			class="znpb-class-selector-noclass"
		>{{$translate('no_global_class_found')}}
		</div>
	</div>

</template>
<script>
import { ref, computed, inject, onBeforeUnmount } from 'vue'
import SingleClass from './SingleClass.vue'
import SingleClassOptions from './SingleClassOptions.vue'
import { useCSSClasses } from '@composables'

export default {
	name: 'GlobalClasses',
	components: {
		SingleClass,
		SingleClassOptions
	},
	setup (props) {
		const { CSSClasses, getClassesByFilter, removeCSSClass, updateCSSClass } = useCSSClasses()
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

		function saveClass (newValues) {
			updateCSSClass(activeClass.value.id, newValues)
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
			updateCSSClass,
			horizontalAccordion,
			// Methods
			onItemSelected,
			onItemCollapsed,
			saveClass,
			deleteClass
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
</style>
