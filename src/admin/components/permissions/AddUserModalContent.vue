<template>
	<div class="znpb-add-specific-permissions-wrapper znpb-fancy-scrollbar">
		<p class="znpb-add-specific-description">{{ $translate('search_description') }}</p>
		<div class="znpb-admin__google-fonts-modal-search">
			<BaseInput
				ref="searchInput"
				v-model="keyword"
				:placeholder="$translate('search_for_users')"
				:icon="!loading ? 'search' : null"
				size="big"
			>
				<template #suffix>
					<Loader v-if="loading" />
				</template>
			</BaseInput>

			<ul v-if="keyword.length > 2" class="znpb-baseselect-list znpb-fancy-scrollbar">
				<ModalListItem v-for="(user, i) in users" :key="i" :user="user" @close-modal="$emit('close-modal', true)" />
			</ul>

			<span v-if="!loading && users.length === 0 && keyword.length > 2" class="znpb-not-found-message">{{
				$translate('no_result')
			}}</span>
		</div>
	</div>
</template>

<script>
import { nextTick, watch, ref } from 'vue';
import { searchUser } from '@/common/api';

// Components
import ModalListItem from './ModalListItem.vue';

export default {
	name: 'AddUserModalContent',
	components: {
		ModalListItem,
	},
	setup() {
		const searchInput = ref(null);
		const keyword = ref('');
		const loading = ref(false);
		const users = ref([]);

		nextTick(() => searchInput.value.focus());

		watch(keyword, (newValue, oldValue) => {
			if (newValue.length > 2) {
				loading.value = true;

				searchUser(newValue)
					.then(result => {
						users.value = result.data;
					})
					.finally(() => {
						loading.value = false;
					});
			} else if (newValue.length === 0) {
				users.value = [];
				loading.value = false;
			}
		});

		return {
			searchInput,
			keyword,
			loading,
			users,
		};
	},
};
</script>

<style lang="scss">
.znpb-add-specific-permissions-wrapper {
	overflow-y: auto;
	padding: 30px;
	.znpb-baseselect-list__option {
		display: flex;
		justify-content: space-between;
		align-items: center;

		.znpb-editor-icon-wrapper:hover {
			color: var(--zb-primary-color);
		}
	}
}
.znpb-add-specific-description {
	margin-top: 0;
	margin-bottom: 16px;
	color: var(--zb-surface-text-color);
	font-size: 13px;
	font-weight: 400;
	line-height: 1;
}
.znpb-admin__google-fonts-modal-search {
	position: relative;
	margin-bottom: 0;
}
.znpb-baseselect-list {
	background: var(--zb-dropdown-bg-color);
	box-shadow: 0 0 16px 0 rgba(0, 0, 0, 0.08);
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
	transform: translateY(5px);
	&__option {
		position: relative;
		padding: 18px 20px;
		margin-bottom: 0;
		color: var(--zb-surface-text-color);
		&:hover {
			color: var(--zb-dropdown-text-active-color);
			cursor: pointer;
		}
		&.znpb-select-option-selected {
			color: var(--zb-dropdown-text-active-color);
		}
	}
}

.znpb-admin-small-loader {
	top: 20px;
	right: 20px;
	left: auto;
	width: 14px;
	height: 14px;
	&:before,
	&:after {
		border: 2px solid var(--zb-surface-border-color);
	}
	&:after {
		border-right-color: var(--zb-surface-border-color);
	}
}
</style>
