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
				<template slot="header">
					<SingleClass
						:class-item="classItem"
						@click.native="activeClass=classItem"
						@delete-class="deleteClass($event)"
						@edit-class="activeClass=classItem"
					/>
				</template>
				<SingleClassOptions
					:class-item="classItem"
					@input="saveClass"
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
import { mapGetters, mapActions } from 'vuex'
import SingleClass from './SingleClass'
import SingleClassOptions from './SingleClassOptions'
import { BaseInput } from '@zb/components/forms'

export default {
	name: 'GlobalClasses',
	inject: ['parentAccordion'],
	components: {
		SingleClass,
		SingleClassOptions,
		BaseInput
	},
	computed: {
		...mapGetters([
			'getClassesByFilter',
			'getClasses'
		]),
		filteredClasses () {
			if (this.keyword.length === 0) {
				return this.getClasses
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
		...mapActions([
			'removeClass',
			'editClass',
			'updateClassSettings'
		]),
		onItemSelected () {
			this.breadCrumbConfig.title = this.activeClass.name
			this.parentAccordion.addBreadcrumb(this.breadCrumbConfig)
		},
		onItemCollapsed () {
			this.breadCrumbConfig.title = null
			this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
		},
		deleteClass (classItem) {
			this.removeClass(classItem)
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
	beforeDestroy () {
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
