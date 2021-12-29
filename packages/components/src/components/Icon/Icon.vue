<template>
	<span
		class="znpb-editor-icon-wrapper"
		:style="iconStyles"
		:class="iconClass"
		v-html="getSvgIcon"
	>
	</span>

</template>

<script>
import { getSearchIcon } from './icons.js'
export default {
	name: 'Icon',
	props: {
		/**
		 * The Name of the icon - String
		 */
		icon: String,
		/**
		 * If the icon should be computed rotated
		 * exmaple of props: true, 45,'45'
		 */
		rotate: {
			type: [Boolean, String, Number],
			required: false,
			default: false
		},
		/**
		 * The size of the icon - number
		 */
		bgSize: {
			type: Number,
			required: false
		},
		/**
		 * The color of the icon
		 */
		color: {
			type: String,
			required: false
		},
		/**
		 * The icon size
		 */
		size: {
			type: Number,
			required: false
		},
		/**
		 * The background-color of the icon
		 */
		bgColor: {
			type: String,
			required: false
		},
		/**
		 * The stroke-color of the icon
		 */
		stroke: {
			type: String,
			required: false
		},
		/**
		 * If set to true, the icon wrapper will be rounded
		 */
		rounded: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		 * preserv aspect ratio
		 */
		preserveAspectRatio: {
			type: String,
			required: false
		}
	},
	data: () => {
		return {}
	},
	computed: {
		iconStyles () {
			return {
				width: this.bgSize + 'px',
				height: this.bgSize + 'px',
				color: this.color,
				fontSize: this.size + 'px',
				background: this.bgColor,
				stroke: this.stroke,
				transform: this.elementTransform
			}
		},
		iconClass () {
			return {
				'znpb-editor-icon--rounded': this.rounded
			}
		},
		elementTransform () {
			let cssStyles = ''
			if (this.rotate) {
				if ((typeof (this.rotate) === 'string') || (typeof (this.rotate) === 'number')) {
					cssStyles = 'rotate' + '(' + this.rotate + 'deg)'
				} else cssStyles = 'rotate(90deg)'
			}
			return cssStyles
		},
		hasPreserveAspect () {
			return this.preserveAspectRatio ? this.preserveAspectRatio : ''
		},
		getSvgIcon () {
			const svg = `<svg
				class="zion-svg-inline znpb-editor-icon zion-${this.icon} zion-icon"
				xmlns="http://www.w3.org/2000/svg"
				aria-hidden="true"
				viewBox="${this.iconViewbox}"
				preserveAspectRatio="${this.hasPreserveAspect}"
			>
				${this.getIcon}
			</svg>`
			return svg
		},
		getIcon () {
			let iconOption = getSearchIcon(this.icon)
			if (iconOption) {
				let iconPath = iconOption.paths
				let pathString = ''
				// if the icon has circles
				if (iconOption.circle) {
					let iconCircle = iconOption.circle
					for (let i = 0; i < iconCircle.length; i++) {
						pathString += `<circle  ${iconCircle[i]} fill="currentColor"></circle>`
					}
				}
				// if the icon has rect
				if (iconOption.rect) {
					let iconRect = iconOption.rect
					for (let i = 0; i < iconRect.length; i++) {
						pathString += `<rect ${iconRect[i]}></rect>`
					}
				}
				// if the icon has polygon
				if (iconOption.polygon) {
					let iconPolygon = iconOption.polygon
					for (let i = 0; i < iconPolygon.length; i++) {
						pathString += `<polygon points='${iconPolygon[i]}' fill="currentColor"></polygon>`
					}
				}
				for (let i = 0; i < iconPath.length; i++) {
					pathString += `<path fill="currentColor" d="${iconPath[i]}"></path>`
				}

				return pathString
			}

			return null
		},
		iconViewbox () {
			let iconOption = getSearchIcon(this.icon)
			if (iconOption) {
				if (iconOption.viewBox) {
					return iconOption.viewBox
				} else return '0 0 50 50 '
			} else return '0 0 50 50 '
		}
	}
}
</script>

<style lang="scss">
.znpb-editor-icon--rounded {
	border-radius: 50%;
}

