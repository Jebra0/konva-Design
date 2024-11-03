<template>
    <AdminLayout title="Create Products" :user="user">
        <v-row>
            <v-col cols="12">
                <v-card title="Product Info" class="pa-4">
                    <v-container fluid>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Product Name" v-model="product_name" outlined dense></v-text-field>
                                <div v-if="errors.product_name" class="text-red-600">{{ errors.product_name }}</div>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="Product Price" v-model="product_price" outlined
                                    dense></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="Product Quantity" v-model="product_quantity" outlined
                                    dense></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card>
            </v-col>

            <v-col cols="12" v-for="(item, optionIndex) in options" :key="optionIndex">
                <v-card class="pa-4 mb-4 position-relative">
                    <v-btn icon color="red" class="remove-btn" @click="removeOption(optionIndex)">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>

                    <v-card-title>Option [{{ optionIndex + 1 }}]</v-card-title>
                    <v-container fluid>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Option Name" v-model="item.opt_name" outlined dense></v-text-field>
                            </v-col>

                            <v-col cols="8">
                                <div v-for="(val, valIndex) in item.opt_values" :key="valIndex">
                                    <v-row>
                                        <v-col cols="5">
                                            <v-text-field label="Option Value" v-model="item.opt_values[valIndex][0]"
                                                outlined dense></v-text-field>
                                        </v-col>
                                        <v-col cols="5">
                                            <v-text-field label="Value Price" v-model="item.opt_values[valIndex][1]"
                                                type="number" outlined dense></v-text-field>
                                        </v-col>
                                        <v-col cols="2">
                                            <v-btn color="red" @click="removeOptionValue(optionIndex, valIndex)">
                                                Remove
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </div>

                                <v-btn @click="addOptionValue(optionIndex)" class="mt-2">Add Option Value</v-btn>
                            </v-col>
                        </v-row>
                    </v-container>

                    <div class="text-center mt-4" v-if="optionIndex + 1 === options.length">
                        <v-btn color="primary" @click="addOption">Add Option</v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" align="center">
                <v-btn @click="addProduct" color="green">Create Product</v-btn>
            </v-col>
        </v-row>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";
import axios from 'axios';

export default {
    components: { AdminLayout, Link, useForm },
    data() {
        return {
            product_name: '',
            product_price: '',
            product_quantity: '',
            options: [
                {
                    opt_name: '',
                    opt_values: [['', '']]
                }
            ],
        };
    },
    computed: {
    },
    methods: {
        addProduct() {
            axios.post(route('admin.product.store'), {
                product_name: this.product_name,
                product_price: this.product_price,
                product_quantity: this.product_quantity,
                options: this.options,
            })
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.error(error);
                });
        },
        addOption() {
            this.options.push({
                opt_name: '',
                opt_values: [['', '']]
            });
        },
        addOptionValue(optionIndex) {
            this.options[optionIndex].opt_values.push(['', '']);
        },
        removeOption(optionIndex) {
            if (this.options.length > 1) {
                this.options.splice(optionIndex, 1);
            }
        },
        removeOptionValue(optionIndex, valIndex) {
            if (this.options[optionIndex].opt_values.length > 1) {
                this.options[optionIndex].opt_values.splice(valIndex, 1);
            }
        },
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        errors: {type: Object}, 
    },
};
</script>

<style scoped>
.textInput {
    padding: 10px;
    border: 1px solid #3333335c;
    display: flex;
    width: 90%;
    flex-direction: column;
}

.textInput:hover {
    box-shadow: 2px 2px 5px 0px rgba(0, 0, 0, 0.5);
}

.Vbtn {
    text-transform: none;
}

.remove-btn {
    position: absolute;
    top: 8px;
    right: 8px;
}
</style>
