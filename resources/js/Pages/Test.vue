<template>
    <Head title="Test"></Head>
    <TestLayout v-if="isLayoutReady" :actions="actions">
        <div class="my-3" id="container"></div>
<!--        {{ selectedObjectIds }}-->
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
                doFunction: this.doFunction,
                //positions
                alignLeft: this.alignLeft,
                alignRight: this.alignRight,
                alignTop: this.alignTop,
                alignBottom: this.alignBottom,
                alignCenter: this.alignCenter,
                alignMiddle: this.alignMiddle,
            },
            isLayoutReady: false,
            transformer: null,
            selectedObjectIds: [],
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
            this.stage.add(newLayer);

            config.id = uuidv4();

            const ShapeConstructor = Konva[config.type];
            const shape = new ShapeConstructor(config);

            shape.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(shape.id());
            });

            newLayer.add(shape);
            newLayer.batchDraw();
        },
    //Transformer and Selection
        toggleSelection(id) {
            const index = this.selectedObjectIds.indexOf(id);
            if (index >= 0) {
                this.selectedObjectIds.splice(index, 1); // Deselect if already selected
            } else {
                this.selectedObjectIds.push(id); // Select if not already selected
            }
            this.updateTransformer();
        },
        clearSelection() {
            this.selectedObjectIds = [];
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
                if (shape) {
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
                this.stage.add(newLayer);

                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    const clonedShape = shape.clone({
                        x: shape.x() + 20,
                        y: shape.y() + 20,
                        id: uuidv4(),
                    });
                    newLayer.add(clonedShape);
                    newSelectedIds.push(clonedShape.id());

                    clonedShape.on("click", (e) => {
                        e.cancelBubble = true;
                        this.toggleSelection(clonedShape.id());
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
        doFunction(unOrRe){

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

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.x(leftMostX);
                    }
                });
            }

            this.defaultLayer.batchDraw();
        },
        alignRight(){
            if (this.selectedObjectIds.length === 0) return;

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if(shape.getClassName() === 'Rect'){
                    shape.x(this.stage.width() - (shape.width() * shape.scaleX()));
                }else {
                    shape.x(this.stage.width() - (shape.width() * shape.scaleX()) + 50);
                }
            } else {
                const rightMostX = Math.max(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.x() + shape.width() : -Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.x(rightMostX - shape.width());
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

    },
}
</script>

<style>
#container {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}
</style>
