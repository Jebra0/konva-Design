<template>
    <v-app>
        <Head><title>Admin Panel</title></Head>
        <v-navigation-drawer permanent width="300">
            <v-list density="compact">

                <v-list-item @click="this.selectedOption.templates = !this.selectedOption.templates"
                             prepend-icon="mdi-view-dashboard" title="Templates" value="Templates"></v-list-item>
                <v-card class=" scroll" v-if="selectedOption.templates" elevation="1" outlined>
                    <div class="imgParent d-flex justify-center" v-for="(temp, id) in templates" :key="id">
                        <img :src="temp.image" width="250px" alt="Text Image" @click=" getSelectedTemplate(temp.id)"
                             style="cursor: pointer">
                        <v-btn class="edit-btn" icon="mdi-pencil"></v-btn>
                        <v-btn class="delete-btn" color="red" icon="mdi-delete"></v-btn>
                    </div>
                </v-card>

                <v-list-item @click="this.selectedOption.text = !this.selectedOption.text"
                             prepend-icon="mdi-format-text" title="Text" value="Text"></v-list-item>
                <v-card class="scroll" v-if="selectedOption.text" elevation="1" outlined>
                    <v-btn v-on:click.native="addHeader()" elevation="0" width="100%"
                           style=" text-transform: none; font-size: 25px;">
                        <h1>Create header</h1>
                    </v-btn>
                    <v-btn @click="addSubHeader()" elevation="0" width="100%"
                           style="text-transform: none; font-size: 18px;">
                        <h4>Create sub header</h4>
                    </v-btn>
                    <v-btn @click="addBodyText()" elevation="0" width="100%"
                           style="text-transform: none; font-size: 14px;">Create Body
                        Text</v-btn>
                    <div class=" imgParent d-flex justify-center" v-for="(temp, id) in textTemplates" :key="id">
                        <img :src="temp.image" width="250px" alt="Text Image" @click=" getSelectedTemplate(temp.id)"
                             style="cursor: pointer">
                        <v-btn class="edit-btn" icon="mdi-pencil"></v-btn>
                        <v-btn class="delete-btn" color="red" icon="mdi-delete"></v-btn>
                    </div>
                </v-card>

                <v-list-item @click="this.selectedOption.photos = !this.selectedOption.photos"
                             prepend-icon="mdi-image-outline" title="Photos" value="Photos"></v-list-item>
                <v-card v-if="selectedOption.photos" class="scroll" elevation="1" outlined>
                    <v-container class="d-flex">
                        <v-text-field label="search" v-model="searchQuery"></v-text-field>
                        <v-icon size="30" class="mt-3 ml-2" icon="mdi-magnify"
                                @click="searchUnsplashImages(searchQuery)" style="cursor: pointer;"></v-icon>
                    </v-container>
                    <div class="imgParent" v-for="(image, index) in images" :key="index">
                        <img :src="image.src" alt="" @click=" addImage(image.src)"
                             style="cursor: pointer; width: 250px; margin-left: 20px; margin-bottom: 15px">
                        <p class="author">Photo by <a style="color: blue;" :href="image.portfolio">{{ image.author
                            }}</a>
                        </p>
                    </div>
                </v-card>

                <v-list-item @click="this.selectedOption.elements = !this.selectedOption.elements"
                             prepend-icon="mdi-shape" title="Elements" value="Elements"></v-list-item>
                <div v-if="selectedOption.elements" class="scroll d-flex mx-4 flex-wrap justify-between">
                    <!-- <div class="d-flex justify-center" v-for="(shape, id) in shapeTemplates" :key="id">
                        <img :src="shape.image" width="70px" alt="Text Image"
                            @click=" getSelectedTemplate(shape.id)" style="cursor: pointer">
                    </div> -->
                    <v-icon icon="mdi-minus" color="black" size="70"></v-icon>
                    <v-icon icon="mdi-arrow-right-thin" color="black" size="70"></v-icon>
                    <v-icon icon="mdi-rectangle" color="rgb(179 177 177)" size="70"
                            @click=" addShape(rectConfig)"></v-icon>
                    <v-icon icon="mdi-circle" color="rgb(179 177 177)" size="70"
                            @click=" addShape(circleConfig)"></v-icon>
                    <v-icon icon="mdi-triangle" color="rgb(179 177 177)" size="70"
                            @click=" addShape(triangleConfig)"></v-icon>
                    <v-icon icon="mdi-hexagon" color="rgb(179 177 177)" size="70"
                            @click=" addShape(hexagonConfig)"></v-icon>
                    <v-icon icon="mdi-octagon" color="rgb(179 177 177)" size="70"
                            @click=" addShape(octagonConfig)"></v-icon>
                    <v-icon icon="mdi-star" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-rhombus" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-pentagon" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-message" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-plus" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-arrow-down-bold" color="rgb(179 177 177)" size="70"></v-icon>
                    <v-icon icon="mdi-heart" color="rgb(179 177 177)" size="70"></v-icon>
                </div>

                <v-list-item @click="this.selectedOption.upload = !this.selectedOption.upload"
                             prepend-icon="mdi-cloud-upload" title="Upload" value="Upload"></v-list-item>
                <v-card class="scroll m-3" v-if="selectedOption.upload" elevation="0" outlined>
                    <v-file-input label="Upload image" prepend-icon="mdi-camera" multiple @change="handleFileUpload">
                        <template v-slot:selection="{ fileNames }">
                            <template v-for="(fileName, index) in fileNames" :key="fileName">
                                <v-chip v-if="index < 2" class="me-2" color="deep-purple-accent-4" size="small" label>
                                    {{ fileName }}
                                </v-chip>
                            </template>
                        </template>
                    </v-file-input>
                    <div class="d-flex justify-center mb-3">
                        <v-btn @click="addTemplateImages(imageUrls)">Add</v-btn>
                    </div>
                    <v-row>
                        <v-col v-for="(image, index) in templateImages" :key="index" class="d-flex child-flex" cols="6">
                            <v-img @click=" addImage(image.src)" style="cursor: pointer;" :src="image.src"
                                   aspect-ratio="1" class="bg-grey-lighten-2" cover>
                                <template v-slot:placeholder>
                                    <v-row align="center" class="fill-height ma-0" justify="center">
                                        <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                    </v-row>
                                </template>
                            </v-img>
                        </v-col>
                    </v-row>
                </v-card>
