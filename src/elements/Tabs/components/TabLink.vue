<template>
	<li class="zb-el-tabs-nav-title" :class="{ 'zb-el-tabs-nav--active': active }">{{ title }}</li>
</template>

<script>
export default {
	name: 'Tablink',
	props: ['title', 'active'],
};
</script>
