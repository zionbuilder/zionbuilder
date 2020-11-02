<template>
	<li
		@click.self="addNewUser(user)"
		class="znpb-baseselect-list__option znpb-add-specific-permissions__list-item"
	>
		{{user.name}}

		<Tooltip
			v-if="checkUser(user)"
			:content="$translate('user_has_permissions_remove')"
		>
			<Icon
				icon="delete"
				@click="deletePermission(user)"
			/>
		</Tooltip>

		<Loader v-if="loadingDelete" />

	</li>
</template>

<script>

import { Icon, Tooltip, Loader } from '@zb/components'
import { saveOptions, getUsersById } from '@zionbuilder/rest'

export default {
	name: 'ModalListItem',
	components: {
		Icon,
		Tooltip,
		Loader
	},
	props: {
		user: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			loadingDelete: false
		}
	},

	methods: {
		addNewUser (user) {
			if (this.checkUser(user)) {
				// check if user already has permissions added
				return
			}
			// Add user data to users object
			this.$zb.users.add(user)

			// Add default User permissions to user permissions object
			this.$zb.options.editUserPermission({
				role: user.id,
				value: {
					allowed_access: true,
					permissions: {
						only_content: false,
						features: [],
						post_types: []
					}
				}
			})
			this.$emit('close-modal', true)
		},
		checkUser (user) {
			// create variables for id, showDelete
			let id = null
			let showDelete = false
			if (getUsersById(user.id)) {
				// get the id of the current list item
				id = user.id
			}

			if (this.$zb.options.getUserPermissions.hasOwnProperty(id)) {
				showDelete = true
			} else showDelete = false

			return showDelete
		},

		deletePermission (role) {
			this.loadingDelete = true
			this.$zb.options.deleteUserPermission(role.id)

			saveOptions()
				.finally(() => {
					this.loadingDelete = false
				})
		}
	}
}
</script>

<style lang="scss">
.znpb-add-specific-permissions__list-item {
	position: relative;
	span:first-child {
		display: flex;
	}
	.znpb-admin-small-loader {
		position: relative;
		top: auto;
		right: 0;
		left: auto;
		width: 14px;
		height: 14px;
		margin-right: 0;
	}
}
</style>
