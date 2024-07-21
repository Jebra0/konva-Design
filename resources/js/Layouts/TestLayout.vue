<template>
    <v-layout class="rounded rounded-md">
        <v-navigation-drawer permanent width="300">
            <v-list density="compact">
                <v-list-item @click="openTemp" prepend-icon="mdi-view-dashboard" title="Templates"
                    value="Templates"></v-list-item>
                <v-card v-if="selectedOption.templates" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openText" prepend-icon="mdi-format-text" title="Text" value="Text"></v-list-item>
                <v-card class="scroll" v-if="selectedOption.text" elevation="1" outlined>
                    <v-btn elevation="0" width="100%" style=" text-transform: none; font-size: 25px;">
                        <h1>Create header</h1>
                    </v-btn>
                    <v-btn elevation="0" width="100%" style="text-transform: none; font-size: 18px;">
                        <h4>Create sub header</h4>
                    </v-btn>
                    <v-btn elevation="0" width="100%" style="text-transform: none; font-size: 14px;">Create Body
                        Text</v-btn>
                    <div v-for="(imageObj, index) in texts" :key="index">
                        <img :src="imageObj.src" alt="Text Image" @click="actions.addHeader" style="cursor: pointer">
                    </div>
                </v-card>

                <v-list-item @click="openPhotos" prepend-icon="mdi-image-outline" title="Photos"
                    value="Photos"></v-list-item>
                <v-card v-if="selectedOption.photos" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>

                <v-list-item @click="openElement" prepend-icon="mdi-shape" title="Elements"
                    value="Elements"></v-list-item>
                <div v-if="selectedOption.elements" class="scroll d-flex mx-4 flex-wrap justify-between">
                    <v-icon icon="mdi-minus" color="black" size="70"></v-icon>
                    <v-icon icon="mdi-arrow-right-thin" color="black" size="70"></v-icon>
                    <v-icon icon="mdi-rectangle" color="rgb(179 177 177)" size="70"
                        @click="actions.addShape(rectConfig)"></v-icon>
                    <v-icon icon="mdi-circle" color="rgb(179 177 177)" size="70"
                        @click="actions.addShape(circleConfig)"></v-icon>
                    <v-icon icon="mdi-triangle" color="rgb(179 177 177)" size="70"
                        @click="actions.addShape(triangleConfig)"></v-icon>
                    <v-icon icon="mdi-hexagon" color="rgb(179 177 177)" size="70"
                        @click="actions.addShape(hexagonConfig)"></v-icon>
                    <v-icon icon="mdi-octagon" color="rgb(179 177 177)" size="70"
                        @click="actions.addShape(octagonConfig)"></v-icon>
                    <v-icon icon="mdi-star" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-rhombus" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-pentagon" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-message" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-plus" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-arrow-down-bold" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-heart" color="rgb(179 177 177)" size="70"></v-icon>
                </div>

                <v-list-item @click="openUp" prepend-icon="mdi-cloud-upload" title="Upload"
                    value="Upload"></v-list-item>
                <v-card v-if="selectedOption.upload" elevation="1" outlined>
                    <v-card-title>TEST</v-card-title>
                    <v-card-text> test test test test etst etst etst ethsj </v-card-text>
                </v-card>
                <!--
                <v-list-item @click="openBack" prepend-icon="mdi-view-grid-compact" title="Background" value="Background"></v-list-item>
                <v-card v-if="selectedOption.background" elevation="1" outlined class="scroll ml-5">
                    <div v-for="(image, index) in images" :key="index">
                        <img
                            :src="image.src"
                            alt="Background image"
                            @click="actions.setImageBackground(image.src)"
                            style="cursor: pointer; width: 150px; margin-left: 20px; margin-bottom: 15px"
                        >
                    </div>
                </v-card>
