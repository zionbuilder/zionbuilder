<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left">
		</div>
		<PageTemplate>

			<Loader v-if="loaded" />
			<template v-else>
				<h3>{{$translate('pro_license_key')}}</h3>
				<div
					v-if="!hasValidLicense"
					class="znpb-admin-license-inputWrapper"
				>
					<BaseInput
						v-model="license"
						:placeholder="$translate('key_example')"
						size="narrow"
					/>
					<BaseButton
						type="line"
						@click.native="callCheckLicense(license)"
					>
						<transition
							name="fade"
							mode="out-in"
						>
							<Loader
								v-if="messageLoading"
								:size="13"
							/>
							<span v-else>{{$translate('add_license')}}</span>
						</transition>
					</BaseButton>

				</div>
				<template v-else>
					<div class="znpb-admin-templates-titles">
						<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--title">{{$translate('key')}}</h5>
						<h5 class="znpb-admin-templates-titles__heading">{{$translate('valid_until')}}</h5>
						<h5 class="znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--actions">{{$translate('actions')}}</h5>
					</div>
					<div class="znpb-admin-single-template">

						<span
							class="znpb-admin-single-template__title"
							v-html="license"
						></span>
						<span
							class="znpb-admin-single-template__author"
							v-html="getValidDate"
						></span>

						<div class="znpb-admin-single-template__actions">
							<Tooltip
								:content="$translate('delete_key')"
								append-to="element"
								class="znpb-admin-single-template__action znpb-delete-icon-pop"
								:modifiers="{ offset: { offset: '0,15px' } }"
								placement="top"
								:positionFixed="true"
							>
								<BaseIcon
									icon="delete"
									@click.native="deleteKey"
								/>
							</Tooltip>
						</div>

					</div>
				</template>
				<p
					v-if="message.length"
					v-html="message"
					class="znpb-admin-license__error-message"
				></p>
			</template>
			<template slot="right">
				<div>
					<p class="znpb-admin-info-p">{{$translate('pro_info')}}</p>
					<p class="znpb-admin-info-p">{{$translate('pro_info_desc')}}</p>
				</div>
			</template>
		</PageTemplate>

	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { BaseInput } from '@/common/components/forms'
import { Tooltip } from '@/common/components/tooltip'
export default {
	name: 'ProLicense',
	components: {
		BaseInput,
		Tooltip
	},
	data () {
		return {
			loaded: false,
			license: 'gtrhththtthtt',
			hasValidLicense: true,
			message: '',
			messageLoading: false
		}
	},
	computed: {
		getValidDate () {
			return '21.05.2021'
		}
	},
	methods: {
		deleteKey () {
			this.license = ''
			this.hasValidLicense = false
		},
		callCheckLicense (licenseKey) {
			if (licenseKey.length === 0) {
				this.message = this.$translate('no_license_input')
			}
		}
	},
	created () {
		// fetch store to see if key is valid

	}
}
</script>
<style lang="scss">
.znpb-admin-license {
	&-inputWrapper {
		display: flex;
		align-items: center;

		.zion-input {
			margin-right: 10px;
		}

		.znpb-button {
			min-width: 150px;
		}
	}
	&__error-message {
		color: #e84655;
	}
}
</style>
