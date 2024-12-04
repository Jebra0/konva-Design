import Konva from "konva";
import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';
import jsPDF from 'jspdf';


// Import the snapping functions
import {
    getLineGuideStops,
    getObjectSnappingEdges,
    getGuides,
    drawGuides,
} from '@/Utils/snapping.js';
import { bodyText, headerText, subHeaderText } from "@/Utils/textConfig.js";

const width = 700;
const height = 500;
const ZOOM_STEP = 0.1;
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 2;

var undoStack = [];
var redoStack = [];

var redoPositionStack = [];
var undoPositionStack = [];

const allFunctions = {
    data() {
        return {
            transformer: null,
            selectedObjectIds: [],
            layers: [],
            objectSelected: [],

            stage: null,
            /////// test ///////////////
            clippingTool: null,
            clippingRect: null,
            clippingTransformer: null,
            /////// //// ///////////////
            selectedFillColor: null,

            undoDisable: true,
            redoDisable: true,
            historyAction: '',

            isDataRedy: false,
            isTemplatesRedy: false,

            templateName: null,
            editedType: null,

            editingTemp: false,
            editedId: null,

            allTemplates: null,
            isPrintActive: false,
            acountNavItems: [
                { title: 'Profile', for: 'all', link: 'profile.edit' },
                { title: 'Dashboard', for: 'admin', link: 'admin.index' },
            ],

            designName: '',
            categoryId: null,
            userId: null,
            productQuantity: 1,
        }
    },

    methods: {
        initializeKonva(template = null) {
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
        addTransformer(layer) {
            // Create a transformer
            this.transformer = new Konva.Transformer();
            layer.add(this.transformer);

            layer.on("dragmove", this.handleDragMove);
            layer.on("dragend", this.handleDragEnd);
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
        handleLayersOrder(newLayer, name) {
            const currentLength = this.layers.length;
            if (currentLength > 1) {
                this.layers[currentLength - 1].lastOne = false;
            }
            this.layers.push({
                'id': newLayer.id(),
                'name': name,
                'layer': newLayer,
                'visible': true,
                'firstOne': this.layers.length === 1,
                'lastOne': true
            })

        },
        //Transformer and Selection
        toggleSelection(id, type, config) {
            const index = this.selectedObjectIds.indexOf(id);
            const index2 = this.objectSelected.indexOf(id);
            if (index >= 0) {
                this.selectedObjectIds.splice(index, 1); // Deselect if already selected
                this.objectSelected.splice(index2, 1);
            } else {
                this.selectedObjectIds.push(id); // Select if not already selected
                this.objectSelected.push({
                    objectId: id,
                    objectType: type,
                    config: config
                });
                if (type === 'Text') {
                    if (this.selectedFont === null) {
                        if (this.objectSelected.length === 1) {
                            this.selectedFont = config.attrs.fontFamily;
                        }
                    }
                }
                if (this.objectSelected.length === 1 && this.selectedFillColor === null) {
                    this.selectedFillColor = config.attrs.fill;
                }
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
            ).filter(Boolean); // Ensure no undefined objects are included
            this.transformer.nodes(selectedShapes);
            this.defaultLayer.batchDraw(); // Ensure the layer is redrawn
        },
        async getTemplate(template) {
            if (Array.isArray(template.children)) {
                template.children.forEach(child => {
                    if (child.className === 'Layer') {
                        const newLayer = new Konva.Layer();
                        newLayer.id(uuidv4());
                        this.stage.add(newLayer);
                        // transformer
                        this.addTransformer(newLayer);

                        let hasNonTransformer = false;

                        child.children.forEach(async grandChild => {
                            if (grandChild.className !== 'Transformer') {
                                hasNonTransformer = true;
                                const objectConstructor = Konva[grandChild.className];
                                const object = new objectConstructor(grandChild.attrs);
                                object.id(uuidv4());

                                if (object.getClassName() === 'Image') {
                                    const imageUrl = grandChild.attrs.src; // Get the src from JSON
                                    if (imageUrl) {
                                        const imageObj = new Image();
                                        imageObj.src = imageUrl;
                                        imageObj.crossOrigin = 'anonymous';

                                        // Load the image and set it to the Konva image object
                                        await new Promise((resolve) => {
                                            imageObj.onload = () => {
                                                object.image(imageObj);
                                                resolve();
                                            };
                                        });
                                    }
                                }
                                if (object.getClassName() === 'Text') {
                                    this.editText(object);
                                }
                                // layers order
                                this.handleLayersOrder(newLayer, object.getClassName());

                                object.on("pointerdown", (e) => {
                                    e.cancelBubble = true;
                                    this.toggleSelection(object.id(), object.getClassName(), object);
                                });

                                if (object.getClassName() === 'Text') {
                                    const fontLoaded = await this.fetchFont(object.fontFamily());
                                    if (fontLoaded) {
                                        newLayer.add(object);
                                    }
                                }
                                newLayer.add(object);
                            }
                        });

                        // If no non-Transformer objects were added, remove the layer
                        if (!hasNonTransformer) {
                            newLayer.destroy();
                        } else {
                            newLayer.batchDraw();
                        }
                    }
                });
            }
        },
        addShape(config) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            config.id = uuidv4()

            const ShapeConstructor = Konva[config.type];
            const shape = new ShapeConstructor(config);

            this.addTransformer(newLayer);
            // layers order
            this.handleLayersOrder(newLayer, shape.getClassName());

            shape.on("pointerdown", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(shape.id(), shape.getClassName(), shape);
            });

            newLayer.add(shape);
            newLayer.batchDraw();
        },
        //return blop
        saveAsPDF() {
            return new Promise((resolve) => {
                const pdf = new jsPDF('l', 'px', [this.stage.width(), this.stage.height()]);
                pdf.setTextColor('#000000');

                // Add text layers to PDF
                this.stage.find('Text').forEach((text) => {
                    const fontSize = text.fontSize() / 0.75;
                    pdf.setFontSize(fontSize);
                    pdf.text(text.text(), text.x(), text.y(), {
                        baseline: 'top',
                        angle: -text.getAbsoluteRotation(),
                    });
                });

                // Add image layer
                pdf.addImage(
                    this.stage.toDataURL({ pixelRatio: 2 }),
                    0,
                    0,
                    this.stage.width(),
                    this.stage.height()
                );

                // Return the Blob
                resolve(pdf.output('blob'));
            });
        },
        // returning file
        // saveAsPDF() {
        //     // Ensure the stage is rendered before creating the PDF
        //     if (!this.stage) return;

        //     const pdf = new jsPDF('l', 'px', [this.stage.width(), this.stage.height()]);
        //     pdf.setTextColor('#000000');

        //     // Add all text elements individually
        //     this.stage.find('Text').forEach((text) => {
        //         const fontSize = text.fontSize() / 0.75; // convert pixels to points
        //         pdf.setFontSize(fontSize);
        //         pdf.text(text.text(), text.x(), text.y(), {
        //             baseline: 'top',
        //             angle: -text.getAbsoluteRotation(),
        //         });
        //     });

        //     // Add the whole stage as an image on top
        //     pdf.addImage(
        //         this.stage.toDataURL({ pixelRatio: 2 }), // Adjust pixelRatio for quality
        //         0,
        //         0,
        //         this.stage.width(),
        //         this.stage.height()
        //     );

        //     pdf.save('design.pdf');
        // },

        //Default  //TrFunctionality
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
                    const layer = shape.getLayer();

                    layer.getChildren().forEach(child => child.remove());

                    this.deleteLayer(layer.id());
                }
            });

            this.selectedObjectIds = [];
            this.clearSelection();
            this.defaultLayer.batchDraw();
        },
        // findObject(id){
        //     const shape = this.stage.findOne(`#${id}`);
        //     console.log(shape);
        // },
        duplicateObjects() {
            const newSelectedIds = [];
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    const objLayer = shape.getLayer();
                    const newLayer = new Konva.Layer({ id: uuidv4() });
                    objLayer.getChildren().forEach(child => {
                        const clonedChild = child.clone({
                            id: uuidv4()
                        });
                        newLayer.add(clonedChild);
                    });
                    this.stage.add(newLayer);

                    const object = newLayer.children[1];
                    object.x(object.x() + 20)
                    object.y(object.y() + 20)

                    //layers order
                    this.handleLayersOrder(newLayer, object.getClassName());

                    object.on("pointerdown", (e) => {
                        e.cancelBubble = true;
                        this.toggleSelection(object.id(), object.getClassName(), object);
                    });
                    newSelectedIds.push(object.id());
                    newLayer.batchDraw();
                }
            });
            this.selectedObjectIds = newSelectedIds;
            this.updateTransformer();
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
        objectOpacity(opacity) {
            this.selectedObjectIds.forEach((id) => {
                const shape = this.stage.findOne(`#${id}`);
                if (shape) {
                    shape.opacity(opacity);
                }
            });
            this.defaultLayer.batchDraw();
        },
        recreateLayer(layer) {
            // Recreate the layer from the JSON object
            const restoredLayer = Konva.Node.create(layer, this.stage);

            // Handle image loading
            const layerNode = restoredLayer.getChildren()[1];
            if (layerNode && layerNode.className === 'Image' && layerNode.attrs.src) {
                const imageObj = new Image();
                imageObj.src = layerNode.attrs.src;
                imageObj.crossOrigin = 'anonymous';
                imageObj.onload = () => {
                    layerNode.image(imageObj);
                    restoredLayer.batchDraw();
                };
            }

            this.addTransformer(restoredLayer);

            // Handle layers order
            this.handleLayersOrder(restoredLayer, layerNode.getClassName());

            layerNode.on("pointerdown", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(layerNode.id(), layerNode.getClassName(), layerNode);
            });

            if (layerNode.getClassName === 'Text') {
                //editable
                this.editText(text);
            }


            this.stage.add(restoredLayer);
        },
        savePositionState(object) {
            const state = {
                type: 'position',
                x: object.x(),
                y: object.y(),
                id: object.id()
            };
            undoStack.push(state);
        },
        unDo() {
            if (undoStack.length > 0) {
                const lastLayer = undoStack.pop();
                switch (lastLayer.type) {
                    case 'add':
                        redoStack.push(lastLayer);
                        //destroy the layer
                        this.deleteLayer(lastLayer.data.attrs.id);
                        //disable button
                        if (undoStack.length === 0) {
                            this.undoDisable = true;
                        }
                        this.redoDisable = false;
                        this.stage.draw();
                        break;
                    case 'position':
                        const obj = this.stage.findOne(`#${lastLayer.id}`);

                        const state = {
                            type: 'position',
                            x: obj.x(),
                            y: obj.y(),
                            id: obj.id()
                        };

                        redoStack.push(state);

                        // Restore the previous state
                        obj.x(lastLayer.x);
                        obj.y(lastLayer.y);

                        if (undoStack.length === 0) {
                            this.undoDisable = true;
                        }
                        this.redoDisable = false;

                        this.stage.draw();
                        break;
                }
            }
        },
        reDo() {
            if (redoStack.length > 0) {
                const lastLayerJson = redoStack.pop();
                switch (lastLayerJson.type) {
                    case 'add':
                        undoStack.push(lastLayerJson);
                        // Recreate the layer
                        this.recreateLayer(lastLayerJson.data);

                        if (redoStack.length === 0) {
                            this.redoDisable = true;
                        }
                        this.undoDisable = false;
                        this.stage.draw();
                        break;
                    case 'position':
                        const obj = this.stage.findOne(`#${lastLayerJson.id}`);

                        const state = {
                            type: 'position',
                            x: obj.x(),
                            y: obj.y(),
                            id: obj.id()
                        };

                        undoStack.push(state);

                        // Restore the previous state
                        obj.x(lastLayerJson.x);
                        obj.y(lastLayerJson.y);

                        if (redoStack.length === 0) {
                            this.redoDisable = true;
                        }
                        this.undoDisable = false;

                        this.stage.draw();
                        break;
                }
            }
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
        alignMiddle() {
            if (this.selectedObjectIds.length === 0) return;

            const stageHeight = this.stage.height();

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape) {
                    shape.y((stageHeight - shape.height() * shape.scaleY()) / 2);
                }
            } else {
                const middleY = Math.min(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.y() : Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.y(middleY + (stageHeight / 2 - shape.height() * shape.scaleY() / 2));
                    }
                });
            }

            this.defaultLayer.batchDraw();
        },
        alignCenter() {
            if (this.selectedObjectIds.length === 0) return;

            const stageWidth = this.stage.width();

            if (this.selectedObjectIds.length === 1) {
                const shape = this.stage.findOne(`#${this.selectedObjectIds[0]}`);
                if (shape) {
                    shape.x((stageWidth - shape.width() * shape.scaleX()) / 2);
                }
            } else {
                const centerX = Math.min(...this.selectedObjectIds.map((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    return shape ? shape.x() : Infinity;
                }));

                this.selectedObjectIds.forEach((id) => {
                    const shape = this.stage.findOne(`#${id}`);
                    if (shape) {
                        shape.x(centerX + (stageWidth / 2 - shape.width() * shape.scaleX() / 2));
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
        //text
        addText(config) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            config.id = uuidv4();

            const textConstructor = Konva['Text'];
            const text = new textConstructor(config);

            // Create a transformer
            this.addTransformer(newLayer);

            //layers order
            this.handleLayersOrder(newLayer, text.getClassName());

            text.on("pointerdown", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(text.id(), text.getClassName(), text);
            });
            // for mobile
            // text.on("touchstart", (e) => {
            //     e.cancelBubble = true;
            //     this.toggleSelection(text.id(), text.getClassName(), text);
            // });

            //editable
            this.editText(text);

            newLayer.add(text);
            newLayer.batchDraw();

            // // undo redo add
            this.undoDisable = false;
            const layerJson = newLayer.toObject();
            undoStack.push({
                type: 'add',
                data: layerJson
            });

            // // undo redo position
            text.on('dragstart', () => {
                this.savePositionState(text);
            });
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

            function handel() {
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
            }

            text.on('dblclick', () => {
                handel();
            });
            // for phons
            text.on('dbltap', () => {
                handel();
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
        changeFontFamily(fontFamily) {
            this.selectedObjectIds.forEach((id) => {
                const object = this.stage.findOne(`#${id}`);
                if (object) {
                    object.fontFamily(fontFamily.name);
                }
            });
            this.defaultLayer.batchDraw();
        },
        async resizeImage(dataURL, maxWidth, maxHeight) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.src = dataURL;

                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((maxWidth / width) * height);
                        width = maxWidth;
                    }

                    if (height > maxHeight) {
                        width = Math.round((maxHeight / height) * width);
                        height = maxHeight;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        resolve(blob);
                    }, 'image/png');
                };

                img.onerror = (err) => {
                    reject(err);
                };
            });
        },
        // templates// type => [text or shape or template ]
        //          // category_id if the selected type is template

        async createImage(dataURL) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.src = dataURL;

                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        resolve(blob);
                    }, 'image/png');
                };

                img.onerror = (err) => {
                    reject(err);
                };
            });
        },

        async saveAsTemplate(type) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let dataURL = this.stage.toDataURL({ pixelRatio: 3 });
                let blob = await this.resizeImage(dataURL, 700, 700);

                if(type === 'myDesigns'){
                    var form = this.myDesignForm;
                }else{
                    var form = this.addTemplateForm;
                }
                form.jsonData = this.stage.toJSON();
                form.image = blob;
                form.post(route('template.add'), {
                    _token: csrfToken,
                    onSuccess: async () => {
                        await this.fetchInitialDesigns();

                        const flashMessage = this.$page.props.flash?.message || 'Template Added Successfully.';
                        if (flashMessage) {
                            this.$page.props.flash = { message: flashMessage };
                        }
                    }
                });
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        },
        async getSelectedTemplate(id, type) {
            let res = await axios.get(`/template/${id}/${type}`);
            let data = res.data[0].data;
            if (data) {
                const template = JSON.parse(data);
                this.getTemplate(template);
            }
        },
        // images
        addImage(url) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            const imageObj = new Image();
            imageObj.src = url;
            imageObj.crossOrigin = 'anonymous';

            imageObj.onload = () => {
                const maxWidth = 700;
                const maxHeight = 500;
                let width = imageObj.width;
                let height = imageObj.height;

                // Calculate the new width and height while maintaining the aspect ratio
                if (width > maxWidth || height > maxHeight) {
                    const scale = Math.min(maxWidth / width, maxHeight / height);
                    width = width * scale;
                    height = height * scale;
                }

                const image = new Konva.Image({
                    image: imageObj,
                    width: width,
                    height: height,
                    draggable: true,
                    id: uuidv4(),
                    src: url,
                    name: 'object',
                });

                // Create a transformer
                this.addTransformer(newLayer);

                // Handle layers order
                this.handleLayersOrder(newLayer, image.getClassName());

                image.on("pointerdown", (e) => {
                    e.cancelBubble = true;
                    this.toggleSelection(image.id(), image.getClassName(), image);
                });

                newLayer.add(image);
                newLayer.batchDraw();

                // // undo redo add
                this.undoDisable = false;
                const layerJson = newLayer.toObject();
                undoStack.push({
                    type: 'add',
                    data: layerJson
                });

                // // undo redo position
                image.on('dragstart', () => {
                    this.savePositionState(image);
                });
            };
        },
        addUploadedImage(dataURL) {
            const newLayer = new Konva.Layer();
            newLayer.id(uuidv4());
            this.stage.add(newLayer);

            const imageObj = new Image();
            imageObj.src = dataURL;
            imageObj.crossOrigin = 'anonymous'

            // Create a transformer
            this.transformer = new Konva.Transformer();
            newLayer.add(this.transformer);

            imageObj.onload = () => {
                const maxWidth = 700;
                const maxHeight = 500;
                let width = imageObj.width;
                let height = imageObj.height;

                // Calculate the new width and height while maintaining the aspect ratio
                if (width > maxWidth || height > maxHeight) {
                    const scale = Math.min(maxWidth / width, maxHeight / height);
                    width = width * scale;
                    height = height * scale;
                }

                const image = new Konva.Image({
                    image: imageObj,
                    width: width,
                    height: height,
                    draggable: true,
                    id: uuidv4(),
                    name: 'object',
                });

                // Create a transformer
                this.addTransformer(newLayer);

                // Handle layers order
                this.handleLayersOrder(newLayer, image.getClassName());

                image.on("pointerdown", (e) => {
                    e.cancelBubble = true;
                    this.toggleSelection(image.id(), image.getClassName(), image);
                });

                newLayer.add(image);
                newLayer.batchDraw();
            }

        },
        /////////////////////////
        handleFileUpload(event) {
            const files = event.target.files;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrls.unshift(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        },

        async addTemplateImages(images, type) {
            this.addImageForm.images = [];

            this.addImageForm.type = type;

            let fetchPromises = images.map(async (image, index) => {
                let response = await fetch(image);
                let blob = await response.blob();

                this.addImageForm.images.push(new File([blob], `temp-${index}-${Date.now()}.png`, { type: blob.type }));
            });

            // Wait for all fetch operations to complete
            await Promise.all(fetchPromises);

            this.addImageForm.post(route('template.addImage'));
            // console.log(this.addImageForm)
        },

        deleteImage(image, type){
            if (type === 'admin'){
                this.deleteAdminImages.image = image;
                this.deleteAdminImages.delete(route('template.deleteAdminImages'));
            }else{
                this.deleteImageForm.delete(route('template.deleteImage', image));
            }
        },

        fetchUnsplashImages() {
            this.isDataRedy = true;
            fetch(`${this.unsplashUrl}?client_id=${this.unsplashAccessKey}`)
                .then(res => res.json())
                .then(json => {
                    json.forEach(element => {
                        this.images.push({
                            src: element.urls.full,
                            portfolio: element.user.portfolio_url,
                            author: element.user.name
                        });
                    });
                })
                .finally(() => {
                    this.isDataRedy = false;
                });
        },
        searchUnsplashImages(query) {
            this.isDataRedy = true;
            fetch(`${this.unsplashSearchUrl}?client_id=${this.unsplashAccessKey}&query=${query}`)
                .then(res => res.json())
                .then(json => {
                    this.images = json.results.map(element => ({
                        src: element.urls.full,
                        portfolio: element.user.portfolio_url,
                        author: element.user.name
                    }));
                })
                .finally(() => {
                    this.isDataRedy = false;
                });
        },
        async searchForTemplate(name) {
            let formData = new FormData();
            formData.append('name', name);
            try {
                const respons = await axios.post('/template/search', formData);
                this.allTemplates = [];
                respons.data.forEach(el => {
                    this.allTemplates.push(el);
                });
            } catch (error) {
                console.log(error);
            }
        },
        toggleFontWeight() {
            this.fontWeight = this.fontWeight === 'normal' ? 'bold' : 'normal';
            this.textStyle(this.fontWeight);
        },
        toggleFontItalic() {
            this.fontItalic = this.fontItalic === 'normal' ? 'italic' : 'normal';
            this.textStyle(this.fontItalic);
        },
        toggleFOntCase() {
            this.fontCase = this.fontCase === 'lower' ? 'upper' : 'lower';
            this.textCase(this.fontCase);
        },
        addHeader() {
            this.addText(headerText);
        },
        addSubHeader() {
            this.addText(subHeaderText);
        },
        addBodyText() {
            this.addText(bodyText);
        },
        async fetchFonts() {
            await fetch(`${this.fontUrl}?key=${this.fontsKey}`)
                .then(res => res.json())
                .then(json => {
                    json.items.forEach(font => {
                        this.GoogleFonts.push({
                            name: font.family,
                            file: font.files.regular
                        });
                    });
                });
        },
        async fetchFont(family) {
            await fetch(`${this.fontUrl}?key=${this.fontsKey}&family=${family}`)
                .then(res => res.json())
                .then(json => {
                    let font = {
                        name: json.items[0].family,
                        file: json.items[0].files.regular
                    }
                    return this.loadGoogleFont(font);
                });
        },
        async loadGoogleFont(font) {
            const fontFace = new FontFace(font.name, `url(${font.file})`);
            try {
                await fontFace.load();
                document.fonts.add(fontFace);
                return true;
            } catch (error) {
                console.error(`Failed to load font: ${font.name}`, error);
                return false;
            }
        },
        async onFontChange() {
            const fontLoaded = await this.loadGoogleFont(this.selectedFont);
            if (fontLoaded) {
                this.changeFontFamily(this.selectedFont);
            } else {
                console.error("Font failed to load, can't change font family.");
            }
        },
        editTemplate(id, type) {
            this.editingTemp = true;
            this.getSelectedTemplate(id, type);
            this.editedId = id;
            this.editedType = type;
        },
        async saveEditedTemplate(id) {
            try {
                let dataURL = this.stage.toDataURL({ pixelRatio: 3 });
                let blob = await this.resizeImage(dataURL, 300, 300);

                this.editTemplateForm.type = this.editedType;
                this.editTemplateForm.image = blob;
                this.editTemplateForm.jsonData = this.stage.toJSON();

                this.editTemplateForm.post(route('template.edit', id), {
                    onSuccess: async () => {
                        await this.fetchInitialDesigns();

                        const flashMessage = this.$page.props.flash?.message || 'Saved Successfully.';
                        if (flashMessage) {
                            this.$page.props.flash = { message: flashMessage };
                        }
                    }
                })
            } catch (error) {
                alert('Error while saving!')
            }
        },
        async deleteTemplate(id, type) {
            if (confirm("Are you sure ? ")) {
                try {
                    this.deleteTemplateForm.type = type;
                    this.deleteTemplateForm.delete(route('template.delete', id), {
                        onSuccess: async () => {
                            await this.fetchInitialDesigns();

                            const flashMessage = this.$page.props.flash?.message || 'Deleted Successfully.';
                            if (flashMessage) {
                                this.$page.props.flash = { message: flashMessage };
                            }
                        }
                    });
                } catch (error) {
                    alert('Error while deleting')
                }
            }
        },
        async addToCart() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let dataURL = this.stage.toDataURL({ pixelRatio: 3 });
                let blob = await this.createImage(dataURL);

                // Get the PDF Blob from saveAsPDF
                // let pdfBlob = await this.saveAsPDF();

                this.printForm.jsonData = this.stage.toJSON();
                this.printForm.image = blob;

                this.printForm.post(route('cart.add'), {_token: csrfToken });
            } catch (error) {
                console.log(error)
            }

        },
    }
};

export default allFunctions;
