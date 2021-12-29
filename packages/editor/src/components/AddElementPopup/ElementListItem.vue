<template>
	<li
		class="znpb-element-box"
		ref="elementBox"
		:class="['znpb-element-box--' + item.element_type]"
	>

		<span
			v-if="item.label"
			class="znpb-element-box__label"
			:style="{ background: item.label.color }"
		>{{item.label.text}}</span>

		<Icon
			icon="quality"
			@click.stop="addToFavorites"
			class="znpb-element-box__favoriteIcon"
			:class="{
				'znpb-element-box__favoriteIcon--active': isActiveFavorite
			}"
		/>

		<UIElementIcon
			:element="item"
			class="znpb-element-box__icon"
		/>

		<span class="znpb-element-box__element-name">
			{{ item.name }}
		</span>
	</li>
</template>

<script>
import { computed } from 'vue'
import { useUserData } from '@composables'

export default {
	name: 'ElementListItem',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const isActiveFavorite = computed(() => {
			const { getUserData } = useUserData()
			return getUserData('favorite_elements', []).includes(props.item.element_type)
		})

		function addToFavorites () {
			const { getUserData, updateUserData } = useUserData()
			const activeFavoritesClone = [...getUserData('favorite_elements', [])]

			if (activeFavoritesClone.includes(props.item.element_type)) {
				const favoriteIndex = activeFavoritesClone.indexOf(props.item.element_type)
				activeFavoritesClone.splice(favoriteIndex, 1)
			} else {
				activeFavoritesClone.push(props.item.element_type)
			}

			updateUserData({
				'favorite_elements': activeFavoritesClone
			})
		}

		return {
			isActiveFavorite,
			addToFavorites
		}
	}
}
</script>

<style lang="scss">
.znpb-element-box {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: center;
	min-width: 0;
	cursor: pointer;
	user-select: none;

	&__label {
		position: absolute;
		top: 8px;
		right: 8px;
		padding: 2px 3px;
		color: #000;
		font-size: 7px;
		font-weight: 700;
		background: var(--zb-pro-color);
		border-radius: 2px;
	}

	.znpb-element-box__icon, .znpb-element-box__image {
		width: 100%;
		margin-bottom: 5px;
		color: var(--zb-surface-text-color);
		background-color: var(--zb-surface-lighter-color);
		border-radius: 4px;
		transition: all .2s;

		&::after {
			content: "";
			display: block;
			padding-top: 100%;
		}
	}

	.znpb-element-box__image {
		padding: 27px;
	}

	&__element-name {
		overflow: hidden;
		max-width: 100%;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 500;
		line-height: 1.3;
		text-align: center;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	&:hover {
		.znpb-editor-icon-wrapper, .znpb-element-box__image {
			color: var(--zb-surface-text-active-color);
		}

		.znpb-element-box__icon {
			background-color: var(--zb-surface-lightest-color);
			box-shadow: 0 4px 20px 0 var(--zb-surface-shadow-hover);
		}
	}
}

.znpb-element-box__icon {
	font-size: 36px;
}

.znpb-element-box__favoriteIcon {
	position: absolute;
	top: 5px;
	right: 5px;
	transition: all .3s;
	opacity: 0;
	visibility: hidden;
}

.znpb-element-box:hover .znpb-element-box__favoriteIcon {
	opacity: 1;
	visibility: visible;
}

.znpb-element-box__favoriteIcon--active {
	color: red;
}
</style>
