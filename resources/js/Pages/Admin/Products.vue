<template>
    <AdminLayout title="Products" :user="user">
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
                                <td><v-icon>mdi-pencil-outline</v-icon></td>
                                <td><v-icon>mdi-delete</v-icon></td>
                            </tr>
                        </tbody>
                    </v-table>  
                </v-card>
                <v-card class="mt-5">
                    <v-pagination v-model="currentPage" :length="totalPages" class="" @input="getResults" ></v-pagination>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';

export default {
    components: {AdminLayout},
    data(){
        return {
            data: { data: [] },
            currentPage: 1,
            totalPages: 1
        };
    },
    props:{
        user: {
            type: Object,
            required: true
        },
        
    },
    mounted(){
        // console.log(products);
        // console.log(data);
        this.getResults();
    },
    methods: {
        getResults() {
            axios.get(`/admin/dashboard/products/data?page=${this.currentPage}`)
                .then(response => {
                    this.data = response.data; 
                    this.totalPages = response.data.last_page;
                })
                .catch(error => {
                    console.error("Error fetching products:", error);
                });
        }
    },
    watch: {
        currentPage() {
          this.getResults(); 
        }
    },

}
</script>