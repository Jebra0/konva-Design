<template>
    <AdminLayout title="Create Products" :user="user">
        <v-row>
            <v-col cols="12">
                <v-card title="Product Info" class="pa-4">
                    <v-container fluid>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Product Name" v-model="product.name" outlined dense></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="Product Price" v-model="product.price" outlined
                                    dense></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="Product Quantity" v-model="product.quantity" outlined
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

                                <!-- Button to add more option values -->
                                <v-btn @click="addOptionValue(optionIndex)" class="mt-2">Add Option Value</v-btn>
                            </v-col>
                        </v-row>
                    </v-container>

                    <!-- Centered Add Option button at the bottom of the card -->
                    <div class="text-center mt-4" v-if="optionIndex+1 === options.length">
                        <v-btn color="primary" @click="addOption">Add Option</v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";
import axios from 'axios';

export default {
    components: { AdminLayout, Link },
    data() {
        return {
            product: {
                name: '',
                price: '',
                quantity: ''
            },
            options: [
                {
                    opt_name: '',
                    opt_values: [['', '']]
                }
            ],
        };
    },
    computed: {
        formattedOptions() {
            return JSON.stringify(this.options, null, 2); // Pretty print the JSON
        },
    },
    methods: {
        addProduct() {
            axios.post(route('admin.product.store'), {
                options: this.options,
            })
                .then(response => {
                    console.log(response);
                    // Handle success, reset form, or redirect as needed
                })
                .catch(error => {
                    console.error(error);
                    // Handle error if needed
                });
        },
        addOption() {
            this.options.push({
                opt_name: '',
                opt_values: [['', '']]
            }); // Add a new empty option with a default opt_values
        },
        addOptionValue(optionIndex) {
            this.options[optionIndex].opt_values.push(['', '']); // Add a new empty value-price pair to the specified option
        },
        removeOption(optionIndex) {
            if (this.options.length > 1) {
                this.options.splice(optionIndex, 1); // Remove the specified option
            }
        },
        removeOptionValue(optionIndex, valIndex) {
            if (this.options[optionIndex].opt_values.length > 1) {
                this.options[optionIndex].opt_values.splice(valIndex, 1); // Remove the specified value from the option
            }
        },
    },
    props: {
        user: {
            type: Object,
            required: true
        },
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

/* Position the Remove Option button in the top right */
.remove-btn {
    position: absolute;
    top: 8px;
    right: 8px;
}
</style>
