<template>
	<div class="znpb-admin-post-types-tab" >

		<span class="znpb-admin-post-types-tab__title">{{post.name}}</span>

		<InputCheckbox class="znpb-admin-checkbox-wrapper" :rounded="true" v-model="isActive"></InputCheckbox>

	</div>

</template>

<script>
import { mapGetters, mapActions } from 'vuex'

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
		...mapGetters([
			'getAllowedPosts'
		]),
		isActive: {
			get () {
				return this.getAllowedPosts.includes(this.post.id)
			},
			set (newValue) {
				// Save the value
				if (newValue) {
					// Add post type
					this.addAllowedPostType(this.post.id)
				} else {
					// Remove post type
					this.deleteAllowedPostType(this.post.id)
				}
			}
		}

	},
	methods: {
		...mapActions([
			'addAllowedPostType',
			'deleteAllowedPostType'
		])
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
