<template>
    <AdminLayout title="Products" :user="user">
        <v-row class="d-flex align-center my-3">
            <v-col cols="9" class="px-0 py-1">
                <form @submit.prevent="searchProduct">
                    <v-card class="d-flex align-center ml-3 mr-1 px-2" outlined rounded>
                        <v-text-field v-model="search_data" label="Search" hide-details single-line dense clearable
                            class="flex-grow-1" outlined rounded></v-text-field>

                        <v-btn icon="mdi-magnify" type="submit" color="white" elevation="0" class="ml-2">
                        </v-btn>
                    </v-card>
                </form>
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
                            <tr v-for="item in data.data" :key="item.id">
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
                <v-card class="mt-5">
                    <v-pagination v-model="currentPage" :length="totalPages" @input="fetchProducts"></v-pagination>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";

export default {
    components: { AdminLayout, Link },
    data() {
        return {
            data: {},
            currentPage: 1,
            totalPages: 1,
            //search
            search_data: '',

            loading: false,

            deleteForm: useForm({
                id: null
            })
        };
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        products: {
            type: Object,
            required: true
        },

    },
    mounted() {
        this.fetchProducts();
    },
    methods: {
        fetchProducts() {
            this.loading = true;
            axios.get(`api/products?page=${this.currentPage}`)
                .then(response => {
                    this.data = response.data;
                    this.totalPages = response.data.last_page;
                    this.loading = false;
                })
                .catch(error => {
                    console.error("Error fetching products:", error);
                    this.loading = false;
                });
        },
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
        searchProduct() {
            this.loading = true;
            this.currentPage = 1;
            axios.post(route('admin.product.search'), { data: this.search_data })
                .then(response => {
                    console.log(response.data.last_page)
                    this.data = response.data;
                    this.totalPages = response.data.last_page;
                    this.loading = false;
                })
                .catch(error => {
                    console.error("No data found:", error);
                    this.loading = false;
                });
        }
    },
    watch: {
        currentPage() {
            if (this.search_data) {
                this.searchProduct();
            } else {
                this.fetchProducts();
            }
        },
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