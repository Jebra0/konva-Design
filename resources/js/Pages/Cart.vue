<template>
    <AppLayout :user="user">
        <v-row justify="center">
            <v-col cols="12">
                <v-card class="mx-auto my-12" max-width="900" v-for="item in cart">
                    <v-btn icon class="ma-2" style="position: absolute; top: 0; right: 0;">
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
                                <v-col cols="6">
                                    quantity
                                    <v-number-input :value="item.quantity" :reverse="false" controlVariant="default"
                                        name="quantity" :hideInput="false" inset></v-number-input>
                                </v-col>
                                <v-col cols="2"></v-col>
                                <v-col cols="4" class="my-4">
                                    <span class="mb-2">
                                        Sub Total = $ 50
                                    </span>
                                </v-col>
                            </v-row>
                        </v-col>
                        <div v-for="item in all_options" :key="item.name" class="mx-3" style="width: 250px;">
                            <label>{{ item.name }}</label>
                            <v-select :items="item.values.map(val => val.value)" label="Select an option"
                                v-model="selectedOptions[item.name]" outlined></v-select>
                        </div>
                    </v-row>
                </v-card>
                <v-card class="mx-auto my-12" max-width="500">
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Subtotal</span>
                        <span>$ {{ this.total }}</span>
                    </div>
                    <hr>
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Shipping estimate</span>
                        <span>$ {{ 0 }}</span>
                    </div>
                    <hr>
                    <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                        <span>Total</span>
                        <span>$ {{ this.total }}</span>
                    </div>
                    <v-row justify="center" class="my-5">
                        <v-btn color="primary">checkout</v-btn>
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
            acountNavItems: [
                { title: 'Profile' },
                { title: 'Cart' },
                { title: 'Log out' },
            ],
            icons: [
                {
                    icon: 'mdi-facebook',
                    link: 'https://www.facebook.com/profile.php?id=100009705940563&mibextid=ZbWKwL'
                },
                {
                    icon: 'mdi-linkedin',
                    link: 'https://www.linkedin.com/in/jebra0/'
                },
                {
                    icon: 'mdi-instagram',
                    link: ''
                },
            ],
        }
    },
    mounted() {
        console.log(this.cart)
        console.log(this.all_options)
    },
    methods: {
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