.znpb-editor-icon-wrapper {
	// icon is inline
	display: inline-flex;

	// if icon has background it stays aligned vertically
	align-items: center;
}
.znpb-editor-icon {
	transform-origin: center center;
	transition: transform 0.5s, opacity 0.2s ease-in-out;
	path,
	circle {
		transition: fill 0.2s ease-in-out;
	}
}
svg:not(:root).icon {
	overflow: visible;
}
.zion-icon.zion-svg-inline {
	display: block;
	width: 1em;
	height: 1em;
	margin: auto;
}
// specific icons hacks
.znpb-editor-icon {
	&.zion-border-bottom,
	&.zion-border-left,
	&.zion-border-right,
	&.zion-border-top,
	&.zion-t-r-corner,
	&.zion-t-l-corner,
	&.zion-b-l-corner,
	&.zion-b-r-corner,
	&.zion-padding-left,
	&.zion-padding-right,
	&.zion-padding-top,
	&.zion-padding-bottom,
	&.zion-margin-left,
	&.zion-margin-right,
	&.zion-margin-top,
	&.zion-margin-bottom,
	&.zion-vertical,
	&.zion-horizontal,
	&.zion-self-baseline,
	&.zion-align-baseline {
		path:first-child {
			opacity: 0.5;
		}
	}
	&.zion-capitalize,
	&.zion-lowercase,
	&.zion-uppercase,
	&.zion-eye,
	&.zion-hidden,
	&.zion-lib {
		width: auto;
	}

	&.zion-heart {
		path:first-child {
			fill: transparent;
		}
		&:hover {
			path,
			path:first-child {
				fill: var(--zb-secondary-color);
			}
		}
	}
	&.zion-element-accordion {
		path:nth-child(2) {
			opacity: 0.5;
		}
	}
	&.zion-element-archive {
		path:nth-child(5),
		path:nth-child(6) {
			opacity: 0.5;
		}
	}
	&.zion-element-column {
		path:first-child,
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-library-illustration {
		path:first-child {
			color: var(--zb-surface-text-color);
		}
		path:nth-child(2),
		path:last-child {
			color: var(--zb-surface-border-color);
		}

		path:last-child {
			opacity: 0.3;
		}
	}
	&.zion-element-bar-counter {
		path:nth-child(3) {
			opacity: 0.3;
		}
		path:nth-child(4),
		path:nth-child(6) {
			opacity: 0.2;
		}
		path:nth-child(5),
		path:nth-child(7) {
			opacity: 0.5;
		}
	}
	&.zion-element-button {
		path:first-child {
			fill: none;
			stroke: currentColor;
			stroke-linejoin: round;
			stroke-miterlimit: 10;
			stroke-width: 2;
		}
		path:nth-child(2) {
			opacity: 0.5;
		}
	}
	&.zion-element-carousel {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-code {
		path:last-child {
			fill: var(--zb-surface-color);
		}
	}
	&.zion-element-divider {
		path:nth-child(2),
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-form {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-image {
		path:first-child {
			fill: none;
			stroke: currentColor;
			stroke-miterlimit: 10;
			stroke-width: 2;
		}
	}
	&.zion-element-image-gallery {
		path:nth-child(3),
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-media {
		path:first-child {
			fill: none;
			stroke: currentColor;
			stroke-linejoin: round;
			stroke-miterlimit: 10;
			stroke-width: 2;
		}
	}
	&.zion-element-pricing-table {
		path:last-child {
			clip-rule: evenodd;
			fill-rule: evenodd;
		}
		path:nth-child(2) {
			opacity: 0.5;
		}
	}
	&.zion-element-section {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-container {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-sidebar {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-tabs {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-testimonial {
		circle {
			opacity: 0.5;
		}
	}
	&.zion-import-big-icon {
		polygon {
			fill: #06bee1;
		}
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-counter-free {
		path:first-child,
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-gallery {
		path:nth-child(3),
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-borders {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-size-spacing {
		path:first-child {
			opacity: 0.8;
		}
	}
	&.zion-transform {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-transitions {
		circle:first-child {
			opacity: 0.4;
		}
		circle:nth-child(2) {
			opacity: 0.6;
		}
	}
	&.zion-zion-icon-logo {
		transform: scale(-1, 1);
	}
	&.zion-element-countdown {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-social-share {
		path:first-child {
			opacity: 0.5;
		}
	}
	&.zion-element-search-form {
		path:nth-child(2) {
			opacity: 0.5;
		}
	}
	&.zion-templates-body {
		path:nth-child(2),
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-templates-footer {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-templates-header {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-tags-attributes {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-woo-description {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-woo-product-meta {
		path:last-child {
			opacity: 0.5;
		}
	}
	&.zion-element-woo-product-images {
		path:first-child {
			opacity: 0.5;
		}
	}
	&.zion-element-woo-product-rating {
		path:first-child {
			opacity: 0.5;
		}
	}
	&.zion-element-woo-product-related {
		path:last-child {
			opacity: 0.5;
		}
	}
}
</style>
