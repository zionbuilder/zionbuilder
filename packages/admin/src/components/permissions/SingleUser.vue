<template>
	<div
		class="znpb-admin-user-template"
		v-if="userData"
	>
		<UserTemplate
			:permission="permissionsNumber"
			:has-delete="true"
			@edit-permission="showModal=true"
			@delete-permission="deleteUserPermission(userId)"
		>
			{{userData.name}}
		</UserTemplate>

		<Modal
			class="znpb-admin-permissions-modal"
			v-model:show="showModal"
			:width="560"
			:title="userData.name + ' ' + $translate('permissions')"
			:show-backdrop="false"
		>
			<UserModalContent
				:permissions="permissions"
				@edit-role="editUserPermission(userId, $event)"
			/>
		</Modal>
	</div>
</template>

<script>
import { computed } from 'vue'
import { useBuilderOptions, useUsers } from '@zionbuilder/composables'

// Components
import UserModalContent from './UserModalContent.vue'
import UserTemplate from './UserTemplate.vue'

export default {
	name: 'SingleUser',
	components: {
		UserModalContent,
		UserTemplate
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
	setup (props, { emit }) {
		const { getUserInfo } = useUsers()
		const { editUserPermission, deleteUserPermission } = useBuilderOptions()
		const userData = getUserInfo(props.userId)

		const permissionsNumber = computed(() => {
			let permNumber = 1
			if (props.permissions.allowed_access === false) {
				return 0
			} else {
				if (props.permissions.permissions.only_content === true) {
					permNumber++
				}
				for (let i in props.permissions.permissions.features) {
					permNumber++
				}
				for (let i in props.permissions.permissions.post_types) {
					permNumber++
				}

				return permNumber
			}
		})

		return {
			permissionsNumber,
			userData,
			editUserPermission,
			deleteUserPermission
		}

	}
}
</script>
