<template>
	<BasePanel
		:panel-name="$translate('history_panel')"
		panel-id="panel-history"
		:show-expand="false"
		:panel="panel"
		@close-panel="panel.close()"
	>
		<div class="znpb-panel__history_panel_wrapper">
			<div ref="historyPanelWrapper" class="znpb-history-wrapper znpb-fancy-scrollbar">
				<ul class="znpb-history-actions">
					<li
						v-for="(item, index) in historyItems"
						:key="index"
						ref="index"
						:title="item.name"
						:class="{ 'znpb-history-action--active': currentHistoryIndex === index }"
						@click="restoreHistoryState(index)"
					>
						<span class="znpb-action-time">{{ item.time }}</span>
						<span class="znpb-action-name">{{ item.name }}</span>
						<span v-if="currentHistoryIndex === index" class="znpb-action-active">{{ $translate('history_now') }}</span>
						<Icon v-else icon="history"></Icon>
					</li>
				</ul>
			</div>
			<div class="znpb-history__action-wrapper">
				<div class="znpb-history__action" :class="{ 'znpb-history__action--inactive': !canUndo }" @click="doUndo">
					<Icon icon="undo"></Icon>
				</div>
				<div class="znpb-history__action" :class="{ 'znpb-history__action--inactive': !canRedo }" @click="doRedo">
					<Icon icon="redo"></Icon>
				</div>
			</div>
		</div>
	</BasePanel>
</template>

<script>
import { useHistory } from '../composables';

// Components
import BasePanel from './BasePanel.vue';

export default {
	name: 'PanelHistory',
	components: {
		BasePanel,
	},
	props: {
		panel: {},
	},
	setup(props) {
		const { canUndo, canRedo, currentHistoryIndex, historyItems, restoreHistoryState, undo, redo } = useHistory();

		return {
			canUndo,
			canRedo,
			currentHistoryIndex,
			historyItems,
			restoreHistoryState,
			undo,
			redo,
		};
	},
	data: () => {
		return {};
	},
	watch: {
		historyItems() {
			this.$nextTick(() => {
				this.$refs.historyPanelWrapper.scrollTop = this.$refs.historyPanelWrapper.scrollHeight;
			});
		},
	},
	mounted() {
		// Scroll to last item
		this.$refs.historyPanelWrapper.scrollTop = this.$refs.historyPanelWrapper.scrollHeight;
	},
	methods: {
		doUndo() {
			if (this.canUndo) {
				this.undo();
			}
		},
		doRedo() {
			if (this.canRedo) {
				this.redo();
			}
		},
	},
};
</script>
<style lang="scss">
/* vars */

.znpb-panel__history_panel_wrapper {
	display: flex;
	flex-direction: column;
	height: 100%;
	min-height: 0;
	color: var(--zb-surface-text-color);
}

.znpb-history-wrapper {
	flex-grow: 1;
	overflow-y: auto;
	padding-top: 20px;
}

.znpb-history-actions {
	position: relative;

	li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		min-height: 48px;
		padding: 17px 15px;
		margin: 0 20px 5px;
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;

		.znpb-action-name {
			flex-grow: 1;
			overflow: hidden;
			width: 100%;
			color: var(--zb-surface-text-active-color);
			font-weight: 500;
			text-overflow: ellipsis;
			white-space: nowrap;
		}

		.znpb-action-time {
			margin-right: 13px;
		}

		&:hover {
			background-color: var(--zb-surface-lightest-color);
			cursor: pointer;
		}

		&.znpb-history-action--active {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);

			.znpb-action-name {
				color: var(--zb-secondary-text-color);
			}
		}
	}
}
.znpb-history__action {
	display: flex;
	justify-content: center;
	align-items: center;
	flex-grow: 1;
	padding: 15px 0;
	margin-right: 5px;
	background: var(--zb-surface-lighter-color);
	border-radius: 3px;
	transition: color linear 0.3s;
	cursor: pointer;
	&-wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		padding: 20px;
	}
	&--inactive {
		opacity: 0.6;
	}
	&:last-child {
		margin-right: 0;
	}

	&:hover {
		color: var(--zb-surface-text-hover-color);
	}

	// &--inactive, &--inactive:hover {
	// 	.zion-icon.zion-svg-inline {
	// 		color: var(--zb-surface-text-hover-color);
	// 	}
	// }

	span {
		margin-right: 7px;

		&:last-child {
			margin-right: 0;
		}
	}
}
</style>
