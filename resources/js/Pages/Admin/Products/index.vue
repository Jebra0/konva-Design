<template>
    <AdminLayout title="Products" :user="user">
        <v-row class="d-flex align-center">
            <v-col cols="9" class="px-0">
                <v-card-text>
                  <v-text-field 
                    :loading="loading"
                    append-inner-icon="mdi-magnify"
                    density="compact"
                    label="Search"
                    variant="solo"
                    hide-details
                    single-line
                    @click:append-inner="onClick"
                  ></v-text-field>
                </v-card-text>
            </v-col>
            <v-col cols="3" class="d-flex justify-end" >
                <v-btn width="100%">
                    <Link :href="route('admin.product.create')">Add New Product</Link>
                </v-btn>
            </v-col>    
        </v-row>
        <v-row>
            <v-col cols="12">
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
                    <v-pagination v-model="currentPage" :length="products.last_page" class="" @input="fetchProducts" ></v-pagination>
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
    components: {AdminLayout, Link},
    data(){
        return {
            data: {},
            currentPage: 1,
            totalPages: 1,
            //search
            loaded: false,
            loading: false,
            form: useForm({
                id: null
            })
        };
    },
    props:{
        user: {
            type: Object,
            required: true
        },
        products: {
            type: Object,
            required: true
        },
        
    },
    mounted(){
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
        onClick () {
            this.loading = true

            setTimeout(() => {
              this.loading = false
              this.loaded = true
            }, 2000)
        },
        deleteProduct(id) {
            if (confirm('Are you sure?')) {
                this.form.delete(route('admin.product.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetchProducts();
                    },
                    onError: (error) => {
                        console.error("Delete failed:", error);
                    }
                });
            }
        }

    },
    watch: {
        currentPage() {
          this.fetchProducts(); 
        }
    },

}
</script>