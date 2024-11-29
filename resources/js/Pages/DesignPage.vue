<template>
    <v-app>

        <Head>
            <title>Konva Design</title>
        </Head>

        <!-- defualt buttons -->
        <v-app-bar :elevation="1">

            <template v-slot:prepend>
                <!-- login button -->
                <Link href="/login" class="mx-2" v-if="this.user == null">
                <v-btn-group class="mx-2" v-if="this.user == null" color="green" density="comfortable" rounded="pill"
                    divided>
                    <v-btn>
                        Login
                    </v-btn>
                </v-btn-group>
                </Link>

                <div class="text-center" v-if="this.user">
                    <v-menu open-on-hover>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                <v-icon color="blue-grey" class="mx-1">
                                    mdi-account
                                </v-icon>
                                {{ this.user?.name?.split(' ')[0] }}
                                <v-icon> mdi-menu-down</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <Link class="nave_list" v-for="(item, index) in acountNavItems" :key="index"
                                :href="route(item.link)"
                                v-show="item.for === 'all' || (item.for === 'admin' && isAdmin) || (item.for === 'user' && !isAdmin)">
                            <v-list-item>{{ item.title }}</v-list-item>
                            </Link>
                            <form @submit.prevent="logoutForm.post(route('logout'))">
                                <v-list-item>
                                    <button type="submit">Log out</button>
                                </v-list-item>
                            </form>
                        </v-list>
                    </v-menu>
                </div>

                <v-dialog v-if="!this.editingTemp && !this.isAdmin" max-width="500">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn-group color="blue-grey" density="comfortable" rounded="pill" divided>
                            <v-btn v-bind="activatorProps" prepend-icon="mdi-printer-outline">
                                print
                            </v-btn>
                        </v-btn-group>
                    </template>
                    <template v-slot:default="{ isActive }">
                        <form>
                            <v-card title="Add to cart">
                                <v-number-input :reverse="false" v-model="printForm.quantity" controlVariant="split"
                                    label="Quantity" :hideInput="false" :inset="false" :min="10"
                                    :max="1000"></v-number-input>
                                <div v-if="errors.quantity" class="text-red-600">{{ errors.quantity }}</div>

                                <v-select label="Category" required v-model="printForm.category_id" :items="categories"
                                    item-title="name" item-value="id"></v-select>
                                <div v-if="errors.category_id" class="text-red-600">{{ errors.category_id }}</div>
                                <v-card-actions>

                                    <v-spacer></v-spacer>
                                    <p style="color: #607D8B; font-weight: bolder">pleas justify your design at the
                                        center
                                    </p>
                                    <v-spacer></v-spacer>
                                    <v-btn-group color="blue-grey" :disable="printForm.processing" @click="addToCart">
                                        <v-btn text="Add to cart"></v-btn>
                                    </v-btn-group>
                                    <v-btn @click="isActive.value = false">close</v-btn>
                                </v-card-actions>
                            </v-card>
                        </form>

                    </template>
                </v-dialog>

                <v-badge :content="items" color="red" v-if="!this.isAdmin && items > 0">
                    <Link v-if="!this.isAdmin" :href="route('cart.index')">
                        <v-icon class="ml-5 " color="blue-grey">mdi-cart</v-icon>
                    </Link>
                </v-badge>
                <!-- <v-btn @click="saveAsPDF()">Export PDF</v-btn> -->


                <input v-if="SelectedObjectType === 'Shape'" class="ml-2" type="color" style="width: 40px; height: 40px"
                    v-model="selectedFillColor" @input="fillColor(selectedFillColor)" />

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
                <!-- add to my designs button -->
                <v-dialog v-if="!this.editingTemp && !this.isAdmin && this.user" max-width="500">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn-group color="blue-grey" density="comfortable" rounded="pill" divided>
                            <v-btn v-bind="activatorProps">
                                Add to my designs
                            </v-btn>
                        </v-btn-group>
                    </template>
                    <template v-slot:default="{ isActive }">
                        <v-card title="Add to my designs">

                            <v-text-field label="Template Name" required v-model="myDesignForm.name"></v-text-field>
                            <div v-if="errors.name" class="text-red-600">{{ errors.name }}</div>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn-group color="blue-grey" @click="saveAsTemplate('myDesigns')">
                                    <v-btn text="Save"></v-btn>
                                </v-btn-group>
                                <v-btn @click="isActive.value = false">Cancel</v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-dialog>

                <div>
                    <!-- save as template -->
                    <v-dialog v-if="!this.editingTemp && this.isAdmin" max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn-group color="blue-grey" density="comfortable" rounded="pill" divided>
                                <v-btn v-bind="activatorProps">
                                    save as template
                                </v-btn>
                            </v-btn-group>
                        </template>
                        <template v-slot:default="{ isActive }">
                            <v-card title="Save as template">
                                <v-text-field label="Template Name" required
                                    v-model="addTemplateForm.name"></v-text-field>
                                <div v-if="errors.name" class="text-red-600">{{ errors.name }}</div>

                                <v-radio-group label="Category" v-model="addTemplateForm.type">
                                    <v-radio label="Text" value="text"></v-radio>
                                    <v-radio label="Shape" value="shape"></v-radio>
                                    <v-radio label="Template" value="template"></v-radio>
                                </v-radio-group>
                                <div v-if="errors.type" class="text-red-600">{{ errors.type }}</div>

                                <v-select v-if="addTemplateForm.type === 'template'" label="Template Type" required
                                    v-model="addTemplateForm.category_id" :items="categories" item-title="name"
                                    item-value="id"></v-select>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn-group color="blue-grey" @click="saveAsTemplate('any')">
                                        <v-btn text="Save"></v-btn>
                                    </v-btn-group>
                                    <v-btn @click="isActive.value = false">cancel</v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                    <!-- save edited template for myDesigns and admin-->
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
            <v-list density="compact" class="pt-0">

                <!-- my designs -->
                <v-list-item v-if="this.user && !this.isAdmin"
                    :style="{ backgroundColor: selectedOption.active === 'myDesigns' ? '#ebebeb' : '' }"
                    @click="selectOption('myDesigns')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'myDesigns' ? '#607D8B' : 'white'"
                        class="icon">mdi-palette</v-icon>
                    <v-tooltip activator="parent" location="bottom">My designs</v-tooltip>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'templates' ? '#ebebeb' : '' }"
                    @click="selectOption('templates')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'templates' ? '#607D8B' : 'white'"
                        class="icon">mdi-view-dashboard</v-icon>
                    <v-tooltip activator="parent" location="bottom">Templates</v-tooltip>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'text' ? '#ebebeb' : '' }"
                    @click="selectOption('text')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'text' ? '#607D8B' : 'white'"
                        class="icon">mdi-format-text</v-icon>
                    <v-tooltip activator="parent" location="bottom">Text</v-tooltip>
                </v-list-item>


                <v-list-item :style="{ backgroundColor: selectedOption.active === 'photos' ? '#ebebeb' : '' }"
                    @click="selectOption('photos')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'photos' ? '#607D8B' : 'white'"
                        class="icon">mdi-image-outline</v-icon>
                    <v-tooltip activator="parent" location="bottom">photos</v-tooltip>
                </v-list-item>


                <v-list-item :style="{ backgroundColor: selectedOption.active === 'elements' ? '#ebebeb' : '' }"
                    @click="selectOption('elements')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'elements' ? '#607D8B' : 'white'"
                        class="icon">mdi-shape</v-icon>
                    <v-tooltip activator="parent" location="bottom">Shapes</v-tooltip>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'upload' ? '#ebebeb' : '' }"
                    @click="selectOption('upload')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'upload' ? '#607D8B' : 'white'"
                        class="icon">mdi-cloud-upload</v-icon>
                    <v-tooltip activator="parent" location="bottom">Upload images</v-tooltip>
                </v-list-item>

                <v-list-item :style="{ backgroundColor: selectedOption.active === 'layers' ? '#ebebeb' : '' }"
                    @click="selectOption('layers')" class="custom-list-item">
                    <v-icon :color="selectedOption.active === 'layers' ? '#607D8B' : 'white'"
                        class="icon">mdi-layers-triple</v-icon>
                    <v-tooltip activator="parent" location="bottom">Layers</v-tooltip>
                </v-list-item>

            </v-list>
        </v-navigation-drawer>

        <v-navigation-drawer permanent>

            <!-- my designs -->
            <v-row class="mx-2 my-2" v-if="selectedOption.myDesigns && this.user && !this.isAdmin" >
                <v-col cols="6" class="imgParent" v-for="(temp, id) in myDesign.data" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image"
                        @click="getSelectedTemplate(temp.id, 'myDesigns')" style="cursor: pointer">
                    <v-icon v-if="this.user" @click="editTemplate(temp.id, 'myDesigns')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                        color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon v-if="this.user" @click="deleteTemplate(temp.id, 'myDesigns')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </v-col>
                <v-col cols="12" class="d-flex justify-center align-center">
                    <v-btn v-if="currentPage < totalPages" @click="myDesignLoadMore">Load More</v-btn>
                </v-col>
            </v-row>

            <v-row v-if="selectedOption.templates">
                <v-col cols="12">
                    <div class="input-with-button p-1">
                        <v-text-field v-model="templateName" label="search" hide-details
                            style="margin-left: 8px; flex: 1;"></v-text-field>

                        <v-btn color="blue-grey" @click="searchForTemplate(templateName)">
                            <v-icon>mdi-magnify</v-icon>
                        </v-btn>
                    </div>
                </v-col>

                <v-col v-if="allTemplates !== null" cols="12" class="d-flex justify-center align-center">
                    <p>Search result</p>
                </v-col>

                <v-col v-if="allTemplates !== null" cols="6" class="imgParent" v-for="(temp, id) in allTemplates"
                    :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image"
                        @click=" getSelectedTemplate(temp.id, 'template')" style="cursor: pointer">
                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="editTemplate(temp.id, 'template')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                        color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="deleteTemplate(temp.id, 'template')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </v-col>

                <v-col cols="12" class="d-flex justify-center align-center">
                    <p>All Templates</p>
                </v-col>

                <v-col cols="6" class="imgParent" v-for="(temp, id) in templates.data" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image"
                        @click=" getSelectedTemplate(temp.id, 'template')" style="cursor: pointer">
                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="editTemplate(temp.id, 'template')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                        color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="deleteTemplate(temp.id, 'template')" size="30"
                        style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                        icon="mdi-delete"></v-icon>
                </v-col>
                <v-col cols="12" class="d-flex justify-center align-center">
                    <v-btn v-if="templateCurrentPage < templateTotalPages" @click="templateLoadMore">Load More</v-btn>
                </v-col>
            </v-row>

            <v-card v-if="selectedOption.text">
                <v-btn v-on:click.native="addHeader()" elevation="0" width="100%"
                       style=" text-transform: none; font-size: 25px;">
                    <h1>Create header</h1>
                </v-btn>
                <v-btn @click="addSubHeader()" elevation="0" width="100%"
                       style="text-transform: none; font-size: 18px;">
                    <h4>Create sub header</h4>
                </v-btn>
                <v-btn @click="addBodyText()" elevation="0" width="100%"
                       style="text-transform: none; font-size: 14px;">Create Body Text</v-btn>

                <div class="imgParent" v-for="(temp, id) in texts.data" :key="id">
                    <img :src="temp.image" width="250px" alt="Text Image" @click="getSelectedTemplate(temp.id, 'text')"
                         style="cursor: pointer">
                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="editTemplate(temp.id, 'text')" size="30"
                            style="cursor: pointer; position: absolute; top: 15px; right: 15px; z-index: 10;"
                            color="blue-grey" icon="mdi-pencil"></v-icon>

                    <v-icon v-if="this.isAdmin && temp.user_id === user.id" @click="deleteTemplate(temp.id, 'text')" size="30"
                            style="cursor: pointer; position: absolute; top: 15px; left: 15px; z-index: 10;" color="red"
                            icon="mdi-delete"></v-icon>
                </div>
                <v-col cols="12" class="d-flex justify-center align-center">
                    <v-btn v-if="textCurrentPage < textTotalPages" @click="textLoadMore">Load More</v-btn>
                </v-col>
            </v-card>

            <v-card v-if="selectedOption.photos" style=" " elevation="1" outlined>
                <v-row>
                    <v-col>
                        <div class="input-with-button p-1">
                            <v-text-field v-model="searchQuery" label="search" hide-details
                                style="margin-left: 8px; flex: 1;"></v-text-field>

                            <v-btn color="blue-grey" @click="searchUnsplashImages(searchQuery)">
                                <v-icon>mdi-magnify</v-icon>
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>

                <v-progress-linear v-if="this.isDataRedy == true" indeterminate color="blue-grey"
                    class="my-3"></v-progress-linear>
                <div v-if="this.isDataRedy == false" class="imgParent" v-for="(image, index) in images" :key="index">
                    <img :src="image.src" alt="" @click=" addImage(image.src)"
                        style="cursor: pointer; width: 250px; margin-left: 3px; margin-bottom: 15px">
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
                    <img :src="shape.image" width="70px" alt="Text Image"
                        @click=" getSelectedTemplate(shape.id, 'shape')" style="cursor: pointer">
                    <v-icon v-if="this.isAdmin && shape.user_id === user.id" @click="deleteTemplate(shape.id, 'shape')" size="25"
                        style="cursor: pointer; position: absolute; top: 5px; left: 1px; z-index: 10;" color="red"
                            icon="mdi-delete"></v-icon>
                </div>
            </div>

            <v-card v-if="user && selectedOption.upload" elevation="0" outlined style=" ">
                <div class="d-flex m-1">
                    <v-file-input label="file" prepend-icon="mdi-camera" multiple @change="handleFileUpload">
                        <template v-slot:selection="{ fileNames }">
                            <template v-for="(fileName, index) in fileNames" :key="fileName">
                                <v-chip v-if="index < 2" class="me-2" color="deep-purple-accent-4" size="small" label>
                                    {{ fileName }}
                                </v-chip>
                            </template>
                        </template>
                    </v-file-input>
                    <v-btn class="my-2 ml-2" @click="addTemplateImages(imageUrls, imageType)" color="blue-grey">Add</v-btn>
                </div>

                <!--admin -->
                <v-row v-if="isAdmin">
                    <v-col style="position: relative" v-for="(image, index) in templateImages" :key="index" class="mx-2 d-flex child-flex"
                        cols="5">
                        <v-img @click=" addImage(image.src)" style="cursor: pointer;" :src="image.src" aspect-ratio="1"
                            class="bg-grey-lighten-2" cover>
                            <template v-slot:placeholder>
                                <v-row align="center" class="fill-height ma-0" justify="center">
                                    <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                        <v-icon @click="deleteImage(image.src, imageType)" size="25"
                                style="background-color: white; cursor: pointer; position: absolute; top: 10px; right: 8px; z-index: 10;" color="red"
                                icon="mdi-delete"></v-icon>
                    </v-col>
                </v-row>

                <!-- user -->
                <v-row v-if="!isAdmin && user">
                    <v-col style="position: relative" v-for="(image, index) in user_images" :key="index" class="mx-2 d-flex child-flex"
                        cols="5">
                        <v-img @click=" addImage(image.image)" style="cursor: pointer;" :src="image.image" aspect-ratio="1"
                            class="bg-grey-lighten-2" cover>
                            <template v-slot:placeholder>
                                <v-row align="center" class="fill-height ma-0" justify="center">
                                    <v-progress-circular color="grey-lighten-5" indeterminate></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                        <v-icon @click="deleteImage(image.id, imageType)" size="25"
                                style="background-color: white; cursor: pointer; position: absolute; top: 10px; right: 8px; z-index: 10;" color="red"
                                icon="mdi-delete"></v-icon>
                    </v-col>
                </v-row>
            </v-card>

            <v-card class="scroll m-3" v-if=" !user && selectedOption.upload" elevation="0" outlined>
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
                        <v-img @click=" addUploadedImage(image)" style="cursor: pointer;" :src="image"
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

        <v-main class="d-flex align-center justify-center" style="min-height: 100vh; max-height: 100%; " :class="{ 'light-theme': true }">
            <Alert />
            <div class="my-3" id="container"></div>
        </v-main>
    </v-app>
