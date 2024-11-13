<template>
    <appLayout title="Payment" :user="$page.props.auth.user" :items="$page.props.cart.itemsCount">
        <div>
            <stripe-checkout
                ref="checkoutRef"
                :pk="publishableKey"
                :sessionId="sessionId"
            />
            <button @click="submit">Pay now!</button>
        </div>
    </appLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { StripeCheckout } from '@vue-stripe/vue-stripe';

export default {
    components: {AppLayout, StripeCheckout},
    data(){
        return {
            publishableKey: this.$page.props.stripe.publishable_Key,
            sessionId: '',
        }
    },
    mounted() {
        this.getSession();
    },
    methods: {
        getSession(){
            axios.get(route('payment.getSession'))
                .then(res => {
                   console.log(res);
                   this.sessionId = res.data.id
                });
        },
        submit () {
            // You will be redirected to Stripe's secure checkout page
            this.$refs.checkoutRef.redirectToCheckout();
        },
    },
}
</script>
