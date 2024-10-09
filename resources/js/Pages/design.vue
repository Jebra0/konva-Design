<template>
    <v-layout>

        <Head>
            <title>Design Tool</title>
        </Head>

        <!-- defualt buttons -->
        <v-app-bar :elevation="1">

            <template v-slot:prepend>

                <input v-if="SelectedObjectType === 'Shape'" class="ml-2" type="color" style="width: 40px; height: 40px"
                    v-model="selectedFillColor" @input="fillColor(selectedFillColor)" />

                <v-btn :disabled="undoDisable" icon="mdi-undo" @click="unDo"></v-btn>
                <v-btn :disabled="redoDisable" icon="mdi-redo" @click="reDo"></v-btn>

                <!-- for image -->
                <!-- <div v-if="SelectedObjectType === 'Image' && objectSelected.length === 1">
        <v-btn @click=" addClip = !addClip; addClippingTool()">
            <v-icon icon="mdi-crop"></v-icon>
        </v-btn>
        <v-btn v-if="this.addClip" @click=" applyClipping(); addClip = !addClip;">crop</v-btn>
    </div> -->

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
                        <v-icon icon="mdi-layers"></v-icon>
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
                                    <v-btn class="mr-9" @click="alignCenter()">
                                        <v-icon icon="mdi-align-horizontal-center"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Center</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="alignMiddle()">
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

                    <!-- save As Json -->
                    <v-btn @click="saveAsJson">
                        <v-icon icon="mdi-content-save"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Save As json</v-tooltip>
                    </v-btn>

                    <!-- duplicate -->
                    <!-- <v-btn @click="duplicateObjects">
            <v-icon icon="mdi-content-copy"></v-icon>
            <v-tooltip activator="parent" location="bottom">Duplicate</v-tooltip>
        </v-btn> -->

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

        <v-navigation-drawer permanent color="blue-grey" width="90">
            <v-list density="compact">
                <!-- //////////////////////////// -->
                <!-- /////// TEMPLATES DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'templates' ? '#ebebeb' : '' }"
                    @click="selectOption('templates')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'templates' ? '#607D8B' : 'white'"
                        class="icon">mdi-view-dashboard</v-icon>
                    <span :style="{ color: selectedOption.active === 'templates' ? '#607D8B' : 'white' }"
                        class="list-title">Templates</span>
                </v-list-item>


                <!-- //////////////////////////// -->
                <!-- /////// TEXT DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'text' ? '#ebebeb' : '' }"
                    @click="selectOption('text')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'text' ? '#607D8B' : 'white'"
                        class="icon">mdi-format-text</v-icon>
                    <span :style="{ color: selectedOption.active === 'text' ? '#607D8B' : 'white' }"
                        class="list-title">Text</span>
                </v-list-item>

                <!-- //////////////////////////// -->
                <!-- /////// IMAGE DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'photos' ? '#ebebeb' : '' }"
                    @click="selectOption('photos')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'photos' ? '#607D8B' : 'white'"
                        class="icon">mdi-image-outline</v-icon>
                    <span :style="{ color: selectedOption.active === 'photos' ? '#607D8B' : 'white' }"
                        class="list-title">Photos</span>
                </v-list-item>


                <!-- //////////////////////////// -->
                <!-- /////// SHAPES DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'elements' ? '#ebebeb' : '' }"
                    @click="selectOption('elements')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'elements' ? '#607D8B' : 'white'"
                        class="icon">mdi-shape</v-icon>
                    <span :style="{ color: selectedOption.active === 'elements' ? '#607D8B' : 'white' }"
                        class="list-title">Elements</span>
                </v-list-item>

                <!-- //////////////////////////// -->
                <!-- /////// UPLOAD DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'upload' ? '#ebebeb' : '' }"
                    @click="selectOption('upload')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'upload' ? '#607D8B' : 'white'"
                        class="icon">mdi-cloud-upload</v-icon>
                    <span :style="{ color: selectedOption.active === 'upload' ? '#607D8B' : 'white' }"
                        class="list-title">Upload</span>
                </v-list-item>


                <!-- //////////////////////////// -->
                <!-- /////// LAYER DONE  //////////-->
                <!-- //////////////////////////// -->

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'layers' ? '#ebebeb' : '' }"
                    @click="selectOption('layers')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'layers' ? '#607D8B' : 'white'"
                        class="icon">mdi-cloud-upload</v-icon>
                    <span :style="{ color: selectedOption.active === 'layers' ? '#607D8B' : 'white' }"
                        class="list-title">Layers</span>
                </v-list-item>


            </v-list>
        </v-navigation-drawer>


        <v-navigation-drawer width="300" style="background-color: #ebebeb;">
            <v-card v-if="selectedOption.templates">
                <div class="d-flex justify-center" v-for="(temp, id) in templates" :key="id">
                    <img :src="temp.image" width="350px" alt="Text Image" @click=" getSelectedTemplate(temp.id)"
                        style="cursor: pointer">
                </div>
            </v-card>

            <v-card v-if="selectedOption.text" elevation="1" outlined>
                <v-btn v-on:click.native="addHeader()" elevation="0" width="100%"
                    style=" text-transform: none; font-size: 25px;">
                    <h1>Create header</h1>
                </v-btn>
                <v-btn @click="addSubHeader()" elevation="0" width="100%"
                    style="text-transform: none; font-size: 18px;">
                    <h4>Create sub header</h4>
                </v-btn>
                <v-btn @click="addBodyText()" elevation="0" width="100%"
                    style="text-transform: none; font-size: 14px;">Create
                    Body
                    Text</v-btn>
                <div class="d-flex justify-center" v-for="(temp, id) in textTemplates" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image" @click=" getSelectedTemplate(temp.id)"
                        style="cursor: pointer">
                </div>
            </v-card>

            <v-card v-if="selectedOption.photos" elevation="1" outlined>
                <v-container class="d-flex">
                    <v-text-field label="search" v-model="searchQuery"></v-text-field>
                    <v-icon size="30" class="mt-3 ml-2" icon="mdi-magnify" @click="searchUnsplashImages(searchQuery)"
                        style="cursor: pointer;"></v-icon>
                </v-container>
                <div class="imgParent" v-for="(image, index) in images" :key="index">
                    <img :src="image.src" alt="" @click=" addImage(image.src)"
                        style="cursor: pointer; width: 250px; margin-left: 20px; margin-bottom: 15px">
                    <p class="author">
                        Photo by
                        <a style="color: blue;" :href="image.portfolio">
                            {{ image.author }}
                        </a>
                    </p>
                </div>
            </v-card>

            <div v-if="selectedOption.elements" class="d-flex mx-4 flex-wrap justify-between">
                <div class="d-flex justify-center" v-for="(shape, id) in shapeTemplates" :key="id">
                    <img :src="shape.image" width="70px" alt="Text Image" @click=" getSelectedTemplate(shape.id)"
                        style="cursor: pointer">
                </div>
            </div>

            <v-card v-if="selectedOption.upload" elevation="0" outlined>
                <v-file-input label="Upload image" prepend-icon="mdi-camera" multiple @change="handleFileUpload">
                    <template v-slot:selection="{ fileNames }">
                        <template v-for="(fileName, index) in fileNames" :key="fileName">
                            <v-chip v-if="index < 2" class="me-2" color="deep-purple-accent-4" size="small" label>
                                {{ fileName }}
                            </v-chip>

                            <span v-else-if="index === 2" class="text-overline text-grey-darken-3 mx-2">
                                +{{ files.length - 2 }} File(s)
                            </span>
                        </template>
                    </template>
                </v-file-input>
                <v-row>
                    <v-col v-for="(image, index) in imageUrls" :key="index" class="d-flex child-flex" cols="6">
                        <v-img @click=" addUploadedImage(image)" style="cursor: pointer;" :src="image" aspect-ratio="1"
                            class="bg-grey-lighten-2" cover>
                            <template v-slot:placeholder>
                                <v-row align="center" class="fill-height ma-0" justify="center">
                                    <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                    </v-col>
                </v-row>
            </v-card>

            <v-card v-if="selectedOption.layers" elevation="1">
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

        </v-navigation-drawer>

        <!-- text buttons -->
        <v-app-bar :style="{ visibility: SelectedObjectType === 'Text' ? 'visible' : 'hidden' }">

            <input class="ml-2" type="color" style="width: 40px; height: 40px" :value="selectedFillColor"
                v-model="colorFill" @input="fillColor(colorFill)" />

            <div class="d-flex">
                <v-combobox clearable :items="GoogleFonts" item-title="name" item-value="file" v-model="selectedFont"
                    @update:modelValue="onFontChange" width="200px" class=" m-2 mt-5">
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
            <!-- {{ undoDisable }} -->
        </v-main>
    </v-layout>
