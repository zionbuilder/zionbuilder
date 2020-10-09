<template>
	<div
		class="znpb-admin-user-template"
		v-if="userData"
	>
		<UserTemplate
			:permission="permissionsNumber"
			:has-delete="true"
			@edit-permission="showModal=true"
			@delete-permission="deletePermission($event)"
		>
			{{userData.name}}
		</UserTemplate>
		<Modal
			v-model:show="showModal"
			:width="560"
			:title="userData.name + ' ' + $translate('permissions')"
			:fullscreen="true"
			:show-backdrop="false"
		>
			<UserModalContent
				:permissions="permissions"
				@edit-role="editRole($event)"
			/>
		</Modal>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

import UserModalContent from './UserModalContent.vue'
import UserTemplate from './UserTemplate.vue'
import { Modal } from '@zionbuilder/components'
// import { getUsersById } from '@zionbuilder/rest'
export default {
	name: 'SingleUser',
	components: {
		UserModalContent,
		UserTemplate,
		Modal
	},
	props: {
		permissions: {
			type: Object,
			required: true
		},
		userId: {
			type: Number,
			required: true
		}
	},
	data () {
		return {
			showModal: false,
			userData: {}
		}
	},
	created () {
		Promise(this.$zb.users.getUsersById(this.userId)).then((response) => {
			console.log(response.data)
			this.userData = response.data
		})
	},
	computed: {

		permissionsNumber () {
			let permNumber = []
			if (this.permissions.allowed_access === false) {
				return 0
			} else {
				if (this.permissions.permissions.only_content === true) {
					permNumber.push('only_content')
				}
				for (let i in this.permissions.permissions.features) {
					permNumber.push(this.permissions.permissions.features[i])
				}
				for (let i in this.permissions.permissions.post_types) {
					permNumber.push(this.permissions.permissions.post_types[i])
				}

				return permNumber.length
			}
		}
	},
	methods: {
		...mapActions(['editUserPermission']),
		editRole (value) {
			let role = this.userId
			this.editUserPermission({ role, value })
		},
		deletePermission (value) {
			let role = this.userId
			this.$emit('delete-permission', role)
		}
	}
}
</script>
