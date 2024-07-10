<template>
    <Head title="Design" />
    <GuestLayout :zoomIn="zoomIn" :zoomOut="zoomOut">
        <div class="d-inline">
            <div class="stage">
                <v-stage
                    :config="configKonva"
                    ref="stage"
                    @mousedown="handleStageMouseDown"
                    @touchstart="handleStageMouseDown"
                >
                    <v-layer ref="layer">
                        <v-text
                            :config="textConfig"
                            @transformend="handleTransformEnd"
                        ></v-text>
                        <v-rect
                            :config="rectConfig"
                            @transformend="handleTransformEnd"
                        ></v-rect>
                        <v-circle
                            :config="circleConfig"
                            @transformend="handleTransformEnd"
                        ></v-circle>
                        <v-transformer ref="transformer" />
                    </v-layer>
                </v-stage>
            </div>
        </div>
    </GuestLayout>
</template>

<script>
import GuestLayout from "../Layouts/GuestLayout.vue";
import { Head } from '@inertiajs/vue3';

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
            textConfig: {
                rotation: 0,
                text: 'Some text here',
                x: 50,
                y: 80,
                fontSize: 20,
                draggable: true,
                width: 100,
                height: 100,
                scaleX: 1,
                scaleY: 1,
                name: 'text',
                fill: 'green'
            },
            rectConfig: {
                rotation: 0,
                x: 10,
                y: 10,
                width: 100,
                height: 100,
                scaleX: 1,
                scaleY: 1,
                fill: 'red',
                name: 'rect',
                draggable: true,
            },
            circleConfig: {
                rotation: 0,
                x: 10,
                y: 10,
                width: 100,
                height: 100,
                scaleX: 1,
                scaleY: 1,
                fill: 'red',
                name: 'circle',
                draggable: true,
            },
            selectedShapeName: '',
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
        handleTransformEnd(e) {
            // shape is transformed, let us save new attrs back to the node
            // find element in our state
            const shape = this.findShape(e.target.name());
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
                this.selectedShapeName = '';
                this.updateTransformer();
                return;
            }

            // clicked on transformer - do nothing
            const clickedOnTransformer = e.target.getParent().className === 'Transformer';
            if (clickedOnTransformer) {
                return;
            }

            // find clicked shape by its name
            const name = e.target.name();
            if (name) {
                this.selectedShapeName = name;
            } else {
                this.selectedShapeName = '';
            }
            this.updateTransformer();
        },
        updateTransformer() {
            // here we need to manually attach or detach Transformer node
            const transformerNode = this.$refs.transformer.getNode();
            const stage = transformerNode.getStage();
            const { selectedShapeName } = this;

            const selectedNode = stage.findOne('.' + selectedShapeName);
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
        findShape(name) {
            if (this.textConfig.name === name) {
                return this.textConfig;
            } else if (this.rectConfig.name === name) {
                return this.rectConfig;
            }
            return null;
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
