<template>
    <Head title="Design" />
    <GuestLayout
        :actions="actions"
    >
        <div class="stage">
            <v-stage
                :config="configKonva"
                ref="stage"
                @mousedown="handleStageMouseDown"
                @touchstart="handleStageMouseDown"
            >
                <v-layer ref="layer">
                    <component
                        v-for="shape in shapes"
                        :is="shape.type"
                        :key="shape.id"
                        :config="shape"
                        @transformend="handleTransformEnd"
                        @click.native="handleShapeClick(shape.id)"
                    ></component>
                    <v-transformer ref="transformer" />
                </v-layer>
            </v-stage>
<!--            <input type="color" v-model="selectedFillColor" @input="updateShapeFill" />-->
        </div>
    </GuestLayout>
</template>

<script>
import GuestLayout from "../Layouts/GuestLayout.vue";
import { Head } from '@inertiajs/vue3';
import { v4 as uuidv4 } from 'uuid';
import {th} from "vuetify/locale";
const width = 500 ;
const height = 500 ;
export default {
    components: {
        GuestLayout,
        Head,
    },
    data() {
        return {
            configKonva: {
                width: width,
                height: height,
            },
            shapes: [],
            selectedShapeId: null,
            selectedFillColor: 'rgb(179, 177, 177)',
            history: [],
            historyStep: -1,
            actions: {
                zoomIn: this.zoomIn,
                zoomOut: this.zoomOut,
                addShape: this.addShape,
                exportShape: this.exportShape,
                saveJson: this.saveJson,
                destroyShape: this.destroyShape,
                duplicateShape: this.duplicateShape,
                shapeOpacity: this.shapeOpacity,
                undoAction: this.undoAction,
                redoAction: this.redoAction,
                AlignLeft: this.AlignLeft,
                AlignRight: this.AlignRight,
                AlignTop: this.AlignTop,
                AlignBottom: this.AlignBottom,
                AlignCenter: this.AlignCenter,
                AlignMiddle: this.AlignMiddle,
                handleImageClick1: this.handleImageClick1,
            },
        };
    },
    methods: {
        handleImageClick1(){console.log('test')},
        zoomIn() {
            let scaleBy = 70;
            let oldScale = this.configKonva.width;
            this.configKonva.width = oldScale + scaleBy;
            this.configKonva.height = oldScale + scaleBy;
        },
        zoomOut() {
            let scaleBy = 70;
            let oldScale = this.configKonva.width;
            this.configKonva.height = oldScale - scaleBy;
            this.configKonva.width = oldScale - scaleBy;
        },
        addShape(shapeConfig) {
            const newShape = { ...shapeConfig, id: uuidv4() };
            this.shapes.push(newShape);
            this.saveHistory();
        },
        handleTransformEnd(e) {
            const shape = this.findShape(e.target.id());
            if (shape) {
                shape.x = e.target.x();
                shape.y = e.target.y();
                shape.rotation = e.target.rotation();
                shape.scaleX = e.target.scaleX();
                shape.scaleY = e.target.scaleY();
                this.saveHistory();
            }
        },
        handleStageMouseDown(e) {
            if (e.target === e.target.getStage()) {
                this.selectedShapeId = null;
                this.updateTransformer();
                return;
            }
            const clickedOnTransformer = e.target.getParent().className === 'Transformer';
            if (clickedOnTransformer) {
                return;
            }
            const id = e.target.id();
            if (id) {
                this.selectedShapeId = id;
            } else {
                this.selectedShapeId = null;
            }
            this.updateTransformer();
        },
        updateTransformer() {
            const transformerNode = this.$refs.transformer.getNode();
            const stage = transformerNode.getStage();
            const { selectedShapeId } = this;
            const selectedNode = stage.findOne('#' + selectedShapeId);
            if (selectedNode === transformerNode.node()) {
                return;
            }
            if (selectedNode) {
                transformerNode.nodes([selectedNode]);
            } else {
                transformerNode.nodes([]);
            }
        },
        findShape(id) {
            return this.shapes.find(shape => shape.id === id);
        },
        handleShapeClick(id) {
            this.selectedShapeId = id;
            this.updateTransformer();
        },
////////////////////////////////////////////
///////////// Default Functionality ////////
        updateShapeFill() {
            if (this.selectedShapeId) {
                const shape = this.findShape(this.selectedShapeId);
                if (shape) {
                    shape.fill = this.selectedFillColor;
                    this.saveHistory();
                }
            }
        },
        saveHistory() {
            const shapesCopy = JSON.parse(JSON.stringify(this.shapes));
            if (this.historyStep < this.history.length - 1) {
                this.history = this.history.slice(0, this.historyStep + 1);
            }
            this.history.push(shapesCopy);
            this.historyStep++;
        },
        undoAction() {
            if (this.historyStep > 0) {
                this.historyStep--;
                this.shapes = JSON.parse(JSON.stringify(this.history[this.historyStep]));
            }
        },
        redoAction() {
            if (this.historyStep < this.history.length - 1) {
                this.historyStep++;
                this.shapes = JSON.parse(JSON.stringify(this.history[this.historyStep]));
            }
        },
        destroyShape() {
            if (this.selectedShapeId) {
                const layer = this.$refs.layer.getNode();
                const shape = layer.findOne(`#${this.selectedShapeId}`);
                if (shape) {
                    shape.destroy();
                    layer.batchDraw();
                    this.shapes = this.shapes.filter(shape => shape.id !== this.selectedShapeId);
                    this.selectedShapeId = null;
                    this.saveHistory();
                }
            }
        },
        duplicateShape() {
            if (this.selectedShapeId) {
                const shape = this.shapes.find(shape => shape.id === this.selectedShapeId);
                if (shape) {
                    const newShape = { ...shape, id: `shape-${Date.now()}`, x: shape.x + 10, y: shape.y + 10 };
                    this.shapes.push(newShape);
                    this.$refs.layer.getNode().batchDraw();
                    this.saveHistory();
                }
            }
        },
        shapeOpacity(opacity) {
            const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
            if (shapeIndex !== -1) {
                this.shapes[shapeIndex].opacity = opacity;
                const layer = this.$refs.layer.getNode();
                const shape = layer.findOne(`#${this.selectedShapeId}`);
                if (shape) {
                    shape.opacity(opacity);
                    layer.batchDraw();
                    this.saveHistory();
                }
            }
        },
        download(uri, name) {
            let link = document.createElement('a');
            link.download = name;
            link.href = uri;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        exportShape() {
            let stage = this.$refs.stage.getNode();
            let dataURL = stage.toDataURL({ pixelRatio: 3 });
            this.download(dataURL, 'Design.png');
        },
        saveJson() {
            const stage = this.$refs.stage.getNode();
            const json = stage.toJSON();
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            this.download(url, 'stage.json');
            URL.revokeObjectURL(url);
        },
        //position functions
        AlignLeft() {
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                    if (shape.type === 'v-rect') {
                        shape.x = 1;
                    } else {
                        shape.x = 50;
                    }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        konvaShape.x(50);
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
        AlignRight(){
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                    if (shape.type === 'v-rect') {
                        shape.x = width - 101;
                    } else {
                        shape.x = width - 50;
                    }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        if (shape.type === 'v-rect') {
                            konvaShape.x(width - 101);
                        }else {
                            konvaShape.x(width - 50);
                        }
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
        AlignTop(){
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                        if (shape.type === 'v-rect') {
                            shape.y = 1;
                        } else {
                            shape.y = 50;
                        }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        if (shape.type === 'v-rect') {
                            konvaShape.y(1);
                        }else {
                            konvaShape.y(50);
                        }
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
        AlignBottom(){
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                    if (shape.type === 'v-rect') {
                        shape.y = height - 101;
                    } else {
                        shape.y = height - 50;
                    }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        if (shape.type === 'v-rect') {
                            konvaShape.y(height - 101);
                        }else {
                            konvaShape.y(height - 50);
                        }
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
        AlignCenter(){
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                    if (shape.type === 'v-rect') {
                        shape.x = 200;
                    } else {
                        shape.x = 250;
                    }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        if (shape.type === 'v-rect') {
                            konvaShape.x(150);
                        }else {
                            konvaShape.x(250);
                        }
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
        AlignMiddle(){
            if (this.selectedShapeId) {
                const shapeIndex = this.shapes.findIndex(shape => shape.id === this.selectedShapeId);
                if (shapeIndex !== -1) {
                    const shape = this.shapes[shapeIndex];
                    if (shape.type === 'v-rect') {
                        shape.y = 200;
                    } else {
                        shape.y = 250;
                    }

                    const konvaShape = this.$refs.layer.getNode().findOne(`#${this.selectedShapeId}`);
                    if (konvaShape) {
                        if (shape.type === 'v-rect') {
                            konvaShape.y(150);
                        }else {
                            konvaShape.y(250);
                        }
                    }

                    this.$refs.layer.getNode().draw();
                    this.saveHistory();
                }
            }
        },
//////////////////////////////////////////////
    },
};
</script>

<style>
.stage {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}
</style>
