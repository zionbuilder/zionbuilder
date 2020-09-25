<template>
	<div class="znpb-admin-content-wrapper znpb-permissions-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left">
		</div>
		<div class="znpb-admin-content__permission-container">
			<PageTemplate>
				<Loader v-if="!loaded" />
				<template v-else>
					<div class="znpb-admin-role-manager-wrapper">
						<h3>{{$translate('role_manager')}}</h3>
						<template>
							<SingleRole
								v-for="(role,i) in getUserRoles"
								:key=i
								:data="role"
							/>
						</template>
					</div>
				</template>
				<template slot="right">
					<p class="znpb-admin-info-p">
						{{$translate('manage_users_permissions')}}
					</p>
				</template>
			</PageTemplate>

			<PageTemplate>
				<UpgradeToPro
					v-if="!isPro"
					:message_link="proLink"
					:message_title="$translate('manage_users_permissions_title')"
					:message_description="$translate('manage_users_permissions_free')"
				/>
				<template v-else-if="loaded">
					<div class="znpb-admin-user-specific-wrapper">
						<h3>{{$translate('user_specific')}}</h3>
						<EmptyList
							v-if="Object.entries(getUserPermissions).length === 0"
							@click.native="showModal=true"
						>{{$translate('no_user_added')}}</EmptyList>
						<SingleUser
							v-for="(permissions, userId) in getUserPermissions"
							:key="userId"
							:user-id="parseInt(userId)"
							:permissions="permissions"
							@delete-permission="deleteUser($event)"
						/>
					</div>
					<div class="znpb-admin-user-specific-actions">
						<BaseButton
							@click.native="showModal=true"
							type="secondary"
						>
							<span class="znpb-add-element-icon"></span>
							{{$translate('add_user')}}
						</BaseButton>
						<Modal
							:show.sync="showModal"
							:width="560"
							:title="$translate('add_user')"
							:fullscreen="true"
							:show-backdrop="false"
						>
							<AddUserModalContent @close-modal="showModal=false" />
						</Modal>
					</div>
				</template>
				<template slot="right">
					<p class="znpb-admin-info-p">
						{{$translate('manage_wordpress_users_permisions')}}
					</p>
				</template>
			</PageTemplate>
		</div>
	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SingleRole from './SingleRole.vue'
import SingleUser from './SingleUser.vue'
import AddUserModalContent from './AddUserModalContent.vue'
import { BaseButton, Loader, Modal, UpgradeToPro } from '@zb/components'

export default {
	name: 'permissions',
	components: {
		SingleRole,
		SingleUser,
		AddUserModalContent,
		UpgradeToPro,
		BaseButton,
		Loader,
		Modal
	},
	data () {
		return {
			loaded: false,
			showModal: false,
			proLink: null
		}
	},
	computed: {
		...mapGetters([
			'getUserRoles',
			'getUserPermissions',
			'isPro'
		])
	},
	methods: {
		...mapActions([
			'fetchUsersById',
			'editUserPermission',
			'deleteUserPermission'
		]),

		deleteUser (value) {
			this.deleteUserPermission(value)
		}
	},
	created () {
		// Fetch system information from rest api
		const userIds = Object.keys(this.getUserPermissions)

		if (userIds.length > 0) {
			const activeUsersData = this.fetchUsersById(userIds)

			activeUsersData.finally((result) => {
				this.loaded = true
			})
		} else {
			this.loaded = true
		}
	}
}
</script>

<style lang="scss" >
.znpb-admin-content__permission-container {
	display: block;
	.znpb-admin-content--center {
		flex: 0;
	}
	.znpb-admin-content__center {
		// flex-basis: 100%;
		flex-grow: 2;
	}
	.znpb-admin-content__right {
		flex-grow: 0;
	}
}
.znpb-admin-role-manager-wrapper {
	position: relative;
	width: 100%;
	padding-bottom: 30px;
	border-bottom: 1px solid $surface-variant;
}
.znpb-permissions-wrapper {
	.znpb-admin-content {
		&__center {
			padding-bottom: 0;
		}
	}
}

.znpb-admin-user-specific-wrapper {
	margin-bottom: 30px;
	.znpb-empty-list__container {
		color: $surface-headings-color;
	}
}

.znpb-admin-user-specific-actions {
	.znpb-button {
		float: right;
		margin-bottom: 30px;
	}
	.znpb-add-element-icon {
		display: inline-block;
		width: 10px;
		height: 10px;
		&:before {
			height: 10px;
		}
		&:after {
			width: 10px;
		}
	}
}
</style>
