<template>
	<div class="znpb-admin-post-types-tab" >

		<span class="znpb-admin-post-types-tab__title">{{post.name}}</span>

		<InputCheckbox class="znpb-admin-checkbox-wrapper" :rounded="true" v-model="isActive"></InputCheckbox>

	</div>

</template>

<script>
export default {
	name: 'PostTypeTab',
	props: {
		post: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			loaded: false
		}
	},
	computed: {
		isActive: {
			get () {
				return this.$zb.options.options['allowed_post_types'].includes(this.post.id)
			},
			set (newValue) {
				// Save the value
				if (newValue) {
					// Add post type
					this.$zb.options.addOptionValue('allowed_post_types', this.post.id)
				} else {
					// Remove post type
					this.$zb.options.deleteOptionValue('allowed_post_types', this.post.id)
				}
			}
		}

	},
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
