import Konva from "konva";
import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';
// Import the snapping functions
import {
    getLineGuideStops,
    getObjectSnappingEdges,
    getGuides,
    drawGuides,
} from '@/Utils/snapping.js';
import {bodyText, headerText, subHeaderText} from "@/Utils/textConfig.js";

const width = 600;
const height = 500;
const ZOOM_STEP = 0.1;
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 2;

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
            /////// test ///////////////
        };
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

                // // Create a transformer
                // this.transformer = new Konva.Transformer();
                // this.defaultLayer.add(this.transformer);
                //
                // this.defaultLayer.on("dragmove", this.handleDragMove);
                // this.defaultLayer.on("dragend", this.handleDragEnd);

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
        loadFonts() {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/css/fonts.css';
            document.head.appendChild(link);
        },
        //get template from json
        async getTemplate(template) {
            //if (template.children) {
            template.children.forEach(child => {
                if (child.className === 'Layer') {
                    const newLayer = new Konva.Layer();
                    newLayer.id(uuidv4());
                    this.stage.add(newLayer);
                    // transformer
                    this.addTransformer(newLayer);

                    child.children.forEach(async grandChild => {
                        // with this condition i create aditional layer with no children
                        if (grandChild.className !== 'Transformer') {
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

                            object.on("click", (e) => {
                                e.cancelBubble = true;
                                this.toggleSelection(object.id(), object.getClassName(), object);
                            });

                            newLayer.add(object);
                        }
                    });
                    newLayer.batchDraw();
                }
            });
            // }
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

            shape.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(shape.id(), shape.getClassName(), shape);
            });

            newLayer.add(shape);
            newLayer.batchDraw();
        },
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

                    this.deleteLayer(layer.id());

                    layer.getChildren().forEach(child => child.destroy());
                }
            });
            this.selectedObjectIds = [];
            this.clearSelection();
            this.defaultLayer.batchDraw();
        },
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

                    object.on("click", (e) => {
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

            text.on("click", (e) => {
                e.cancelBubble = true;
                this.toggleSelection(text.id(), text.getClassName(), text);
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
        // templates
        async saveAsTemplate(name, type) {
            try {
                let dataURL = this.stage.toDataURL({ pixelRatio: 3 });

                if (type === 'Shapes') {
                    let blob = await this.resizeImage(dataURL, 300, 200);
                }

                let blob = await this.resizeImage(dataURL, 300, 300);

                let formData = new FormData();
                formData.append('name', name);
                formData.append('data', this.stage.toJSON());
                formData.append('type', type);
                formData.append('image', blob, `${name}.png`);

                let res = await axios.post('/template/add', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (res.status === 201) {
                    alert('Template created successfully!');
                } else {
                    alert('Try again!');
                }
            } catch (error) {
                console.error('Error saving template:', error);
                alert('An error occurred. Please try again.');
            }
        },
        async getSelectedTemplate(id) {
            let res = await axios.get(`/template/${id}`);
            let data = res.data.data;
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
                const image = new Konva.Image({
                    image: imageObj,
                    width: width - 100,
                    height: height - 100,
                    draggable: true,
                    id: uuidv4(),
                    src: url,
                    name: 'object',
                });

                // Create a transformer
                this.addTransformer(newLayer);

                // lsyers order
                this.handleLayersOrder(newLayer, image.getClassName());

                image.on("click", (e) => {
                    e.cancelBubble = true;
                    this.toggleSelection(image.id(), image.getClassName(), image);
                });

                newLayer.add(image);
                newLayer.batchDraw();
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
                const image = new Konva.Image({
                    image: imageObj,
                    width: 400,
                    height: 500,
                    draggable: true,
                    id: uuidv4(),
                });

                const currentLength = this.layers.length;
                if (currentLength > 1) {
                    this.layers[currentLength - 1].lastOne = false;
                }
                this.layers.push({
                    'id': newLayer.id(),
                    'name': image.getClassName(),
                    'layer': newLayer,
                    'visible': true,
                    'firstOne': this.layers.length === 1,
                    'lastOne': true
                })

                image.on("click", (e) => {
                    e.cancelBubble = true;
                    this.toggleSelection(image.id(), image.getClassName(), image);
                });

                newLayer.add(image);
                newLayer.batchDraw();
            }

        },
        ///////////// test crop ///////////////////
        addClippingTool() {
            if (this.clippingTool) {
                this.removeClippingTool();
            }

            this.clippingTool = new Konva.Layer();
            this.stage.add(this.clippingTool);

            this.clippingRect = new Konva.Rect({
                x: 200,
                y: 200,
                width: 200,
                height: 200,
                draggable: true,
                fill: 'rgba(255, 0, 0, 0.3)',
            });

            this.clippingTransformer = new Konva.Transformer({
                nodes: [this.clippingRect],
                resizeEnabled: true,
                rotateEnabled: false,
                borderStroke: 'blue',
                borderStrokeWidth: 2,
                cornerRadius: 6,
                enabledAnchors: ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
            });

            this.clippingTool.add(this.clippingRect);
            this.clippingTool.add(this.clippingTransformer);
            this.stage.add(this.clippingTool);
            this.clippingTool.batchDraw();
        },
        // applyClipping() {
        //     if (this.clippingRect && this.clippingTool) {
        //         const selectedId = this.selectedObjectIds[0];
        //         const image = this.stage.findOne(`#${selectedId}`);

        //         const { x, y, width, height } = this.clippingRect.attrs;

        //         const imagLayer = image.getLayer();

        //         image.cache();
        //         imagLayer.clip({
        //             x: x,
        //             y: y,
        //             width: width,
        //             height: height
        //         });

        //         imagLayer.batchDraw();
        //         ///////////////////////
        //         const cropCanvas = document.createElement('canvas');
        //         const cropCtx = cropCanvas.getContext('2d');

        //         cropCanvas.width = width;
        //         cropCanvas.height = height;

        //         const imageObj = image.image();

        //         cropCtx.drawImage(
        //             imageObj,
        //             x - image.x(),
        //             y - image.y(),
        //             width,
        //             height,
        //             0, 0,
        //             width,
        //             height
        //         );

        //         const croppedImage = new Image();
        //         croppedImage.src = cropCanvas.toDataURL();
        //         croppedImage.onload = () => {
        //             image.image(croppedImage);
        //             this.stage.batchDraw();
        //         };
        //         ///////////////////////
        //         this.removeClippingTool();

        //     } else {
        //         console.error('Clipping tool not initialized.');
        //     }
        // },
        applyClipping() {
            if (this.clippingRect && this.clippingTool) {
                const selectedId = this.selectedObjectIds[0];
                const object = this.stage.findOne(`#${selectedId}`);

                if (!object || object.className !== 'Image') {
                    console.error('Selected object is not an image.');
                    return;
                }

                const { x, y, width, height } = this.clippingRect.attrs;
                console.log(x, y);

                const imageObj = object.image();
                if (!imageObj) {
                    console.error('No image object found.');
                    return;
                }

                const cropCanvas = document.createElement('canvas');
                const cropCtx = cropCanvas.getContext('2d');

                cropCanvas.width = width;
                cropCanvas.height = height;

                cropCtx.drawImage(
                    imageObj,
                    x - object.x(),
                    y - object.y(),
                    width,
                    height,
                    0, 0,
                    width,
                    height
                );

                const croppedImage = new Image();
                croppedImage.src = cropCanvas.toDataURL();
                croppedImage.onload = () => {
                    object.image(croppedImage);
                    this.stage.batchDraw();
                };

                this.removeClippingTool();

            } else {
                console.error('Clipping tool not initialized.');
            }
        },
        removeClippingTool() {
            if (this.clippingTool) {
                this.clippingTool.destroy();
                this.clippingTool = null;
                this.clippingRect = null;
                this.clippingTransformer = null;
                this.stage.batchDraw();
            }
        },
        ///////////// test crop ///////////////////
        async addTemplateImages(images) {
            let formData = new FormData();
            let fetchPromises = images.map(async (image, index) => {
                try {
                    let response = await fetch(image);
                    let blob = await response.blob();
        
                    let fileName = `temp-${index}-${Date.now()}.png`;
                    formData.append('images[]', blob, fileName);
                } catch (error) {
                    console.error(`Failed to fetch image from ${image}:`, error);
                }
            });
        
            // Wait for all fetch operations to complete
            await Promise.all(fetchPromises);
        
            try {
                let res = await axios.post('/template/picture/add', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
            } catch (error) {
                console.error('Error uploading images:', error);
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

    }
};

export default allFunctions;
