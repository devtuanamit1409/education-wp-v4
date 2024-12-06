const { createHigherOrderComponent: createHOC } = wp.compose;
const { Fragment } = wp.element;
const { InspectorAdvancedControls } = wp.blockEditor;
const { TextareaControl } = wp.components;
const { addFilter } = wp.hooks;
const { __ } = wp.i18n;

const _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];for (var key in source) {
            if (Object.prototype.hasOwnProperty.call(source, key)) {
                target[key] = source[key];
            }
        }
    }
    return target;
};


// Blocks that are not compatible at all with widget logic.
const globallyIncompatible = ['core/freeform', 'core/legacy-widget', 'core/widget-area'];

/**
 * Add logic to block.
 *
 * @param {object} cfg Current block config.
 *
 * @returns {object} Modified block settings.
 */
const addWidgetLogicControlAttribute = cfg => {
    // The freeform (Classic Editor) blocks are not compatible because it does not
    // support custom attributes and can be config with prev version of widget logic.
    if (globallyIncompatible.includes(cfg.name)) {
        return cfg;
    }

    if ('undefined' !== typeof cfg.attributes) {
        const attributes = {
            widgetLogic: {
                type: 'string',
                default: '',
            },
        }

        // Use Lodash's assign to gracefully handle if attributes are undefined
        cfg.attributes = _extends(cfg.attributes ?? {}, attributes);
        cfg.supports = _extends(cfg.supports ?? {}, {
            widgetLogic: true,
        });
    }

    return cfg;
};

addFilter('blocks.registerBlockType', 'widgetLogic/attribute', addWidgetLogicControlAttribute);

/**
 * Create Higher order component to add React layout control to inspector controls of block.
 */
const withLogicField = createHOC(BlockEdit => {
    return props => {
        const { attributes, setAttributes, isSelected } = props;
        const { widgetLogic } = attributes;

        return React.createElement(
            Fragment,
            null,
            React.createElement(BlockEdit, props),
            isSelected && !globallyIncompatible.includes(props.name) && React.createElement(
                InspectorAdvancedControls,
                null,
                React.createElement(TextareaControl, {
                    rows: '2',
                    label: __('Widget Logic', 'widget-logic'),
                    help: __('Add PHP condition that returns true or false or valid PHP conditional tags.', 'widget-logic'),
                    value: widgetLogic ? widgetLogic : '',
                    onChange: newValue => setAttributes({
                        widgetLogic: newValue
                    })
                })
            )
        );
    };
}, 'withLogicField');



addFilter('editor.BlockEdit', 'widgetLogic/controlField', withLogicField);

/**
 * add a data attribute to the block wrapper "editor-block-list__block"
 * this hook only fires for the BE/Admin View
 */

const addWrapperField = createHOC(BlockListBlock => {
        return props => {
                const { widgetLogic } = props.attributes;

                const { name } = props.block;

                let newName = name.replace(/\w+\//gm, '');
                newName = newName.charAt(0).toUpperCase() + newName.slice(1);

                let wrapperProps = props.wrapperProps;
                let customData = {};

                if ('undefined' !== typeof widgetLogic && widgetLogic) {
                        customData = _extends(customData, {
                                'data-cfg-widget-logic': __('Widget Logic:', 'widget-logic') + ' ' + widgetLogic
                        });
                }

                wrapperProps = _extends(wrapperProps ?? {}, customData);

                return React.createElement(BlockListBlock, _extends({}, props, { wrapperProps: wrapperProps }));
        };
}, 'addWrapperField');



addFilter('editor.BlockListBlock', 'widgetLogic/addWrapperField', addWrapperField);
