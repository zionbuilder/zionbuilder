<template>

	<div class="znpb-element-options__vertical-breadcrumbs-wrapper">
		<div
			class="znpb-element-options__vertical-breadcrumbs-item znpb-element-options__vertical-breadcrumbs-item--first"
			:class="{'znpb-element-options__vertical-breadcrumbs-item--active': activeElementUid===elementTemplateData.uid}"
			@mouseenter.capture="highlightElement"
			@mouseleave="unHighlightElement"
			@click.stop="editElement(elementTemplateData.uid)"
		>
			<span>{{elementName}}</span>
		</div>
		<template v-if="parents.children.length > 0">
			<BreadcrumbsItem
				v-for="(child,i) in parents.children"
				:key="i"
				:element-uid="child.uid"
				:parents="child.children"
			/>
		</template>
	</div>

</template>

<script>
import { mapActions } from 'vuex'
import BreadcrumbsItem from './BreadcrumbsItem.vue'
import templateElementMixin from '../../mixins/templateElement.js'
import { usePanels } from '@data'

export default {
	name: 'Breadcrumbs',
	mixins: [
		templateElementMixin
	],
	components: {
		BreadcrumbsItem
	},
	props: {
		parents: {
			type: Object
		},
		activeElementUid: {
			type: String,
			required: false
		}
	},
	setup (props) {
		const { openPanel } = usePanels()

		return {
			openPanel
		}
	},
	methods: {
		...mapActions([
			'setActiveElement'
		]),

		editElement (uid) {
			if (uid !== this.activeElementUid) {
				this.setActiveElement(uid)
				this.openPanel('PanelElementOptions')
			}
		}

	}
}
</script>

<style lang="scss">
.znpb-element-options__vertical-breadcrumbs {
	&-wrapper {
		position: relative;
		display: flex;
		flex-direction: column;
		overflow: hidden;
		box-shadow: none;

		&:after {
			content: "";
			position: absolute;
			top: 21px;
			left: 0;
			width: 1px;
			height: calc(100% - 38px);
			margin-top: 0;
			background: rgba($surface-headings-color, .3);
		}
	}

	&-item {
		position: relative;
		display: flex;
		flex-direction: column;
		padding-left: 7px;
		margin-left: 5px;
		cursor: pointer;

		& > span {
			display: block;
			margin-bottom: 12px;
			color: $font-color;
			font-size: 13px;
			font-weight: 500;
			white-space: nowrap;
			transition: all .15s ease-in;

			&:hover {
				color: $surface-active-color;
			}
		}

		&:before {
			content: "";
			position: absolute;
			top: 7px;
			right: calc(100% - 2px);
			width: 100px;
			height: 1px;
			background: rgba($surface-headings-color, .3);
		}

		&--active {
			&::before {
				background: rgba($surface-headings-color, .8);
			}

			& > span {
				color: $surface-active-color;
			}
		}

		.znpb-editor-icon-wrapper {
			width: 10px;
			margin-left: 10px;
		}

		&:last-child {
			.znpb-editor-icon-wrapper {
				display: none;
			}
		}
		&--first {
			padding-left: 0;
			margin: 0;
			border: 0;
			& > span {
				margin-left: 7px;
			}
		}

		.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
			padding: 0 7px;
		}
	}
}
.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
	overflow: visible;
	&:after {
		display: none;
	}
}
.znpb-element-options__breadcrumbs > .znpb-element-options__vertical-breadcrumbs-wrapper > .znpb-element-options__vertical-breadcrumbs-item--first {
	&:before {
		display: none;
	}
	& > span {
		margin-left: 0;
	}
}
.znpb-element-options__vertical-breadcrumbs-item--first ~ .znpb-element-options__vertical-breadcrumbs-item {
	margin-left: 14px;
}
.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
	& > .znpb-element-options__vertical-breadcrumbs-item--first {
		&:before {
			display: block;
		}
	}
}
</style>
