<template>
	<base-panel
		:panel-name="$translate('history_panel')"
		panel-id="panel-history"
		:show-expand="false"
		v-on:close-panel="closePanel('panel-history')"
	>
		<div class="znpb-panel__history_panel_wrapper">
			<div
				class="znpb-history-wrapper znpb-fancy-scrollbar"
				ref="historyPanelWrapper"
			>
				<ul class="znpb-history-actions">
					<li
						:title="item.name"
						v-for="(item, index) in getHistoryItems"
						:class="{'znpb-history-action--active': activeHistoryIndex === index }"
						v-bind:key="index"
						ref="index"
						@click="restoreHistoryState(index)"
					>
						<span class="znpb-action-time">{{item.time}}</span>
						<span class="znpb-action-name">{{item.name}}</span>
						<span
							class="znpb-action-active"
							v-if="activeHistoryIndex === index"
						>{{$translate('history_now')}}</span>
						<BaseIcon
							v-else
							icon="history"
						></BaseIcon>
					</li>
				</ul>
			</div>
			<div class="znpb-history__action-wrapper">
				<div
					class="znpb-history__action"
					:class="{'znpb-history__action--inactive': ! canUndo }"
					@click="doUndo"
				>
					<BaseIcon icon="undo"></BaseIcon>
				</div>
				<div
					class="znpb-history__action"
					:class="{'znpb-history__action--inactive': ! canRedo }"
					@click="doRedo"
				>
					<BaseIcon icon="redo"></BaseIcon>
				</div>
			</div>
		</div>
	</base-panel>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
export default {
	name: 'panel-history',
	data: () => {
		return {}
	},
	computed: {
		...mapGetters([
			'getHistoryItems',
			'getActiveAreaContent',
			'canUndo',
			'canRedo',
			'activeHistoryIndex'
		])
	},
	methods: {
		...mapActions([
			'closePanel',
			'restoreHistoryState',
			'undo',
			'redo'
		]),
		doUndo () {
			if (this.canUndo) {
				this.undo()
			}
		},
		doRedo () {
			if (this.canRedo) {
				this.redo()
			}
		}
	},
	mounted () {
		// Scroll to last item
		this.$refs.historyPanelWrapper.scrollTop = this.$refs.historyPanelWrapper.scrollHeight
	},
	watch: {
		getHistoryItems () {
			this.$nextTick(() => {
				this.$refs.historyPanelWrapper.scrollTop = this.$refs.historyPanelWrapper.scrollHeight
			})
		}
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
	color: $font-color;
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
		background-color: $surface-variant;
		border-radius: 3px;

		.znpb-action-name {
			flex-grow: 1;
			overflow: hidden;
			width: 100%;
			color: $surface-active-color;
			font-weight: 500;
			text-overflow: ellipsis;
			white-space: nowrap;
		}

		.znpb-action-time {
			margin-right: 13px;
		}

		&:hover {
			background-color: darken($surface-variant, 3%);
			cursor: pointer;
		}

		&.znpb-history-action--active {
			color: $surface;
			background-color: $secondary;

			.znpb-action-name {
				color: $surface;
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
	background: $surface-variant;
	border-radius: 3px;
	transition: color linear .3s;
	cursor: pointer;
	&-wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		padding: 20px 30px;
	}
	&--inactive {
		opacity: .5;
	}
	&:last-child {
		margin-right: 0;
	}

	&:hover {
		color: darken($font-color, 15%);
	}

	// &--inactive, &--inactive:hover {
	// 	.zion-icon.zion-svg-inline {
	// 		color: darken($font-color, 15%);
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
