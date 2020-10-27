<template>

	<div class="znpb-global-css-classes__wrapper">

		<BaseInput
			class="znpb-global-css-classes__search"
			v-model="keyword"
			:filterable="true"
			icon="search"
			:clearable="true"
			placeholder="Search for a class"
			ref="input"
		/>

		<template v-if="filteredClasses.length">
			<HorizontalAccordion
				v-for="(classItem, index) in filteredClasses"
				v-bind:key="index"
				:show-trigger-arrow="false"
				:has-breadcrumbs="false"
				@expand="onItemSelected"
				@collapse="onItemCollapsed"
				class="znpb-global-css-classes__accordion-wrapper"
				ref="horizontalAccordion"
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
					@input-classname="saveClass"
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
import SingleClass from './SingleClass.vue'
import SingleClassOptions from './SingleClassOptions.vue'
import { useCSSClasses } from '@data'

export default {
	name: 'GlobalClasses',
	inject: ['parentAccordion'],
	components: {
		SingleClass,
		SingleClassOptions
	},
	setup() {
		const { CSSClasses, getClassesByFilter, removeCSSClass, updateCSSClass } = useCSSClasses()

		return {
			CSSClasses,
			removeCSSClass,
			getClassesByFilter,
			updateCSSClass
		}
	},
	computed: {
		filteredClasses () {
			if (this.keyword.length === 0) {
				return this.CSSClasses.value
			} else {
				return this.getClassesByFilter(this.keyword)
			}
		}
	},
	data () {
		return {
			keyword: '',
			activeClass: null,
			breadCrumbConfig: {
				title: null,
				previousCallback: this.closeAccordion
			}
		}
	},
	methods: {
		onItemSelected () {
			this.breadCrumbConfig.title = this.activeClass.name
			this.parentAccordion.addBreadcrumb(this.breadCrumbConfig)
		},
		onItemCollapsed () {
			this.breadCrumbConfig.title = null
			this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
		},
		deleteClass (classItem) {
			this.removeCSSClass(classItem)
		},
		saveClass (newValues) {
			this.updateClassSettings({
				classId: this.activeClass.id,
				newValues
			})
		},
		closeAccordion () {
			// Find the expanded accordion from ref
			const activeAccordion = this.$refs.horizontalAccordion.find((accordion) => {
				return accordion.localCollapsed
			})
			if (activeAccordion) {
				activeAccordion.closeAccordion()
			}
		}
	},
	beforeUnmount () {
		if (this.activeClass) {
			this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
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
