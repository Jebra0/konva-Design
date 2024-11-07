<template>
    <v-dialog v-model="showAlert" max-width="500" class="d-flex">
        <v-alert
            v-if="flash.message || flash.error"
            :type="flash.error ? 'error' : 'success'"
            class=""
        >
            <v-row>
                <v-col cols="6">
                    {{ flash.message || flash.error }}
                </v-col>
                <v-col cols="6" class="d- flex justify-end">
                    <v-btn color="white" @click="closeAlert">Close</v-btn>
                </v-col>
            </v-row>
        </v-alert>
    </v-dialog>
</template>
<script>
export default {
    data(){
        return {
            //alert
            showAlert: false,
            alertMessage: '',
        }
    },
    //alert
    watch: {
        flash: {
            immediate: true,
            handler(newFlash) {
                if (newFlash.message || newFlash.error) {
                    this.alertTitle = newFlash.message ? 'Success' : 'Error';
                    this.alertMessage = newFlash.message || newFlash.error;
                    this.showAlert = true;
                }
            },
        },
    },
    methods: {
        //alert
        closeAlert() {
            this.showAlert = false;
        },
    },
    computed: {
        //test
        flash() {
            return this.$page.props.flash;
        },
    },
}
</script>
