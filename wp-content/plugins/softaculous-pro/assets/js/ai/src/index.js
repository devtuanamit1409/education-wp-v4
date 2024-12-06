const { registerPlugin } = wp.plugins;
const { PluginToolbarButton } = wp.editPost;
const { addFilter } = wp.hooks;
const { Fragment, useEffect } = wp.element;
const { BlockControls } = wp.blockEditor;
const { Button, ToolbarGroup, ToolbarButton } = wp.components;
const { __ } = wp.i18n;

let soft_ai_icon = (<svg xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 151.668 151.616"><defs><linearGradient id="b"><stop offset="0" style={{ stopColor: "#0e44ac", stopOpacity: 1 }} /><stop offset="1" style={{stopColor: "#664ef1", stopOpacity: 1}}/></linearGradient><linearGradient id="a"><stop offset="0" style={{stopColor:"#0e44ac", stopOpacity:1}}/><stop offset="1" style={{stopColor: "#664ef1", stopOpacity:1}}/></linearGradient><linearGradient xlinkHref="#a" id="c" x1="10.148" x2="116.734" y1="62.229" y2="62.229" gradientUnits="userSpaceOnUse"/><linearGradient xlinkHref="#b" id="d" x1="11.811" x2="115.07" y1="62.229" y2="62.229" gradientUnits="userSpaceOnUse" /></defs><path d="M64.039 88.243c-8.629.199-42.93 30.806-49.172 24.845-6.242-5.96 22.757-41.633 22.559-50.261-.198-8.63-30.806-42.93-24.845-49.172s41.633 22.757 50.262 22.559 42.93-30.806 49.172-24.845-22.758 41.633-22.56 50.262 30.807 42.93 24.846 49.171c-5.962 6.242-41.633-22.757-50.262-22.559z" style={{fill: "none", stroke: "url(#c)", strokeWidth: 10.9, strokeDasharray: "none", strokeDashoffset: 0.8655, strokeOpacity: 1,}} transform="rotate(-44.365 86.218 53.877)"/><path d="M64.039 88.243c-8.629.199-42.93 30.806-49.172 24.845-6.242-5.96 22.757-41.633 22.559-50.261-.198-8.63-30.806-42.93-24.845-49.172s41.633 22.757 50.262 22.559 42.93-30.806 49.172-24.845-22.758 41.633-22.56 50.262 30.807 42.93 24.846 49.171c-5.962 6.242-41.633-22.757-50.262-22.559" style={{fill: "url(#d)", stroke: "none", strokeWidth: 1.431, strokeDasharray: "1.431,2.862", strokeDashoffset: 0.7155,}} transform="matrix(.2651 -.25928 .25928 .2651 92.639 125.492)"/></svg>);

// Function to add custom toolbar button
const soft_ai_button = (BlockEdit) => (props) => {
    return (
		<Fragment>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={soft_ai_icon}
						label={__('Ask AI', 'softaculous')}
						onClick={() => {
							if(jQuery('.spro-chat').css('right') != '0px'){
								jQuery('.spro-chat').css({'right': 0, 'visibility': 'visible'});
								
								let response_div = jQuery('.spro-chat-response-section .spro-chat-response');
								// Remove the chat response if the previous response was same as well
								if(response_div.length > 0 && response_div.eq(response_div.length - 1).data('type') == 'content'){
									response_div.eq(response_div.length - 1).remove();
								}
								jQuery('.spro-prompt-shortcuts').css('display', 'flex');
								jQuery('.spro-ai-chat-overlay').show();
								jQuery('.spro-chat-startup-placeholder').hide();
								soft_handle_ai_content(props);
								return;
							}

							jQuery('.spro-chat').css({'right': '-25vw'});
						}}
					/>
				</ToolbarGroup>
			</BlockControls>
			<BlockEdit {...props} />
		</Fragment>
	);
};

// Hook to add the custom toolbar button
addFilter(
    'editor.BlockEdit',
    'softaculous/ai',
    soft_ai_button
);


const SoftAIToolBarBtn = () => {
	useEffect(() => {
		var button = jQuery('#soft-ai-toolbar-btn').closest('div');
		var g = jQuery('.editor-header__settings');

		if (g.length < 1) {
			return;
		}

		button.detach();
		g.prepend(button);
		button.show();
		
		jQuery('#soft-ai-toolbar-btn').off('click').click(function(){
			console.log('Hello');
			if(jQuery('.spro-chat').css('right') != '0px'){
				jQuery('.spro-chat').css({'right': 0, 'visibility': 'visible'});
				jQuery('.spro-prompt-shortcuts').hide();
				jQuery('.spro-ai-chat-overlay').show();
				let response_div = jQuery('.spro-chat-response-section .spro-chat-response');

				// Remove the chat response if the previous response was same as well
				if(response_div.length > 0 && response_div.eq(response_div.length - 1).data('type') == 'content'){
					response_div.eq(response_div.length - 1).remove();
				}

				// Show the suggestions only when the chat is empty.
				if(jQuery('.spro-chat-response-section .spro-chat-response').length < 1){
					jQuery('.spro-chat-startup-placeholder').show();
				} else {
					jQuery('.spro-prompt-shortcuts').show();
				}

				soft_handle_ai_content();

				return;
			}

			jQuery('.spro-chat').css({'right': '-25vw'});
		});
	});
	
	return(<Button id="soft-ai-toolbar-btn" aria-label="Ask AI" style={{'height': 'auto', 'padding': '4px 8px', 'font-size': '13px', 'display': 'flex', 'align-items': 'center', 'width': 'max-content'}}>{soft_ai_icon}</Button>);
}

registerPlugin('soft-ai-sidebar', {
    render : SoftAIToolBarBtn,
    icon : {soft_ai_icon}
})
