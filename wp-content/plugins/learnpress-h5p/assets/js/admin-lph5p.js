( function( $ ) {
	function _ready() {
		$( '#_lp_h5p_interact' ).select2( {
			placeholder: 'Select a H5P item',
			minimumInputLength: 0,
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				allowClear: true,
				delay: 250,
				data( params ) {
					return {
						q: params.term || '',
						action: 'learnpress_search_h5p',
					};
				},
				processResults( data ) {
					const options = [];
					if ( data ) {
						// data is the array of arrays, and each of them contains ID and the Label of the option
						$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[ 0 ], text: text[ 1 ] } );
						} );
					}
					return {
						results: options,
					};
				},
				cache: true,
			},
			escapeMarkup( markup ) {
				return markup;
			},
		} );
	}
	$( document ).ready( _ready );
}( jQuery ) );
