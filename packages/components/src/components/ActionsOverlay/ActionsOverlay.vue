<template>
	<div class="znpb-actions-overlay__wrapper">
		<slot></slot>

		<div
			class="znpb-actions-overlay__actions-wrapper"
			v-if="showOverlay"
		>
			<div class="znpb-actions-overlay__actions">
				<slot name="actions"></slot>
			</div>
		</div>

	</div>
</template>
<script>
export default {
	name: 'ActionsOverlay',
	props: {
		showOverlay: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	data () {
		return {
		}
	}
}
</script>

<style lang="scss">
.znpb-actions-overlay {
	&__wrapper {
		position: relative;
		overflow: hidden;
		height: 100%;
		border-radius: 3px;

		&--user-select {
			user-select: none;
		}
	}

	&__actions {
		background: var(--zb-surface-color);
		border-radius: 3px;

		.znpb-editor-icon-wrapper {
			cursor: pointer;
		}

		&-wrapper {
			position: absolute;
			bottom: 0;
			left: 0;
			display: flex;
			justify-content: flex-end;
			align-items: flex-end;
			width: 100%;
			padding: 10px;
			transition: all 0.3s;
			opacity: 0;
			visibility: hidden;
			&--mouseover-expander {
				opacity: 1;
				visibility: visible;
			}
		}
	}

	&__wrapper:hover &__actions-wrapper {
		opacity: 1;
		visibility: visible;
	}
}
</style>
