<template>
	<PageTemplate>

		<h3>{{$translate('replace_url')}}</h3>

		<div class="znpb-admin-replace">
			<h4 class="znpb-admin-replace__title">
				{{$translate('update_site_address_url')}}
			</h4>
			<BaseInput v-model="oldUrl" size="narrow">
				<span slot="prepend">https://</span>
			</BaseInput>
			<BaseIcon icon="long-arrow-right" class="znpb-admin-replace__icon"/>
			<BaseInput v-model="newUrl" size="narrow">
				<span slot="prepend">https://</span>
			</BaseInput>

		</div>
		<BaseButton type="line" class="znpb-admin-replace-button" @click.native="callReplaceUrl()">
			{{$translate('update_url')}}
		</BaseButton>
		<template slot="right">
			<div>
				<p class="znpb-admin-info-p">
					{{$translate('enter_old_and_new_url')}}
				</p>
				<p class="znpb-admin-info-p"><strong>Important:</strong> It is strongly recommended that you <a href="#">backup your database</a> before using Replace URL.</p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { mapActions } from 'vuex'

export default {
	name: 'ToolsPage',
	data () {
		return {
			loaded: false,
			localOldUrl: '',
			localNewUrl: ''
		}
	},
	computed: {
		oldUrl: {
			get () {
				return 'old-url.com'
			},
			set (newval) {
				this.localOldUrl = newval
			}
		},
		newUrl: {
			get () {
				return 'new-url.com'
			},
			set (newval) {
				this.localNewUrl = newval
			}
		}
	},
	methods: {
		...mapActions([
			'replaceUrl'
		]),
		callReplaceUrl () {
			this.replaceUrl([this.localOldUrl, this.localNewUrl])
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
	&-button {
		float: right;
	}
	&__icon {
		margin: 0 10px;
		font-size: 14px;
	}

	.zion-input__prefix {
		font-weight: 500;
		background: $surface-variant;
	}

	.zion-input__prepend {
		padding: 0 12px;
	}
}

</style>