<!--                add font-->
                <v-list-item @click="this.selectedOption.addFonts = !this.selectedOption.addFonts"
                             prepend-icon="mdi-format-font" title="Add Font" value="addFonts"></v-list-item>
                <v-card v-if="selectedOption.addFonts" elevation="0" class=" m-2">
                    <v-file-input accept="application/x-font-ttf" label="Font"></v-file-input>
                    <div class="d-flex justify-center mt-3">
                        <v-btn>Add</v-btn>
                    </div>
                </v-card>

<!--                add category-->
                <v-list-item @click="this.selectedOption.addCategory = !this.selectedOption.addCategory"
                             prepend-icon="mdi-format-list-bulleted-square" title="Add Category" value="addCategory"></v-list-item>
                <v-card v-if="selectedOption.addCategory" elevation="0" class=" m-2">
                    <v-text-field label="category name"></v-text-field>
                    <div class="d-flex justify-center mt-3">
                        <v-btn>Add</v-btn>
                    </div>
                </v-card>


                <v-list-item @click=" this.selectedOption.layers = !this.selectedOption.layers"
                             prepend-icon="mdi-layers-triple" title="Layers" value="Layers"></v-list-item>
                <v-card v-if="selectedOption.layers" elevation="1" class="scroll">
                    <div class="m-2 p-2 layer d-flex" v-for="(layer, i) in reversedLayers" :key="i" :value="layer">
                        <span v-if="layer.name !== 'Defualt Layer'">
                            <button @click=" hideLayer(!layer.visible, layer.id)" class="mr-2">
                                <v-icon :icon="layer.visible ? 'mdi-eye' : 'mdi-eye-closed'"></v-icon>
                            </button>
                            <button @click="deleteLayer(layer.layer.id())"><v-icon icon="mdi-delete"></v-icon></button>
                        </span>
                        <span v-text="layer.name" class="ml-1" style="width: 120px;"></span>

                        <span class="ml-5" v-if="layer.name !== 'Defualt Layer'">
                            <button v-if="(!layer.lastOne)" @click=" moveLayer('up', layer.id)"><v-icon
                                icon="mdi-arrow-up"></v-icon></button>
                            <button v-if="(!layer.firstOne)" @click=" moveLayer('down', layer.id)"><v-icon
                                icon="mdi-arrow-down"></v-icon></button>
                        </span>
                    </div>
                </v-card>
            </v-list>
        </v-navigation-drawer>
        <!-- defualt buttons -->
        <v-app-bar :elevation="1">

            <template v-slot:prepend>

                <input v-if="SelectedObjectType === 'Shape'" class="ml-2" type="color" style="width: 40px; height: 40px"
                       :value="selectedFillColor"
                       v-model="colorFill" @input="fillColor(colorFill)" />

                <v-btn color="red" icon="mdi-undo" @click="unDo"></v-btn>
                <v-btn color="red" icon="mdi-redo" @click="reDo"></v-btn>

                <!-- for image -->
                <div v-if="SelectedObjectType === 'Image' && objectSelected.length === 1">
                    <v-btn @click=" addClip = !addClip; addClippingTool()">
                        <v-icon icon="mdi-crop"></v-icon>
                    </v-btn>
                    <v-btn v-if="this.addClip" @click=" applyClipping(); addClip = !addClip;">crop</v-btn>
                </div>

            </template>

            <span class="ml-20">

                <v-btn @click=" zoomFunction('in')">
                    <v-icon icon="mdi-magnify-plus-outline" size="25px"></v-icon>
                    <v-tooltip activator="parent" location="bottom">Zoom In</v-tooltip>
                </v-btn>

                <v-btn @click=" zoomFunction('out')">
                    <v-icon icon="mdi-magnify-minus-outline" size="25px"></v-icon>
                    <v-tooltip activator="parent" location="bottom">Zoom Out</v-tooltip>
                </v-btn>

            </span>

            <template v-slot:append>

                <div style="">
                    <!-- positions -->
                    <v-btn>
                        <v-icon color="red" icon="mdi-layers"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Position</v-tooltip>
                        <v-menu activator="parent">
                            <v-list style="width: 200px">
                                <v-list-item>
                                    <v-list-item-title>Position</v-list-item-title>
                                    <v-btn class="mr-9" @click="alignLeft">
                                        <v-icon icon="mdi-align-horizontal-left"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Left</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="alignRight">
                                        <v-icon icon="mdi-align-horizontal-right"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Right</v-tooltip>
                                    </v-btn>
                                    <v-btn class="mr-9" @click="alignTop">
                                        <v-icon icon="mdi-align-vertical-top"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Top</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="alignBottom">
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

                    <!-- opacity -->
                    <v-btn>
                        <v-icon icon="mdi-opacity"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Opacity</v-tooltip>
                        <v-menu activator="parent">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Transparency</v-list-item-title>
                                    <v-slider @mouseout=" objectOpacity(opacity)" v-model="opacity" :max="max"
                                              :min="min" style="width: 250px" class="align-center" hide-details>
                                    </v-slider>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>

                    <!-- save as template -->
                    <v-dialog max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn v-bind="activatorProps">
                                <v-icon icon="mdi-content-save"></v-icon>
                                <v-tooltip activator="parent" location="bottom">Save As Template</v-tooltip>
                            </v-btn>
                        </template>
                        <template v-slot:default="{ isActive }">
                            <v-card title="Save as template">
                                <v-text-field label="Template Name" required v-model="templateName"></v-text-field>

                                <v-select label="Template Type" required v-model="templateType"
                                          :items="['Text', 'Fold brochure', 'Shapes']"></v-select>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn text="Save"
                                           @click=" saveAsTemplate(templateName, templateType); isActive.value = false"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>

                    <!-- save As Json -->
                    <v-btn @click="saveAsJson">
                        <v-icon icon="mdi-content-save"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Save As json</v-tooltip>
                    </v-btn>

                    <!-- duplicate -->
                    <v-btn @click="duplicateObjects">
                        <v-icon icon="mdi-content-copy"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Duplicate</v-tooltip>
                    </v-btn>

                    <!-- delete -->
                    <v-btn @click="destroyObjects()">
                        <v-icon icon="mdi-delete"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Delete</v-tooltip>
                    </v-btn>

                    <!-- export as image -->
                    <v-btn @click="exportImage">
                        <v-icon icon="mdi-download"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Download As Image</v-tooltip>
                    </v-btn>

                </div>
            </template>
        </v-app-bar>

        <!-- text buttons -->
        <v-app-bar :style="{ visibility: SelectedObjectType === 'Text' ? 'visible' : 'hidden' }">

            <input class="ml-2" type="color" style="width: 40px; height: 40px"
                   :value="selectedFillColor"
                   v-model="colorFill" @input="fillColor(colorFill)" />

            <div class="d-flex">
                <v-combobox clearable label="font style" :items="fonts" item-title="name" item-value="name"
                            :value="selectedFont" v-model="fontSelected" @update:modelValue=" changeFontFamily(fontSelected)" width="200px"
                            class=" m-2 mt-5">
                </v-combobox>
            </div>

            <input type="number" v-model="fontSize" @input=" textSize($event.target.value)"
                   style="width: 100px; border: 1px solid #ddd;" />

            <v-btn>
                <v-icon icon="mdi-format-align-center"></v-icon>
                <v-menu activator="parent">
                    <v-list>
                        <v-list-item>
                            <v-btn class="" @click=" alignText('justify')">
                                <v-icon icon="mdi-format-align-justify"></v-icon>
                            </v-btn>
                            <v-btn @click=" alignText('right')">
                                <v-icon icon="mdi-format-align-right"></v-icon>
                            </v-btn>
                            <v-btn class="" @click=" alignText('left')">
                                <v-icon icon="mdi-format-align-left"></v-icon>
                            </v-btn>
                            <v-btn @click=" alignText('center')">
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
                            <v-slider @mouseout=" textCharSpacing(charSpacing)" :max="sMax" :min="sMin"
                                      v-model="charSpacing" style="width: 250px" class="align-center" hide-details>
                            </v-slider>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>line height</v-list-item-title>
                            <v-slider @mouseout=" textLineHight(lineHight)" :max="lMax" :min="lMin" v-model="lineHight"
                                      style="width: 250px" class="align-center" hide-details>
                            </v-slider>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>

        </v-app-bar>

        <v-main class="d-flex align-center justify-center"
                style="min-height: 100vh; max-height: 100%; background-color: #ebebeb;">
            <div class="my-3" id="container"></div>
            <!-- {{ selectedObjectIds }} -->
        </v-main>
    </v-app>
