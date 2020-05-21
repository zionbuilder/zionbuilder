<template>
	<li
		class="znpb-editor-library-modal-category__item"
		:class="{'znpb-editor-library-modal-category__item--active': isActive}"
		@click.stop="$emit('activate-subcategory', subcategory)"
	>
		<h5>{{subcategory.name}}</h5>
		<span
			v-if="showCount"
			class="znpb-editor-library-modal-category__number"
		>
			{{subcategory.count}}
		</span>
	</li>

</template>
<script>

export default {
	name: 'CategoriesLibraryItem',
	props: {
		subcategory: {
			type: Object,
			required: false
		},
		isActive: {
			type: Boolean,
			required: false
		},
		showCount: {
			type: Boolean,
			required: false
		}
	},
	data () {
		return {

		}
	},
	computed: {

	},
	methods: {
	}
}
</script>
<style lang="scss">
.znpb-editor-library-modal-category__item {
	&.znpb-editor-library-modal-category__item--active {
		color: $surface-active-color;
		font-weight: 500;
		h5 {
			font-weight: 500;
		}
	}
}
</style>
