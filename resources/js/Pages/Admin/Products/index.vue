<template>
    <AdminLayout title="Products" :user="user">
        <v-row class="d-flex align-center my-0">
            <v-col cols="9" class="px-0 py-1">
                <form @submit.prevent="searchProduct">
                    <input 
                        type="text" 
                        v-model="search_data" 
                        class="searchInput" 
                        placeholder="search"
                    >
                    <!-- <div v-if="searchForm.errors.data">{{ searchForm.errors.data }}</div> -->
                    <button type="submit" class="searchBTN"><v-icon>mdi-magnify</v-icon></button>
                </form>
            </v-col>
            <v-col cols="3" class="d-flex justify-end" >
                <v-btn width="100%" class="createProductBTN">
                    <Link :href="route('admin.product.create')" class="mb-2">Add New Product</Link>
                </v-btn>
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
                    <v-pagination v-model="currentPage" :length="totalPages" @input="fetchProducts" ></v-pagination>
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
            search_data: '',
            // searchForm: useForm({
            //     data: '',
            // }),

            loading: false,

            deleteForm: useForm({
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
.searchInput{
    width: 90%;
    margin: 12px;
}
.searchBTN{
    background-color: white;
    padding: 7px;
    padding-top: 9px;
    margin-left: -13px;
    border-left: 1px solid #3333;
}
.searchBTN:hover{
    background-color: #3333;
    color: white
}
.createProductBTN{
    padding-top: 8px;
    padding-bottom: 8px;
}
</style>