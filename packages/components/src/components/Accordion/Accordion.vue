<template>
	<div
		class="znpb-accordion"
		:class="{'znpb-accordion--collapsed': localCollapsed}"
	>
		<div
			class="znpb-accordion__header"
			@click="localCollapsed = !localCollapsed"
		>
			<!-- @slot Content that will be placed inside the accordion header -->
			<slot name="header">{{header}}</slot>
			<Icon
				icon="select"
				class="znpb-accordion-title-icon"
			/>
		</div>
		<div
			class="znpb-accordion__content"
			v-if="localCollapsed"
		>
			<!-- @slot Content that will be placed inside the accordion content -->
			<slot></slot>
		</div>
	</div>
</template>

<script>
import Icon from '../Icon/Icon.vue'

export default {
	name: 'Accordion',
	components: {
		Icon
	},
	props: {
		/**
		 * If the accordion should be open or closed by default
		 */
		collapsed: {
			required: false,
			type: Boolean,
			default: false
		},
		/**
		 * Text that will be displayed inside the accordion header
		 */
		header: {
			type: String,
			required: false
		}
	},
	data () {
		return {
			localCollapsed: this.collapsed
		}
	}
}
</script>

<style lang="scss">
.znpb-accordion {
	width: 100%;
	border-bottom: 1px solid var(--zb-surface-lighter-color);

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 30px;
		font-size: 12px;
		font-weight: bold;
		line-height: 1;
	}

	&__content {
		padding: 0 30px;
	}

	&--collapsed {
		.znpb-accordion__header {
			h2.znpb-accordion-title {
				color: var(--zb-primary-color);
			}
			.znpb-accordion-title-icon {
				transform: rotate(180deg);
			}
		}
	}
}
</style>
