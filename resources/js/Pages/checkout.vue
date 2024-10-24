<template>
    <AppLayout :user="user" title="Checkout">
        <v-row justify="center">
            <v-col cols="10" class="my-7">
                <!-- <v-stepper v-model="step" :items="['Step 1', 'Step 2', 'Step 3']">
                    <template v-slot:item.1>
                        <v-card title="Personal address" flat>
                            <v-form ref="form1" v-model="validStep1" lazy-validation>
                                <v-row>
                                    <v-col cols="6">
                                        <v-text-field label="First name" required :rules="[rules.required]"
                                            v-model="personalData.firstName" class="ml-2"></v-text-field>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-text-field label="Last name" required :rules="[rules.required]"
                                            v-model="personalData.lastName" class="mr-2"></v-text-field>
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
                                            v-model="personalData.postalCode" class="mr-2"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>

<template v-slot:item.2>
                        <v-card title="Shipping address" flat>
                            <v-form ref="form2" v-model="validStep2" lazy-validation>
                                <v-row>
                                    <v-col cols="6">
                                        <v-text-field label="First name" required :rules="[rules.required]"
                                            v-model="shippingData.firstName" class="ml-2"></v-text-field>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-text-field label="Last name" required :rules="[rules.required]"
                                            v-model="shippingData.lastName" class="mr-2"></v-text-field>
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
                                            v-model="shippingData.postalCode" class="mr-2"></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <v-checkbox v-model="samAddress"
                                            label="My delivery and mailing addresses are the same."></v-checkbox>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>

<template v-slot:item.3>
                        <v-card title="Payment Info" flat>
                            <v-form ref="form3" v-model="validStep3" lazy-validation>
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
                                    <v-col cols="9">
                                        <v-row>
                                            <v-col cols="3">Expiration</v-col>
                                            <v-col cols="6"></v-col>
                                            <v-col cols="3">CVC/CVV</v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="4">
                                                <v-text-field label="MM" required :rules="[rules.required]"
                                                    v-model="paymentData.expirationMM"></v-text-field>
                                            </v-col>
                                            <v-col cols="4">
                                                <v-text-field label="YYY" required :rules="[rules.required]"
                                                    v-model="paymentData.expirationyyy"></v-text-field>
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
                                        <v-btn color="green" width="100%" height="50px">Pay now</v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </template>
</v-stepper> -->

                <v-stepper v-model="curr" color="green">
                    <v-stepper-header class="overflow-x-auto">
                        <v-stepper-step v-for="(step, n) in steps" :key="n" :complete="stepComplete(n + 1)"
                            :step="n + 1" :rules="[value => !!step.valid]" :color="stepStatus(n + 1)">
                            {{ step.name }}
                        </v-stepper-step>
                    </v-stepper-header>
                    <v-stepper-content v-for="(step, n) in steps" :step="n + 1" :key="n">
                        {{ step.name }}
                        <v-card color="grey lighten-1" class="mb-12" height="200px">
                            <v-card-text>
                                <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                                    <v-text-field label="Enter something..." :rules="step.rules"></v-text-field>
                                </v-form>
                            </v-card-text>
                        </v-card>
                        <v-btn v-if="n + 1 < lastStep" color="primary" @click="validate(n)"
                            :disabled="!step.valid">Continue</v-btn>
                        <v-btn v-else color="success" @click="done()">Done</v-btn>
                        <v-btn text @click="curr = n">Back</v-btn>
                    </v-stepper-content>
                </v-stepper>
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

            step: 1,
            validStep1: false,
            validStep2: false,
            validStep3: false,
            personalData: {
                firstName: '',
                lastName: ''
            },
            shippingData: {
                address: '',
                city: ''
            },
            paymentData: {
                cardNumber: ''
            },
            rules: {
                required: value => !!value || 'This field is required'
            },

            //test
            curr: 1,
            lastStep: 4,
            steps: [
                { name: "Start", rules: [v => !!v || "Required."], valid: true },
                { name: "Step 2", rules: [v => !!v || "Required."], valid: true },
                { name: "Step 3", rules: [v => (v && v.length >= 4) || "Enter at least 4 characters."], valid: true },
                { name: "Complete" },
            ],
            valid: false,
            stepForm: []
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

        // nextStep() {
        //     if (this.step === 1 && this.$refs.form1.validate()) {
        //         this.step++
        //     } else if (this.step === 2 && this.$refs.form2.validate()) {
        //         this.step++
        //     } else if (this.step === 3 && this.$refs.form3.validate()) {
        //         // Final step logic (e.g., submitting the form)
        //     }
        // },
        // prevStep() {
        //     if (this.step > 1) this.step--
        // }

        stepComplete(step) {
            return this.curr > step
        },
        stepStatus(step) {
            return this.curr > step ? 'green' : 'blue'
        },
        validate(n) {
            this.steps[n].valid = false
            let v = this.$refs.stepForm[n].validate()
            if (v) {
                this.steps[n].valid = true
                // continue to next
                this.curr = n + 2
            }
        },
        done() {
            this.curr = 5
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