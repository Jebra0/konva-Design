<template>
    <Head title="Test"></Head>
    <TestLayout 
        v-if="isLayoutReady" 
        :actions="actions" 
        :layers="layers"
        :objectSelected="objectSelected"
    >
        <div class="my-3" id="container"></div>
    </TestLayout>
</template>

<script>
import TestLayout from '@/Layouts/TestLayout.vue';
import { Head } from '@inertiajs/vue3';
import Konva from "konva";
import { nextTick } from 'vue';
import { v4 as uuidv4 } from 'uuid';

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
                addHeader: this.addHeader,
            },
            isLayoutReady: false,
            transformer: null,
            selectedObjectIds: [],
            layers: [],
            objectSelected:[],
        };
    },
    mounted() {
        this.isLayoutReady = true;
        nextTick(() => {
            this.initializeKonva();
        });
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
                this.layers.push({'id': 0, 'name': 'Defualt Layer'});
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
                this.toggleSelection(shape.id(), 'shape');
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
                this.objectSelected.push({objectId: id, objectType: type});
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
        zoomFunction(inOrOut){
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
        exportImage(){
            let dataURL = this.stage.toDataURL({ pixelRatio: 3 });
            this.download(dataURL, 'Design.png');
        },
        destroyObjects(){
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {shape
                    const layer = shape.getLayer();
                    this.deleteLayer(layer.id());
                    shape.destroy();
                }
            });
            this.clearSelection();
            this.defaultLayer.batchDraw();
        },
        duplicateObjects(){
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
                        this.toggleSelection(clonedShape.id(), 'shape');
                    });

                }
                newLayer.batchDraw();
                this.selectedObjectIds = newSelectedIds;
            });
        },
        objectOpacity(opacity){
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    shape.opacity(opacity);
                }
            });
            this.defaultLayer.batchDraw();
        },
        unDo(){
            
        },
        reDo(){
            
        },
        //positions
        alignLeft() {
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if(shape.getClassName() === 'Rect'){
                    shape.x(0);
                }else {
                    shape.x(50);
                }
            } else {
                const leftMostX = Math.min(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.x() : Infinity;
                }));
            }

        },
        alignRight(){
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
                if(shape.getClassName() === 'Rect'){
                    shape.y(0);
                }else {
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
        alignBottom(){
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if(shape.getClassName() === 'Rect'){
                    shape.y(this.stage.height() - (shape.height() * shape.scaleY()));
                }else {
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
        hideLayer(action, layerId){
            const layerObj = this.layers.find(layerObj => layerObj.id === layerId);            
            layerObj.layer.visible(action);
            layerObj.visible = action;
            layerObj.layer.batchDraw();
        },  
        deleteLayer(layerId){
            const layerIndex = this.layers.findIndex(layerObj => layerObj.id === layerId);
            const layerObj = this.layers[layerIndex];
            if (layerObj.firstOne) {
                if(!layerObj.lastOne){
                    this.layers[layerIndex + 1].firstOne = true;
                }
            }
            if (layerObj.lastOne) {
                if(!layerObj.firstOne){
                    this.layers[layerIndex - 1].lastOne = true;
                }
            }
            layerObj.layer.destroy();
            this.layers.splice(layerIndex, 1);
        },
        moveLayer(action, layerId) {
            const layerIndex = this.layers.findIndex(layerObj => layerObj.id === layerId);
            const layerObj = this.layers[layerIndex];
            if(action === 'up'){
                const nextObj = this.layers[layerIndex + 1];
                if(nextObj.lastOne){
                    //change
                    layerObj.lastOne = true;
                    nextObj.lastOne = false;   
                }
                if(layerObj.firstOne){
                        nextObj.firstOne = true;
                        layerObj.firstOne = false;
                }
                const konvaLayer = this.stage.findOne(`#${layerId}`);
                konvaLayer.moveUp();
                //swap
                let temp = this.layers[layerIndex];
                this.layers[layerIndex] = this.layers[layerIndex + 1];
                this.layers[layerIndex + 1] = temp;
            }else{
                const nextObj = this.layers[layerIndex - 1];
                if(nextObj.firstOne){
                    //change
                    layerObj.firstOne = true;
                    nextObj.firstOne = false;
                }
                if(layerObj. lastOne){
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
        addHeader(){
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            var text = new Konva.Text({
                    x: this.stage.width() / 2,
                    y: 15,
                    text: 'Simple Text',
                    fontSize: 40,
                    fontFamily: 'Audiowide',
                    fill: 'black',
                    id: uuidv4(),
                    draggable: true
            });

            const currentLength = this.layers.length;
            if (currentLength > 1) {
                this.layers[currentLength - 1].lastOne = false;
            }
            this.layers.push({
                'id': newLayer.id(),
                'name': 'text',//+ text.text.substring(0, 10)+'..',
                'layer': newLayer,
                'visible': true,
                'firstOne': this.layers.length === 1,
                'lastOne': true
            })
            
            text.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(text.id(), 'text');
            });

            newLayer.add(text);
            newLayer.batchDraw();
        },

    },
}
</script>

<style>
#container {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}
</style>
