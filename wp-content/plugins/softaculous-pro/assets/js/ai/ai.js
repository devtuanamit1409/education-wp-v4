(function($, i18n){

const supported_languages = ['arabic', 'chinese', 'czech', 'danish', 'dutch', 'english', 'finnish', 'french', 'german', 'greek', 'hindi', 'hebrew', 'hungarian', 'indonesian', 'italian', 'japanese', 'korean', 'marathi', 'punjabi', 'persian', 'polish', 'portuguese', 'russian', 'spanish', 'swedish', 'thai', 'turkish', 'vietnamese'],
supported_tones = ['casual', 'confidence', 'formal', 'friendly', 'inspirational', 'motivational', 'nostalgic', 'playful', 'professional', 'scientific', 'straightforward', 'witty'];
let old_date_string = '';

	
$(document).ready(function(){
	let chat_tmpl = build_chat();

	$('body').append(chat_tmpl);
	
	$('#soft-ai-generation').click(request_soft_ai);
	$('.spro-ai-shortcut').click(request_soft_ai);
	$('.spro-ai-shortcut-select').change(request_soft_ai);
	$('.spro-ai-chat-history-icon').click(toggle_history);
	$('.spro-ai-suggestion-btns').click(handle_suggestions);

	// Handling Enter press when user is focused writing AI Prompt.
	$('#spro_prompt_input').on('keydown', function(e){
		if(e.shiftKey || e.key != 'Enter'){
			return;
		}
		
		e.preventDefault();

		let jEle = $(e.target);		
		if(jEle.val().trim() == ''){
			return;
		}

		request_soft_ai(e);
	});
	
	$('.spro-ai-chat-close').click(function(e){
		let jEle = $(e.target);

		jEle.closest('.spro-chat').css('right', '-25vw');
		$('.spro-ai-chat-overlay').hide();
	})

	// To close AI chat when user gets into something else
	$('.spro-ai-chat-overlay').on('click', function(){
		let soft_chat = $('.spro-chat')

		if(soft_chat.css('right') != '0px'){
			return;
		}

		soft_chat.css('right', '-25vw');
		$('.spro-ai-chat-overlay').hide();
	});
	
	$(document).on('click', '.spro-copy-ai-response', function(e){
		e.preventDefault();
		let jEle = $(e.target).closest('.spro-copy-ai-response'),
		response = jEle.closest('.spro-response-actions').prev();
		jEle.addClass('active');

		navigator.clipboard.writeText(response.html())
		
		setTimeout(() => {jEle.removeClass('active');}, 2000);
	});
	
	$(document).on('click', '.spro-chat-history-link', function(e){
		let jEle = $(e.target),
		history_id = jEle.data('id');
		
		if(history_id == 0){
			return;
		}
		
		// We won't process if we do not have the base class
		if($('.spro-ai-chat-history-view').length < 1){
			return;
		}

		request_history(history_id);
	});
	
	// Handling history arrow icon
	$('.spro-chat').on('click', '.spro-ai-history-single-close', function(e){
		$('.spro-ai-chat-history-view').empty();
		$(e.target).css('visibility', 'hidden'); // This is to prevent the other spans from moving positions
		$('.spro-ai-chat-history-list').show();
	});
	
	// Handling history close icon
	$('.spro-chat').on('click', '.spro-ai-history-close', function(e){
		$('.spro-ai-chat-history-view').empty();
		$('.spro-ai-chat-history-list').show();
		$('.spro-ai-chat-history-icon').trigger('click');
	});

	$(document).on('click', 'button.spro-ai-use-this', use_ai_content);
	
	$('.spro-ai-chat-history').scroll(function (e){ 
		if(e.target.scrollTop + e.target.clientHeight >= e.target.scrollHeight - 2) {
			request_history();
		}
	});
});

function build_chat(){
	return `<div class="spro-chat">
		<div class="spro-snackbar"></div>
		<div class="spro-chat-header">
			<span class="dashicons dashicons-arrow-right-alt spro-ai-chat-close"></span>
			<span>Softaculous AI&nbsp;<img src="${soft_ai.ai_icon_url}" class="spro-ai-icon" width="20"/></span>
			<span class="dashicons dashicons-backup spro-ai-chat-history-icon"></span>
		</div>
		<div class="spro-ai-token-count">${soft_ai.tokens ? ('Tokens used ' + soft_ai.tokens.used_tokens + ' / ' + soft_ai.tokens.total_tokens) : ''}</div>
		<div class="spro-chat-response-section">
		<div class="spro-chat-startup-placeholder">
			<h1>What can I help you with?</h1>
			<div class="spro-prompt-suggestions">
				<p>Suggestions</p>
				<button class="spro-ai-suggestion-btns" data-prompt="blog_post">Write me a blog post about</button>
				<button class="spro-ai-suggestion-btns" data-prompt="create_table">Create a table of</button>
				<button class="spro-ai-suggestion-btns" data-prompt="desc_title">Write a title based on description</button>
				<button class="spro-ai-suggestion-btns" data-prompt="p_50">Write a 50 word paragraph about topic</button>
			</div>
		</div>
		</div>
		<div class="spro-ai-chat-history">
			<div class="spro-chat-history-header">
				<span class="dashicons dashicons-arrow-right-alt spro-ai-history-single-close" title="Go back to history list"></span>
				<span class="spro-history-header-label">History <span class="spro-spinner spro-spinner__default spro-spinner__dark"></span></span>
				<span class="dashicons dashicons-no-alt spro-ai-history-close" title="Close History"></span>
			</div>
			<div class="spro-ai-chat-history-view"></div>
			<div class="spro-ai-chat-history-list"></div>
		</div>
		
		<div class="spro-ai-chat-options-section">
			<div class="spro-prompt-shortcuts">
				<button class="soft-btn spro-ai-shortcut" action="shorter">Make it shorter</button>
				<button class="soft-btn spro-ai-shortcut" action="longer">Make it longer</button>
				<button class="soft-btn spro-ai-shortcut" action="simplify">Simplify the language</button>
				<button class="soft-btn spro-ai-shortcut" action="grammer">Fix spelling & grammer</button>
				<div>
					<select name="tone" class="spro-ai-select spro-ai-shortcut-select">
						<option selected disabled>Change Tone</option>
						${supported_tones.map((tone) => (`<option value="${tone}">${tone.charAt(0).toUpperCase() + tone.slice(1)}</option>`)).join('')}
					</select>
					<select name="translate" class="spro-ai-select spro-ai-shortcut-select">
						<option selected disabled>Translate</option>
						${supported_languages.map((lang) => (`<option value="${lang}">${lang.charAt(0).toUpperCase() + lang.slice(1)}</option>`)).join('')}
						</select>
				</div>
			</div>
			<div class="soft-prompt-input">
				<textarea id="spro_prompt_input" name="soft_prompt" rows="3"></textarea>
				<p class="description">AI responses can be inaccurate, please double check</p>
			</div>
			<div class="soft-prompt-options"></div>
			<div class="spro-prompt-action"><button id="soft-ai-generation" class="soft-btn soft-btn-black">Generate<span class="spro-spinner spro-spinner__default spro-spinner__light"></span></button></div>
		</div>
	</div>
	<div class="spro-ai-chat-overlay"></div>`;
}

function show_snackbar(msg){
	snack_bar = $('.spro-snackbar');
	snack_bar?.text(msg);
	snack_bar?.addClass('show');
	setTimeout(function(){ snack_bar?.removeClass('show'); }, 3500);
}

function request_soft_ai(e){
	e.preventDefault();
	
	let jEle = $(e.target),
	ai_prompt = $('#spro_prompt_input').val(),
	options_section = jEle.closest('.spro-ai-chat-options-section'),
	shortcut = jEle.attr('action'),
	spinner = options_section.find('span.spro-spinner'),
	response_section = jQuery('.spro-chat-response-section');

	if(!ai_prompt && !shortcut){
		shortcut = jEle.val();
	}

	if(!ai_prompt && !shortcut){
		alert('Enter a prompt');
		return;
	}

	options_section.addClass('disabled');
	
	// Bringing the response in the view port.
	response_section.find('.spro-chat-response')?.last()?.get(0)?.scrollIntoView({
		behavior: 'smooth',
		block: 'start'
	});

	spinner.addClass('spro-spinner-active');
	
	// We do not want to add a section when using shortcut
	if(!shortcut){
		soft_handle_ai_content(ai_prompt, 'prompt');
	}
	
	response_section.append('<div class="soft-ai-chat-loader">Softaculous AI Is Thinking<div class="spro-dot-loader"></div></div>');
	
	$('#spro_prompt_input').val(''); // Unset the prompt textarea

	// Making call to get AI reponse
	$.ajax({
		method : 'POST',
		url : soft_ai.ajax_url,
		data : {
			'nonce' : soft_ai.nonce,
			'prompt' : ai_prompt ?? '',
			'shortcut' : shortcut ?? '',
			'content': soft_ai.content ?? '',
			'action' : 'softaculous_ai_generation',
		},
		success: function(res){
			if(!res.success){
				show_snackbar(res.data);
				return;
			}
			
			if(res.data.error){
				show_snackbar(res.data.error);
				return;
			}
			
			// Updating the UI
			$('.spro-chat-startup-placeholder').slideUp();
			soft_handle_ai_content(res.data.ai, 'assistant');
		}
	}).always(function(res){
		if(res.data && res.data.used_tokens){
			update_tokens(res.data.used_tokens, res.data.total_tokens);
		}

		options_section.removeClass('disabled');
		spinner.removeClass('spro-spinner-active');
		response_section.find('.soft-ai-chat-loader').remove(); // Removing the loader.
		
		if($('.spro-chat-response-section .spro-chat-response').length){
			$('.spro-prompt-shortcuts').show();
		}
	});
}

function use_ai_content(e){
	e.preventDefault();

	let jEle = $(e.target);
	content = jEle.closest('p').prev().html();
	console.log(jEle.closest('p').prev());
	content = content.trim();

	// We are using markdown because the html responses are not that good, and can have unexpected tags, having markdown makes sure the tags which we will have to handle will be basic which won't cause any issue getting added to WordPress.
	/*content = marked.parse(content);
	
	// We need to remove the p tag in li as that breaks the gutenberg editor formatting for list.
	content = content.replace(/<li>(.*?)<\/li>/gs, (match) => {
		return match.replace(/<\/?p>(<br\/?>)?/gm, '');
	});*/
	let blocks = wp.blocks.rawHandler({HTML:content}),
	block_id = wp.data.select('core/block-editor').getSelectedBlockClientId();
	
	if(!block_id){
		wp.data.dispatch('core/block-editor').insertBlocks(blocks);
	} else {
		wp.data.dispatch('core/block-editor').replaceBlock(block_id, blocks);
	}
}

function toggle_history(e){

	let jEle = $(e.target);
	chat_wrap = jEle.closest('.spro-chat'),
	history_tab = chat_wrap.find('.spro-ai-chat-history'),
	chat_response = chat_wrap.find('.spro-chat-response-section'),
	chat_options = chat_wrap.find('.spro-ai-chat-options-section');
	
	if(history_tab.css('display') == 'none'){
		history_tab.show();
		chat_options.hide();
		chat_response.hide();

		// We dont want to request more if we already have some history.
		if($('.spro-chat-history-link').length > 0){
			return;
		}

		history_tab.find('.spro-chat-history-header span.spro-spinner').addClass('spro-spinner-active');
		request_history();
		
		return;
	}
	
	history_tab.hide();
	chat_options.show();
	chat_response.show();	
}

function show_single_history(response){
	html = '';
	
	$('.spro-ai-chat-history-list').hide();
	$('.spro-ai-chat-history-view').empty();
	
	if(response.content){
		html += `<div class="spro-chat-response" data-type="content">
		<p><b>Content:</b></p>
		<p>${response.content}</p>
	</div>`;
	}
	
	if(response.prompt){
		html += `<div class="spro-chat-response" data-type="prompt">
		<p><b>Prompt:</b></p>
		<p>${response.prompt}</p>
	</div>`;
	}
	
	if(response.assistant){
		response.assistant = spro_markdown_to_html(response.assistant);

		html += `<div class="spro-chat-response" data-type="assistant">
		<p><b>Assistant:</b></p>
		<div>${response.assistant}</div>
		<p class="spro-response-actions"><button class="spro-ai-use-this">Use This</button><button class="spro-copy-ai-response"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 7c0-.932 0-1.398-.152-1.765a2 2 0 0 0-1.083-1.083C12.398 4 11.932 4 11 4H8c-1.886 0-2.828 0-3.414.586S4 6.114 4 8v3c0 .932 0 1.398.152 1.765a2 2 0 0 0 1.083 1.083C5.602 14 6.068 14 7 14" stroke="#222"/><rect x="10" y="10" width="10" height="10" rx="2" stroke="#222"/></svg></button></p>
	</div>`;
	}
	
	$('.spro-ai-chat-history-view').append(html);
	$('.spro-ai-history-single-close').css('visibility', 'visible');
	
}

function marktohtml(mark){
	
}

function update_tokens(used_tokens, total_tokens){
	$('.spro-ai-token-count').text('Tokens used ' + used_tokens+ ' / ' + total_tokens)
}

function request_history(history_id = 0){
	
	let history_links = $('.spro-chat-history-link'),
	offset = history_links.length,
	total_links = $('.spro-ai-chat-history-list').data('total');
	
	// We should not send ajax request if all the history items are visible.
	if(offset == total_links && !history_id){
		return;
	}

	$.ajax({
		method : 'POST',
		url : soft_ai.ajax_url,
		data : {
			'nonce' : soft_ai.nonce,
			'action' : 'softaculous_ai_history',
			'history_id' : history_id,
			'offset' : offset,
		},
		success: function(res){
			if(!res.success){
				alert(res.message);
				return;
			}
			
			// In case of single history we want to handle the append in different place
			if(history_id != 0){
				show_single_history(res.data);
				return;
			}
			
			// Updating the UI
			append_history(res.data);
		}
	}).always(function(){
		$('.spro-chat-history-header span.spro-spinner').removeClass('spro-spinner-active');
	});
}

function append_history(history){

	let html = '',
	total = history['total'];
	history = history['history'];

	for(let i in history){
		let date = history[i]['date'],
		date_obj = new Date(date),
		current_date = new Date(),
		date_string = '';

		if(date_obj.getDate() == current_date.getDate() && date_obj.getMonth() == current_date.getMonth()){
			date_string = 'Today';
		} else if(date_obj.getDate() == (current_date.getDate() - 1) && date_obj.getMonth() == current_date.getMonth()){
			date_string = 'Yesterday';
		} else {
			date_string = date_obj.toLocaleString('en-US', {month:'long'})
		}

		if(!date_string || old_date_string != date_string){
			html += `<p><em><strong>${date_string}</strong></em></p>`;
		}
		
		old_date_string = date_string;

		html += `<div class="spro-chat-response spro-chat-history-link" data-id="${history[i]['id']}">${history[i]['title']}</div>`;
	}

	$('.spro-ai-chat-history-list').append(html);
	$('.spro-ai-chat-history-list').attr('data-total', total);
}

function handle_suggestions(e){
	e.preventDefault();
	
	let jEle = $(e.target),
	suggestion = jEle.data('prompt'),
	prompts = {
		'p_50': 'Write a 50 word paragraph about topic [write the topic name]',
		'desc_title': 'Write a title based on description [write a description you want the title on]',
		'create_table': 'Create a table of [topic of the table you want]',
		'blog_post': 'Write me a blog post about [write your topic here]',
	};

	if(!prompts.hasOwnProperty(suggestion)){
		return;
	}
	
	$('#spro_prompt_input').text(prompts[suggestion]).focus();
}

})(jQuery, soft_ai.i18n)