</template>
<script>
import allFunctions from '@/Utils/allFunctions.js';
import { rectConfig } from '../Utils/shapesConfig.js';
import { headerText, subHeaderText, bodyText } from '../Utils/textConfig.js';
import { Head } from '@inertiajs/vue3';

export default {
    mixins: [allFunctions],
    components: { Head },
    methods: {
        selectOption(type) {
            switch (type) {
                case 'templates':
                    this.selectedOption.templates = true;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'templates';
                    break;
                case 'text':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = true;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'text';
                    break;
                case 'photos':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = true;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'photos';
                    break;
                case 'elements':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = true;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'elements';
                    break;
                case 'upload':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = true;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'upload';
                    break;
                case 'layers':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = true;
                    this.selectedOption.active = 'layers';
                    break;
                default:
            }
        }
    },

    data() {
        return {
            selectedOption: {
                templates: true,
                text: false,
                photos: false,
                elements: false,
                upload: false,
                // background: false,
                layers: false,
                // addFonts: false,
                // addCategory: false,
                active: 'templates'
            },
            rectConfig,
            ////opacity/////
            min: 0,
            max: 1,
            opacity: 1,
            ////////////////
            texts: [],
            images: [],
            hide: true,
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

            fontFile: null,
            fontName: '',
            fontsKey: 'AIzaSyATi0D1tPNKzPRhsEJVJ-0Ikv4SPlBD9uY',
            fontUrl: 'https://www.googleapis.com/webfonts/v1/webfonts',
            GoogleFonts: [],

            selectedFont: null,
        }
    },
    mounted() {
        this.fetchUnsplashImages();
        this.initializeKonva();
        this.fetchFonts();
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
        // selectedFillColor() {
        //     if (this.objectSelected.length === 1) {
        //         return this.objectSelected[0].config.attrs.fill;
        //     } else {
        //         return '#0000';
        //     }
        // },
        fontSize() {
            if (this.objectSelected.length === 1) {
                return this.objectSelected[0].config.attrs.fontSize;
            } else {
                return '11';
            }
        }
    },
    props: {
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

.custom-list-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.icon {
    font-size: 36px;
}

.list-title {
    margin-top: 4px;
    font-weight: bold;
    color: white;
}

.active {
    color: #CFD8DC;
}
</style>
