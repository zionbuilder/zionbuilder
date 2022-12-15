<template>
	<li class="znpb-element-box" :class="['znpb-element-box--' + item.element_type]" :title="item.name">
		<span v-if="item.label" class="znpb-element-box__label" :style="{ background: item.label.color }">{{
			item.label.text
		}}</span>

		<Icon
			icon="pin"
			class="znpb-element-box__favoriteIcon"
			:class="{
				'znpb-element-box__favoriteIcon--active': isActiveFavorite,
			}"
			@click.stop="addToFavorites"
		/>

		<UIElementIcon :element="item" class="znpb-element-box__icon" />

		<span class="znpb-element-box__element-name">
			{{ item.name }}
		</span>
	</li>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useUserData } from '/@/editor/composables';

const props = defineProps<{
	item: Record<string, unknown>;
}>();

const isActiveFavorite = computed(() => {
	const { getUserData } = useUserData();
	return getUserData('favorite_elements', []).includes(props.item.element_type);
});

function addToFavorites() {
	const { getUserData, updateUserData } = useUserData();
	const activeFavoritesClone = [...getUserData('favorite_elements', [])];

	if (activeFavoritesClone.includes(props.item.element_type)) {
		const favoriteIndex = activeFavoritesClone.indexOf(props.item.element_type);
		activeFavoritesClone.splice(favoriteIndex, 1);
	} else {
		activeFavoritesClone.push(props.item.element_type);
	}

	updateUserData({
		favorite_elements: activeFavoritesClone,
	});
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

	&__favoriteIcon {
		position: absolute;
		top: 6px;
		right: 6px;
		transition: all 0.1s;
		opacity: 0;
		visibility: hidden;

		.zion-icon {
			transition: color 0.1s;

			path:first-child {
				fill: transparent;
			}

			path:last-child {
				fill: var(--zb-surface-icon-active-color);
			}
		}

		&:hover .zion-icon {
			path:first-child {
				fill: var(--zb-surface-icon-active-color);
			}

			path:last-child {
				fill: transparent;
			}
		}

		&--active,
		&--active:hover {
			.zion-icon path:first-child {
				fill: var(--zb-secondary-color);
			}

			.zion-icon path:last-child {
				fill: transparent;
			}
		}
	}

	&:hover &__favoriteIcon {
		opacity: 1;
		visibility: visible;
	}

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

	&__icon,
	&__image {
		width: 100%;
		margin-bottom: 5px;
		color: var(--zb-surface-text-color);
		background-color: var(--zb-surface-lighter-color);
		border-radius: 4px;
		transition: all 0.2s;

		&::after {
			content: '';
			display: block;
			padding-top: 100%;
		}
	}

	&__image {
		padding: 27px;
	}

	&__icon {
		font-size: 36px;
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
		.znpb-editor-icon-wrapper,
		.znpb-element-box__image {
			color: var(--zb-surface-text-active-color);
		}

		.znpb-element-box__icon {
			background-color: var(--zb-surface-lightest-color);
			box-shadow: 0 4px 20px 0 var(--zb-surface-shadow-hover);
		}
	}
}
</style>
