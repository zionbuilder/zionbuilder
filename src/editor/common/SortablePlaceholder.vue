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
    align-self: center;
    display: block;
	align-items: center;
	transform-origin: center center;
	animation-fill-mode: forwards;
	z-index: 99999;
	height: 0;
	width: 100%;
    align-self: auto;
	animation: placeholder-animation--horizontal .3s ease;

	&-element {
		bottom: 0;
		display: block;
		width: 100%;
		height: 3px;
		background-color: $secondary;
		transform: translateY(-50%);
	}
}

.vuebdnd__placeholder-empty-container.vuebdnd__placeholder-container {
	// Vertical
	&.znpb__sortable-container--vertical > .znpb-sortable__placeholder {
		position: absolute;
		left: 0;
		top: 50%;
		width: 100%;
		animation: placeholder-animation--horizontal .3s ease;
	}
	// Horizontal
	&.znpb__sortable-container--horizontal > .znpb-sortable__placeholder {
		position: absolute;
		left: 50%;
		top: 0;
		height: 100%;
		animation: placeholder-animation--vertical .3s ease;
	}

	& > .znpb-empty-placeholder > .znpb-empty-placeholder__add-element-button {
		opacity: 0;
		visibility: hidden;
	}
}

.znpb__sortable-container--horizontal > .znpb-sortable__placeholder {
	height: auto;
	width: 0;
	animation: placeholder-animation--vertical .3s ease;
}

.znpb__sortable-container--horizontal > .znpb-sortable__placeholder > .znpb-sortable__placeholder-element {
	height: 100%;
	top: 0;
	left: 0;
	width: 3px;
	transform: translateX(-50%)
}

@keyframes placeholder-animation--horizontal {
	from {
		transform: scaleX(0.6);
		opacity: 0;
	}
	to {
		transform: scaleX(1);
		opacity: 1;
	}
}
@keyframes placeholder-animation--vertical {
	from {
		transform: scaleY(0.6);
		opacity: 0;
	}
	to {
		transform: scaleY(1);
		opacity: 1;
	}
}

</style>
