<template>
	<div class="znpb-admin-content-wrapper znpb-permissions-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left znpb-admin-content--hiddenXs"></div>
		<div class="znpb-admin-content__permission-container">
			<PageTemplate>
				<Loader v-if="loading" />
				<template v-else>
					<div class="znpb-admin-role-manager-wrapper">
						<h3>{{ $translate('role_manager') }}</h3>
						<SingleRole v-for="(role, i) in dataSets.user_roles" :key="i" :role="role" />
					</div>
				</template>
				<template #right>
					<p class="znpb-admin-info-p">
						{{ $translate('manage_users_permissions') }}
					</p>
				</template>
			</PageTemplate>

			<PageTemplate>
				<UpgradeToPro
					v-if="!isPro"
					:info_text="proLink"
					:message_title="$translate('manage_users_permissions_title')"
					:message_description="$translate('manage_users_permissions_free')"
				/>
				<template v-else-if="!loading">
					<div class="znpb-admin-user-specific-wrapper">
						<h3>{{ $translate('user_specific') }}</h3>

						<EmptyList v-if="Object.entries(userPermissions).length === 0" @click="showModal = true">{{
							$translate('no_user_added')
						}}</EmptyList>

						<SingleUser
							v-for="(permissions, userId) in userPermissions"
							:key="userId"
							:user-id="parseInt(userId)"
							:permissions="permissions"
						/>
					</div>
					<div class="znpb-admin-user-specific-actions">
						<Button type="secondary" @click="showModal = true">
							<span class="znpb-add-element-icon"></span>
							{{ $translate('add_user') }}
						</Button>
						<Modal
							v-model:show="showModal"
							class="znpb-admin-permissions-modal"
							:width="560"
							:title="$translate('add_user')"
							:show-backdrop="false"
						>
							<AddUserModalContent @close-modal="showModal = false" />
						</Modal>
					</div>
				</template>
				<template #right>
					<p class="znpb-admin-info-p">
						{{ $translate('manage_wordpress_users_permisions') }}
					</p>
				</template>
			</PageTemplate>
		</div>
	</div>
</template>

<script>
import { ref } from 'vue';
import { useBuilderOptions, useUsers, useDataSets } from '@common/composables';

// Components
import SingleRole from './SingleRole.vue';
import SingleUser from './SingleUser.vue';
import AddUserModalContent from './AddUserModalContent.vue';

export default {
	name: 'Permissions',
	components: {
		SingleRole,
		SingleUser,
		AddUserModalContent,
	},
	setup() {
		const isPro = window.ZnPbAdminPageData.is_pro_active;
		const { fetchUsersData } = useUsers();
		const { getOptionValue } = useBuilderOptions();

		const { dataSets } = useDataSets();
		const userPermissions = getOptionValue('users_permissions');
		const loading = ref(true);
		const showModal = ref(false);
		const proLink = ref(null);

		// Fetch system information from rest api
		const userIds = Object.keys(userPermissions);

		if (userIds.length > 0) {
			fetchUsersData(userIds).finally(result => {
				loading.value = false;
			});
		} else {
			loading.value = false;
		}

		return {
			isPro,
			dataSets,
			userPermissions,
			loading,
			showModal,
			proLink,
		};
	},
};
</script>

<style lang="scss">
.znpb-admin-permissions-modal {
	& > .znpb-modal__wrapper--full-size {
		width: 100%;
		height: 90%;
	}

	& > .znpb-modal__wrapper {
		width: calc(100% - 40px);
	}
}
.znpb-admin-content__permission-container {
	display: block;
	width: 100%;
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
	border-bottom: 1px solid var(--zb-surface-border-color);
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
		color: var(--zb-surface-text-color);
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