function soft_handle_ai_content(props, role = 'content'){

	let content = '';
	
	if(!props){
		return;
	}

	// Storing the gutenberg object so we can update the content using setAttribute method.
	if(role == 'content'){	
		content = props.attributes.content;
		soft_ai.gutenberg = props;
	} else {
		content = props;
	}

	response_section = jQuery('.spro-chat-response-section');
	
	// Updating our global
	if(content.text){
		soft_ai.content = content.text;
	} else if(role == 'assistant'){
		// We update the content to assistants content as the next we will process the AI generated content.
		soft_ai.content = content;
		content = spro_markdown_to_html(content);
	}

	chat_response = `<div class="spro-chat-response" data-type="${role}">
		<p><b>${role.charAt(0).toUpperCase() + role.slice(1)}:</b></p>
		<div>${content}</div>
			${role === 'assistant' ? '<p class="spro-response-actions"><button class="spro-ai-use-this">Use This</button><button class="spro-copy-ai-response"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 7c0-.932 0-1.398-.152-1.765a2 2 0 0 0-1.083-1.083C12.398 4 11.932 4 11 4H8c-1.886 0-2.828 0-3.414.586S4 6.114 4 8v3c0 .932 0 1.398.152 1.765a2 2 0 0 0 1.083 1.083C5.602 14 6.068 14 7 14" stroke="#222"/><rect x="10" y="10" width="10" height="10" rx="2" stroke="#222"/></svg></button></p>':''}
	</div>`;

	response_section.append(chat_response);
	
	// Bringing the response in the view port.
	response_section.find('.spro-chat-response').last().get(0).scrollIntoView({
		behavior: 'smooth',
		block: 'start'
	});
}

function spro_markdown_to_html(markdown){
	// We are using markdown because the html responses are not that good, and can have unexpected tags, having markdown makes sure the tags which we will have to handle will be basic which won't cause any issue getting added to WordPress.
	let content = marked.parse(markdown);
	
	// We need to remove the p tag in li as that breaks the gutenberg editor formatting for list.
	content = content.replace(/<li>(.*?)<\/li>/gs, (match) => {
		return match.replace(/<\/?p>(<br\/?>)?/gm, '');
	});
	
	return content;
}