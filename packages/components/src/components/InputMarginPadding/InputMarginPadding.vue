<template>
	<div class="option">
		<div class="option__margin">
			<div class="option__value option__valueMargin option__valueMargin--top">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valueMargin option__valueMargin--right">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valueMargin option__valueMargin--bottom">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valueMargin option__valueMargin--left">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__label">
				<span class="option__labelTitle">Margin</span>
				<span class="option__link">
					<svg
						class="zion-svg-inline znpb-editor-icon zion-unlink zion-icon"
						xmlns="http://www.w3.org/2000/svg"
						aria-hidden="true"
						viewBox="0 0 28 28"
						preserveAspectRatio=""
					>
						<path
							fill="currentColor"
							d="M4 14c0-2.2 1.8-4 4-4h2.2l4-4H8c-4.4 0-8 3.6-8 8 0 1.7.6 3.3 1.5 4.7l2.9-2.9c-.2-.6-.4-1.2-.4-1.8zM23.8 7l1.5-1.5-2.8-2.8L2.7 22.5l2.8 2.8L8.8 22H20c4.4 0 8-3.6 8-8 0-3-1.7-5.6-4.2-7zM20 18h-7.2l7.9-7.9c1.8.4 3.2 2 3.2 3.9.1 2.2-1.7 4-3.9 4z"
						></path>
					</svg>
				</span>
			</div>
			<div class="option__svg">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 320 186"
				>
					<path
						cursor="s-resize"
						fill="#2F2F34"
						d="M0 0h320l-50 36H50L0 0Z"
					/>
					<path
						cursor="s-resize"
						fill="#2F2F34"
						d="M50 150h220l50 36H0l50-36Z"
					/>
					<path
						cursor="w-resize"
						fill="#35353A"
						d="m0 3 50 36v108L0 183V3Z"
					/>
					<path
						cursor="w-resize"
						fill="#35353A"
						d="m320 183-50-36V39l50-36v180Z"
					/>
				</svg>
			</div>
		</div>
		<div class="option__padding">
			<div class="option__value option__valuePadding option__valuePadding--top">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valuePadding option__valuePadding--right">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valuePadding option__valuePadding--bottom">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__value option__valuePadding option__valuePadding--left">
				<input
					type="text"
					placeholder="-"
					readonly
				>
			</div>
			<div class="option__label">
				<span class="option__labelTitle">Padding</span>
				<span class="option__link">
					<svg
						class="zion-svg-inline znpb-editor-icon zion-unlink zion-icon"
						xmlns="http://www.w3.org/2000/svg"
						aria-hidden="true"
						viewBox="0 0 28 28"
						preserveAspectRatio=""
					>
						<path
							fill="currentColor"
							d="M4 14c0-2.2 1.8-4 4-4h2.2l4-4H8c-4.4 0-8 3.6-8 8 0 1.7.6 3.3 1.5 4.7l2.9-2.9c-.2-.6-.4-1.2-.4-1.8zM23.8 7l1.5-1.5-2.8-2.8L2.7 22.5l2.8 2.8L8.8 22H20c4.4 0 8-3.6 8-8 0-3-1.7-5.6-4.2-7zM20 18h-7.2l7.9-7.9c1.8.4 3.2 2 3.2 3.9.1 2.2-1.7 4-3.9 4z"
						></path>
					</svg>
				</span>
			</div>
			<div class="option__svg">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 214 108"
				>
					<path
						cursor="s-resize"
						fill="#2F2F34"
						d="M0 0h214l-50 36H50L0 0Z"
					/>
					<path
						cursor="s-resize"
						fill="#2F2F34"
						d="M214 108H0l50-36h114l50 36Z"
					/>
					<path
						cursor="w-resize"
						fill="#35353A"
						d="m0 3 50 36v30L0 105V3Z"
					/>
					<path
						cursor="w-resize"
						fill="#35353A"
						d="m214 105-50-36V39l50-36v102Z"
					/>
				</svg>
			</div>
		</div>
		<span class="option__info">Margin right</span>
	</div>
</template>

<script>
export default {
	name: 'InputMarginPadding',
	setup (props, { emit }) {
		return {}
	}
}
</script>

<style lang="scss">
.option {
	display: grid;
	width: 320px;
	height: 186px;
	outline-style: none;
	user-select: none;

	grid-template-columns: 50px 3px 50px 1fr 50px 3px 50px;
	grid-template-rows: 36px 3px 36px 1fr 36px 3px 36px;
}

.option__margin {
	position: relative;
	display: grid;
	width: 320px;
	height: 186px;

	grid-area: 1 / 1 / -1 / -1;
	grid-template-columns: 50px 1fr 50px;
	grid-template-rows: 36px minmax(36px, 1fr) 36px;
	justify-items: center;
}

.option__padding {
	position: relative;
	display: grid;
	width: 214px;
	height: 108px;

	grid-area: 3 / 3 / span 3 / span 3;
	grid-template-columns: 50px 1fr 50px;
	grid-template-rows: 36px minmax(36px, 1fr) 36px;
	justify-items: center;
}

.option__info {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 108px;
	height: 30px;
	margin: 3px 0 0 3px;
	color: #858585;
	font-size: 10px;
	font-weight: bold;
	line-height: 1;
	text-transform: uppercase;

	grid-area: 4 / 4 / span 4 / span 4;
}

.option__label {
	position: absolute;
	top: 4px;
	left: 24px;
	display: flex;
	align-items: center;
}

.option__labelTitle {
	color: #606060;
	font-size: 9px;
	font-weight: bold;
	line-height: 1;
	text-transform: uppercase;
	pointer-events: none;
}

.option__link {
	margin-left: 4px;
	color: #858585;
	font-size: 12px;
	cursor: pointer;
}

.option__link svg {
	display: block;
	width: 1em;
	height: 1em;

	fill: currentColor;
}

.option__value {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 2px;
	color: #858585;
	font-size: 11px;
	font-weight: 500;
	line-height: 1;
	text-overflow: ellipsis;
	white-space: nowrap;
	cursor: pointer;
	user-select: none;

	place-self: center;
}

.option__value input {
	max-width: 40px;
	color: #858585;
	font-weight: bold;
	text-align: center;
	background: transparent;
	border: 0;
	cursor: pointer;
}

.option__value input:focus {
	outline: none;
}

.option__valueMargin--top {
	grid-area: 1 / 2 / 2 / 3;
}

.option__valueMargin--right {
	grid-area: 2 / 3 / 3 / 4;
}

.option__valueMargin--bottom {
	grid-area: 3 / 2 / 4 / 3;
}

.option__valueMargin--left {
	grid-area: 2 / 1 / 3 / 2;
}

.option__valuePadding--top {
	grid-area: 1 / 2 / 2 / 3;
}

.option__valuePadding--right {
	grid-area: 2 / 3 / 3 / 4;
}

.option__valuePadding--bottom {
	grid-area: 3 / 2 / 4 / 3;
}

.option__valuePadding--left {
	grid-area: 2 / 1 / 3 / 2;
}

.option__svg {
	grid-area: 1 / 1 / -1 / -1;
}

.option__svg svg {
	display: block;
	width: 100%;
	height: 100%;
}

.option__svg path {
	transition: fill .1s;
}

.option__svg path:hover {
	fill: #3e3e43;
}
</style>