<template>
    <v-layout class="rounded rounded-md">

        <v-navigation-drawer
            permanent
            width="200"
        >

            <v-list density="compact">
                <v-list-item @click="openTemp" prepend-icon="mdi-view-dashboard" title="Templates" value="Templates"></v-list-item>
                <v-card v-if="selectedOption.templates" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openText" prepend-icon="mdi-format-text" title="Text" value="Text"></v-list-item>
                <v-card v-if="selectedOption.text" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openPhotos" prepend-icon="mdi-image-outline" title="Photos" value="Photos"></v-list-item>
                <v-card v-if="selectedOption.photos" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openElement" prepend-icon="mdi-shape" title="Elements" value="Elements"></v-list-item>
                <div v-if="selectedOption.elements" class="d-flex mx-4 flex-wrap justify-between">
                    <v-icon icon="mdi-rectangle" color="rgb(179 177 177)" size="70" @click="addShape(rectConfig)" ></v-icon>
                    <v-icon icon="mdi-circle" color="rgb(179 177 177)" size="70" @click="addShape(circleConfig)"></v-icon>
                    <v-icon icon="mdi-triangle" color="rgb(179 177 177)" size="70" @click="addShape(triangleConfig)"></v-icon>
                    <v-icon icon="mdi-hexagon" color="rgb(179 177 177)" size="70" @click="addShape(hexagonConfig)"></v-icon>
                    <v-icon icon="mdi-octagon" color="rgb(179 177 177)" size="70" @click="addShape(octagonConfig)"></v-icon>
                </div>
                <v-list-item @click="openUp" prepend-icon="mdi-cloud-upload" title="Upload" value="Upload"></v-list-item>
                <v-card v-if="selectedOption.upload" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openBack" prepend-icon="mdi-view-grid-compact" title="Background" value="Background"></v-list-item>
                <v-card v-if="selectedOption.background" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openLayer" prepend-icon="mdi-layers-triple" title="Layers" value="Layers"></v-list-item>
                <v-card v-if="selectedOption.layers" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

            </v-list>
        </v-navigation-drawer>
        <v-app-bar :elevation="1">
            <template v-slot:prepend>
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
import { rectConfig, circleConfig, triangleConfig, hexagonConfig, octagonConfig } from '../Pages/shapesConfig.js';

export default {
    data () {
        return {
            selectedOption: {
                templates: false,
                text: false,
                photos: false,
                elements: false,
                upload: false,
                background: false,
                layers: false,
            },
            rectConfig,
            circleConfig,
            triangleConfig,
            hexagonConfig,
            octagonConfig,
        }
    },methods: {
        openTemp(){
            this.selectedOption.templates = !this.selectedOption.templates;
        },
        openText(){
            this.selectedOption.text = !this.selectedOption.text;
        },
        openPhotos(){
            this.selectedOption.photos = !this.selectedOption.photos;
        },
        openElement(){
            this.selectedOption.elements = !this.selectedOption.elements;
        },
        openUp(){
            this.selectedOption.upload = !this.selectedOption.upload;
        },
        openBack(){
            this.selectedOption.background = !this.selectedOption.background;
        },
        openLayer(){
            this.selectedOption.layers = !this.selectedOption.layers;
        },
    },
    props: {
        zoomIn: Function,
        zoomOut: Function,
        addShape: Function
    }
}
</script>
