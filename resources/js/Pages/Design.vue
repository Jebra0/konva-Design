<template>
    <Head title="Design" />
    <GuestLayout :zoomIn="zoomIn" :zoomOut="zoomOut" :addShape="addShape">
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
                <input type="color" v-model="selectedFillColor" @input="updateShapeFill" />
        </div>
    </GuestLayout>
</template>

<script>
import GuestLayout from "../Layouts/GuestLayout.vue";
import { Head } from '@inertiajs/vue3';
import { v4 as uuidv4 } from 'uuid';

export default {
    components: {
        GuestLayout,
        Head,
    },
    data() {
        return {
            configKonva: {
                width: 500,
                height: 500
            },
            shapes: [],
            selectedShapeId: null,
            selectedFillColor: 'rgb(179 177 177',
        };
    },
    methods: {
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
        },
        handleTransformEnd(e) {
            // shape is transformed, let us save new attrs back to the node
            const shape = this.findShape(e.target.id());
            if (shape) {
                shape.x = e.target.x();
                shape.y = e.target.y();
                shape.rotation = e.target.rotation();
                shape.scaleX = e.target.scaleX();
                shape.scaleY = e.target.scaleY();
            }
        },
        handleStageMouseDown(e) {
            // clicked on stage - clear selection
            if (e.target === e.target.getStage()) {
                this.selectedShapeId = null;
                this.updateTransformer();
                return;
            }

            // clicked on transformer - do nothing
            const clickedOnTransformer = e.target.getParent().className === 'Transformer';
            if (clickedOnTransformer) {
                return;
            }

            // find clicked shape by its id
            const id = e.target.id();
            if (id) {
                this.selectedShapeId = id;
            } else {
                this.selectedShapeId = null;
            }
            this.updateTransformer();
        },
        updateTransformer() {
            // here we need to manually attach or detach Transformer node
            const transformerNode = this.$refs.transformer.getNode();
            const stage = transformerNode.getStage();
            const { selectedShapeId } = this;

            const selectedNode = stage.findOne('#' + selectedShapeId);
            // do nothing if selected node is already attached
            if (selectedNode === transformerNode.node()) {
                return;
            }

            if (selectedNode) {
                // attach to another node
                transformerNode.nodes([selectedNode]);
            } else {
                // remove transformer
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
        updateShapeFill() {
            // Update the fill color of the selected shape
            if (this.selectedShapeId) {
                const shape = this.findShape(this.selectedShapeId);
                if (shape) {
                    shape.fill = this.selectedFillColor;
                }
            }
        },
    }
};
</script>

<style>
.stage {
    background-color: white;
    border: 1px solid rgba(184, 184, 184, 0.78);
}
</style>
