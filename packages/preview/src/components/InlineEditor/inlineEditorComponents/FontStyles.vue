<template>
	<PopOver icon="ite-font">
		<div class="zion-inline-editor-group">
			<Tabs tab-style="minimal">
				<Tab :name="$translate('family')">
					<FontList />
				</Tab>
				<Tab
					:name="$translate('heading')"
					class="zion-inline-editor-group__heading"
				>
					<div class=" zion-inline-editor__font-panel znpb-fancy-scrollbar">
						<zion-inline-editor-button
							formatter="h1"
							buttontext="H1"
						></zion-inline-editor-button>
						<zion-inline-editor-button
							formatter="h2"
							buttontext="H2"
						></zion-inline-editor-button>
						<zion-inline-editor-button
							formatter="h3"
							buttontext="H3"
						></zion-inline-editor-button>
						<zion-inline-editor-button
							formatter="h4"
							buttontext="H4"
						></zion-inline-editor-button>
						<zion-inline-editor-button
							formatter="h5"
							buttontext="H5"
						></zion-inline-editor-button>
						<zion-inline-editor-button
							formatter="h6"
							buttontext="H6"
						></zion-inline-editor-button>
					</div>
				</Tab>
				<Tab :name="$translate('font_size')">
					<zion-inline-editor-font-size />
				</Tab>

				<Tab :name="$translate('line_height')">
					<zion-inline-editor-line-height></zion-inline-editor-line-height>
				</Tab>

				<Tab :name="$translate('letter_spacing')">
					<zion-inline-editor-letter-spacing></zion-inline-editor-letter-spacing>
				</Tab>
			</Tabs>
		</div>
	</PopOver>
</template>

<script>
import { ref, computed } from 'vue'

// Components
import fontSize from './fontSize.vue'
import lineHeight from './lineHeight.vue'
import letterSpacing from './letterSpacing.vue'
import buttonComponent from './button.vue'
import FontList from './fonts-list.vue'
import PopOver from './PopOver.vue'

export default {
	name: 'FontStyles',
	components: {
		'zion-inline-editor-font-size': fontSize,
		'zion-inline-editor-line-height': lineHeight,
		'zion-inline-editor-letter-spacing': letterSpacing,
		'zion-inline-editor-button': buttonComponent,
		FontList,
		PopOver
	},
	setup () {
		const isOpen = ref(false)

		const buttonClasses = computed(() => {
			let classes = []

			if (isOpen.value) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		return {
			isOpen,
			buttonClasses
		}
	}
}
</script>

<style lang="scss">
.zion-inline-editor-group {
	display: flex;
	color: $font-color;
	background: $surface;

	.znpb-tabs--minimal {
		display: flex;
		flex-direction: column-reverse;
		flex-grow: 1;

		.znpb-tabs__header {
			justify-content: space-evenly;
			align-items: center;
			border-top: 1px solid $surface-variant;

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
		padding: 10px 16px 4px;

		.zion-inline-editor-button {
			padding: 0;
			margin-right: 20px;
			font-weight: 500;
			transition: all .2s;

			&:first-child {
				font-size: 28px;
			}
			&:nth-child(2) {
				font-size: 24px;
			}
			&:nth-child(3) {
				font-size: 20px;
			}
			&:nth-child(4) {
				font-size: 16px;
			}
			&:nth-child(5) {
				font-size: 12px;
			}
			&:last-child {
				font-size: 8px;
			}
			&.zion-inline-editor-button--active {
				color: $surface-active-color;
			}
			&:hover {
				color: $surface-active-color;
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