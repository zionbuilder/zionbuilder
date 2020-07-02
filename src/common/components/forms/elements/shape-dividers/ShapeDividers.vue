<template>

	<div>
		<shape
			class="znpb-active-shape-preview"
			:shape="valueModel"
			:class="[{'mask-active': value !== undefined}]"
		>
			<EmptyList
				class="znpb-style-shape__empty"
				v-if="!value || value === undefined"
				:no-margin="true"
			>
				{{$translate('select_shape')}}
			</EmptyList>
			<BaseIcon
				v-else
				:rounded="true"
				bgColor="#fff"
				icon="check"
				:bgSize="20"
			/>
		</shape>

		<shape
			v-for="(shape,i) in freeShapes"
			:key="i"
			:shape="shape"
			@click.native="valueModel=shape"
		></shape>
	</div>

</template>

<script>
import Shape from './Shape.vue'
import EmptyList from '@/common/components/forms/elements/empty-list/EmptyList'
export default {
	name: 'ShapeDividers',
	components: {
		EmptyList,
		Shape
	},
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: String
		}

	},
	data () {
		return {
			freeShapes: ['shape-oblique', 'shape-double', 'shape-oblique-mirror', 'shape-curved-mirror', 'shape-split', 'shape-wavy']
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.value
			},
			set (newValue) {
				/**
					 *It emits the new value
					 */
				this.$emit('input', newValue)
			}
		}
	},
	methods: {

	}
}
</script>
<style lang="scss">
</style>
