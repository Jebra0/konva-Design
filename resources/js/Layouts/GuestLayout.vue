<template>
    <v-layout class="rounded rounded-md">
        <v-navigation-drawer
            v-model="drawer"
            :rail="rail"
            permanent
            @click="rail = false"
            >

            <v-list density="compact" nav>
                <v-list-item prepend-icon="mdi-view-dashboard" title="Templates" value="Templates">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-format-text" title="Text" value="Text">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-image-outline" title="Photos" value="Photos">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-shape" title="Elements" value="Elements">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-cloud-upload" title="Upload" value="Upload">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-view-grid-compact" title="Background" value="Background">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="mdi-layers-triple" title="Layers" value="Layers">
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-chevron-down"
                            variant="text"
                        ></v-btn>
                    </template>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar :elevation="1">
            <template v-slot:prepend>
                <v-app-bar-nav-icon variant="text" @click.stop="rail = !rail"></v-app-bar-nav-icon>
                <v-btn icon="mdi-undo"></v-btn>
                <v-btn icon="mdi-redo"></v-btn>
            </template>

            <span class="ml-12">
                <v-icon icon="mdi-plus-circle" @click="zoomIn"></v-icon>
                <v-icon icon="mdi-minus-circle" @click="zoomOut"></v-icon>
            </span>

            <template v-slot:append>
                <v-btn>
                    <v-icon icon="mdi-layers"></v-icon>
                    position
                </v-btn>
                <v-btn>
                    <v-icon icon="mdi-download"></v-icon>
                    download
                </v-btn>
            </template>
        </v-app-bar>

        <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color: #ebebeb;">
            <slot />
        </v-main>
    </v-layout>
</template>
<script>
export default {
    data () {
        return {
            drawer: true,
            rail: true,
        }
    },
    props: {
        zoomIn: Function,
        zoomOut: Function
    }
}
</script>
