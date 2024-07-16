<template>
    <v-layout class="rounded rounded-md">
        <v-navigation-drawer permanent width="250">
            <v-list density="compact">
                <v-list-item @click="openTemp" prepend-icon="mdi-view-dashboard" title="Templates" value="Templates"></v-list-item>
                <v-card v-if="selectedOption.templates" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openText" prepend-icon="mdi-format-text" title="Text" value="Text"></v-list-item>
                <v-card class="scroll" v-if="selectedOption.text" elevation="1" outlined>
                    <v-btn elevation="0" width="100%" style=" text-transform: none; font-size: 25px;"><h1>Create header</h1></v-btn>
                    <v-btn elevation="0" width="100%" style="text-transform: none; font-size: 18px;"><h4>Create sub header</h4></v-btn>
                    <v-btn elevation="0" width="100%" style="text-transform: none; font-size: 14px;">Create Body Text</v-btn>
                    <div v-for="(imageObj, index) in texts" :key="index">
                        <img
                            :src="imageObj.src"
                            alt="Text Image"
                            @click="imageObj.method"
                            style="cursor: pointer"
                        >
                    </div>
                </v-card>

                <v-list-item @click="openPhotos" prepend-icon="mdi-image-outline" title="Photos" value="Photos"></v-list-item>
                <v-card v-if="selectedOption.photos" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openElement" prepend-icon="mdi-shape" title="Elements" value="Elements"></v-list-item>
                <div v-if="selectedOption.elements" class="scroll d-flex mx-4 flex-wrap justify-between">
                    <v-icon icon="mdi-minus" color="black" size="70" @click="actions.addShape(minusConfig)" ></v-icon>
                    <v-icon icon="mdi-arrow-right-thin" color="black" size="70" @click="actions.addShape(minusConfig)" ></v-icon>
                    <v-icon icon="mdi-rectangle" color="rgb(179 177 177)" size="70" @click="actions.addShape(rectConfig)" ></v-icon>
                    <v-icon icon="mdi-circle" color="rgb(179 177 177)" size="70" @click="actions.addShape(circleConfig)"></v-icon>
                    <v-icon icon="mdi-triangle" color="rgb(179 177 177)" size="70" @click="actions.addShape(triangleConfig)"></v-icon>
                    <v-icon icon="mdi-hexagon" color="rgb(179 177 177)" size="70" @click="actions.addShape(hexagonConfig)"></v-icon>
                    <v-icon icon="mdi-octagon" color="rgb(179 177 177)" size="70" @click="actions.addShape(octagonConfig)"></v-icon>
                    <v-icon icon="mdi-star" color="rgb(179 177 177)" size="70" @click="actions.addShape(starConfig)"></v-icon>
                    <v-icon icon="mdi-rhombus" color="rgb(179 177 177)" size="70" @click="actions.addShape(rhombusConfig)"></v-icon>
                    <v-icon icon="mdi-pentagon" color="rgb(179 177 177)" size="70" @click="actions.addShape(pentagonConfig)"></v-icon>
                    <v-icon icon="mdi-message" color="rgb(179 177 177)" size="70" @click="actions.addShape(messageConfig)"></v-icon>
                    <v-icon icon="mdi-plus" color="rgb(179 177 177)" size="70" @click="actions.addShape(plusConfig)"></v-icon>
                    <v-icon icon="mdi-arrow-down-bold" color="rgb(179 177 177)" size="70" @click="actions.addShape(arrowConfig)"></v-icon>
                    <v-icon icon="mdi-heart" color="rgb(179 177 177)" size="70" @click="actions.addShape(heartConfig)"></v-icon>

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
                <v-btn icon="mdi-undo" @click="actions.undoAction"></v-btn>
                <v-btn icon="mdi-redo" @click="actions.redoAction"></v-btn>
            </template>

            <span class="ml-20">
                <v-btn @click="actions.zoomIn">
                    <v-icon icon="mdi-magnify-plus-outline" size="25px"></v-icon>
                    <v-tooltip
                        activator="parent"
                        location="bottom"
                    >Zoom In</v-tooltip>
                </v-btn>
                <v-btn @click="actions.zoomOut">
                    <v-icon icon="mdi-magnify-minus-outline" size="25px"></v-icon>
                    <v-tooltip
                        activator="parent"
                        location="bottom"
                    >Zoom Out</v-tooltip>
                </v-btn>
            </span>

            <template v-slot:append>
                <div style="" >
                    <v-btn>
                        <v-icon icon="mdi-layers"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Position</v-tooltip>
                        <v-menu activator="parent">
                            <v-list style="width: 200px">
                                <v-list-item >
                                    <v-list-item-title>Position</v-list-item-title>
                                    <v-btn class="mr-9" @click="actions.AlignLeft">
                                        <v-icon icon="mdi-align-horizontal-left"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Left</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="actions.AlignRight">
                                        <v-icon  icon="mdi-align-horizontal-right"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Right</v-tooltip>
                                    </v-btn>
                                    <v-btn class="mr-9" @click="actions.AlignTop">
                                        <v-icon icon="mdi-align-vertical-top"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Top</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="actions.AlignBottom">
                                        <v-icon  icon="mdi-align-vertical-bottom"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Down</v-tooltip>
                                    </v-btn>
                                    <v-btn class="mr-9" @click="actions.AlignCenter">
                                        <v-icon icon="mdi-align-horizontal-center"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Center</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="actions.AlignMiddle">
                                        <v-icon  icon="mdi-align-vertical-center"></v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="bottom"
                                        >Align Middle</v-tooltip>
                                    </v-btn>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>
                    <v-btn>
                        <v-icon icon="mdi-opacity"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Opacity</v-tooltip>
                        <v-menu activator="parent">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Transparency</v-list-item-title>
                                    <v-slider
                                        @mouseout="actions.shapeOpacity(opacity)"
                                        v-model="opacity"
                                        :max="max"
                                        :min="min"
                                        style="width: 250px"
                                        class="align-center"
                                        hide-details
                                    >
                                    </v-slider>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>
                    <v-btn @click="actions.saveJson">
                        <v-icon icon="mdi-content-save"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Save As Json File</v-tooltip>
                    </v-btn>
                    <v-btn @click="actions.duplicateShape">
                        <v-icon icon="mdi-content-copy"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Duplicate</v-tooltip>
                    </v-btn>
                    <v-btn @click="actions.destroyShape">
                        <v-icon icon="mdi-delete"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Delete</v-tooltip>
                    </v-btn>
                    <v-btn  @click="actions.exportShape">
                        <v-icon icon="mdi-download"></v-icon>
                        <v-tooltip
                            activator="parent"
                            location="bottom"
                        >Download As Image</v-tooltip>
                    </v-btn>
                </div>
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
            ////opacity/////
            min: 0,
            max: 1,
            opacity: 1,
            ////////////////
            texts: [],
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
        loadImages() {
            for (let i = 1; i <= 34; i++) {
                this.texts.push({
                    src: `/images/text/p${i}.png`,
                    method: this.actions[`handleImageClick${i}`]
                });
            }
        },
    },
    mounted() {
        this.loadImages();
    },
    props: {
        actions: {
            type: Object,
            required: true,
        },
    }
}
</script>
<style scoped>
.scroll{
    max-height: 300px;
    overflow-y: scroll;
}
</style>
