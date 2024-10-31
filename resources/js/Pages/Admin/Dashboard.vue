<template>
    <AdminLayout title="Dashboard" :user="user">
        <v-row>
            <v-col cols="12">
                <v-card class="">
                    <v-card-title style="font-size: 30px;">Dashboard</v-card-title>
                </v-card>
            </v-col>
        </v-row> 
        <v-row> 
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Total Orders</v-card-title>
                    <v-card-text>{{total_orders}}</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Total Revenue</v-card-title>
                    <v-card-text>$ {{total_revenue}}</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Average Order Value</v-card-title>
                    <v-card-text>$ {{AOV}}</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Completed Orders</v-card-title>
                    <v-card-text> {{completed_orders}}</v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Pending Orders</v-card-title>
                    <v-card-text>{{pending_orders}}</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Cancelled Orders</v-card-title>
                    <v-card-text>{{cancelled_orders}}</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Abandoned Cart</v-card-title>
                    <v-card-text>1500</v-card-text>
                </v-card>
            </v-col>
            <v-col cols="3" class="text-center">
                <v-card class="">
                    <v-card-title>Refunded Orders</v-card-title>
                    <v-card-text>{{refunded_orders}}</v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-card class="text-center">
                    <v-card-title class="mb-0">Top silling products</v-card-title>
                    <v-table fixed-header>
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Quantity in stock
                                </th>
                                <th>
                                    Price
                                </th>
                                <th>
                                    # of sales
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in top_products" :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.quantity ?? '-' }}</td>
                                <td>$ {{ item.price }}</td>
                                <td>{{ item.orders_count }}</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
export default {
    components: { AdminLayout },
    mounted: () => {
        // console.log(top_products)
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        top_products: {
            type: Object,
            required: true
        },
        total_orders: {type: Number},
        total_revenue: {type: Number},
        AOV: {type: Number},
        completed_orders: {type: Number},
        pending_orders: {type: Number},
        cancelled_orders: {type: Number},
        refunded_orders: {type: Number},
    },
    data: () => {
        return {

        }
    },
}
</script>