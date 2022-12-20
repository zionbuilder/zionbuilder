<template>
	<PageTemplate>
		<h3>{{ __('Replace URL', 'zionbuilder') }}</h3>

		<div class="znpb-admin-replace">
			<h4 class="znpb-admin-replace__title">
				{{ __('Update Site Address (URL)', 'zionbuilder') }}
			</h4>
			<BaseInput v-model="oldUrl" :placeholder="__('Old URL', 'zionbuilder')" size="narrow"> </BaseInput>
			<Icon icon="long-arrow-right" class="znpb-admin-replace__icon" />
			<BaseInput v-model="newUrl" :placeholder="__('New URL', 'zionbuilder')" size="narrow"> </BaseInput>
		</div>
		<div class="znpb-admin-replace__actions">
			<Button :type="disabled ? 'disabled' : 'line'" class="znpb-admin-replace-button" @click="callReplaceUrl">
				<transition name="fade" mode="out-in">
					<Loader v-if="loading" :size="13" />
					<span v-else>{{ __('Update URL', 'zionbuilder') }}</span>
				</transition>
			</Button>

			<!-- eslint-disable-next-line vue/no-v-html The message comes from the server and may contain markup -->
			<p v-if="message.length" v-html="message"></p>
		</div>
		<template #right>
			<div>
				<p class="znpb-admin-info-p">
					{{
						__(
							'Enter your old and new URLs for your WordPress installation, to update all references (Relevant for domain transfers or move to "HTTPS").',
							'zionbuilder',
						)
					}}
				</p>

				<!-- eslint-disable-next-line vue/no-v-html The message comes from the server and may contain markup -->
				<p class="znpb-admin-info-p" v-html="panelInfo"></p>
			</div>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';
import { replaceUrl } from '@zb/api';

const loading = ref(false);
const message = ref('');
const oldUrl = ref('');
const newUrl = ref('');

const disabled = computed(() => {
	return !(oldUrl.value.length > 0 && newUrl.value.length > 0);
});

const panelInfo = __(
	`<strong>Important:</strong> It is strongly recommended that you
					<a href="https://zionbuilder.io/documentation/replace-url-s/" target="_blank">backup your database</a> before using Replace
					URL.`,
	'zionbuilder',
);

function callReplaceUrl() {
	message.value = '';
	loading.value = true;

	replaceUrl({
		find: oldUrl.value,
		replace: newUrl.value,
	})
		.then(response => {
			loading.value = false;
			message.value = response.data.message;
		})
		.catch(() => {
			loading.value = false;
		})
		.finally(() => {
			setTimeout(() => {
				message.value = '';
			}, 5000);
		});
}
</script>
<style lang="scss">
.znpb-admin-replace {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 13px;

	@media (max-width: 575px) {
		flex-wrap: wrap;

		.zion-input {
			margin-bottom: 8px;
		}
	}

	h4.znpb-admin-replace__title {
		flex-basis: 80%;
		margin-right: 30px;
		margin-bottom: 0;

		@media (max-width: 575px) {
			margin-bottom: 12px;
		}
	}
	&__actions {
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		align-items: flex-end;
		p {
			color: var(--zb-info-color);
		}
	}

	&__icon {
		margin: 0 10px;
		font-size: 14px;

		@media (max-width: 575px) {
			display: none;
		}
	}
}
</style>
