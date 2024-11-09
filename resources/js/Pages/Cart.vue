<template>
    <AppLayout :user="user" title="Cart" :items="cart.length">
        <v-row justify="center">
            <v-col cols="12">
                <v-card class="mx-auto my-12" max-width="900" v-for="(item, id) in cart" :key="id">
                    <v-btn @click="deleteCartItem(item.id)" icon class="ma-2"
                        style="position: absolute; top: 0; right: 0;">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                    <v-row no-gutters class="mx-8 my-3">
                        <v-col cols="4">
                            <v-img :src="item.image" height="200px" contain></v-img>
                        </v-col>

                        <v-col cols="8">
                            <v-card-item>
                                <v-card-title>{{ item.category.name }}</v-card-title>
                                <v-card-subtitle v-if="item.category.quantity == 0">
                                    <span class="me-1">
                                        not in stock
                                    </span>
                                </v-card-subtitle>
                            </v-card-item>

                            <v-row>
                                <v-col cols="6" class="ml-5">
                                    <div class="d-flex">
                                        <label class="mt-2 mr-3">Quantity</label>
                                        <select v-model="item.quantity" @change="updateQuantity(item.id, $event)">
                                            <option disabled selected>Select</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.quantity" class="text-red-600">{{ errors.quantity }}</div>
                                </v-col>
                                <v-col cols="1"></v-col>
                                <v-col cols="4" class="my-4">
                                    <span class="mb-2">
                                        Sub Total = $ {{ calcSupTotal(item) }}
                                    </span>
                                </v-col>
                            </v-row>
                        </v-col>
                        <div class="mx-3" v-for="(i, index) in item.category.options" :key="index"
                            style="width: 250px;">

                            <label>{{ i.name }}</label><br>
                            <select class="select_options" @change="selectOption($event, item.id, item.quantity)">
                                <option value="" disabled selected>select</option>
                                <option v-for="(v, idx) in i.values" :value="v.id" :key="idx"
                                    :selected="isSelectedOption(v.id, item.id, i.id)">{{ v.value }}</option>
                            </select>
                        </div>
                    </v-row>
                </v-card>
                <v-card class="mx-auto my-12" max-width="500">
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Subtotal</span>
                        <span>$ {{ this.cartTotal() }}</span>
                    </div>
                    <hr>
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Shipping estimate</span>
                        <span>$ {{ this.shippingEstimate }}</span>
                    </div>
                    <hr>
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Total</span>
                        <span>$ {{ this.cartTotal() + this.shippingEstimate }}</span>
                    </div>
                    <v-row justify="center" class="my-5">
                        <Link href="/checkout">
                            <v-btn color="primary">checkout</v-btn>
                        </Link>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {VNumberInput} from 'vuetify/labs/VNumberInput'
import {useForm, Link} from "@inertiajs/vue3";

export default {
    components: { AppLayout, VNumberInput, Link },
    data() {
        return {
            selectedOptions: {},
            shippingEstimate: 0,

            deleteForm: useForm({}),
            updateForm: useForm({
                quantity: null,
                option_val_id: null
            }),
        }
    },
    mounted() {
        console.log('cart\'s options//', this.cart);
        this.setSelectedOptions();
    },
    computed: {
    },
    methods: {
        setSelectedOptions() {
            this.cart.forEach((item) => {
                if (!this.selectedOptions[item.id]) {
                    this.selectedOptions[item.id] = {};
                }
                item.options.forEach((option) => {
                    this.selectedOptions[item.id][option.option_id] = option.id;
                });
            });
        },
        isSelectedOption(optionId, itemId, optionGroupId) {
            return this.selectedOptions[itemId] && this.selectedOptions[itemId][optionGroupId] === optionId;
        },
        updateQuantity(cart_id, quantityEvent){
            this.updateForm.quantity = quantityEvent.target.value;
            this.updateForm.post(route('cart.update', cart_id));
        },
        selectOption(event, cart_id, quantity) {
            this.updateForm.quantity = quantity;

            if(event && event.target ){
                this.updateForm.option_val_id = event.target.value;
            }
            this.updateForm.post(route('cart.update', cart_id));
        },
        calcSupTotal(item) {
            if (!item || !item.category) return 0;
            let p = item.category.price;
            let sumOptions = item.options.reduce((acc, i) => acc + i.price, 0);

            return item.quantity * (p + sumOptions);
        },
        cartTotal() {
            return this.cart.reduce((acc, item) => acc + this.calcSupTotal(item), 0);
        },
        async deleteCartItem(cart) {
            try {
                 this.deleteForm.delete(route('cart.delete', cart));
            } catch (error) {
                alert('error happened try again');
            }
        },
    },
    props: {
        cart: {
            type: Object,
            required: true,
        },
        all_options: {
            type: Object,
            required: true,
        },
        total: {
            type: parseFloat,
            required: true,
        },
        user: {
            type: Object,
            required: true
        },
        errors: {type: Object}
    },
}
</script>
<style scoped>
.select_options {
    margin-left: 0;
    border: 1px solid #c7c7c7;
    width: 100%;
}
</style>
