const { registerBlockType } = wp.blocks;
const { Fragment: FragmentMy, createElement } = wp.element;
const { RichText, InnerBlocks } = wp.blockEditor;
const { __: __My } = wp.i18n;

registerBlockType('widget-logic/widget-block', {
    title: __My('Live Match', 'widget-logic'),
    icon: 'embed-generic',
    category: 'widgets',
    script: 'widget-logic_live_match_widget',
    attributes: {
        title: {
            type: 'string',
            source: 'text',
            selector: 'h2',
            default: __My('Live Match', 'widget-logic') // Default value for the title
        }
    },
    edit: props => {
        return createElement(
            FragmentMy,
            null,
            createElement(RichText, {
                tagName: 'h2',
                value: props.attributes.title,
                onChange: title => props.setAttributes({ title }),
                placeholder: __My('Live Match title', 'widget-logic')
            }),
            createElement(
                'div',
                { className: 'widget-logic-widget-widget-content' },
                createElement(InnerBlocks, null)
            )
        );
    },
    save: props => {
        return createElement(
            'div',
            { className: 'widget-logic-widget-widget-container' },
            createElement(RichText.Content, { tagName: 'h2', value: props.attributes.title }),
            createElement(
                'div',
                { className: 'widget-logic-widget-widget-content' },
                createElement(
                    'div',
                    { 'data-place': 'widget-live-match' },
                    __My('Live Match will be here', 'widget-logic')
                ),
                createElement(InnerBlocks.Content, null)
            )
        );
    }
});
