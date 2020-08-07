<template>
	<div
		v-if="!loading"
		class="znpb-admin__wrapper"
		id="znpb-admin"
	>
		<div class="znpb-admin__header">
			<div class="znpb-admin__header-top">
				<div class="znpb-admin__header-logo">
					<img :src="logoUrl" />
					<ZionLabel
						v-if="isPro"
						:text="$translate('pro')"
						type="warning"
						class="znpb-option__upgrade-to-pro-label"
					/>
					<span class="znpb-admin__header-logo-version">v{{version}}</span>
				</div>
				<div class="znpb-admin__header-actions">
					<router-link
						to="/get-pro"
						class="znpb-button znpb-button--secondary"
						v-if="!isPro"
					>
						<BaseIcon icon="quality"></BaseIcon>
						{{$translate('upgrade_to_pro')}}
					</router-link>

					<a
						href="https://zionbuilder.io/help-center/"
						:title="$translate('documentation')"
						target="_blank"
						class="znpb-button znpb-button--line"
					>{{$translate('documentation')}}</a>

				</div>
			</div>
			<div class="znpb-admin__header-menu-wrapper">
				<div class="znpb-admin__header-menu">
					<router-link
						v-for="(menuItem, key) in menuItems"
						:key="key"
						:to="`${menuItem.path}`"
						class="znpb-admin__header-menu-item"
					>{{menuItem.title}}</router-link>
				</div>
			</div>
		</div>
		<router-view></router-view>

		<!-- notices -->
		<div class="znpb-admin-notices-wrapper">
			<Notice
				@close-notice="removeNotice(error)"
				v-for="(error, index) in getErrors"
				:error="error"
				:key="index"
			/>
			<OptionsSaveLoader />
		</div>
	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import ZionLabel from '@/editor/common/Label'
import OptionsSaveLoader from './components/OptionsSaveLoader.vue'

export default {
	name: 'App',
	data () {
		return {
			loading: true,
			hasError: false,
			savingOptions: false
		}
	},
	components: {
		ZionLabel,
		OptionsSaveLoader
	},
	computed: {
		...mapGetters([
			'get_options',
			'getErrors'
		]),
		menuItems () {
			var routes = []
			for (var i in this.$router.options.routes) {
				if (!this.$router.options.routes.hasOwnProperty(i)) {
					continue
				}
				var route = this.$router.options.routes[i]
				if (route.hasOwnProperty('title')) {
					routes.push(route)
				}
			}

			return routes
		},
		logoUrl () {
			return window.ZnPbAdminPageData.urls.logo
		},
		version () {
			return window.ZnPbAdminPageData.plugin_version
		},
		isPro () {
			return window.ZnPbAdminPageData.is_pro_active
		}
	},

	methods: {
		...mapActions([
			'fetchGoogleFonts',
			'fetchOptions',
			'removeNotice',
			'initialiseDataSets'
		])
	},
	created () {
		Promise.all([
			this.fetchGoogleFonts(),
			this.fetchOptions(),
			this.initialiseDataSets()
		]).then((values) => {
		}).catch(error => {
			this.hasError = true
			// eslint-disable-next-line
			console.error(error)
		}).finally(() => {
			this.loading = false
		})
	}
}
</script>

<style lang="scss">
@import "@/scss/admin.scss";

.znpb-admin {
	&__wrapper {
		position: relative;
		box-sizing: border-box;
		max-width: 80%;
		margin: 20px 0 40px 0;

		.znpb-admin__header-menu {
			.znpb-admin__header-menu-item {
				color: $font-color;
			}

			.router-link-active, .znpb-admin__header-menu-item:hover {
				color: $surface-active-color;
			}
		}
	}

	&__header {
		position: relative;
		z-index: 1;
		padding: 0 30px;
		background: $surface;
		box-shadow: 0 1px 8px rgba(0, 0, 0, .1);

		&-logo {
			display: flex;
			justify-content: center;
			align-items: center;

			img {
				height: 36px;
				margin-right: 10px;
			}
			span {
				margin-right: 5px;
				margin-bottom: 0;
				margin-left: 0;

				&:last-child {
					margin-right: 0;
				}
			}

			.znpb-label {
				font-size: 10px;
			}

			&-version {
				padding: 5px 6px 4px;
				font-size: 11px;
				font-weight: 500;
				background: $surface-variant;
				border-radius: 2px;
			}
		}

		&-actions {
			display: flex;
			flex-direction: row;
			align-items: center;
			.znpb-button--secondary {
				padding-top: 13px;
				padding-bottom: 13px;
				margin-right: 10px;
			}
			.znpb-editor-icon-wrapper {
				position: relative;
				top: 1px;
				margin-right: 6px;
				font-size: 14px;

				.zion-icon.zion-svg-inline {
					width: auto;
				}
			}
			.router-link-active {
				.zion-quality {
					color: $pro-color;
				}
			}
			a:visited {
				color: #fff;
			}
			a:last-child {
				color: $surface-active-color;
				&:visited {
					color: $surface-active-color;
				}
			}
		}

		&-top {
			display: flex;
			justify-content: space-between;
			padding: 15px 0;
		}

		&-menu {
			margin: 0;
			margin-left: -20px;
			font-size: 15px;
			font-weight: 500;
			list-style: none;

			&-item {
				display: inline-block;
				padding: 20px;
			}
		}
	}

	&-notices-wrapper {
		position: fixed;
		right: 50px;
		bottom: 50px;
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		min-width: 200px;
	}
}
</style>
