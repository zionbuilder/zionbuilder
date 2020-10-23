<template>
	<div class="znpb-admin-post-types-tab" >

		<span class="znpb-admin-post-types-tab__title">{{post.name}}</span>

		<InputCheckbox class="znpb-admin-checkbox-wrapper" :rounded="true" v-model="isActive"></InputCheckbox>

	</div>

</template>

<script>
import { computed, inject,} from 'vue'
export default {
	name: 'PostTypeTab',
	props: {
		post: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const $zb = inject('$zb')
		let isActive = computed({
				get: () => {
					return $zb.options.getOptionValue('allowed_post_types').includes(props.post.id)
				},
				set: val => {
					if (val) {
						// Add post type
						$zb.options.addOptionValue('allowed_post_types', props.post.id)
					} else {
						// Remove post type
						$zb.options.deleteOptionValue('allowed_post_types', props.post.id)
					}
				}
		})
		return {
			isActive,
			post: props.post
		}
	}

}
</script>
<style lang="scss">
.znpb-admin-post-types-tab {
	@extend %list-item-helper;
	padding: 17px 20px;

	&__title {
		color: $surface-active-color;
		font-weight: 500;
	}
}

.znpb-admin-checkbox-wrapper {
	width: 24px;
	height: 24px;
}
</style>
