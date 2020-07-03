<template>
	<div class="znpb-editor-shapeWrapper">
		<slot></slot>
		<BaseIcon
			v-if="shape !== undefined"
			:icon="shape"
			class="znpb-shape-divider-icon"
			preserveAspectRatio="none"
		/>
	</div>
</template>

<script>

export default {
	name: 'Shape',

	props: {
		/**
		 * Value for input
		 */
		shape: {
			type: String,
			required: false
		}

	},
	data () {
		return {

		}
	},
	computed: {
	},
	methods: {

	}
}
</script>
<style lang="scss">
.znpb-shape-divider-icon {
	display: flex;
	align-items: flex-end;
	color: #fff;
	width: 100%;
	height: 72px;
	.zion-icon.zion-svg-inline {
		display: block;
		width: 100%;
		height: auto;
		margin: 0;
	}
}
.znpb-editor-shapeWrapper {
	position: relative;
	display: flex;
	margin-bottom: 18px;
	color: #fff;
	background-color: rgb(0, 109, 210);
	border-radius: 3px;
	cursor: pointer;
	transition: box-shadow 0.2s linear;

	&:last-of-type {
		margin-bottom: 0;
	}

	&:hover {
		box-shadow: 0 2px 15px 0 rgba(86, 86, 86, 0.1);
	}

	&.znpb-active-shape-preview {
		background-color: #fff;
		.znpb-empty-list__container {
			width: 100%;
		}
		&.mask-active {
			background-color: rgb(0, 109, 210);
		}
	}

	&.mask-active {
		box-shadow: 0 5px 10px 0 rgba(86, 86, 86, .2);
		.znpb-editor-icon--rounded {
			position: absolute;
			top: 10px;
			right: 10px;
			color: #858585;
			font-size: 10px;
			box-shadow: 0 5px 10px 0 rgba(86, 86, 86, .2);
		}
	}
}
</style>
