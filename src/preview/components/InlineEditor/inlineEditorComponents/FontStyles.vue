<template>
	<PopOver icon="ite-font" :full-size="true">
		<div class="zion-inline-editor-group">
			<Tabs tab-style="minimal">
				<Tab :name="__('family', 'zionbuilder')">
					<FontList />
				</Tab>
				<Tab :name="__('Heading', 'zionbuilder')" class="zion-inline-editor-group__heading">
					<div>
						<Button formatter="h1" buttontext="H1" />
						<Button formatter="h2" buttontext="H2" />
						<Button formatter="h3" buttontext="H3" />
						<Button formatter="h4" buttontext="H4" />
						<Button formatter="h5" buttontext="H5" />
						<Button formatter="h6" buttontext="H6" />
					</div>
				</Tab>
				<Tab :name="__('Size', 'zionbuilder')">
					<FontSize />
				</Tab>

				<Tab :name="__('Height', 'zionbuilder')">
					<LineHeight />
				</Tab>

				<Tab :name="__('Spacing', 'zionbuilder')">
					<LetterSpacing />
				</Tab>
			</Tabs>
		</div>
	</PopOver>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref } from 'vue';

// Components
import FontSize from './FontSize.vue';
import LineHeight from './LineHeight.vue';
import LetterSpacing from './LetterSpacing.vue';
import Button from './Button.vue';
import FontList from './FontsList.vue';
import PopOver from './PopOver.vue';
</script>

<style lang="scss">
.zion-inline-editor-group {
	display: flex;
	width: 100%;
	color: var(--zb-surface-text-color);
	background: var(--zb-surface-color);

	.znpb-tabs--minimal {
		display: flex;
		flex-direction: column-reverse;
		flex-grow: 1;

		.znpb-tabs__header {
			justify-content: space-evenly;
			align-items: center;
			border-top: 1px solid var(--zb-surface-border-color);

			& > .znpb-tabs__header-item {
				padding: 8px;
				text-transform: capitalize;
			}
		}
	}
	.znpb-input-range__label {
		margin-bottom: 0;
	}

	&__heading {
		.zion-inline-editor-button {
			display: inline-flex;
			justify-content: center;
			align-items: center;
			width: 45px;
			height: 45px;
			vertical-align: middle;

			&:first-child {
				font-size: 24px;
			}
			&:nth-child(2) {
				font-size: 20px;
			}
			&:nth-child(3) {
				font-size: 16px;
			}
			&:nth-child(4) {
				font-size: 12px;
			}
			&:nth-child(5) {
				font-size: 10px;
			}
			&:last-child {
				font-size: 8px;
			}
		}
	}
}
.zion-inline-editor__font-panel {
	overflow-y: scroll;
	max-height: 150px;
	margin-bottom: 0;
}
</style>