</template>
<script>
import allFunctions from '@/Utils/allFunctions.js';
import { rectConfig } from '../Utils/shapesConfig.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import { Inertia } from '@inertiajs/inertia';
import Alert from "@/Components/Alert.vue";
import axios from "axios";
export default {
    mixins: [allFunctions],
    components: { Head, VNumberInput, Link, useForm, Inertia, Alert },
    data() {
        return {
            myDesign: {data: []},
            currentPage: 1,
            totalPages: null,

            templates: {data: []},
            templateCurrentPage: 1,
            templateTotalPages: null,

            texts: {data: []},
            textCurrentPage: 1,
            textTotalPages: null,

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

            logoutForm: useForm({}),
            printForm: useForm({
                quantity: null,
                category_id: null,
                jsonData: null,
                image: null,
            }),
            addTemplateForm: useForm({
                name: null,
                type: null,
                category_id: null,
                image: null,
                jsonData: null,
            }),
            myDesignForm: useForm({
                name: null,
                type: "myDesigns",
                category_id: null,
                image: null,
                jsonData: null,
            }),
            deleteTemplateForm: useForm({
                type: null
            }),
            editTemplateForm: useForm({
                type: null,
                image: null,
                jsonData: null
            }),
            addImageForm: useForm({
                images: [],
                type: ''
            }),
            deleteImageForm: useForm({}),
            deleteAdminImages: useForm({ image: null }),
            imageType: '', // to determine to which path save the images { user or admin }
        }
    },
    async mounted() {
        this.fetchUnsplashImages();
        this.initializeKonva();
        await this.fetchFonts();
        this.check();

        await this.fetchInitialDesigns();
    },
    methods: {
        async fetchInitialDesigns() {
            // get my designs
            axios.get(`/get/design`).then(res => {
                this.myDesign.data = res.data.data
                this.totalPages = res.data.last_page
            })
            // get templates
            axios.get(`/template`).then(res => {
                this.templates.data = res.data.data
                this.templateTotalPages = res.data.last_page
            })
            // get texts
            axios.get(`/texts`).then(res => {
                this.texts.data = res.data.data
                this.textTotalPages = res.data.last_page
            })
        },
        async myDesignLoadMore() {
            if (this.totalPages && this.currentPage >= this.totalPages) {
                return;
            }

            try {
                this.currentPage += 1;

                const response = await axios.get(`/get/design?page=${this.currentPage}`);
                const newDesigns = response.data.data;
                const totalPages = response.data.last_page;

                // Update the designs and totalPages
                this.myDesign.data = [...this.myDesign.data, ...newDesigns];
                this.totalPages = totalPages;
            } catch (error) {
                console.error('Error loading more designs:', error);
            }
        },
        async templateLoadMore() {
            if (this.templateTotalPages && this.templateCurrentPage >= this.templateTotalPages) {
                return;
            }

            try {
                this.templateCurrentPage += 1;

                const response = await axios.get(`/template?page=${this.templateCurrentPage}`);
                const newDesigns = response.data.data;
                const totalPages = response.data.last_page;

                // Update the designs and totalPages
                this.templates.data = [...this.templates.data, ...newDesigns];
                this.templateTotalPages = totalPages;
            } catch (error) {
                console.error('Error loading more designs:', error);
            }
        },
        async textLoadMore() {
            if (this.textTotalPages && this.textCurrentPage >= this.textTotalPages) {
                return;
            }

            try {
                this.textCurrentPage += 1;

                const response = await axios.get(`/texts?page=${this.textCurrentPage}`);
                const newDesigns = response.data.data;
                const totalPages = response.data.last_page;

                // Update the designs and totalPages
                this.texts.data = [...this.texts.data, ...newDesigns];
                this.textTotalPages = totalPages;
            } catch (error) {
                console.error('Error loading more designs:', error);
            }
        },
        check(){
            if(this.isAdmin){
                this.imageType = 'admin'
            }else{
                this.imageType = 'user'
            }
        },
        selectOption(type) {
            switch (type) {
                case 'myDesigns':
                    this.selectedOption.myDesigns = true;
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'myDesigns';
                    break;
                case 'templates':
                    this.selectedOption.myDesigns = false;
                    this.selectedOption.templates = true;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'templates';
                    break;
                case 'text':
                    this.selectedOption.myDesigns = false;
                    this.selectedOption.templates = false;
                    this.selectedOption.text = true;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'text';
                    break;
                case 'photos':
                    this.selectedOption.myDesigns = false;
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = true;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'photos';
                    break;
                case 'elements':
                    this.selectedOption.myDesigns = false;
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = true;
                    this.selectedOption.upload = false;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'elements';
                    break;
                case 'upload':
                    this.selectedOption.myDesigns = false;
                    this.selectedOption.templates = false;
                    this.selectedOption.text = false;
                    this.selectedOption.photos = false;
                    this.selectedOption.elements = false;
                    this.selectedOption.upload = true;
                    this.selectedOption.layers = false;
                    this.selectedOption.active = 'upload';
                    break;
                case 'layers':
                    this.selectedOption.myDesigns = false;
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
        },
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
        errors: { type: Object },
        shapeTemplates: {
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
        },
        isAdmin: {
            type: Boolean,
            required: true
        },
        user: {
            type: Object,
            required: true
        },
        user_images: {
            type: Object,
            required: true
        },
        items: {
            type: Number
        }
    }
}
</script>

<style scoped>
.light-theme {
    background-color: #bebebe;
}

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

.custom-list-item {
    padding-bottom: 15px;
    padding-top: 15px;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.icon {
    font-size: 36px;
}
.input-with-button {
    display: flex;
    align-items: center;
}
.categoryItem .icon {
    cursor: pointer;
}

.pagination {
    display: inline-flex;
    padding: 0;
    margin: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pagination a {
    color: black;
    padding: 12px 18px;
    text-decoration: none;
    background-color: white;
    border: 1px solid #ddd;
    margin-left: -1px;
}

.pagination a:hover {
    background-color: #f0f0f0;
}

.pagination a.active {
    background-color: #4a90e2;
    color: white;
    border-color: #4a90e2;
}

.pagination-arrow {
    font-weight: bold;
}

/* First and last items */
.pagination a:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.pagination a:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}
</style>
