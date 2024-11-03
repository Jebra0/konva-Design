<template>
    <AdminLayout title="Edit Products" :user="user">
    <form @submit.prevent="form.put(route('admin.product.update', product.id))">
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
    data() {
        return {
        };
    },
    methods: {
        addOption() {
            this.form.options.push({
                opt_name: '',
                opt_values: [{ value: '', price: 0 }]
            });
        },
        addOptionValue(optionIndex) {
            this.form.options[optionIndex].opt_values.push({ value: '', price: 0 });
        },
        removeOption(optionIndex) {
            if (this.form.options.length > 1 && confirm('You going to delete an option, are you sure?')) {
                this.form.options.splice(optionIndex, 1);
            }
        },
        removeOptionValue(optionIndex, valIndex) {
            if (this.form.options[optionIndex].opt_values.length > 1) {
                this.form.options[optionIndex].opt_values.splice(valIndex, 1);
                console.log(this.form.options[optionIndex].opt_values);
            }
        },
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        product: {
            type: Object,
            required: true
        },
        errors: { type: Object },
    },
    setup(props) {
        const form = useForm({
            product_name: props.product.name || '',
            product_price: props.product.price || 0,
            product_quantity: props.product.quantity || '',
            options: props.product.options.map(option => ({
                opt_id: option.id || null,
                opt_name: option.name || '',
                opt_values: option.values.map(value => ({
                    value_id: value.id || null,
                    value: value.value,
                    price: value.price
                }))
            })),
        });

        return { form }
    }
};
</script>