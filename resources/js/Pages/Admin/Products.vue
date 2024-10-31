<template>
    <AdminLayout title="Products" :user="user">

    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
export default {
    components: {AdminLayout},
    data: ()=>{
        return {

        }
    },
    props:{
        user: {
            type: Object,
            required: true
        },
    }
}
</script>