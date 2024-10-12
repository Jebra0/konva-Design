<template>
    <v-app>

        <Head>
            <title>Admin Panel</title>
        </Head>

        <!-- defualt buttons -->
        <v-app-bar :elevation="1">

            <template v-slot:prepend>

                <input v-if="SelectedObjectType === 'Shape'" class="ml-2" type="color" style="width: 40px; height: 40px"
                    v-model="selectedFillColor" @input="fillColor(selectedFillColor)" />

                <!-- <v-btn :disabled="undoDisable" icon="mdi-undo" @click="unDo"></v-btn>
                <v-btn :disabled="redoDisable" icon="mdi-redo" @click="reDo"></v-btn> -->

                <!-- crop image -->
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
                    <!-- save as template -->
                    <v-dialog v-if="!this.editingTemp" max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn-group color="blue-grey" density="comfortable" rounded="pill" divided>
                                <v-btn v-bind="activatorProps">
                                    save as template
                                </v-btn>
                            </v-btn-group>
                        </template>
                        <template v-slot:default="{ isActive }">
                            <v-card title="Save as template">
                                <v-text-field label="Template Name" required v-model="templateName"></v-text-field>

                                <v-select label="Template Type" required v-model="templateType" :items="categories"
                                    item-title="name" item-value="id"></v-select>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn text="Save"
                                        @click=" saveAsTemplate(templateName, templateType); isActive.value = false"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                    <!-- save edited template -->
                    <v-btn-group color="blue-grey" density="comfortable" rounded="pill" divided>
                        <v-btn v-if="this.editingTemp" @click="saveEditedTemplate(this.editedId)">Save</v-btn>
                    </v-btn-group>

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
                                    <v-btn @click="alignCenter" class="mr-9">
                                        <v-icon icon="mdi-align-horizontal-center"></v-icon>
                                        <v-tooltip activator="parent" location="bottom">Align Center</v-tooltip>
                                    </v-btn>
                                    <v-btn @click="alignMiddle">
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

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'templates' ? '#ebebeb' : '' }"
                    @click="selectOption('templates')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'templates' ? '#607D8B' : 'white'"
                        class="icon">mdi-view-dashboard</v-icon>
                    <span :style="{ color: selectedOption.active === 'templates' ? '#607D8B' : 'white' }"
                        class="list-title">Templates</span>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'text' ? '#ebebeb' : '' }"
                    @click="selectOption('text')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'text' ? '#607D8B' : 'white'"
                        class="icon">mdi-format-text</v-icon>
                    <span :style="{ color: selectedOption.active === 'text' ? '#607D8B' : 'white' }"
                        class="list-title">Text</span>
                </v-list-item>


                <v-list-item :style="{ backgroundColor: selectedOption.active === 'photos' ? '#ebebeb' : '' }"
                    @click="selectOption('photos')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'photos' ? '#607D8B' : 'white'"
                        class="icon">mdi-image-outline</v-icon>
                    <span :style="{ color: selectedOption.active === 'photos' ? '#607D8B' : 'white' }"
                        class="list-title">Photos</span>
                </v-list-item>


                <v-list-item :style="{ backgroundColor: selectedOption.active === 'elements' ? '#ebebeb' : '' }"
                    @click="selectOption('elements')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'elements' ? '#607D8B' : 'white'"
                        class="icon">mdi-shape</v-icon>
                    <span :style="{ color: selectedOption.active === 'elements' ? '#607D8B' : 'white' }"
                        class="list-title">Elements</span>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'upload' ? '#ebebeb' : '' }"
                    @click="selectOption('upload')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'upload' ? '#607D8B' : 'white'"
                        class="icon">mdi-cloud-upload</v-icon>
                    <span :style="{ color: selectedOption.active === 'upload' ? '#607D8B' : 'white' }"
                        class="list-title">Upload</span>
                </v-list-item>

                <!-- add category-->
                <v-list-item :style="{ backgroundColor: selectedOption.active === 'addCategory' ? '#ebebeb' : '' }"
                    @click="selectOption('addCategory')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'addCategory' ? '#607D8B' : 'white'"
                        class="icon">mdi-format-list-bulleted-square</v-icon>
                    <span :style="{ color: selectedOption.active === 'addCategory' ? '#607D8B' : 'white' }"
                        class="list-title">Add
                        Category</span>
                </v-list-item>


                <v-list-item :style="{ backgroundColor: selectedOption.active === 'layers' ? '#ebebeb' : '' }"
                    @click="selectOption('layers')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'layers' ? '#607D8B' : 'white'"
                        class="icon">mdi-layers-triple</v-icon>
                    <span :style="{ color: selectedOption.active === 'layers' ? '#607D8B' : 'white' }"
                        class="list-title">Layers</span>
                </v-list-item>

            </v-list>
        </v-navigation-drawer>

        <v-navigation-drawer style="background-color: #ebebeb;" permanent>

            <v-row v-if="selectedOption.templates" style="background-color: #ebebeb;">
                <v-col cols="6" class="imgParent" v-for="(temp, id) in templates" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image" @click=" getSelectedTemplate(temp.id)"
                        style="cursor: pointer">
                    <v-icon @click="editTemplate(temp.id)" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                        color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon @click="deleteTemplate(temp.id)" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </v-col>
            </v-row>

            <v-card v-if="selectedOption.text" style="background-color: #ebebeb;">
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
                    Text
                </v-btn>
                <div class=" imgParent d-flex justify-center" v-for="(temp, id) in textTemplates" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image" @click="getSelectedTemplate(temp.id)"
                        style="cursor: pointer">
                    <!-- <v-btn @click="editTemplate(temp.id)" class="edit-btn" icon="mdi-pencil"></v-btn>
                    <v-btn @click="deleteTemplate(temp.id)" class="delete-btn" color="red" icon="mdi-delete"></v-btn> -->
                    <v-icon @click="editTemplate(temp.id)" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                        color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon @click="deleteTemplate(temp.id)" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </div>
            </v-card>

            <v-card v-if="selectedOption.photos" style="background-color: #ebebeb;" elevation="1" outlined>
                <v-container class="d-flex">
                    <v-text-field style="width: 50px;" label="search" v-model="searchQuery"></v-text-field>
                    <v-btn @click="searchUnsplashImages(searchQuery)" class="my-2" color="blue-grey">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                </v-container>
                <v-progress-linear v-if="this.isDataRedy == true" indeterminate color="blue-grey"
                    class="my-3"></v-progress-linear>
                <div v-if="this.isDataRedy == false" class="imgParent" v-for="(image, index) in images" :key="index">
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

            <div v-if="selectedOption.elements" class=" d-flex mx-4 flex-wrap justify-between">
                <div style="position: relative;" class=" d-flex justify-center" v-for="(shape, id) in shapeTemplates"
                    :key="id">
                    <img :src="shape.image" width="70px" alt="Text Image" @click=" getSelectedTemplate(shape.id)"
                        style="cursor: pointer">
                    <v-icon @click="deleteTemplate(shape.id)" size="25"
                        style="cursor: pointer; position: absolute; top: 5px; left: 1px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </div>
                <!-- <v-icon icon="mdi-rectangle" color="rgb(179 177 177)" size="70"
                        @click=" addShape(rectConfig)"></v-icon> -->
            </div>

            <v-card v-if="selectedOption.upload" elevation="0" outlined style="background-color: #ebebeb;">
                <div class="d-flex m-1">
                    <v-file-input label="Upload image" prepend-icon="mdi-camera" multiple @change="handleFileUpload">
                        <template v-slot:selection="{ fileNames }">
                            <template v-for="(fileName, index) in fileNames" :key="fileName">
                                <v-chip v-if="index < 2" class="me-2" color="deep-purple-accent-4" size="small" label>
                                    {{ fileName }}
                                </v-chip>
                            </template>
                        </template>
                    </v-file-input>
                    <v-btn class="my-2 ml-2" @click="addTemplateImages(imageUrls)" color="blue-grey">Add</v-btn>
                </div>

                <!-- save images to storage -->
                <v-row>
                    <v-col v-for="(image, index) in templateImages" :key="index" class="mx-2 d-flex child-flex"
                        cols="5">
                        <v-img @click=" addImage(image.src)" style="cursor: pointer;" :src="image.src" aspect-ratio="1"
                            class="bg-grey-lighten-2" cover>
                            <template v-slot:placeholder>
                                <v-row align="center" class="fill-height ma-0" justify="center">
                                    <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                    </v-col>
                </v-row>
                <!-- save image temporary -->
                <!-- <v-row>
                    <v-col v-for="(image, index) in imageUrls" :key="index" class="mx-2 d-flex child-flex" cols="5">
                        <v-img @click=" addUploadedImage(image)" style="cursor: pointer;" :src="image"
                            class="bg-grey-lighten-2" cover>
                            <template v-slot:placeholder>
                                <v-row align="center" class="fill-height ma-0" justify="center">
                                    <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                    </v-col>
                </v-row>  -->
            </v-card>

            <v-card v-if="selectedOption.addCategory" style="background-color: #ebebeb;" elevation="0" class=" m-2">
                <v-text-field v-model="categoryName" label="category name"></v-text-field>
                <div class="d-flex justify-center mt-3">
                    <v-btn @click="addTemplateCategory(categoryName)" color="blue-grey">Add</v-btn>
                </div>
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
        <transition name="fade">
            <v-app-bar absolute v-show="SelectedObjectType === 'Text'">

                <input class="ml-2" type="color" style="width: 40px; height: 40px" :value="selectedFillColor"
                    v-model="colorFill" @input="fillColor(colorFill)" />

                <div class="d-flex">
                    <v-combobox clearable :items="GoogleFonts" item-title="name" item-value="file"
                        v-model="selectedFont" @update:modelValue="onFontChange" width="200px" class=" m-2 mt-5">
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
                                <v-slider @mouseout=" textLineHight(lineHight)" :max="lMax" :min="lMin"
                                    v-model="lineHight" style="width: 250px" class="align-center" hide-details>
                                </v-slider>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-btn>

            </v-app-bar>
        </transition>

        <v-main class="d-flex align-center justify-center"
            style="min-height: 100vh; max-height: 100%; background-color: #ebebeb;">
            <div class="my-3" id="container"></div>
        </v-main>
    </v-app>
