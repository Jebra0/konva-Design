<template>
    <v-layout class="rounded rounded-md">
        <v-navigation-drawer permanent width="300">
            <v-list density="compact">
                <v-list-item @click="openTemp" prepend-icon="mdi-view-dashboard" title="Templates"
                    value="Templates"></v-list-item>
                <v-card class="scroll" v-if="selectedOption.templates" elevation="1" outlined>
                    <div class="d-flex justify-center" v-for="(temp, id) in templates" :key="id">
                        <img :src="temp.image" width="250px" alt="Text Image"
                            @click="actions.getSelectedTemplate(temp.id)" style="cursor: pointer">
                    </div>
                </v-card>
                <v-list-item @click="openText" prepend-icon="mdi-format-text" title="Text" value="Text"></v-list-item>
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
                    <div class="d-flex justify-center" v-for="(temp, id) in textTemplates" :key="id">
                        <img :src="temp.image" width="250px" alt="Text Image"
                            @click="actions.getSelectedTemplate(temp.id)" style="cursor: pointer">
                    </div>
                </v-card>
                <v-list-item @click="openPhotos" prepend-icon="mdi-image-outline" title="Photos"
                    value="Photos"></v-list-item>
                <v-card v-if="selectedOption.photos" class="scroll" elevation="1" outlined>
                    <v-container class="d-flex">
                        <v-text-field label="search" v-model="searchQuery"></v-text-field>
                        <v-icon size="30" class="mt-3 ml-2" icon="mdi-magnify"
                            @click="searchUnsplashImages(searchQuery)" style="cursor: pointer;"></v-icon>
                    </v-container>
                    <div class="imgParent" v-for="(image, index) in images" :key="index">
                        <img :src="image.src" alt="" @click="actions.addImage(image.src)"
                            style="cursor: pointer; width: 250px; margin-left: 20px; margin-bottom: 15px">
                        <p class="author">Photo by <a style="color: blue;" :href="image.portfolio">{{ image.author
                                }}</a>
                        </p>
                    </div>
                </v-card>

                <v-list-item @click="openElement" prepend-icon="mdi-shape" title="Elements"
                    value="Elements"></v-list-item>
                <div v-if="selectedOption.elements" class="scroll d-flex mx-4 flex-wrap justify-between">
                    <!-- <div class="d-flex justify-center" v-for="(shape, id) in shapeTemplates" :key="id">
                        <img :src="shape.image" width="70px" alt="Text Image"
                            @click="actions.getSelectedTemplate(shape.id)" style="cursor: pointer">
                    </div> -->
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
                <v-card class="scroll m-3" v-if="selectedOption.upload" elevation="0" outlined>
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
                            <v-img @click="actions.addUploadedImage(image)" style="cursor: pointer;" :src="image"
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

                <v-list-item>
                    <v-text-field v-model="name"></v-text-field>
                    <v-file-input label="Upload image" prepend-icon="mdi-camera" @change="handleFileUpload">
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
                            <v-img @click="actions.addTemplateImage(image, name)" style="cursor: pointer;" :src="image"
                                aspect-ratio="1" class="bg-grey-lighten-2" cover>
                                <template v-slot:placeholder>
                                    <v-row align="center" class="fill-height ma-0" justify="center">
                                        <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                    </v-row>
                                </template>
                            </v-img>
                        </v-col>
                    </v-row>
                    <hr>
                    <v-img @click="actions.addImage(image)" style="cursor: pointer;" :src="image" aspect-ratio="1"
                        class="bg-grey-lighten-2" cover>
                        <template v-slot:placeholder>
                            <v-row align="center" class="fill-height ma-0" justify="center">
                                <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                            </v-row>
                        </template>
                    </v-img>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <!-- defualt buttons -->
        <v-app-bar :elevation="1">
            <template v-slot:prepend>
                <input v-if="SelectedObjectType === 'Shape'" class="ml-2" type="color" style="width: 40px; height: 40px"
                    v-model="selectedFillColor" @input="actions.fillColor(selectedFillColor)" />
                <v-btn color="red" icon="mdi-undo" @click="actions.unDo"></v-btn>
                <v-btn color="red" icon="mdi-redo" @click="actions.reDo"></v-btn>
                <!-- for image -->
                <div v-if="SelectedObjectType === 'Image' && objectSelected.length === 1">
                    <v-btn @click=" addClip = !addClip; actions.addClippingTool()">
                        <v-icon icon="mdi-crop"></v-icon>
                    </v-btn>
                    <v-btn v-if="this.addClip" @click="actions.applyClipping(); addClip = !addClip;">crop</v-btn>
                </div>
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

                    <!-- ///////////////// -->
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
                                        @click="actions.saveAsTemplate(templateName, templateType); isActive.value = false"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                    <!-- /////////////// -->
                    <v-btn @click="actions.saveAsJson">
                        <v-icon icon="mdi-content-save"></v-icon>
                        <v-tooltip activator="parent" location="bottom">Save As json</v-tooltip>
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
        <v-app-bar :style="{ visibility: SelectedObjectType === 'Text' ? 'visible' : 'hidden' }">
            <input class="ml-2" type="color" style="width: 40px; height: 40px" v-model="selectedFillColor"
                @input="actions.fillColor(selectedFillColor)" />
            <div class="d-flex">
                <v-combobox clearable label="font style" :items="fonts" item-title="name" item-value="name"
                    v-model="selectedFont" @update:modelValue="actions.changeFontFamily(selectedFont)"
                    :style="`font-type: ${name}`" width="150px" class=" m-2 mt-5">
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
        <v-main class="d-flex align-center justify-center" style="min-height: 100vh; max-height: 100%; background-color: #ebebeb;">
            <slot />
        </v-main>
    </v-layout>
