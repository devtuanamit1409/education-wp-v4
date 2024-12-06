import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Spinner, Button } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { download } from '@wordpress/icons';

import { generateCSVDataFromTable, generateCSVFileName, downloadCSVFile } from '@woocommerce/csv-export';

const HEADERS = [
	{ label: __( 'User ID', 'learnpress-gradebook' ), key: '"user_id"' },
	{ label: __( 'Username', 'learnpress-gradebook' ), key: '"user_nicename"' },
	{ label: __( 'Email', 'learnpress-gradebook' ), key: '"user_email"' },
	{ label: __( 'Start time', 'learnpress-gradebook' ), key: '"start_time"' },
	{ label: __( 'Status', 'learnpress-gradebook' ), key: '"graduation"' },
	{ label: __( 'Average', 'learnpress-gradebook' ), key: '"average"' },
];

const HEADERS_FULL = [
	{ label: __( 'User ID', 'learnpress-gradebook' ), key: '"user_id"' },
	{ label: __( 'Username', 'learnpress-gradebook' ), key: '"user_nicename"' },
	{ label: __( 'Email', 'learnpress-gradebook' ), key: '"user_email"' },
	{ label: __( 'Start time', 'learnpress-gradebook' ), key: '"start_time"' },
	{ label: __( 'End time', 'learnpress-gradebook' ), key: '"end_time"' },
	{ label: __( 'Status', 'learnpress-gradebook' ), key: '"graduation"' },
	{ label: __( 'Average', 'learnpress-gradebook' ), key: '"average"' },
];

export default function ExportStudent( { courseID } ) {
	const [ loading, setLoading ] = useState( false );
	const [ fullLoading, setFullLoading ] = useState( false );

	const getDataExport = async () => {
		setLoading( true );

		try {
			const response = await apiFetch( {
				path: addQueryArgs( 'lp-gradebook/export-students', { courseID } ),
				method: 'GET',
			} );
			const data = await response.data;

			const dataForm = await generateCSVDataFromTable( HEADERS, data );
			const name = await generateCSVFileName( 'gradebook', { list: 'students' } );
			downloadCSVFile( name, dataForm );

			setLoading( false );
		} catch ( error ) {
			console.log( error.message );
			setLoading( false );
		}
	};

	const getDataExportFull = async () => {
		setFullLoading( true );

		try {
			const response = await getRequest( [], [], courseID, 10, 1 );

			if ( ! response ) {
				setFullLoading( false );
				return;
			}

			const { headerData, dataList } = response;

			const dataForm = generateCSVDataFromTable( [ ...HEADERS_FULL, ...headerData ], dataList );
			const name = generateCSVFileName( 'gradebook', { list: 'students' } );
			downloadCSVFile( name, dataForm );

			setFullLoading( false );
		} catch ( error ) {
			console.log( error.message );
			setFullLoading( false );
		}
	};

	async function getRequest( headerData, dataList, courseID, limit, offset ) {
		try {
			const response = await apiFetch( {
				path: addQueryArgs( 'lp-gradebook/export-students-full', { courseID, limit, offset } ),
				method: 'GET',
			} );

			const { status, data, header, count } = response;

			if ( status === 'success' ) {
				headerData = [ ...headerData, ...header ];
				dataList = [ ...dataList, ...data ];

				if ( count > limit * offset ) {
					return getRequest( headerData, dataList, courseID, limit, offset + 1 );
				}

				return { headerData, dataList };
			}
			return false;
		} catch ( error ) {
			return false;
		}
	}

	return (
		<>
			<Button
				className="learnpress-gradebook__export__button"
				isSecondary
				variant="secondary"
				onClick={ getDataExport }
				icon={ loading ? <Spinner /> : download }
				iconSize={ 20 }
				text={ __( 'Export CSV', 'learnpress-gradebook' ) }
			/>

			<Button
				className="learnpress-gradebook__export__button"
				isSecondary
				variant="secondary"
				onClick={ getDataExportFull }
				icon={ fullLoading ? <Spinner /> : download }
				iconSize={ 20 }
				text={ __( 'Export Full CSV', 'learnpress-gradebook' ) }
			/>
		</>
	);
}
