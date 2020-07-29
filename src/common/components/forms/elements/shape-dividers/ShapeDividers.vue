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
			<span
				v-else
				class="znpb-active-shape-preview__action"
				@mouseover="showDelete=true"
				@mouseleave="showDelete=false"
			>
				<transition
					name="slide-fade"
					mode="out-in"
				>
					<BaseIcon
						v-if="!showDelete"
						icon="check"
						:size="10"
						key=1
					/>

					<BaseIcon
						key=2
						v-else
						icon="close"
						:size="10"
						@click.native.stop="valueModel=undefined,showDelete=false"
					/>
				</transition>
			</span>
		</shape>
		<div class="znpb-shape-list znpb-fancy-scrollbar">
			<shape
				v-for="(shape,i) in freeShapes"
				:key="i"
				:shape="shape"
				@click.native="valueModel=shape"
			></shape>
		</div>
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
			showDelete: false,
			freeShapes: ['shape-oblique', 'shape-double', 'shape-oblique-mirror', 'shape-curved-mirror', 'shape-split', 'shape-wavy', 'shape-oblique-top', 'shape-double-top', 'shape-oblique-mirror-top', 'shape-curved-mirror-top', 'shape-split-top', 'shape-wavy-top']
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
.znpb-shape-list {
	display: flex;
	flex-direction: column;
	max-height: 400px;
	padding: 20px;
	margin: 0 -20px;
	background-color: #f1f1f1;
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-active, .slide-fade-leave-active {
	transition: all .1s;
}

.slide-fade-enter, .slide-fade-leave-to {
	opacity: 0;
}
</style>
