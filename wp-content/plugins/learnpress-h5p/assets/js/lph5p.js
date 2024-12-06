jQuery( document ).ready( function() {
	if ( jQuery( '.learn_press_h5p_content' ).hasClass( 'interactivevideo' ) ) {
		jQuery( '#complete_h5p_button' ).hide();
	}

	jQuery( '#complete_h5p_button' ).on( 'click', function( e ) {
		const question = jQuery( '#complete_h5p_button' ).data( 'confirm' );
		const ok = confirm( question );

		if ( ok ) {
			return true;
		}

		e.preventDefault();
		return false;
	} );

	if ( typeof H5P !== 'undefined' && H5P.externalDispatcher ) {
		H5P.externalDispatcher.on( 'xAPI', onXapi );
	}
} );

function showError( message, code ) {
	console.error( 'Unable to save xAPI statement' );

	if ( xapi_settings.alerts == true ) {
		alert( 'Unable to save result data.\n\nMessage: ' + message + '\n' + 'Code: ' + code );
	} else {
		console.log( 'Unable to save result data.\n\nMessage: ' + message + '\n' + 'Code: ' + code );
	}
}

function onXapiPostError( xhr, message, error ) {
	console.log( 'xapi post error' );
	console.log( xhr.responseText );

	showError( message, xhr.status );
}

function onXapiPostSuccess( res, textStatus, xhr ) {
	jQuery( '#complete_h5p_button' ).removeAttr( 'disabled' );

	if ( res.data.result == 'reached' ) {
		if ( res.data.reload ) {
			jQuery( 'form.complete-h5p' ).trigger( 'submit' );
		}
	} else if ( jQuery( '.learn_press_h5p_content' ).hasClass( 'interactivevideo' ) ) {
		jQuery( 'form.complete-h5p' ).trigger( 'submit' );
	}
}

function onXapi( event ) {
	if ( ! lpH5pSettings.ajax_url || ! lpH5pSettings.conditional_h5p ) {
		return;
	}

	if ( ( event.getVerb() === 'completed' || event.getVerb() === 'answered' ) && ! event.getVerifiedStatementValue( [ 'context', 'contextActivities', 'parent' ] ) ) {
		const score = event.getScore();
		const maxScore = event.getMaxScore();
		const contentId = event.getVerifiedStatementValue( [ 'object', 'definition', 'extensions', 'http://h5p.org/x-api/h5p-local-content-id' ] );

		if ( lpH5pSettings.conditional_h5p != contentId ) {
			return;
		}

		const data = {
			action: 'lph5p_process',
			score,
			maxScore,
			contentId,
			item_id: lpH5pSettings.id,
			course_id: lpH5pSettings.course_id,
		};

		jQuery.ajax( {
			type: 'POST',
			url: lpH5pSettings.ajax_url,
			data,
			dataType: 'json',
			success: onXapiPostSuccess,
		} );
	}
}
