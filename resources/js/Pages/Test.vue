<template>

    <Head title="Test"></Head>
    <TestLayout v-if="isLayoutReady" :actions="actions" :layers="layers" :objectSelected="objectSelected"
        :fonts="fonts">
        <div class="my-3" id="container"></div>
        <!-- {{ fonts }} -->
    </TestLayout>
</template>

<script>
import TestLayout from '@/Layouts/TestLayout.vue';
import { Head } from '@inertiajs/vue3';
import Konva from "konva";
import { nextTick } from 'vue';
import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';
// Import the snapping functions
import {
    getLineGuideStops,
    getObjectSnappingEdges,
    getGuides,
    drawGuides,
} from '@/Utils/snapping.js';

const width = 500;
const height = 500;
const ZOOM_STEP = 0.1;
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 2;
export default {
    components: {
        TestLayout,
        Head,
    },
    props: {
        fonts: Array
    },
    data() {
        return {
            actions: {
                addShape: this.addShape,
                saveAsJson: this.saveAsJson,
                zoomFunction: this.zoomFunction,
                exportImage: this.exportImage,
                destroyObjects: this.destroyObjects,
                duplicateObjects: this.duplicateObjects,
                objectOpacity: this.objectOpacity,
                unDo: this.unDo,
                reDo: this.reDo,
                //positions
                alignLeft: this.alignLeft,
                alignRight: this.alignRight,
                alignTop: this.alignTop,
                alignBottom: this.alignBottom,
                alignCenter: this.alignCenter,
                alignMiddle: this.alignMiddle,
                //layers
                hideLayer: this.hideLayer,
                deleteLayer: this.deleteLayer,
                moveLayer: this.moveLayer,
                //background
                //setImageBackground: this.setImageBackground,
                //text
                addText: this.addText,
                //decoration
                fillColor: this.fillColor,
                textSize: this.textSize,
                textStyle: this.textStyle,
                textCase: this.textCase,
                textLineHight: this.textLineHight,
                textCharSpacing: this.textCharSpacing,
                alignText: this.alignText,
                changeFontFamily: this.changeFontFamily,
                //templates
                saveAsTemplate: this.saveAsTemplate,
            },
            isLayoutReady: false,
            transformer: null,
            selectedObjectIds: [],
            layers: [],
            objectSelected: [],
        };
    },
    mounted() {
        this.isLayoutReady = true;
        nextTick(() => {
            this.initializeKonva();
        });
        this.loadFonts();
    },
    methods: {
        initializeKonva() {
            const container = document.getElementById('container');
            if (container) {
                this.stage = new Konva.Stage({
                    container: 'container',
                    width: width,
                    height: height,
                });

                this.defaultLayer = new Konva.Layer();
                this.layers.push({ 'id': 0, 'name': 'Defualt Layer' });
                this.stage.add(this.defaultLayer);

                // Create a transformer
                this.transformer = new Konva.Transformer();
                this.defaultLayer.add(this.transformer);

                this.defaultLayer.on("dragmove", this.handleDragMove);
                this.defaultLayer.on("dragend", this.handleDragEnd);

                // Click outside of shapes to remove the transformer
                this.stage.on('click', (e) => {
                    if (e.target === this.stage) {
                        this.clearSelection();
                    }
                });
                this.defaultLayer.draw();

                ///////
                this.testTextTemplate();
            } else {
                // Retry initializing after a short delay if the container is not found
                setTimeout(this.initializeKonva, 100);
            }
        },
        // snap objects
        handleDragMove(e) {
            this.stage.find('.guid-line').forEach((l) => l.destroy());

            const lineGuideStops = getLineGuideStops(e.target, this.stage);
            const itemBounds = getObjectSnappingEdges(e.target);
            const guides = getGuides(lineGuideStops, itemBounds);

            if (!guides.length) {
                return;
            }

            drawGuides(guides, this.defaultLayer);

            let absPos = e.target.absolutePosition();
            guides.forEach((lg) => {
                switch (lg.orientation) {
                    case 'V':
                        absPos.x = lg.lineGuide + lg.offset;
                        break;
                    case 'H':
                        absPos.y = lg.lineGuide + lg.offset;
                        break;
                }
            });
            e.target.absolutePosition(absPos);
        },
        handleDragEnd(e) {
            this.stage.find('.guid-line').forEach((l) => l.destroy());
        },
        testTextTemplate() {
            //const template = { "attrs": { "width": 500, "height": 500 }, "className": "Stage", "children": [{ "attrs": {}, "className": "Layer", "children": [{ "attrs": {}, "className": "Transformer" }] }, { "attrs": { "id": "050d8d54-ea31-40f9-b65a-3b96ec15ae63" }, "className": "Layer", "children": [{ "attrs": { "x": 173, "y": 205, "text": "Heading", "fontSize": 45, "fontFamily": "cairo", "fill": "black", "id": "2c5914b9-a0e3-42c2-930b-2fc89bd4d9f6", "draggable": true }, "className": "Text" }] }, { "attrs": { "id": "cad3d5d7-9c77-4370-b242-c798e124e153" }, "className": "Layer", "children": [{ "attrs": { "x": 181, "y": 245, "text": "Subheading", "fontSize": 28, "fontFamily": "cairo", "fill": "black", "id": "3044c0f2-03be-444c-af41-f8d002b6aa59", "draggable": true }, "className": "Text" }] }] };
            const template = {"attrs":{"width":500,"height":500},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"x":23,"y":54},"className":"Transformer"}]},{"attrs":{"id":"e0bcd1b2-ecce-4fde-92e7-0fec9bf14b9d"},"className":"Layer","children":[]},{"attrs":{"id":"47764d29-f4eb-42c5-a740-679b933915bc"},"className":"Layer","children":[{"attrs":{"x":16,"y":41,"text":"Subheading","fontSize":28,"fontFamily":"cairo","fill":"black","id":"4ee15bd6-bd4c-4d9a-8270-dbfea772d720","draggable":true},"className":"Text"}]},{"attrs":{"id":"5a887a75-3b17-4d88-bc30-f9da637013d1"},"className":"Layer","children":[{"attrs":{"type":"Rect","x":248,"y":211,"id":"91af62a5-d681-42b3-b6e8-db99ce46ddc3","width":100,"height":100,"fill":"rgb(179 177 177)","draggable":true,"name":"object"},"className":"Rect"}]},{"attrs":{"id":"03c7b349-9684-454d-80e2-684aa5145e0a"},"className":"Layer","children":[{"attrs":{"type":"Circle","x":427,"y":203,"radius":50,"id":"4e738924-4107-490f-adc7-41c4ee3ae470","fill":"rgb(179 177 177)","draggable":true,"name":"object"},"className":"Circle"}]},{"attrs":{"id":"60aa6c7e-6508-46da-9133-dddf29d46ffc"},"className":"Layer","children":[{"attrs":{"type":"RegularPolygon","x":82,"y":217,"radius":50,"id":"922c3e1b-1c4b-41c5-8374-bf3cc2b02aa3","sides":3,"fill":"rgb(179 177 177)","draggable":true,"name":"object"},"className":"RegularPolygon"}]},{"attrs":{"id":"f38e52f9-8394-4cff-9659-985c566fefe4"},"className":"Layer","children":[{"attrs":{"type":"RegularPolygon","x":322,"y":126,"radius":50,"id":"4e707ad9-6e1a-478a-bb04-f8d2937b389d","sides":6,"fill":"rgb(179 177 177)","draggable":true,"name":"object"},"className":"RegularPolygon"}]},{"attrs":{"id":"453e1ae1-4829-4e4c-9722-e648e632363d"},"className":"Layer","children":[{"attrs":{"type":"RegularPolygon","x":208,"y":144,"id":"e011bc48-f6c3-4ed7-90c8-d33aa47d6ff2","sides":8,"radius":50,"fill":"rgb(179 177 177)","draggable":true,"name":"object"},"className":"RegularPolygon"}]}]}
            template.children.forEach(child => {
                if (child.className === 'Layer') {
                    const newLayer = new Konva.Layer();
                    newLayer.id(uuidv4());
                    this.stage.add(newLayer);

                    child.children.forEach(grandChild => {
                        // with this condition i create aditional layer with no children
                        if (grandChild.className !== 'Transformer') {
                            const objectConstructor = Konva[grandChild.className];
                            const object = new objectConstructor(grandChild.attrs);
                            object.id(uuidv4());

                            const currentLength = this.layers.length;
                            if (currentLength > 1) {
                                this.layers[currentLength - 1].lastOne = false;
                            }
                            this.layers.push({
                                'id': newLayer.id(),
                                'name': object.getClassName(),
                                'layer': newLayer,
                                'visible': true,
                                'firstOne': this.layers.length === 1,
                                'lastOne': true
                            })

                            object.on("click", (e) => {
                                e.cancelBubble = true;
                                this.toggleSelection(object.id(), object.getClassName());
                            });

                            newLayer.add(object);
                        }
                    });
                    newLayer.batchDraw();
                }
            });
        },
        addShape(config) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            config.id = uuidv4()

            const ShapeConstructor = Konva[config.type];
            const shape = new ShapeConstructor(config);

            const currentLength = this.layers.length;
            if (currentLength > 1) {
                this.layers[currentLength - 1].lastOne = false;
            }
            this.layers.push({
                'id': newLayer.id(),
                'name': shape.getClassName(),
                'layer': newLayer,
                'visible': true,
                'firstOne': this.layers.length === 1,
                'lastOne': true
            })

            shape.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(shape.id(), shape.getClassName());
            });

            newLayer.add(shape);
            newLayer.batchDraw();
        },
        //Transformer and Selection
        toggleSelection(id, type) {
            const index = this.selectedObjectIds.indexOf(id);
            const index2 = this.objectSelected.indexOf(id);
            if (index >= 0) {
                this.selectedObjectIds.splice(index, 1); // Deselect if already selected
                this.objectSelected.splice(index2, 1);
            } else {
                this.selectedObjectIds.push(id); // Select if not already selected
                this.objectSelected.push({ objectId: id, objectType: type });
            }
            this.updateTransformer();
        },
        clearSelection() {
            this.selectedObjectIds = [];
            this.objectSelected = [];
            this.updateTransformer();
        },
        updateTransformer() {
            const selectedShapes = this.selectedObjectIds.map((id) =>
                this.stage.findOne(`#${id}`)
            );
            this.transformer.nodes(selectedShapes);
            this.defaultLayer.batchDraw(); // Ensure the layer is redrawn
        },
        //Default Functionality
        download(uri, name) {
            let link = document.createElement('a');
            link.download = name;
            link.href = uri;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        saveAsJson() {
            const blob = new Blob([this.stage.toJSON()], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            this.download(url, 'stage.json');
            URL.revokeObjectURL(url);
        },
        zoomFunction(inOrOut) {
            const oldScale = this.stage.scaleX();
            let newScale;
            if (inOrOut === 'in') {
                newScale = oldScale + ZOOM_STEP;
            } else if (inOrOut === 'out') {
                newScale = oldScale - ZOOM_STEP;
            }

            newScale = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, newScale));

            this.stage.scale({ x: newScale, y: newScale });
            this.stage.width(width * newScale);
            this.stage.height(height * newScale);

            this.stage.batchDraw();
        },
        exportImage() {
            let dataURL = this.stage.toDataURL({ pixelRatio: 3 });
            this.download(dataURL, 'Design.png');
        },
        destroyObjects() {
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    shape
                    const layer = shape.getLayer();
                    this.deleteLayer(layer.id());
                    shape.destroy();
                }
            });
            this.clearSelection();
            this.defaultLayer.batchDraw();
        },
        duplicateObjects() {
            const newSelectedIds = [];
            this.selectedObjectIds.forEach((id) => {
                const newLayer = new Konva.Layer();
                newLayer.id(uuidv4());
                this.stage.add(newLayer);

                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    const clonedShape = shape.clone({
                        x: shape.x() + 20,
                        y: shape.y() + 20,
                        id: uuidv4(),
                    });
                    newLayer.add(clonedShape);
                    const currentLength = this.layers.length;
                    if (currentLength > 1) {
                        this.layers[currentLength - 1].lastOne = false;
                    }
                    this.layers.push({
                        'id': newLayer.id(),
                        'name': clonedShape.getClassName(),
                        'layer': newLayer,
                        'visible': true,
                        'firstOne': this.layers.length === 1,
                        'lastOne': true
                    });
                    newSelectedIds.push(clonedShape.id());

                    clonedShape.on("click", (e) => {
                        e.cancelBubble = true;
                        this.toggleSelection(clonedShape.id(), clonedShape.getClassName());
                    });

                }
                newLayer.batchDraw();
                this.selectedObjectIds = newSelectedIds;
            });
        },
        objectOpacity(opacity) {
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    shape.opacity(opacity);
                }
            });
            this.defaultLayer.batchDraw();
        },
        unDo() {

        },
        reDo() {

        },
        //positions
        alignLeft() {
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape.getClassName() === 'Rect') {
                    shape.x(0);
                } else {
                    shape.x(50);
                }
            } else {
                const leftMostX = Math.min(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.x() : Infinity;
                }));
            }

        },
        alignRight() {
            if (this.selectedObjectIds.length === 0) return;

            const stageWidth = this.stage.width();

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape) {
                    const shapeType = shape.getClassName();
                    if (shapeType === 'Rect') {
                        shape.x(stageWidth - shape.width() * shape.scaleX() - 1);
                    } else if (shapeType === 'Circle' || shapeType === 'Ellipse' || shapeType === 'RegularPolygon') {
                        shape.x(stageWidth - shape.radius() * shape.scaleX() - 1);
                    } else {
                        shape.x(stageWidth - 50); // Default alignment for other shapes
                    }
                }
            } else {
                const rightMostX = Math.max(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.x() + (shape.getClassName() === 'Rect' ? shape.width() * shape.scaleX() : shape.radius() * shape.scaleX()) : -Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        const shapeType = shape.getClassName();
                        if (shapeType === 'Rect') {
                            shape.x(rightMostX - shape.width() * shape.scaleX());
                        } else if (shapeType === 'Circle' || shapeType === 'Ellipse' || shapeType === 'RegularPolygon') {
                            shape.x(rightMostX - shape.radius() * shape.scaleX());
                        } else {
                            shape.x(rightMostX - 50); // Default alignment for other shapes
                        }
                    }
                });
            }

            this.defaultLayer.batchDraw();
        },
        alignTop() {
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape.getClassName() === 'Rect') {
                    shape.y(0);
                } else {
                    shape.y(50);
                }
            } else {
                const topMostY = Math.min(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.y() : Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.y(topMostY);
                    }
                });
            }

            this.defaultLayer.batchDraw();
        },
        alignBottom() {
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape.getClassName() === 'Rect') {
                    shape.y(this.stage.height() - (shape.height() * shape.scaleY()));
                } else {
                    shape.y(this.stage.height() - (shape.height() * shape.scaleY()) + 50);
                }
            } else {
                const bottomMostY = Math.max(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.y() + shape.height() : -Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.y(bottomMostY - shape.height());
                    }
                });
            }

            this.defaultLayer.batchDraw();
        },
        // Layering //////////////
        hideLayer(action, layerId) {
            const layerObj = this.layers.find(layerObj => layerObj.id === layerId);
            layerObj.layer.visible(action);
            layerObj.visible = action;
            layerObj.layer.batchDraw();
        },
        deleteLayer(layerId) {
            const layerIndex = this.layers.findIndex(layerObj => layerObj.id === layerId);
            const layerObj = this.layers[layerIndex];
            if (layerObj.firstOne) {
                if (!layerObj.lastOne) {
                    this.layers[layerIndex + 1].firstOne = true;
                }
            }
            if (layerObj.lastOne) {
                if (!layerObj.firstOne) {
                    this.layers[layerIndex - 1].lastOne = true;
                }
            }
            layerObj.layer.destroy();
            this.layers.splice(layerIndex, 1);
        },
        moveLayer(action, layerId) {
            const layerIndex = this.layers.findIndex(layerObj => layerObj.id === layerId);
            const layerObj = this.layers[layerIndex];
            if (action === 'up') {
                const nextObj = this.layers[layerIndex + 1];
                if (nextObj.lastOne) {
                    //change
                    layerObj.lastOne = true;
                    nextObj.lastOne = false;
                }
                if (layerObj.firstOne) {
                    nextObj.firstOne = true;
                    layerObj.firstOne = false;
                }
                const konvaLayer = this.stage.findOne(`#${layerId}`);
                konvaLayer.moveUp();
                //swap
                let temp = this.layers[layerIndex];
                this.layers[layerIndex] = this.layers[layerIndex + 1];
                this.layers[layerIndex + 1] = temp;
            } else {
                const nextObj = this.layers[layerIndex - 1];
                if (nextObj.firstOne) {
                    //change
                    layerObj.firstOne = true;
                    nextObj.firstOne = false;
                }
                if (layerObj.lastOne) {
                    nextObj.lastOne = true;
                    layerObj.lastOne = false;
                }
                const konvaLayer = this.stage.findOne(`#${layerId}`);
                konvaLayer.moveDown();
                //swap
                let temp = this.layers[layerIndex];
                this.layers[layerIndex] = this.layers[layerIndex - 1];
                this.layers[layerIndex - 1] = temp;
            }

            this.stage.batchDraw();
        },
        //image background
        /*setImageBackground(url){
            console.log(this.layers)
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);
            const imageObj = new Image();

            const scale = this.stage.scale();

            imageObj.onload = () => {
                const backgroundImage = new Konva.Image({
                    image: imageObj,
                    width: width*scale.x,
                    height: height*scale.y,
                });
                newLayer.add(backgroundImage);
                newLayer.batchDraw(); 
            };
            imageObj.src = url;
            // checkk if ther is already background 
            if( this.layers.length >= 2 && this.layers[1].name === 'Background'){
                this.deleteLayer(this.layers[1].id);
            }
            // check if it have other layers 
            if(this.layers.length >= 2){
                this.layers[1].firstOne = false;
            }
            // creat new element in layers
            let newElement = ({
                'id': newLayer.id(),
                'name': 'Background',
                'layer': newLayer,
                'visible': true,
                'firstOne': true,
                'lastOne': this.layers.length === 1 ? true : false
            })
            // add the new element to the first 
            this.layers.splice(1, 0, newElement);

            this.defaultLayer = new Konva.Layer();
            this.stage.add(this.defaultLayer);
            // Create a transformer
            this.transformer = new Konva.Transformer();
            this.defaultLayer.add(this.transformer);

            this.defaultLayer.on("dragmove", this.handleDragMove);
            this.defaultLayer.on("dragend", this.handleDragEnd);

            // Click outside of shapes to remove the transformer
            this.stage.on('click', (e) => {
                if (e.target === this.stage) {
                    this.clearSelection();
                }
            });

        },*/
        //text
        addText(config) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            config.id = uuidv4();

            const textConstructor = Konva['Text'];
            const text = new textConstructor(config);

            const currentLength = this.layers.length;
            if (currentLength > 1) {
                this.layers[currentLength - 1].lastOne = false;
            }
            this.layers.push({
                'id': newLayer.id(),
                'name': text.getClassName(),
                'layer': newLayer,
                'visible': true,
                'firstOne': this.layers.length === 1,
                'lastOne': true
            })

            text.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(text.id(), text.getClassName());
            });

            //editable
            this.editText(text);

            newLayer.add(text);
            newLayer.batchDraw();
        },
        //editable text
        editText(text) {
            const transformer = this.transformer;
            const stage = this.stage;

            text.on('transform', () => {
                const scaleX = text.scaleX();
                text.setAttrs({
                    width: text.width() * scaleX,
                    scaleX: 1,
                });
            });

            text.on('dblclick', () => {
                text.hide();
                transformer.hide();

                const textPosition = text.absolutePosition();
                const areaPosition = {
                    x: stage.container().offsetLeft + textPosition.x,
                    y: stage.container().offsetTop + textPosition.y,
                };

                const textarea = document.createElement('textarea');
                document.body.appendChild(textarea);

                Object.assign(textarea.style, {
                    position: 'absolute',
                    top: `${areaPosition.y}px`,
                    left: `${areaPosition.x}px`,
                    width: `${text.width() - text.padding() * 2}px`,
                    height: `${text.height() - text.padding() * 2 + 5}px`,
                    fontSize: `${text.fontSize()}px`,
                    border: 'none',
                    padding: '3px',
                    margin: '0px',
                    overflow: 'hidden',
                    background: 'none',
                    outline: 'none',
                    resize: 'none',
                    lineHeight: text.lineHeight(),
                    fontFamily: text.fontFamily(),
                    transformOrigin: 'left top',
                    textAlign: text.align(),
                    color: text.fill(),
                    transform: `rotateZ(${text.rotation()}deg) translateY(-0px)`,
                });

                textarea.value = text.text();
                textarea.focus();

                const removeTextarea = () => {
                    textarea.parentNode.removeChild(textarea);
                    window.removeEventListener('click', handleOutsideClick);
                    text.show();
                    transformer.show();
                    transformer.forceUpdate();
                };

                const setTextareaWidth = (newWidth) => {
                    if (!newWidth) {
                        newWidth = text.placeholder.length * text.fontSize();
                    }
                    textarea.style.width = `${Math.ceil(newWidth)}px`;
                };

                const handleKeydown = (e) => {
                    if (e.keyCode === 13 && !e.shiftKey) {
                        text.text(textarea.value);
                        removeTextarea();
                    } else if (e.keyCode === 27) {
                        removeTextarea();
                    } else {
                        const scale = text.getAbsoluteScale().x;
                        setTextareaWidth(text.width() * scale);
                        textarea.style.height = 'auto';
                        textarea.style.height = `${textarea.scrollHeight + text.fontSize()}px`;
                    }
                };

                const handleOutsideClick = (e) => {
                    if (e.target !== textarea) {
                        text.text(textarea.value);
                        removeTextarea();
                    }
                };

                textarea.addEventListener('keydown', handleKeydown);
                setTimeout(() => window.addEventListener('click', handleOutsideClick));
            });
        },
        //decoration
        fillColor(color) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.fill(color);
                }
            });
            this.defaultLayer.batchDraw();
        },
        textSize(size) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.fontSize(size);
                }
            });
            this.defaultLayer.batchDraw();
        },
        // for bold & italic
        textStyle(style) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.fontStyle(style);
                }
            });
            this.defaultLayer.batchDraw();
        },
        textCase(fontCase) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    if (fontCase === 'upper') {
                        object.text(object.text().toUpperCase());
                    } else if (fontCase === 'lower') {
                        object.text(object.text().toLowerCase());
                    }
                }
            });
            this.defaultLayer.batchDraw();
        },
        textLineHight(textLineHight) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.lineHeight(textLineHight);
                }
            });
            this.defaultLayer.batchDraw();
        },
        textCharSpacing(charSpacing) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.letterSpacing(charSpacing);
                }
            });
            this.defaultLayer.batchDraw();
        },
        alignText(align) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.align(align);
                }
            });
            this.defaultLayer.batchDraw();
        },
        loadFonts() {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/css/fonts.css';
            document.head.appendChild(link);
        },
        changeFontFamily(fontFamily) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.fontFamily(fontFamily.name);
                }
            });
            this.defaultLayer.batchDraw();
        },
        async saveAsTemplate(type) {
            // console.log(type)
            const satageJson = this.stage.toJSON();
            let res = await axios.post('/template/add', {data: satageJson, type: type });
        }
    },
}
</script>

<style>
#container {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}
</style>