-->
                <v-list-item @click="openLayer" prepend-icon="mdi-layers-triple" title="Layers"
                    value="Layers"></v-list-item>
                <v-card v-if="selectedOption.layers" elevation="1" class="scroll">
                    <div class="m-2 p-2 layer d-flex" v-for="(layer, i) in reversedLayers" :key="i" :value="layer">
                        <span v-if="layer.name !== 'Defualt Layer'">
                            <button @click="actions.hideLayer(!layer.visible, layer.id)" class="mr-2">
                                <v-icon :icon="layer.visible ? 'mdi-eye' : 'mdi-eye-closed'"></v-icon>
                            </button>
                            <button @click="actions.deleteLayer(layer.id)"><v-icon icon="mdi-delete"></v-icon></button>
                        </span>
                        <span v-text="layer.name" class="ml-1" style="width: 120px;"></span>

                        <span class="ml-5" v-if="layer.name !== 'Defualt Layer'">
                            <button v-if="(!layer.lastOne)" @click="actions.moveLayer('up', layer.id)"><v-icon
                                    icon="mdi-arrow-up"></v-icon></button>
                            <button v-if="(!layer.firstOne)" @click="actions.moveLayer('down', layer.id)"><v-icon
                                    icon="mdi-arrow-down"></v-icon></button>
                        </span>
                    </div>
                </v-card>
            </v-list>
        </v-navigation-drawer>
        <!-- defualt buttons -->
        <v-app-bar :elevation="1">
            <template v-slot:prepend>
                <v-btn color="red" icon="mdi-undo" @click="actions.unDo"></v-btn>
                <v-btn color="red" icon="mdi-redo" @click="actions.reDo"></v-btn>
            </template>

            <span class="ml-20">
                <v-btn @click="actions.zoomFunction('in')">
                    <v-icon icon="mdi-magnify-plus-outline" size="25px"></v-icon>
                    <v-tooltip activator="parent" location="bottom">Zoom In</v-tooltip>
                </v-btn>
                <v-btn @click="actions.zoomFunction('out')">
                    <v-icon icon="mdi-magnify-minus-outline" size="25px"></v-icon>
                    <v-tooltip activator="parent" location="bottom">Zoom Out</v-tooltip>
                </v-btn>
            </span>

            <template v-slot:append>
                <div style="">
                    <v-btn>
                        <v-icon color="red" icon="mdi-layers"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Position</v-tooltip>
                        <v-menu activator="parent">
                            <v-list style="width: 200px">
                                <v-list-item>
                                    <v-list-item-title>Position</v-list-item-title>
                                    <v-btn class="mr-9" @click="actions.alignLeft">
                                        <v-icon icon="mdi-align-horizontal-left"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Left</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="actions.alignRight">
                                        <v-icon icon="mdi-align-horizontal-right"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Right</v-tooltip>
                                    </v-btn>
                                    <v-btn class="mr-9" @click="actions.alignTop">
                                        <v-icon icon="mdi-align-vertical-top"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Top</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="actions.alignBottom">
                                        <v-icon icon="mdi-align-vertical-bottom"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Down</v-tooltip>
                                    </v-btn>
                                    <v-btn class="mr-9">
                                        <v-icon icon="mdi-align-horizontal-center"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Center</v-tooltip>
                                    </v-btn>
                                    <v-btn>
                                        <v-icon icon="mdi-align-vertical-center"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Middle</v-tooltip>
                                    </v-btn>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>
                    <v-btn>
                        <v-icon icon="mdi-opacity"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Opacity</v-tooltip>
                        <v-menu activator="parent">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Transparency</v-list-item-title>
                                    <v-slider @mouseout="actions.objectOpacity(opacity)" v-model="opacity" :max="max"
                                        :min="min" style="width: 250px" class="align-center" hide-details>
                                    </v-slider>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>
                    <v-btn @click="actions.saveAsJson">
                        <v-icon icon="mdi-content-save"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Save As Json File</v-tooltip>
                    </v-btn>
                    <v-btn @click="actions.duplicateObjects">
                        <v-icon icon="mdi-content-copy"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Duplicate</v-tooltip>
                    </v-btn>
                    <v-btn @click="actions.destroyObjects">
                        <v-icon icon="mdi-delete"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Delete</v-tooltip>
                    </v-btn>
                    <v-btn @click="actions.exportImage">
                        <v-icon icon="mdi-download"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Download As Image</v-tooltip>
                    </v-btn>
                </div>
            </template>
        </v-app-bar>

        <!-- text buttons -->
        <v-app-bar v-if="objectSelected.length === 1 && objectSelected[0].objectType === 'text'">
            <input class="ml-2" type="color" style="width: 40px; height: 40px" v-model="selectedFillColor"
                @input="actions.fillColor(selectedFillColor)" />
            <div class="d-flex">
                <v-combobox 
                    clearable
                    label="font style"
                    :items="fonts"
                    item-title="name"
                    item-value="name"
                    v-model="selectedFont" 
                    @update:modelValue="actions.changeFontFamily(selectedFont)"
                    style="width: 150px;" class="m-2 mt-5"
                >
                </v-combobox>
            </div>
            <input type="number" v-model="fontSize" @input="actions.textSize(fontSize)"
                style="width: 100px; border: 1px solid #ddd;" />
            <v-btn>
                <v-icon icon="mdi-format-align-center"></v-icon>
                <v-menu activator="parent">
                    <v-list>
                        <v-list-item>
                            <v-btn class="" @click="actions.alignText('justify')">
                                <v-icon icon="mdi-format-align-justify"></v-icon>
                            </v-btn>
                            <v-btn @click="actions.alignText('right')">
                                <v-icon icon="mdi-format-align-right"></v-icon>
                            </v-btn>
                            <v-btn class="" @click="actions.alignText('left')">
                                <v-icon icon="mdi-format-align-left"></v-icon>
                            </v-btn>
                            <v-btn @click="actions.alignText('center')">
                                <v-icon icon="mdi-format-align-center"></v-icon>
                            </v-btn>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
            <v-btn @click="toggleFOntCase()">
                <v-icon icon="mdi-format-letter-case-upper"></v-icon>
            </v-btn>
            <v-btn @click="toggleFontWeight">
                <v-icon icon="mdi-format-bold"></v-icon>
            </v-btn>
            <v-btn @click="toggleFontItalic()">
                <v-icon icon="mdi-format-italic"></v-icon>
            </v-btn>
            <v-btn>
                <v-icon icon="mdi-format-line-spacing"></v-icon>
                <v-menu activator="parent">
                    <v-list>
                        <v-list-item>
                            <v-list-item-title>Spacing</v-list-item-title>
                            <v-slider @mouseout="actions.textCharSpacing(charSpacing)" :max="sMax" :min="sMin"
                                v-model="charSpacing" style="width: 250px" class="align-center" hide-details>
                            </v-slider>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>line height</v-list-item-title>
                            <v-slider @mouseout="actions.textLineHight(lineHight)" :max="lMax" :min="lMin"
                                v-model="lineHight" style="width: 250px" class="align-center" hide-details>
                            </v-slider>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
        </v-app-bar>
        <!-- shape buttons -->
        <v-app-bar v-if="objectSelected.length === 1 && objectSelected[0].objectType === 'shape'">
            <input class="ml-2" type="color" style="width: 40px; height: 40px" v-model="selectedFillColor"
                @input="actions.fillColor(selectedFillColor)" />
        </v-app-bar>
        <!-- image buttons -->
        <v-app-bar v-if="objectSelected.length === 1 && objectSelected[0].objectType === 'image'">
            <v-btn>
                <v-icon icon="mdi-crop"></v-icon>
            </v-btn>
        </v-app-bar>

        <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color: #ebebeb;">
            <slot />
        </v-main>
    </v-layout>
