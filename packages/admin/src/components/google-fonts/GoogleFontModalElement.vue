<template>
	<div class="znpb-admin__google-fonts-modal-item">
		<div class="znpb-admin__google-fonts-modal-item-header">
			<div>{{font.family}}</div>
			<div v-if="! isActive" @click="$emit('font-selected', font)" class="znpb-circle-icon-line">
				<Icon icon="plus"></Icon>
			</div>
			<div v-if="isActive" @click="$emit('font-removed', font.family)" class="znpb-circle-icon-line znpb-circle-delete">
				<Icon icon="minus"></Icon>
			</div>
		</div>
		<div class="znpb-admin__google-fonts-modal-item-preview" contenteditable="true" :style="fontStyle">{{previewText}}</div>
	</div>
</template>

<script>
import { Icon } from '@zionbuilder/components'

export default {
	name: 'GoogleFontModalElement',
	components: {
		Icon
	},
	props: {
		font: {
			type: Object,
			required: true
		},
		isActive: {
			type: Boolean,
			required: true
		}
	},
	data () {
		return {
			previewText: this.font.family
		}
	},
	computed: {
		fontStyle () {
			return {
				'font-family': this.font.family
			}
		}
	}
}
</script>

<style lang="scss">

.znpb-admin__google-fonts-modal-item {
	flex-basis: 100%;
	padding: 20px;
	margin-bottom: 10px;
	color: $font-color;
	background: $surface;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .1);
	border: 1px solid $surface-variant;
	border-radius: 3px;

	&-preview {
		color: $surface-active-color;
		font-size: 24px;
		line-height: 30px;
		word-break: break-word;
	}

	&-header {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		font-weight: 500;
	}

	.znpb-editor-icon-wrapper {
		width: 24px;
		height: 24px;
		color: #fff;
		font-size: 10px;
		text-align: center;
		border: 2px solid #fff;
		border-radius: 50%;
		cursor: pointer;
	}

	&:hover {
		.znpb-editor-icon-wrapper {
			color: $secondary;
			border-color: $secondary;
		}
		.znpb-circle-delete {
			.znpb-editor-icon-wrapper {
				color: $red;
				border-color: $red;
			}
		}
	}
}

</style>
