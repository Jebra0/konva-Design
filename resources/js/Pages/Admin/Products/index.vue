<template>
    <AdminLayout title="Products" :user="$page.props.auth.user">
        <v-row class="d-flex align-center my-3">
            <v-col cols="9" class="px-0 py-1">
                <v-card class="d-flex align-center ml-3 mr-1 px-2" outlined rounded>
                    <v-text-field v-model="searchProductForm.searshData" label="Search" hide-details single-line dense clearable outlined
                        rounded></v-text-field>
                    <div v-if="errors.searshData" class="text-red-600">{{ errors.searshData }}</div>

                    <v-btn @click="search()" icon="mdi-magnify" color="white" elevation="0"
                        class="ml-2"></v-btn>
                </v-card>
            </v-col>

            <v-col cols="3" class="d-flex justify-end">
                <Link class="" :href="route('admin.product.create')">
                <v-btn width="240px" height="53px" class="createProductBTN">
                    Add New Product
                </v-btn>
                </Link>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" class="pt-0">
                <v-card>
                    <v-table fixed-header>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in products.data" :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.quantity ?? '-' }}</td>
                                <td>$ {{ item.price }}</td>

                                <td>
                                    <Link :href="route('admin.product.edit', item.id)">
                                    <v-btn color="green">edit</v-btn>
                                    </Link>
                                </td>

                                <td>
                                    <form @submit.prevent="deleteProduct(item.id)">
                                        <v-btn color="red" type="submit">
                                            delete
                                        </v-btn>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
                <Pagination :links="products.links" />
            </v-col>
        </v-row>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

export default {
    components: { AdminLayout, Link, useForm, Pagination },
    data() {
        return {
            searchProductForm: useForm({
                searshData: null,
            }),

            deleteForm: useForm({
                id: null
            })
        };
    },
    props: {
        products: {
            type: Object,
            required: true
        },
        errors: { type: Object }
    },
    methods: {
        deleteProduct(id) {
            if (confirm('Are you sure?')) {
                this.deleteForm.delete(route('admin.product.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetchProducts();
                    },
                    onError: (error) => {
                        console.error("Delete failed:", error);
                    }
                });
            }
        },
        search() {
            this.searchProductForm.get(route('admin.product.search'));
        }
    },
    
}
</script>
<style>
.textInput {
    width: 90%;
    margin: 12px;
}

.searchBTN {
    background-color: white;
    padding: 7px;
    padding-top: 9px;
    margin-left: -13px;
    border-left: 1px solid #3333;
}

.searchBTN:hover {
    background-color: #3333;
    color: white
}

.createProductBTN {
    padding-top: 8px;
    padding-bottom: 8px;
}
</style>