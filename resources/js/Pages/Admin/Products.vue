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
                <TailwindPagination align="center" limit="15" :data="products" @pagination-change-page="getResults"/>
            </v-col>
        </v-row>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { TailwindPagination } from 'laravel-vue-pagination';

export default {
    components: {AdminLayout, TailwindPagination},
    data(){
        return {
            data: this.products,
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
        //console.log(products);
    },
    methods: {
        getResults(page) {
            axios.get(`/admin/dashboard/products/data?page=${page}`).then(response => {
                this.data = response.data; 
            });
        }
    }

}
</script>