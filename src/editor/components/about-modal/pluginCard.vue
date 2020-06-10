<template>
	<div
		class="znpb-about-modal__version-card"
		:class="{'znpb-about-modal__version-card--active' : !isPro }"
	>
		<BaseIcon icon="zion-icon-logo"/>
		<div v-if="isPro" class="znpb-pro-item">{{$translate('pro')}}</div>
		<span class="znpb-about-modal__plugin-title">Zion Page Builder
			<span v-if="isPro">{{$translate('pro')}}</span>
			<span v-else>{{$translate('free')}}</span>
		</span>
		<div class="znpb-about-modal-text-wrapper">

			<template
				v-if="version!==null && updateVersion!==null"

			>
				<span >{{version}}</span>
				<a	href="#" class="znpb-about-modal__help"
				>{{$translate('view_changelog')}}</a>

			</template>
			<span
				v-if="version === null && isPro"
			>
				{{$translate('not_installed')}}
			</span>
		</div>
		<BaseButton
			v-if="updateVersion !== version && version !== null"
			class="znpb-about-modal__version-card-button"
		>{{$translate('update_to_version')}} {{updateVersion}}
		</BaseButton>
		<span
			v-if="updateVersion === version && updateVersion !== null"
			class="znpb-about-modal-text-wrapper__up-to-date">
			{{$translate('you_are_upto_date')}}
		</span>

		<BaseButton
			v-if="version === null && isPro"
			type="secondary"
		>{{$translate('buy_pro')}}
		</BaseButton>

	</div>

</template>

<script>

export default {
	name: 'pluginCard',
	props: {
		isPro: {
			type: Boolean,
			required: false
		},
		version: {
			type: String,
			required: false
		},
		updateVersion: {
			type: String,
			required: false
		}
	},
	data () {
		return {

		}
	}
}
</script>

<style lang="scss">
.znpb-about-modal-text-wrapper {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	align-items: center;
	height: 100%;
}
.znpb-about-modal {
	&__plugin-title {
		margin-bottom: 16px;
		color: $surface-active-color;
		font-weight: 500;
		span {
			text-transform: uppercase;
		}
	}

	&__version-card {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		flex: 1;
		margin-bottom: 25px;
		border: 1px solid rgba($surface-variant,.7);
		border-radius: 3px;

		span {
			margin-bottom: 15px;
			opacity: .7;
		}
		.znpb-editor-icon-wrapper {
			padding: 30px 0 20px 0;
			margin-bottom: 0;
			color: $surface-headings-color;
			font-size: 44px;
			opacity: .7;
		}

		&:hover, &--active {
			border: 1px solid $surface-variant;
			.znpb-editor-icon-wrapper, span {
				opacity: 1;
			}
		}
		&:first-of-type {
			margin-right: 20px;
		}
		&:last-of-type {
			margin-right: 0;
		}
		.znpb-button {
			padding: 14px 20px;
		}

		.znpb-about-modal__version-card-button {
			background-color: #66c461;
		}
		.znpb-about-modal__help {
			margin-bottom: 20px;
		}
		& > .znpb-button {
			&.znpb-about-modal__version-card-button, &.znpb-button--secondary {
				margin-bottom: 13px;
			}
		}
		.znpb-about-modal-text-wrapper__up-to-date {
			margin-bottom: 28px;
		}
	}
}

.znpb-pro-item {
	position: absolute;
	top: 30px;
	right: 90px;
	padding: 2px 8px;
	color: #fff;
	font-size: 10px;
	font-weight: 700;
	text-transform: uppercase;
	background-color: $pro-color;
	border-radius: 2px;
}
</style>
