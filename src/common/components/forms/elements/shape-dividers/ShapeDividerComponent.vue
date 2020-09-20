<template>
	<div>
		<shape
			class="znpb-active-shape-preview"
			:shapePath="value"
			:class="[{'mask-active': value}]"
			:position="position"
		>
			<EmptyList
				class="znpb-style-shape__empty"
				v-if="!value"
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
						@click.native.stop="$emit('input', null),showDelete=false"
					/>
				</transition>
			</span>
		</shape>
		<div class="znpb-shape-list znpb-fancy-scrollbar">
			<shape
				v-for="(shape,i) in shapes"
				:key="i"
				:shape-path="shape"
				:position="position"
				@click.native="$emit('input', shape)"
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
import BaseIcon from '@/common/components/BaseIcon.vue'
import Shape from './Shape.vue'
import EmptyList from '@/common/components/forms/elements/empty-list/EmptyList'
import UpgradeToPro from '@/editor/manager/options/UpgradeToPro/UpgradeToPro.vue'
import { mapGetters } from 'vuex'
export default {
	name: 'ShapeDividerComponent',
	components: {
		EmptyList,
		Shape,
		UpgradeToPro,
		BaseIcon
	},
	props: {
		/**
		 * Position
		 */
		position: {
			type: String
		},
		value: {
			type: String
		}
	},
	data () {
		return {
			showDelete: false
		}
	},
	computed: {
		...mapGetters([
			'isPro',
			'getMasks'
		]),
		shapes () {
			return Object.values(this.getMasks)
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
.slide-fade-enter-active, .slide-fade-leave-active {
	transition: all .1s;
}

.slide-fade-enter, .slide-fade-leave-to {
	opacity: 0;
}
</style>
