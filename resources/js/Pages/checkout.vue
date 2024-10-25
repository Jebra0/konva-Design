<template>
    <AppLayout :user="user" title="Checkout">
        <v-row justify="center" class="my-5 mx-2">
            <v-col cols="10">
                <v-card class="py-3">
                    <v-card-title>Personal Info</v-card-title>
                    <v-form ref="form1" v-model="validStep1" lazy-validation>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="First name" required :rules="[rules.required]"
                                    v-model="personalData.first_name" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Last name" required :rules="[rules.required]"
                                    v-model="personalData.last_name" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="Email" required :rules="[rules.required]"
                                    v-model="personalData.email" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Phone" required :rules="[rules.required]"
                                    v-model="personalData.phone" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="City" required :rules="[rules.required]"
                                    v-model="personalData.city" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="State" required :rules="[rules.required]"
                                    v-model="personalData.state" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="Street" required :rules="[rules.required]"
                                    v-model="personalData.street" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Postal code" required :rules="[rules.required]"
                                    v-model="personalData.postal_code" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center" class="my-5 mx-2">
            <v-col cols="10">
                <v-card class="py-3">
                    <v-card-title>Shipping Info</v-card-title>
                    <v-form ref="form2" v-model="validStep2" lazy-validation>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="First name" required :rules="[rules.required]"
                                    v-model="shippingData.first_name" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Last name" required :rules="[rules.required]"
                                    v-model="shippingData.last_name" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="Email" required :rules="[rules.required]"
                                    v-model="shippingData.email" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Phone" required :rules="[rules.required]"
                                    v-model="shippingData.phone" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="City" required :rules="[rules.required]"
                                    v-model="shippingData.city" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="State" required :rules="[rules.required]"
                                    v-model="shippingData.state" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field label="Street" required :rules="[rules.required]"
                                    v-model="shippingData.street" class="ml-2"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field label="Postal code" required :rules="[rules.required]"
                                    v-model="shippingData.postal_code" class="mr-2"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-checkbox v-model="samAddress" @change="fillShippingData"
                                    label="My delivery and mailing addresses are the same."></v-checkbox>
                            </v-col>
                        </v-row>
                        <v-row>
                            <p class="mx-5 mb-5" style="font-size: 20px;">Choos shipping method</p>
                        </v-row>
                        <v-row justify="center">
                            <v-btn :disabled="!validStep1 || !validStep2" @click="makeOrder()"
                                color="blue-grey" class="mb-5">save & Continue</v-btn>
                        </v-row>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center" class="mb-5">
            <v-col cols="10">
                <v-row>
                    <v-col cols="8">
                        <v-card class="py-5">
                            <v-card-title>Payment Info</v-card-title>
                            <v-form ref="form3" :disabled="allDataDone" v-model="validStep3" lazy-validation>
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
                            </v-form>
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
            samAddress: false,
            allDataDone: true, //must be true to make it disabled

            step: 1,
            validStep1: false,
            validStep2: false,
            validStep3: false,
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
    mounted() {

    },
    computed: {
    },
    methods: {
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
        async makeOrder(){
            this.allDataDone = !this.allDataDone
            const res = await axios.post('/checkout', {
                billing_address: this.personalData,
                shipping_address: this.shippingData,
            });
            console.log(res);
        }
    },
    props: {
        cart: {
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
<style scoped></style>