<template>

    <Head>
        <title>{{ title }}</title>
    </Head>
    <v-app>
        <v-app-bar title="Konva Design">
            <template v-slot:prepend>
                <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            </template>

            <template v-slot:append>
                <v-menu open-on-hover>
                    <template v-slot:activator="{ props }">
                        <v-badge content="2" class="mx-2" color="red">
                            <v-icon v-bind="props">mdi-bell</v-icon>
                        </v-badge>
                        <!-- <v-icon class="mx-1" v-bind="props">mdi-bell</v-icon> -->
                    </template>
                    <v-list>
                        <v-list-item v-for="(item, index) in this.acountNavItems" :key="index" :value="index">
                            <v-list-item-title @click="this.getPage(item.title)">{{ item.title
                                }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>

                <v-btn-group @click="getPage('Design')" class="mx-2" color="blue-grey" density="comfortable"
                    rounded="pill" divided>
                    <v-btn>
                        Design
                    </v-btn>
                </v-btn-group>
                <v-switch @click="toggleTheme" label="toggle theme" class="mr-5 mt-5"></v-switch>
            </template>

        </v-app-bar>
        <v-navigation-drawer v-model="drawer">
            <div v-if="user">
                <v-menu open-on-hover>
                    <template v-slot:activator="{ props }">
                        <v-list-item class="my-3" v-bind="props"
                            prepend-avatar="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png"
                            nav>
                            <v-list-item-title style="font-size: 20px;">{{ user.name }}</v-list-item-title>

                            <template v-slot:append>
                                <v-icon>mdi-menu-down</v-icon>
                            </template>
                        </v-list-item>
                    </template>
                    <v-list>
                        <v-list-item v-for="(item, index) in this.acountNavItems" :key="index" :value="index">
                            <v-list-item-title @click="this.getPage(item.title)">{{ item.title
                                }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
            <v-divider></v-divider>

            <v-list density="compact" nav>
                <v-list-item v-for="item in navItems" :prepend-icon="item.icon" :title="item.title"
                    :value="item.title" :active="title === item.title" @click="getPage(item.title)"></v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-main :class="{ 'light-theme': isLightTheme }">
            <v-container fluid>
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import { Head } from '@inertiajs/vue3';

export default {
    components: { Head },
    props: {
        title: { type: String },
        user: {
            type: Object,
            required: true
        },
    },
    data: () => {
        return {
            drawer: true,
            acountNavItems: [
                { title: 'Profile' },
                { title: 'Dashboard' },
                { title: 'Log out' },
            ],
            navItems: [
                {
                    title: 'Dashboard',
                    icon: 'mdi-view-dashboard',
                },
                {
                    title: 'Products',
                    icon: 'mdi-storefront',
                },
                {
                    title: 'Orders',
                    icon: 'mdi-text-box-check',
                },
            ],
        }
    },
    methods: {
        async getPage(name) {
            switch (name) {
                case 'Profile':
                    window.location.href = '/profile';
                    break;
                case 'Log out':
                    axios.post('/logout');
                    window.location.reload();
                    break;
                case 'Design':
                    window.location.href = '/';
                    break;
                case 'Dashboard':
                    window.location.href = '/admin/dashboard';
                    break;
                case 'Products':
                    window.location.href = '/admin/dashboard/products';
                    break;
                case 'Orders':
                    window.location.href = '/admin/dashboard/orders';
                    break;

            }
        },
    }
}
</script>

<script setup>
import { ref } from 'vue'
import { useTheme } from 'vuetify'

const theme = useTheme()
const isLightTheme = ref(!theme.global.current.value.dark)

function toggleTheme() {
    isLightTheme.value = !isLightTheme.value
    theme.global.name.value = isLightTheme.value ? 'light' : 'dark';
}
</script>

<style>
.light-theme {
    background-color: #bebebe;
}
</style>