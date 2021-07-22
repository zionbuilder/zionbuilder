<template>
	<span class="znpb-sortable__placeholder-element"></span>
</template>

<script>
export default {
	name: 'SortablePlaceholder'
}
</script>

<style lang="scss">
.znpb-sortable__placeholder {
	position: relative;
	z-index: 99998;
	display: block;
	align-items: center;
	align-self: center;
	align-self: auto;
	width: 100%;
	height: 0;
	transform-origin: center center;
	animation: placeholder-animation--horizontal .3s ease;
	animation-fill-mode: forwards;

	&-element {
		bottom: 0;
		display: block;
		width: 100%;
		height: 3px;
		background-color: var(--zb-secondary-color);
		transform: translateY(-50%);
	}
}

.vuebdnd__placeholder-empty-container.vuebdnd__placeholder-container {
	// Vertical
	&.znpb__sortable-container--vertical > .znpb-sortable__placeholder {
		position: absolute;
		top: 50%;
		left: 0;
		width: 100%;
		animation: placeholder-animation--horizontal .3s ease;
	}
	// Horizontal
	&.znpb__sortable-container--horizontal > .znpb-sortable__placeholder {
		position: absolute;
		top: 0;
		left: 50%;
		height: 100%;
		animation: placeholder-animation--vertical .3s ease;
	}

	& > .znpb-empty-placeholder > .znpb-empty-placeholder__add-element-button {
		opacity: 0;
		visibility: hidden;
	}
}

.znpb__sortable-container--horizontal > .znpb-sortable__placeholder {
	width: 0;
	height: auto;
	animation: placeholder-animation--vertical .3s ease;
}

.znpb__sortable-container--horizontal > .znpb-sortable__placeholder > .znpb-sortable__placeholder-element {
	top: 0;
	left: 0;
	width: 3px;
	height: 100%;
	transform: translateX(-50%);
}

@keyframes placeholder-animation--horizontal {
	from {
		transform: scaleX(.6);
		opacity: 0;
	}
	to {
		transform: scaleX(1);
		opacity: 1;
	}
}
@keyframes placeholder-animation--vertical {
	from {
		transform: scaleY(.6);
		opacity: 0;
	}
	to {
		transform: scaleY(1);
		opacity: 1;
	}
}
</style>
