<template>

    <AppLayout :user="user" title="Cart">
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
                                    quantity
                                    <v-number-input v-model="item.quantity" :reverse="false" controlVariant="default"
                                        name="quantity" :hideInput="false" inset min="5" max="1000">
                                    </v-number-input>

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
                                <option value="select">select</option>
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
                        <v-btn @click="getPage('checkout')" color="primary">checkout</v-btn>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import axios from 'axios';

export default {
    components: { AppLayout, VNumberInput },
    data() {
        return {
            selectedOptions: {},
            shippingEstimate: 0,
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
        async selectOption(event, cart_id, quantity) {
            if (event.target.value !== 'select') {
                const res = await axios.post('/cart/update', {
                    id: cart_id,
                    quantity: quantity,
                    option_val_id: event.target.value
                });
                window.location.reload();
            }
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
        goToLink(link) {
            window.open(link, '_blank');
        },
        async getPage(name) {
            switch (name) {
                case 'Profile':
                    window.location.href = '/profile';
                    break;
                case 'Cart':
                    window.location.href = '/cart';
                    break;
                case 'Log out':
                    axios.post('/logout');
                    window.location.reload();
                    break;
                case 'login':
                    window.location.href = '/login';
                    break;
                case 'checkout':
                    window.location.href = '/checkout';
            }
        },
        async deleteCartItem(cart) {
            try {
                const res = await axios.delete(`/cart/delete/${cart}`);
                console.log(res);
                if (res.status === 200) {
                    alert(res.data.message);
                }
                this.getPage('Cart');
            } catch (error) {
                alert(res.data.message);
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