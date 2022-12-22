<template>
	<div class="znpb-key-shortcuts-wrapper">
		<slot></slot>
		<div class="znpb-key-shortcuts">
			<div class="znpb-key-shortcuts__keys-wrapper">
				<div v-for="(key, index) in shortcutKey" :key="index" class="znpb-key-shortcuts__key">
					<div class="znpb-key-shortcuts__key-item">
						{{ key }}
					</div>
					<span class="znpb-key-shortcuts__plus">+</span>
				</div>
			</div>
			<span class="znpb-key-shortcuts__separator"></span>
			<div class="znpb-key-shortcuts__description">
				{{ description }}
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
defineProps<{
	shortcutKey: string[];
	description: string;
}>();
</script>

<style lang="scss">
.znpb-key-shortcuts {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	margin-bottom: 20px;
	&__plus {
		padding: 10px;
		font-weight: 700;
		opacity: 0.4;
	}
	&__keys-wrapper {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		width: 40%;
		color: var(--zb-surface-text-active-color);
		font-size: 11px;
	}
	&__key {
		display: flex;
		align-items: center;
		&:last-child {
			.znpb-key-shortcuts__plus {
				display: none;
			}
		}
	}
	&__key-item {
		display: flex;
		display: flex;
		justify-content: center;
		min-width: 30px;
		padding: 6px;
		font-weight: 700;
		border: 1px solid var(--zb-surface-border-color);
		border-radius: 2px;
	}
	&__description {
		width: 40%;
		font-weight: 500;
		&--details {
			display: inline-block;
			font-size: 12px;
		}
	}
	&__separator {
		display: block;
		width: 30px;
		height: 1px;
		padding: 0 20px;
		margin: 0 20px;
		background-color: var(--zb-surface-border-color);
	}
}
</style>
