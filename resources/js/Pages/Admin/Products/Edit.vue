<template>
	<AdminLayout title="Edit Products" :user="user">

	</AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";

export default {
    components: {AdminLayout, Link},
    data(){
    	return {

    	};
    },
    mounted() {
        //console.log('User prop:', this.user);
    },
    props:{
        user: {
            type: Object,
            required: true
        },
        product: {
            type: Object,
            required: true
        },
    },
}
</script>