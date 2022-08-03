<template>
	<BasePanel :panel-name="translate('history_panel')" panel-id="panel-history" :show-expand="false" :panel="panel">
		<div class="znpb-panel__history_panel_wrapper">
			<div ref="historyPanelWrapper" class="znpb-history-wrapper znpb-fancy-scrollbar">
				{{ historyStore.state }}
				<ul class="znpb-history-actions">
					<li
						v-for="(item, index) in historyStore.state"
						:key="index"
						:title="item.title"
						:class="{ 'znpb-history-action--active': historyStore.activeHistoryIndex === index }"
						@click="historyStore.restoreHistoryToIndex(index)"
					>
						<!-- <span class="znpb-action-time">{{ item.time }}</span> -->
						<span class="znpb-action-name">{{ item.title }}</span>
						<span class="znpb-action-name">{{ item.subtitle }}</span>
						<span class="znpb-action-name">{{ item.action }}</span>
						<span v-if="historyStore.activeHistoryIndex === index" class="znpb-action-active">{{
							translate('history_now')
						}}</span>
						<Icon v-else icon="history"></Icon>
					</li>
				</ul>
			</div>
			<div class="znpb-history__action-wrapper">
				<div
					class="znpb-history__action"
					:class="{ 'znpb-history__action--inactive': !historyStore.canUndo }"
					@click="doUndo"
				>
					<Icon icon="undo"></Icon>
				</div>
				<div
					class="znpb-history__action"
					:class="{ 'znpb-history__action--inactive': !historyStore.canRedo }"
					@click="doRedo"
				>
					<Icon icon="redo"></Icon>
				</div>
			</div>
		</div>
	</BasePanel>
</template>

<script lang="ts" setup>
import { onMounted, watch, ref, nextTick, computed } from 'vue';
import { useHistoryStore } from '/@/editor/store';
import { translate } from '/@/common/modules/i18n';

// Components
import BasePanel from './BasePanel.vue';

defineProps<{
	panel: ZionPanel;
}>();

const historyPanelWrapper = ref(null);
const historyStore = useHistoryStore();

watch(historyStore.state, newValue => {
	nextTick(() => {
		historyPanelWrapper.value.scrollTop = historyPanelWrapper.value.scrollHeight;
	});
});

onMounted(() => {
	historyPanelWrapper.value.scrollTop = historyPanelWrapper.value.scrollHeight;
});

function doUndo() {
	if (historyStore.canUndo) {
		historyStore.undo();
	}
}
function doRedo() {
	if (historyStore.canRedo) {
		historyStore.redo();
	}
}
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
	display: flex;
	flex-direction: column-reverse;

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
