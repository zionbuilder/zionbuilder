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
	width: 100%;
	.zion-icon.zion-svg-inline {
		display: block;
		width: 100%;
		height: 100%;
	}
}
.znpb-editor-shapeWrapper {
	position: relative;
	display: flex;
	margin-bottom: 30px;
	color: #fff;
	background-color: rgb(0, 109, 210);
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .08);
	border: solid 1px rgb(241, 241, 241);
	border-radius: 3px;
	cursor: pointer;

	&:hover {
		opacity: .8;
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
			top: 5px;
			right: 10px;
			color: $icons-color;
			box-shadow: 0 5px 10px 0 rgba(86, 86, 86, .2);
		}
	}
}
</style>
