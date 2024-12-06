/**
 * Code nhu quan que
 * Edit: Nhamdv
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Spinner, Button } from '@wordpress/components';
import { download } from '@wordpress/icons';

import { generateCSVDataFromTable, generateCSVFileName, downloadCSVFile } from '@woocommerce/csv-export';

const HEADER_EXPORT = [
	{ label: __( 'Title', 'learnpress-gradebook' ), key: '"post_title"' },
	{ label: __( 'Type', 'learnpress-gradebook' ), key: '"item_type"' },
	{ label: __( 'Start time', 'learnpress-gradebook' ), key: '"start_time"' },
	{ label: __( 'End time', 'learnpress-gradebook' ), key: '"end_time"' },
	{ label: __( 'Graduation', 'learnpress-gradebook' ), key: '"graduation"' },
	{ label: __( 'Status', 'learnpress-gradebook' ), key: '"status"' },
];

export default function ExportItems( { studentID, courseID } ) {
	const [ loading, setLoading ] = useState( false );

	async function ExportItem() {
		setLoading( true );

		try {
			const response = await apiFetch( {
				path: addQueryArgs( 'lp-gradebook/export-items', { studentID, courseID } ),
				method: 'GET',
			} );

			if ( response?.data ) {
				const dataForm = generateCSVDataFromTable( HEADER_EXPORT, response.data );
				const name = generateCSVFileName( 'gradebook', { list: 'items' } );

				downloadCSVFile( name, dataForm );
			}

			setLoading( false );
		} catch ( e ) {
			setLoading( false );
		}
	}

	return (
		<Button
			className="learnpress-gradebook__export__button"
			isSecondary
			variant="secondary"
			onClick={ ExportItem }
			icon={ loading ? <Spinner /> : download }
			iconSize={ 20 }
			text={ __( 'Export CSV', 'learnpress-gradebook' ) }
		/>
	);
}
