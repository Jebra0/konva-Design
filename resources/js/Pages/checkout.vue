<template>
    <AppLayout :user="user" title="Checkout" :items="cart.length">
        <v-form v-model="validation" lazy-validation>
            <v-row justify="center" class="my-5 mx-2">
                <v-col cols="10">
                    <v-card class="py-3">
                        <v-card-title>Personal Info</v-card-title>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="First name" required :rules="[rules.required]"
                                                  v-model="personalData.first_name" class="ml-2"></v-text-field>
                                    <div v-if="errors.billing_address?.first_name" class="text-red-600">{{ errors.billing_address?.first_name }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Last name" required :rules="[rules.required]"
                                                  v-model="personalData.last_name" class="mr-2"></v-text-field>
                                    <div v-if="errors.billing_address?.last_name" class="text-red-600">{{ errors.billing_address?.last_name }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="Email" required :rules="[rules.required]"
                                                  v-model="personalData.email" class="ml-2"></v-text-field>
                                    <div v-if="errors.billing_address?.email" class="text-red-600">{{ errors.billing_address?.email }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Phone" required :rules="[rules.required]"
                                                  v-model="personalData.phone" class="mr-2"></v-text-field>
                                    <div v-if="errors.billing_address?.phone" class="text-red-600">{{ errors.billing_address?.phone }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="City" required :rules="[rules.required]"
                                                  v-model="personalData.city" class="ml-2"></v-text-field>
                                    <div v-if="errors.billing_address?.city" class="text-red-600">{{ errors.billing_address?.city }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="State" required :rules="[rules.required]"
                                                  v-model="personalData.state" class="mr-2"></v-text-field>
                                    <div v-if="errors.billing_address?.state" class="text-red-600">{{ errors.billing_address?.state }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="Street" required :rules="[rules.required]"
                                                  v-model="personalData.street" class="ml-2"></v-text-field>
                                    <div v-if="errors.billing_address?.street" class="text-red-600">{{ errors.billing_address?.street }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Postal code" required :rules="[rules.required]"
                                                  v-model="personalData.postal_code" class="mr-2"></v-text-field>
                                    <div v-if="errors.billing_address?.postal_code" class="text-red-600">{{ errors.billing_address?.postal_code }}</div>
                                </v-col>
                            </v-row>
                    </v-card>
                </v-col>
            </v-row>
            <v-row justify="center" class="my-5 mx-2">
                <v-col cols="10">
                    <v-card class="py-3">
                        <v-card-title>Shipping Info</v-card-title>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="First name" required :rules="[rules.required]"
                                                  v-model="shippingData.first_name" class="ml-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.first_name" class="text-red-600">{{ errors.shipping_address?.first_name }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Last name" required :rules="[rules.required]"
                                                  v-model="shippingData.last_name" class="mr-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.last_name" class="text-red-600">{{ errors.shipping_address?.last_name }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="Email" required :rules="[rules.required]"
                                                  v-model="shippingData.email" class="ml-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.email" class="text-red-600">{{ errors.shipping_address?.email }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Phone" required :rules="[rules.required]"
                                                  v-model="shippingData.phone" class="mr-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.phone" class="text-red-600">{{ errors.shipping_address?.phone }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="City" required :rules="[rules.required]"
                                                  v-model="shippingData.city" class="ml-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.city" class="text-red-600">{{ errors.shipping_address?.city }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="State" required :rules="[rules.required]"
                                                  v-model="shippingData.state" class="mr-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.state" class="text-red-600">{{ errors.shipping_address?.state }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field label="Street" required :rules="[rules.required]"
                                                  v-model="shippingData.street" class="ml-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.street" class="text-red-600">{{ errors.shipping_address?.street }}</div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Postal code" required :rules="[rules.required]"
                                                  v-model="shippingData.postal_code" class="mr-2"></v-text-field>
                                    <div v-if="errors.shipping_address?.postal_code" class="text-red-600">{{ errors.shipping_address?.postal_code }}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <v-checkbox v-model="samAddress" @change="fillShippingData"
                                                label="My delivery and mailing addresses are the same."></v-checkbox>
                                </v-col>
                            </v-row>

                            <v-row justify="center">
                                <v-btn :disabled="!validation" @click="makeOrder"
                                       color="blue-grey" class="mb-5">save & Continue</v-btn>
                            </v-row>
                    </v-card>
                </v-col>
            </v-row>
            <v-row justify="center" class="mb-5">
                <v-col cols="10">
                    <v-row>
                        <v-col cols="8">
                            <v-card class="py-5">
                                <v-card-title>Payment Info</v-card-title>
                                    <v-row justify="center">
                                        <v-col cols="9">
                                            <v-text-field label="Cardholder Name" required :rules="[rules.required]"
                                                          v-model="paymentData.cardHolderName"></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row justify="center">
                                        <v-col cols="9">
                                            <v-text-field label=" Card Number " required :rules="[rules.required]"
                                                          v-model="paymentData.cardNumber"></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row justify="center">
                                        <v-col cols="9" class="my-4">
                                            <v-row>
                                                <v-col cols="3">Expiration</v-col>
                                                <v-col cols="6"></v-col>
                                                <v-col cols="3">CVC/CVV</v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="4">
                                                    <v-text-field label="MM" required :rules="[rules.required]"
                                                                  v-model="paymentData.expirationMm"></v-text-field>
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field label="YYY" required :rules="[rules.required]"
                                                                  v-model="paymentData.expirationYyy"></v-text-field>
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field autocomplete="off" name="cvv" type="password" label="***"
                                                                  required :rules="[rules.required]"
                                                                  v-model="paymentData.cvv"></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>
                                    <v-row justify="center" class="mb-3">
                                        <v-col cols="5">
                                            <v-btn @click="" :disabled="allDataDone" color="green" width="100%" height="50px">Pay
                                                now</v-btn>
                                        </v-col>
                                    </v-row>
                            </v-card>
                        </v-col>
                        <v-col cols="4">
                            <v-card class="mx-auto">
                                <v-card-title>Order Price</v-card-title>
                                <hr>
                                <div v-for="(item, index) in cart" style="font-size: 20px;"
                                     class="d-flex justify-space-between my-4 mx-5">
                                    <span>{{ item.category.name }}</span>
                                    <span>$ {{ calcSupTotal(item) }}</span>
                                </div>
                                <hr />
                                <div style="font-size: 20px;" class="d-flex justify-space-between my-4 mx-5">
                                    <span>Total</span>
                                    <span>$ {{ total }}</span>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>{{orderForm}}
        </v-form>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import axios from 'axios';
import {useForm} from "@inertiajs/vue3";

export default {
    components: { AppLayout, VNumberInput },
    data() {
        return {
            samAddress: false,
            allDataDone: true, //must be true to make it disabled

            validation: false,
            orderForm: useForm({
                billing_address: null,
                shipping_address: null,
            }),
            personalData: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                city: '',
                state: '',
                street: '',
                postal_code: '',
            },
            shippingData: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                city: '',
                state: '',
                street: '',
                postal_code: '',
            },
            paymentData: {
                cardHolderName: '',
                expirationMm: '',
                expirationYyy: '',
                cvv: '',
            },
            rules: {
                required: value => !!value || 'This field is required'
            },
        }
    },
    methods: {
        fillShippingData() {
            if (this.samAddress && this.personalData) {
                this.shippingData = this.personalData;
            }
        },
        calcSupTotal(item) {
            if (!item || !item.category) return 0;
            let p = item.category.price;
            let sumOptions = item.options.reduce((acc, i) => acc + i.price, 0);

            return item.quantity * (p + sumOptions);
        },

        makeOrder(){
            this.orderForm.billing_address = this.personalData;
            this.orderForm.shipping_address = this.shippingData;

            this.orderForm.post(route('checkout.store'));
        }
    },
    props: {
        cart: {
            type: Object,
            required: true,
        },
        total: {
            type: Number,
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
<style scoped></style>
