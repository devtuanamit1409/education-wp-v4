import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Spinner, Button } from '@wordpress/components';
import { download } from '@wordpress/icons';

import { generateCSVDataFromTable, generateCSVFileName, downloadCSVFile } from '@woocommerce/csv-export';

const HEADER_EXPORT = [
	{ label: __( 'Question', 'leanpress-gradebook' ), key: '"title"' },
	{ label: __( 'Type', 'learnpress-gradebook' ), key: '"type"' },
	{ label: __( 'Retake', 'learnpress-gradebook' ), key: '"retake_count"' },
	{ label: __( 'Correct', 'learnpress-gradebook' ), key: '"correct"' },
	{ label: __( 'Retake detail', 'learnpress-gradebook' ), key: '"retake"' },
];

export default function ExportQuestion( { courseID, quizzID, studentID } ) {
	const [ loading, setLoading ] = useState( false );

	async function exportQuestions() {
		setLoading( true );

		try {
			const response = await apiFetch( {
				path: addQueryArgs( 'lp-gradebook/export-questions', { courseID, quizzID, studentID } ),
				method: 'GET',
			} );

			const dataForm = generateCSVDataFromTable( HEADER_EXPORT, response.data.export );
			const name = generateCSVFileName( 'gradebook', { list: 'questions' } );
			downloadCSVFile( name, dataForm );

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
			onClick={ exportQuestions }
			icon={ loading ? <Spinner /> : download }
			iconSize={ 20 }
			text={ __( 'Export CSV', 'learnpress-gradebook' ) }
		/>
	);
}
