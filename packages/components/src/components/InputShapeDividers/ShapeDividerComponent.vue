<template>
	<div>
		<shape
			class="znpb-active-shape-preview"
			:shapePath="modelValue"
			:class="[{'mask-active': modelValue}]"
			:position="position"
		>
			<EmptyList
				class="znpb-style-shape__empty"
				v-if="!modelValue"
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
					<Icon
						v-if="!showDelete"
						icon="check"
						:size="10"
						key=1
					/>

					<Icon
						key=2
						v-else
						icon="close"
						:size="10"
						@click.stop="$emit('update:modelValue', null),showDelete=false"
					/>
				</transition>
			</span>
		</shape>
		<div class="znpb-shape-list znpb-fancy-scrollbar">
			<shape
				v-for="(shape,shapeID) in masks"
				:key="shapeID"
				:shape-path="shape"
				:position="position"
				@click="$emit('update:modelValue', shapeID)"
			></shape>

			<UpgradeToPro
				v-if="!isPro"
				:message_title="$translate('pro_masks_title')"
				:message_description="$translate('pro_masks_description')"
				:message_link="$translate('learn_more_about_pro')"
			/>
		</div>
	</div>

</template>

<script>
import { toRefs, onMounted, ref, readonly, inject } from 'vue'
import { Icon } from '../Icon'
import Shape from './Shape.vue'
import { EmptyList } from '../EmptyList'
import { UpgradeToPro } from '../UpgradeToPro'

export default {
	name: 'ShapeDividerComponent',
	components: {
		EmptyList,
		Shape,
		UpgradeToPro,
		Icon
	},
	props: {
		/**
		 * Position
		 */
		position: {
			type: String
		},
		modelValue: {
			type: String
		}
	},
	data () {
		return {
			showDelete: false
		}
	},
	setup () {
		const masks = inject('masks')
		const plugin_info = inject('plugin_info')

		return {
			masks,
			plugin_info
		}
	},
	computed: {
		isPro () {
			return this.plugin_info.is_pro_active
		}
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
.slide-fade-enter-to, .slide-fade-leave-from {
	transition: all .1s;
}

.slide-fade-enter-to, .slide-fade-leave-to {
	opacity: 0;
}
</style>