</template>
<script>
import allFunctions from '@/Utils/allFunctions.js';
import { rectConfig } from '../Utils/shapesConfig.js';
import { headerText, subHeaderText, bodyText } from '../Utils/textConfig.js';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    mixins: [allFunctions],
    components: { Head },
    data() {
        return {
            selectedOption: {
                templates: true,
                text: false,
                photos: false,
                elements: false,
                upload: false,
                layers: false,
                addCategory: false,
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

            categoryName: '',

            editingTemp: false,
            editedId: null,
        }
    },
    async mounted() {
        this.fetchUnsplashImages();
        this.initializeKonva();
        this.fetchFonts();
        // this.fetchFont('Khand')
    },
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
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'templates';
                    break;
                case 'text':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = true;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'text';
                    break;
                case 'photos':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = true;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'photos';
                    break;
                case 'elements':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = true;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'elements';
                    break;
                case 'upload':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = true;
                    this.selectedOption.layers = false;
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'upload';
                    break;
                case 'layers':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = true;
                    this.selectedOption.addCategory = false;
                    this.selectedOption.active = 'layers';
                    break;
                case 'addCategory':
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.addCategory = true;
                    this.selectedOption.active = 'addCategory';
                    break;
                default:
            }
        },
        editTemplate(id) {
            this.editingTemp = true;
            this.getSelectedTemplate(id);
            this.editedId = id;
        },
        async saveEditedTemplate(id) {
            try {
                let dataURL = this.stage.toDataURL({ pixelRatio: 3 });

                let blob = await this.resizeImage(dataURL, 300, 300);

                let formData = new FormData();
                formData.append('data', this.stage.toJSON());
                formData.append('image', blob, `new.png`);

                let res = await axios.post(`/template/edit/${id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                console.log(res);
                alert('Updated successfully!')
            } catch (error) {
                alert('Error while saving!')
            }
        },
        async deleteTemplate(id) {
            if (confirm("Are you sure ? ")) {
                try {
                    let res = await axios.post(`/template/delete/${id}`);
                    console.log(res)
                    alert('deleted successfully')
                } catch (error) {
                    alert('Error while deleting')
                }
            }
        }
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
        templateImages: {
            type: Object,
            required: true,
        },
        categories: {
            type: Object,
            required: true,
        }
    }
}
</script>
<style scoped>
#container {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
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

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
