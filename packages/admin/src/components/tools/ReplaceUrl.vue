<template>
	<PageTemplate>

		<h3>{{$translate('replace_url')}}</h3>

		<div class="znpb-admin-replace">
			<h4 class="znpb-admin-replace__title">
				{{$translate('update_site_address_url')}}
			</h4>
			<BaseInput
				v-model="oldUrl"
				:placeholder="$translate('old_url')"
				size="narrow"
			>

			</BaseInput>
			<BaseIcon
				icon="long-arrow-right"
				class="znpb-admin-replace__icon"
			/>
			<BaseInput
				v-model="newUrl"
				:placeholder="$translate('new_url')"
				size="narrow"
			>

			</BaseInput>

		</div>
		<div class="znpb-admin-replace__actions">
			<BaseButton
				:type="disabled ? 'disabled' : 'line'"
				class="znpb-admin-replace-button"
				@click="callReplaceUrl()"
			>
				<transition
					name="fade"
					mode="out-in"
				>
					<Loader
						v-if="loading"
						:size="13"
					/>
					<span v-else>{{$translate('update_url')}}</span>
				</transition>
			</BaseButton>

			<p
				v-if="message.length"
				v-html="message"
			></p>
		</div>
		<template v-slot:right>
			<div>
				<p class="znpb-admin-info-p">
					{{$translate('enter_old_and_new_url')}}
				</p>
				<p
					class="znpb-admin-info-p"
					v-html="$translate('replace_info')"
				></p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { replaceUrl } from '@zb/rest'
import { BaseIcon, BaseButton, Loader } from '@zb/components'

export default {
	name: 'ToolsPage',
	components: {
		BaseIcon,
		BaseButton,
		Loader
	},
	data () {
		return {
			loading: false,
			oldUrl: '',
			newUrl: '',
			message: ''
		}
	},
	computed: {
		disabled () {
			return !((this.oldUrl.length > 0 && this.newUrl.length > 0))
		}
	},
	methods: {
		callReplaceUrl () {
			this.message = ''
			this.loading = true
			replaceUrl({
				find: this.oldUrl,
				replace: this.newUrl
			}).then((response) => {
				this.loading = false
				this.message = response.data.message
			}).catch((error) => {
				this.loading = false
				console.error('error', error.message)
			}).finally(() => {
				setTimeout(() => {
					this.message = ''
				}, 5000)
			})
		}
	}

}
</script>
<style lang="scss">
.znpb-admin-replace {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 13px;
	h4.znpb-admin-replace__title {
		flex-basis: 80%;
		margin-right: 30px;
		margin-bottom: 0;
	}
	&__actions {
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		align-items: flex-end;
		p {
			color: $info;
		}
	}

	&__icon {
		margin: 0 10px;
		font-size: 14px;
	}
}
</style>
