import Konva from "konva";

// Define your constant for the guideline offset
export const GUIDELINE_OFFSET = 5;

/**
 * Get the line guide stops for the shapes on the stage.
 * @param {Konva.Node} skipShape - The shape to skip in the calculations.
 * @param {Konva.Stage} stage - The Konva stage object.
 * @returns {Object} - Object containing vertical and horizontal line guide stops.
 */
export function getLineGuideStops(skipShape, stage) {
    const vertical = [0, stage.width() / 2, stage.width()];
    const horizontal = [0, stage.height() / 2, stage.height()];

    stage.find('.object').forEach((guideItem) => {
        if (guideItem === skipShape) {
            return;
        }
        const box = guideItem.getClientRect();
        vertical.push([box.x, box.x + box.width, box.x + box.width / 2]);
        horizontal.push([box.y, box.y + box.height, box.y + box.height / 2]);
    });

    return {
        vertical: vertical.flat(),
        horizontal: horizontal.flat(),
    };
}

/**
 * Get the snapping edges for the object.
 * @param {Konva.Node} node - The Konva node object.
 * @returns {Object} - Object containing vertical and horizontal edges for snapping.
 */
export function getObjectSnappingEdges(node) {
    const box = node.getClientRect();
    const absPos = node.absolutePosition();

    return {
        vertical: [
            { guide: Math.round(box.x), offset: Math.round(absPos.x - box.x), snap: 'start' },
            { guide: Math.round(box.x + box.width / 2), offset: Math.round(absPos.x - box.x - box.width / 2), snap: 'center' },
            { guide: Math.round(box.x + box.width), offset: Math.round(absPos.x - box.x - box.width), snap: 'end' },
        ],
        horizontal: [
            { guide: Math.round(box.y), offset: Math.round(absPos.y - box.y), snap: 'start' },
            { guide: Math.round(box.y + box.height / 2), offset: Math.round(absPos.y - box.y - box.height / 2), snap: 'center' },
            { guide: Math.round(box.y + box.height), offset: Math.round(absPos.y - box.y - box.height), snap: 'end' },
        ],
    };
}

/**
 * Get the guides for snapping based on line guide stops and object edges.
 * @param {Object} lineGuideStops - Object containing vertical and horizontal line guide stops.
 * @param {Object} itemBounds - Object containing vertical and horizontal edges for snapping.
 * @returns {Array} - Array of guide objects for drawing snapping lines.
 */
export function getGuides(lineGuideStops, itemBounds) {
    const resultV = [];
    const resultH = [];

    lineGuideStops.vertical.forEach((lineGuide) => {
        itemBounds.vertical.forEach((itemBound) => {
            const diff = Math.abs(lineGuide - itemBound.guide);
            if (diff < GUIDELINE_OFFSET) {
                resultV.push({ lineGuide, diff, snap: itemBound.snap, offset: itemBound.offset });
            }
        });
    });

    lineGuideStops.horizontal.forEach((lineGuide) => {
        itemBounds.horizontal.forEach((itemBound) => {
            const diff = Math.abs(lineGuide - itemBound.guide);
            if (diff < GUIDELINE_OFFSET) {
                resultH.push({ lineGuide, diff, snap: itemBound.snap, offset: itemBound.offset });
            }
        });
    });

    const guides = [];
    const minV = resultV.sort((a, b) => a.diff - b.diff)[0];
    const minH = resultH.sort((a, b) => a.diff - b.diff)[0];
    if (minV) {
        guides.push({ lineGuide: minV.lineGuide, offset: minV.offset, orientation: 'V', snap: minV.snap });
    }
    if (minH) {
        guides.push({ lineGuide: minH.lineGuide, offset: minH.offset, orientation: 'H', snap: minH.snap });
    }
    return guides;
}

/**
 * Draw the snapping guides on the layer.
 * @param {Array} guides - Array of guide objects for drawing snapping lines.
 * @param {Konva.Layer} layer - The Konva layer object.
 */
export function drawGuides(guides, layer) {
    guides.forEach((lg) => {
        if (lg.orientation === 'H') {
            const line = new Konva.Line({
                points: [-6000, 0, 6000, 0],
                stroke: 'rgb(0, 161, 255)',
                strokeWidth: 1,
                name: 'guid-line',
                dash: [4, 6],
            });
            layer.add(line);
            line.absolutePosition({ x: 0, y: lg.lineGuide });
        } else if (lg.orientation === 'V') {
            const line = new Konva.Line({
                points: [0, -6000, 0, 6000],
                stroke: 'rgb(0, 161, 255)',
                strokeWidth: 1,
                name: 'guid-line',
                dash: [4, 6],
            });
            layer.add(line);
            line.absolutePosition({ x: lg.lineGuide, y: 0 });
        }
    });
}
