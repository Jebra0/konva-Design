<template>

    <Head>
        <title>{{ title }}</title>
    </Head>
    <v-app>
        <v-app-bar>
            <template v-slot:prepend>
                <!-- login button -->
                <v-btn-group @click="getPage('login')" class="mx-2" v-if="this.user == null" color="green"
                    density="comfortable" rounded="pill" divided>
                    <v-btn>
                        Login
                    </v-btn>
                </v-btn-group>

                <div class="text-center" v-if="this.user">
                    <v-menu open-on-hover>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                <v-icon color="blue-grey" class="mx-1">
                                    mdi-account
                                </v-icon>
                                {{ this.user?.name }}
                                <v-icon>mdi-menu-down</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item v-for="(item, index) in this.acountNavItems" :key="index" :value="index">
                                <v-list-item-title @click="this.getPage(item.title)">{{ item.title
                                    }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </template>

            <v-app-bar-title>Konva Design</v-app-bar-title>

            <p class="" style="font-size: 20px; font-weight: bold;">{{ title }}</p>

            <v-spacer></v-spacer>


            <template v-slot:append>
                <v-btn-group @click="getPage('Design')" class="mx-2" color="blue-grey" density="comfortable"
                    rounded="pill" divided>
                    <v-btn>
                        Back To Design
                    </v-btn>
                </v-btn-group>
                <v-badge :content="items" color="red" class="mr-5" v-if="items > 0">
                    <v-icon  @click="getPage('Cart')" color="blue-grey" style="cursor: pointer;">mdi-cart</v-icon>
                </v-badge>
                <v-icon v-else @click="getPage('Cart')" color="blue-grey" style="cursor: pointer;">mdi-cart</v-icon>
            </template>
        </v-app-bar>

        <v-main>
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
import { Head } from '@inertiajs/vue3';

export default {
    components: { Head },
    data() {
        return {
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
        }
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
                case 'Design':
                    window.location.href = '/';
                    break;
                case 'Dashboard':
                    window.location.href = '/admin/dashboard';
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
        title: {
            type: String
        },
        items: {
            type: Number
        }
    },
}
</script>
<style>
main {
    background-color: #ebebeb;
}
</style>