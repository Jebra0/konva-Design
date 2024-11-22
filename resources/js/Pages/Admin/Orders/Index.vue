<template>
    <AdminLayout title="Orders" :user="$page.props.auth.user">
        <v-row>
            <v-col cols="12">
                <v-row>
                    <v-col cols="2">
                        <v-card>
                            <v-text-field label="Order Number" v-model="filterForm.number" outlined dense
                                @input="filter()" hide-details ></v-text-field>
                            <div v-if="errors.number" class="text-red-600">{{ errors.number }}</div>
                        </v-card>
                    </v-col>

                    <v-col cols="2">
                        <v-card>
                            <v-text-field hide-details label="User Id" v-model="filterForm.user" outlined dense
                                @input="filter()"></v-text-field>
                            <div v-if="errors.user" class="text-red-600">{{ errors.user }}</div>
                        </v-card>
                    </v-col>

                    <v-col cols="4">
                        <v-card class="d-flex justify-between">
                            <label class="ml-3 my-4">Order Status</label>
                            <select @change="filter()" v-model="order_status" class="my-2">
                                <option value=""></option>
                                <option value="pending">pending</option>
                                <option value="processing">processing</option>
                                <option value="completed">completed</option>
                                <option value="cancelled">cancelled</option>
                                <option value="refunded">refunded</option>
                            </select>
                            <div v-if="errors.status" class="text-red-600">{{ errors.status }}</div>
                        </v-card>
                    </v-col>

                    <v-col cols="4">
                        <v-card class="d-flex justify-between">
                            <label class="ml-3 my-4">Payment Status</label>
                            <select @change="filter()" v-model="payment_status" class="my-2">
                                <option value=""></option>
                                <option value="pending">pending</option>
                                <option value="paid">paid</option>
                                <option value="failed">failed</option>
                            </select><br>
                            <div v-if="errors.payment_status" class="text-red-600">{{ errors.payment_status
                                }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" class="pt-0 mt-3">
                <v-card>
                    <v-table fixed-header>
                        <thead>
                            <tr>
                                <th>User Id</th>
                                <th>Order Numbr</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Payment Method</th>
                                <th>Shipping Cost</th>
                                <th>Tax</th>
                                <th>Discount</th>
                                <th>Order Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in orders.data" :key="item.id">
                                <td>{{ item.user_id }}</td>
                                <td>{{ item.number }}</td>

                                <td>
                                    <select @change="selectStatus($event, item.id)">
                                        <option :selected="item.status === 'pending'" value="pending">Pending</option>

                                        <option :selected="item.status === 'processing'" value="processing">Processing
                                        </option>

                                        <option :selected="item.status === 'completed'" value="completed">Completed
                                        </option>

                                        <option :selected="item.status === 'cancelled'" value="cancelled">Cancelled
                                        </option>

                                        <option :selected="item.status === 'refunded'" value="refunded">Refunded
                                        </option>
                                    </select>
                                    <div v-if="errors.statusValue" class="text-red-600">{{ errors.statusValue }}</div>
                                </td>

                                <td>
                                    <select @change="selectPayStatus($event, item.id)">
                                        <option :selected="item.payment_status == 'pending'" value="pending">Pending
                                        </option>

                                        <option :selected="item.payment_status == 'paid'" value="paid">Paid</option>

                                        <option :selected="item.payment_status == 'failed'" value="failed">Failed
                                        </option>
                                    </select>
                                    <div v-if="errors.payStatusValue" class="text-red-600">{{ errors.payStatusValue }}
                                    </div>
                                </td>
                                <td> {{ item.payment_method }}</td>
                                <td>$ {{ item.shipping_cost }}</td>
                                <td>$ {{ item.tax }}</td>
                                <td>% {{ item.discount }}</td>
                                <td>$ {{ item.total }}</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
                <Pagination :links="orders.links" />
            </v-col>
        </v-row>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

export default {
    components: { Link, useForm, AdminLayout, Pagination },
    data() {
        return {
            statusForm: useForm({
                statusValue: null,
            }),
            paymentStatusForm: useForm({
                payStatusValue: null,
            }),

            order_status: null,
            payment_status: null,
            filterForm: useForm({
                number: null,
                user: null,
                status: null,
                payment_status: null
            }),
        };
    },
    methods: {
        selectStatus(val, id) {
            let value = val.target.value;
            this.statusForm.statusValue = value;
            this.statusForm.post(route('admin.orders.status_update', id), {
                statusValue: value
            });
        },
        selectPayStatus(val, id) {
            let value = val.target.value;
            this.paymentStatusForm.payStatusValue = value;
            this.paymentStatusForm.post(route('admin.orders.payment_status_update', id), {
                payStatusValue: value
            })
        },
        filter() {
            this.filterForm.payment_status = this.payment_status;
            this.filterForm.status = this.order_status;

            this.filterForm.post(route('admin.orders.filter'));
        },
    },
    props: {
        errors: { type: Object },
        orders: { type: Object, Required: true }
    }
}

</script>