</template>
<script>
import allFunctions from '@/Utils/allFunctions.js';
import { rectConfig, circleConfig, triangleConfig, hexagonConfig, octagonConfig } from '../Utils/shapesConfig.js';
import { headerText, subHeaderText, bodyText } from '../Utils/textConfig.js';
import { Head } from '@inertiajs/vue3';

export default {
    mixins: [allFunctions],
    components: {Head},
    data() {
        return {
            image: '/images/Templates/fold_brochure_template.png',
            selectedOption: {
                templates: false,
                text: false,
                photos: false,
                elements: false,
                upload: false,
                background: false,
                layers: false,
                addFonts: false,
                addCategory: false,
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
            images: [],
            hide: true,
            // fontSize: 30,
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

            // selectedFont: 'Cairo',
            fontFamilies: [],

            templateName: '',
            templateType: '',

            unsplashAccessKey: 'LhMEo6peuEizFSw0vjF5kANy-B6dgWvBoNmvxSdOlL0',
            unsplashUrl: 'https://api.unsplash.com/photos',
            unsplashSearchUrl: 'https://api.unsplash.com/search/photos',
            searchQuery: '',

            imageUrls: [],
            addClip: false,

            name: '',
            colorFill: null,
            fontSelected: '',
            sizeFont: '',
        }
    },
    mounted() {
        this.loadFonts();
        this.fontFamilies = this.fonts.map(font => font.name);
        this.fetchUnsplashImages();
        this.initializeKonva();

    },
    computed: {
        reversedLayers() {
            return [...this.layers].reverse();
        },
        SelectedObjectType() {
            if (this.objectSelected.length === 1) {
                if (this.objectSelected[0].objectType === 'Text') {
                    return 'Text';
                } else if (this.objectSelected[0].objectType === 'Image') {
                    return 'Image';
                } else {
                    return 'Shape';
                }
            }
            if (this.objectSelected.length > 1) {
                const firstType = this.objectSelected[0].objectType;
                const allSameType = this.objectSelected.every(obj => obj.objectType === firstType);

                if (allSameType) {
                    if (firstType === 'Text' || firstType === 'Image') {
                        return firstType;
                    } else {
                        return 'Shape';
                    }
                } else {
                    const hasTextOrImage = this.objectSelected.some(obj => obj.objectType === 'Text' || obj.objectType === 'Image');
                    if (hasTextOrImage) {
                        return null;
                    }
                    return 'Shape';
                }
            }
        },
        selectedFont() {
            if (this.objectSelected.length === 1) {
                return this.objectSelected[0].config.attrs.fontFamily;
            } else {
                return 'Cairo';
            }
        },
        selectedFillColor() {
            if (this.objectSelected.length === 1) {
                return this.objectSelected[0].config.attrs.fill;
            } else {
                return '#0000';
            }
        },
        fontSize() {
            if (this.objectSelected.length === 1) {
                return this.objectSelected[0].config.attrs.fontSize;
            } else {
                return '11';
            }
        }
    },
    props: {
        fonts: {
            type: Array,
            required: true,
        },
        textTemplates: {
            type: Object,
            required: true,
        },
        shapeTemplates: {
            type: Object,
            required: true,
        },
        templates: {
            type: Object,
            required: true,
        },
        templateImages: {
            type: Object,
            required: true,
        },
    }
}
</script>
<style scoped>
#container {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}

.scroll {
    max-height: 300px;
    overflow-y: scroll;
}

.layer {
    box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;
}

.imgParent {
    position: relative;
    display: inline-block;
}

.imgParent img {
    display: block;
    width: 100%;
}

.author {
    position: absolute;
    bottom: 4%;
    left: 7%;
    color: white;
    background-color: rgba(0, 0, 0, 0.5);
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
    width: 250px;
}

.edit-btn {
  position: absolute;
  top: 17px;
  right: 4px;
  z-index: 10;
  background-color: white;
  padding: 0;
}
.delete-btn {
  position: absolute;
  top: 17px;
  left: 4px;
  z-index: 10;
  background-color: white;
  padding: 0;
}
</style>
