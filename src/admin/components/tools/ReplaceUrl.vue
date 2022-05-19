<template>
	<PageTemplate>
		<h3>{{ $translate('replace_url') }}</h3>

		<div class="znpb-admin-replace">
			<h4 class="znpb-admin-replace__title">
				{{ $translate('update_site_address_url') }}
			</h4>
			<BaseInput v-model="oldUrl" :placeholder="$translate('old_url')" size="narrow"> </BaseInput>
			<Icon icon="long-arrow-right" class="znpb-admin-replace__icon" />
			<BaseInput v-model="newUrl" :placeholder="$translate('new_url')" size="narrow"> </BaseInput>
		</div>
		<div class="znpb-admin-replace__actions">
			<Button :type="disabled ? 'disabled' : 'line'" class="znpb-admin-replace-button" @click="callReplaceUrl">
				<transition name="fade" mode="out-in">
					<Loader v-if="loading" :size="13" />
					<span v-else>{{ $translate('update_url') }}</span>
				</transition>
			</Button>

			<p v-if="message.length" v-html="message"></p>
		</div>
		<template #right>
			<div>
				<p class="znpb-admin-info-p">
					{{ $translate('enter_old_and_new_url') }}
				</p>
				<p class="znpb-admin-info-p" v-html="$translate('replace_info')"></p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { ref, computed } from 'vue';
import { replaceUrl } from '@common/api';

export default {
	name: 'ToolsPage',
	setup() {
		const loading = ref(false);
		const message = ref('');
		const oldUrl = ref('');
		const newUrl = ref('');

		const disabled = computed(() => {
			let check = !(oldUrl.value.length > 0 && newUrl.value.length > 0);
			return check;
		});

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
				.catch(error => {
					loading.value = false;
				})
				.finally(() => {
					setTimeout(() => {
						message.value = '';
					}, 5000);
				});
		}
		return {
			loading,
			message,
			oldUrl,
			newUrl,
			callReplaceUrl,
			disabled,
		};
	},
};
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
