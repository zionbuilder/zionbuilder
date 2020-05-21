<template>
	<div class="znpb-get-pro">
		<div class="znpb-get-pro__image">
			<img :src="proUrl" />
		</div>
		<h1 class="znpb-get-pro__title">{{$translate('upgrade_to_pro_now')}}</h1>
		<p class="znpb-get-pro__description">{{message}}</p>
		<div class="znpb-get-pro__more">
			<a
				href="https://zionbuilder.io/documentation/pro-version/"
				target="_blank"
			>
				{{$translate('learn_more_about_pro')}}
			</a>
		</div>
		<a
			href="https://zionbuilder.io/"
			target="_blank"
			class="znpb-button znpb-get-pro__cta znpb-button--secondary znpb-option__upgrade-to-pro-button"
		>
			{{$translate('upgrade_to_pro')}}
		</a>
	</div>
</template>

<script>
export default {
	name: 'GetPro',
	props: {
		message: {
			type: String,
			required: false,
			default () {
				return this.$translate('pro_features')
			}
		}
	},
	computed: {
		proUrl () {
			return window.ZnPbAdminPageData.urls.pro_logo
		}
	}
}
</script>
<style lang="scss" scoped>
.znpb-get-pro {
	width: 100%;
	padding: 60px 0 140px;
	text-align: center;
	background-color: #fff;

	.znpb-editor-icon-wrapper {
		margin: 0 auto;
		color: $surface-variant;
		font-size: 125px;
	}

	&__image {
		margin-bottom: 40px;
	}

	&__title {
		margin: 0 0 24px;
		color: $surface-active-color;
		font-size: 22px;
		font-weight: 600;

		strong {
			font-weight: 700;
			text-transform: uppercase;
		}
	}

	&__description {
		max-width: 40%;
		margin: 0 auto 24px;
		font-size: 15px;
		text-align: center;
	}

	&__more {
		margin-bottom: 45px;
		text-align: center;

		& > a {
			padding-bottom: 5px;
			color: $surface-active-color;
			font-weight: 500;
			border-bottom: 1px solid #e5e5e5;

			&:hover {
				color: lighten($surface-active-color, 15%);
			}
		}
	}

	&__cta {
		text-transform: uppercase;
	}
}
</style>
