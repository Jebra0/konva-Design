<template>

    <Head>
        <title>{{ title }}</title>
    </Head>
    <v-app>
        <v-app-bar>
            <template v-slot:prepend>
                <!-- login button -->
                <Link href="/login" class="mx-2" v-if="user == null">
                    <v-btn-group class="mx-2" v-if="user == null" color="green" density="comfortable" rounded="pill" divided>
                        <v-btn>
                            Login
                        </v-btn>
                    </v-btn-group>
                </Link>

                <div class="text-center" v-if="this.user">
                    <v-menu open-on-hover>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                <v-icon color="blue-grey" class="mx-1">
                                    mdi-account
                                </v-icon>
                                {{ this.user?.name?.split(' ')[0] }}
                                <v-icon> mdi-menu-down</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <Link class="nave_list" v-for="(item, index) in acountNavItems" :key="index"
                                  :href="route(item.link)"
                                  v-show="item.for === 'all' || (item.for === 'admin' && isAdmin) || (item.for === 'user' && !isAdmin)">
                                <v-list-item>{{ item.title }}</v-list-item>
                            </Link>
                            <form @submit.prevent="logoutForm.post(route('logout'))">
                                <v-list-item>
                                    <button type="submit">Log out</button>
                                </v-list-item>
                            </form>
                        </v-list>
                    </v-menu>
                </div>
            </template>

            <v-app-bar-title>Konva Design</v-app-bar-title>

            <p class="" style="font-size: 20px; font-weight: bold;">{{ title }}</p>

            <v-spacer></v-spacer>

            <template v-slot:append>
                <Link href="/" class="design_btn mx-6" >Design</Link>

                <v-badge :content="items" color="red" class="mr-5" v-if="items > 0">
                    <Link href="/cart" >
                      <v-icon color="blue-grey" style="cursor: pointer;">mdi-cart</v-icon>
                    </Link>
                </v-badge>
            </template>
        </v-app-bar>
        <v-main :class="{ 'light-theme': true }">
            <Alert />
            <slot />
        </v-main>
        <v-footer class="text-center d-flex flex-column">
            <div>
                <v-btn v-for="icon in icons" :key="icon" :icon="icon.icon" class="mx-4" variant="text"
                    @click="goToLink(icon.link)"></v-btn>
            </div>

            <div class="pt-0">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat dolore quas nesciunt, aspernatur
                voluptate
                culpa placeat, id velit praesentium asperiores illo. Cum, earum vero. Eaque, nobis unde. Odio, sit
                cupiditate?
            </div>

            <v-divider></v-divider>

            <div>
                {{ new Date().getFullYear() }} — <strong>Jebra</strong>
            </div>
        </v-footer>
    </v-app>
</template>
<script>
import {Head, Link, useForm} from '@inertiajs/vue3';
import Alert from "@/Components/Alert.vue";
export default {
    components: { Head, Link, Alert },
    data() {
        return {
            isAdmin: this.user?.is_admin,
            logoutForm: useForm({}),
            selectedOptions: {},
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
            acountNavItems: [
                { title: 'Profile', for: 'all', link: 'profile.edit' },
                { title: 'Dashboard', for: 'admin', link: 'admin.index' },
            ],

        }
    },
    methods: {
        goToLink(link) {
            window.open(link, '_blank');
        },
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        title: {
            type: String
        },
        items: {
            type: Number
        }
    },
}
</script>
<style scoped>
.light-theme {
    background-color: #bebebe;
}
.design_btn {
    margin-left: 10px;
    margin-right: 10px;
    padding: 10px;
    width: 100px;
    color: white;
    background-color: #607D8B;
    border-radius: 20px;
    text-align: center;
}
</style>
