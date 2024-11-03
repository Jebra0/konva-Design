<template>
    <AdminLayout title="Create Products" :user="user">
        <form @submit.prevent="form.post(route('admin.product.store'))">
            <Form :form="form" :errors="errors" />
        </form>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Form from '@/Pages/Admin/Products/Partials/_form.vue'

export default {
    components: { AdminLayout, Link, useForm, Form },
    props: {
        user: {
            type: Object,
            required: true
        },
        errors: { type: Object },
    },
    setup() {
        const form = useForm({
            product_name: '',
            product_price: 0,
            product_quantity: '',
            options: [
                {
                    opt_name: '',
                    opt_values: [
                        {
                            value: '',
                            price: ''
                        }
                    ]
                }
            ],
        });

        return { form }
    }
}
</script>