</template>
<script>
import { rectConfig, circleConfig, triangleConfig, hexagonConfig, octagonConfig } from '../Utils/shapesConfig.js';

export default {
    data() {
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
            selectedFillColor: 'white',
            images: [],
            hide: true,
            fontSize: 30,
            fontWeight: 'normal',
            fontItalic: 'normal',
            fontCase: 'lower',
            //// lineHight /////
            lMin: 0,
            lMax: 3,
            lineHight: 1,
            //// charspacing /////
            sMin: -10,
            sMax: 50,
            charSpacing: 1,

            selectedFont: 'cairo',
            fontFamilies: []
        }
    },
    computed: {

    },
    methods: {
        openTemp() {
            this.selectedOption.templates = !this.selectedOption.templates;
        },
        openText() {
            this.selectedOption.text = !this.selectedOption.text;
        },
        openPhotos() {
            this.selectedOption.photos = !this.selectedOption.photos;
        },
        openElement() {
            this.selectedOption.elements = !this.selectedOption.elements;
        },
        openUp() {
            this.selectedOption.upload = !this.selectedOption.upload;
        },
        openBack() {
            this.selectedOption.background = !this.selectedOption.background;
        },
        openLayer() {
            this.selectedOption.layers = !this.selectedOption.layers;
        },
        loadImages() {
            for (let i = 1; i <= 34; i++) {
                this.texts.push({
                    src: `/images/text/p${i}.png`,
                });
            }
        },
        backgroundImages() {
            for (let i = 1; i <= 21; i++) {
                this.images.push({
                    src: `/images/backgrounds/p${i}.jpg`,
                });
            }
        },
        toggleFontWeight() {
            this.fontWeight = this.fontWeight === 'normal' ? 'bold' : 'normal';
            this.actions.textStyle(this.fontWeight);
        },
        toggleFontItalic() {
            this.fontItalic = this.fontItalic === 'normal' ? 'italic' : 'normal';
            this.actions.textStyle(this.fontItalic);
        },
        toggleFOntCase() {
            this.fontCase = this.fontCase === 'lower' ? 'upper' : 'lower';
            this.actions.textCase(this.fontCase);
        },
    },
    mounted() {
        this.loadImages();
        this.backgroundImages();
        this.fontFamilies = this.fonts.map(font => font.name);
    },
    computed: {
        reversedLayers() {
            return [...this.layers].reverse();
        },
    },
    props: {
        actions: {
            type: Object,
            required: true,
        },
        layers: {
            type: Array,
            default: () => [],
        },
        objectSelected: {
            type: Array,
            default: () => [],
        },
        fonts: {
            type: Array,
            required: true,
        },
    }
}
</script>
<style scoped>
.scroll {
    max-height: 300px;
    overflow-y: scroll;
}

.layer {
    box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;
}
</style>
