<template>
	<div class="znpb-add-specific-permissions-wrapper znpb-fancy-scrollbar">
		<p class="znpb-add-specific-description">{{$translate('search_description')}}</p>
		<div class="znpb-admin__google-fonts-modal-search">
			<BaseInput
				v-model="keyword"
				:placeholder="$translate('search_for_users')"
				:icon="!loading ? 'search' : null"
				size="big"
				ref="searchInput"
			>
				<Loader
					v-if="loading"
					slot="suffix"
				/>
			</BaseInput>

			<ul v-if="keyword.length > 2" class="znpb-baseselect-list">
				<ModalListItem
					v-for="(user, i) in users"
					v-bind:key="i"
					:user="user"
					@close-modal="$emit('close-modal', true)"
				/>
			</ul>

			<span v-if="!loading && (users.length === 0) && (keyword.length > 2)" class="znpb-not-found-message">{{$translate('no_result')}}</span>
		</div>
	</div>
</template>

<script>

import { searchUser } from '@/api/Users'

import ModalListItem from './ModalListItem.vue'
export default {
	name: 'AddUserModalContent',
	data () {
		return {
			enteredValue: '',
			loading: false,
			users: []
		}
	},
	components: {
		ModalListItem
	},
	computed: {
		keyword: {
			get () {
				return this.enteredValue
			},
			set (newValue) {
				this.enteredValue = newValue
				if (newValue.length > 2) {
					this.loading = true

					searchUser(this.keyword).then((result) => {
						this.users = result.data
					}).finally(() => {
						this.loading = false
					})
				} else if (this.keyword.length === 0) {
					this.users = []
					this.loading = false
				}
				if (this.users.length > 0) {
					this.loading = false
				}
			}
		}
	},
	methods: {

	},
	created () {
		// Add focus on the input element when modal is created
		this.$nextTick(() => this.$refs.searchInput.focus())
	}
}
</script>

<style lang="scss" scoped>
.znpb-add-specific-permissions-wrapper {
	overflow-y: auto;
	padding: 30px;
	.znpb-baseselect-list__option {
		display: flex;
		justify-content: space-between;
		align-items: center;

		.znpb-editor-icon-wrapper:hover {
			color: $primary-color;
		}
	}
}
.znpb-add-specific-description {
	margin-top: 0;
	margin-bottom: 16px;
	color: $surface-headings-color;
	font-size: 13px;
	font-weight: 400;
	line-height: 1;
}
.znpb-admin__google-fonts-modal-search {
	position: relative;
	margin-bottom: 0;
}
.znpb-baseselect-list {
	background: $surface;
	box-shadow: 0 0 16px 0 rgba(0, 0, 0, .08);
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
	transform: translateY(5px);
	&__option {
		position: relative;
		padding: 18px 20px;
		margin-bottom: 0;
		color: $font-color;
		&:hover {
			color: $surface-active-color;
			cursor: pointer;
		}
		&.znpb-select-option-selected {
			color: $surface-active-color;
		}
	}
}

.znpb-admin-small-loader {
	top: 20px;
	right: 20px;
	left: auto;
	width: 14px;
	height: 14px;
	&:before, &:after {
		border: 2px solid lighten($surface-headings-color, 10%);
	}
	&:after {
		border-right-color: darken($surface-headings-color, 10%);
	}
}
</style>