</template>
<script>
import { rectConfig, circleConfig, triangleConfig, hexagonConfig, octagonConfig } from '../Utils/shapesConfig.js';
import { headerText, subHeaderText, bodyText } from '../Utils/textConfig.js';

export default {
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
            fontFamilies: [],

            templateName: '',
            templateType: '',

            unsplashAccessKey: 'LhMEo6peuEizFSw0vjF5kANy-B6dgWvBoNmvxSdOlL0',
            unsplashUrl: 'https://api.unsplash.com/photos',
            unsplashSearchUrl: 'https://api.unsplash.com/search/photos',
            searchQuery: '',

            imageUrls: [],
            addClip: false,
        }
    },
    methods: {
        handleFileUpload(event) {
            const files = event.target.files;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrls.push(e.target.result);
                };
                reader.readAsDataURL(file); 
            });
        },
        fetchUnsplashImages() {
            fetch(`${this.unsplashUrl}?client_id=${this.unsplashAccessKey}`)
                .then(res => res.json())
                .then(json => {
                    // console.log(json[0].urls.full)
                    json.forEach(element => {
                        this.images.push({
                            src: element.urls.full,
                            portfolio: element.user.portfolio_url,
                            author: element.user.name
                        });
                    });
                });
        },
        searchUnsplashImages(query) {
            fetch(`${this.unsplashSearchUrl}?client_id=${this.unsplashAccessKey}&query=${query}`)
                .then(res => res.json())
                .then(json => {
                    console.log(this.searchQuery)
                    json.results.forEach(element => {
                        //this.images =[];
                        this.images.unshift({
                            src: element.urls.full,
                            portfolio: element.user.portfolio_url,
                            author: element.user.name
                        });
                    });
                });
        },
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
        openLayer() {
            this.selectedOption.layers = !this.selectedOption.layers;
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
        addHeader() {
            this.actions.addText(headerText);
        },
        addSubHeader() {
            this.actions.addText(subHeaderText);
        },
        addBodyText() {
            this.actions.addText(bodyText);
        },
        loadFonts() {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/css/fonts.css';
            document.head.appendChild(link);
        },
    },
    mounted() {
        this.loadFonts();
        this.fontFamilies = this.fonts.map(font => font.name);
        this.fetchUnsplashImages();
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
        }
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
</style>
