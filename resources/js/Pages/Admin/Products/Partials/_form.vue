<template>
    <v-row>
        <v-col cols="12">
            <v-card title="Product Info" class="pa-4">
                <v-container fluid>
                    <v-row>
                        <v-col cols="4">
                            <v-text-field label="Product Name" v-model="form.product_name" outlined dense color="black"
                                class="custom-input"></v-text-field>
                            <div v-if="errors.product_name" class="text-red-600">{{ errors.product_name }}</div>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Product Price" v-model="form.product_price" outlined
                                dense></v-text-field>
                            <div v-if="errors.product_price" class="text-red-600">{{ errors.product_price }}
                            </div>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Product Quantity" v-model="form.product_quantity" outlined
                                dense></v-text-field>
                            <div v-if="errors.product_quantity" class="text-red-600">{{ errors.product_quantity
                                }}
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card>
        </v-col>

        <v-col cols="12" v-for="(item, optionIndex) in form.options" :key="optionIndex">
            <v-card class="pa-4 mb-4 position-relative">
                <v-btn v-if="optionIndex + 1 === form.options.length" icon color="red" class="remove-btn"
                    @click="removeOption(optionIndex)">
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
                                        <v-text-field label="Option Value" v-model="item.opt_values[valIndex]['value']"
                                            outlined dense></v-text-field>
                                    </v-col>
                                    <v-col cols="5">
                                        <v-text-field label="Value Price" v-model="item.opt_values[valIndex]['price']"
                                            type="number" outlined dense></v-text-field>
                                    </v-col>
                                    <v-col cols="2">
                                        <v-btn class="Vbtn" v-if="item.opt_values.length !== 1" color="red"
                                            @click="removeOptionValue(optionIndex, valIndex)">
                                            Remove
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </div>

                            <v-btn @click="addOptionValue(optionIndex)" class="Vbtn mt-2">Add Option
                                Value</v-btn>
                        </v-col>
                    </v-row>
                </v-container>

                <div class="text-center mt-4" v-if="optionIndex + 1 === form.options.length">
                    <v-btn class="Vbtn" color="primary" @click="addOption">Add Option</v-btn>
                </div>
            </v-card>
            <div v-if="errors.options" class="text-red-600">{{ errors.options }}</div>
        </v-col>
    </v-row>
    <v-row>
        <v-col cols="12" align="center">
            <v-btn :disabled="form.processing" type="submit" color="green">Create Product</v-btn>
        </v-col>
    </v-row>
</template>
<script>
import { useForm } from '@inertiajs/vue3';


export default {
    components: { useForm },
    props: {
        form: {
            type: Object,
            required: true
        },
        errors: { type: Object },
    },
    methods: {
        addOption() {
            this.form.options.push({
                opt_name: '',
                opt_values: [{ value: '', price: 0 }]
            });
        },
        addOptionValue(optionIndex) {
            this.form.options[optionIndex].opt_values.push({ value: '', price: 0 });
        },
        removeOption(optionIndex) {
            if (this.form.options.length > 1 && confirm('You going to delete an option, are you sure?')) {
                this.form.options.splice(optionIndex, 1);
            }
        },
        removeOptionValue(optionIndex, valIndex) {
            if (this.form.options[optionIndex].opt_values.length > 1) {
                this.form.options[optionIndex].opt_values.splice(valIndex, 1);
                console.log(this.form.options[optionIndex].opt_values);
            }
        },
    },
}
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
