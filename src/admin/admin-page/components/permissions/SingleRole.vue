<template>
	<div class="znpb-admin-user-template">
		<UserTemplate
			:permission="permissionsNumber"
			@edit-permission="showModal=true"
		>
			{{data.name}}
		</UserTemplate>
		<modal
			:show.sync="showModal"
			:width="560"
			:title="data.name + ' ' + $translate('permissions')"
			:fullscreen="true"
			:show-backdrop="false"
		>
			<UserModalContent
				:permissions="permissionConfig"
				@edit-role="editRole($event)"
			/>
		</modal>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

import UserModalContent from './UserModalContent.vue'
import UserTemplate from './UserTemplate.vue'

export default {
	name: 'SingleRole',
	components: {
		UserTemplate,
		UserModalContent
	},
	props: {
		data: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			showModal: false
		}
	},
	computed: {
		...mapGetters(['getPermissions']),
		permissionConfig () {
			return this.getPermissions(this.data.id)
		},
		permissionsNumber () {
			let permNumber = []
			if (this.permissionConfig.allowed_access === false) {
				return 0
			} else {
				if (this.permissionConfig.permissions.only_content === true) {
					permNumber.push('only_content')
				}
				for (let i in this.permissionConfig.permissions.features) {
					permNumber.push(this.permissionConfig.permissions.features[i])
				}
				for (let i in this.permissionConfig.permissions.post_types) {
					permNumber.push(this.permissionConfig.permissions.post_types[i])
				}

				return permNumber.length
			}
		}
	},
	methods: {
		...mapActions(['editUserRole']),
		editRole (value) {
			let role = this.data.id
			this.editUserRole({ role, value })
		}
	}
}
</script>
