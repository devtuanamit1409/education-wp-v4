import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Spinner , Notice } from '@wordpress/components';
import Skeleton from '../skeleton';

import { Pie } from 'react-chartjs-2';

export default function ChartQuestion( { courseID, quizzID, studentID } ) {
	const [ data, setData ] = useState( '' );
	const [ loading, setLoading ] = useState( false );

	useEffect( () => {
		setLoading( true );

		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-chart-questions', { courseID, quizzID, studentID } ),
					method: 'GET',
				} );

				if ( response?.data ) {
					setData( {
						labels: response.data.labels,
						datasets: [
							{
								data: response.data.datasets.data,
								backgroundColor: response.data.datasets.backgroundColor,
								borderColor: response.data.datasets.borderColor,
							},
						],
					} );
				}

				setLoading( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
			setLoading( false );
		}
	}, [ quizzID, studentID ] );

	return (
        <>
            { ! loading ? (
    		    <>
                    { data ? (
                        <>
                            <div className={ `${ loading ? 'detail-chart__loading' : '' }` }>
                				<Pie data={ data } height={ 160 } />

                				{ loading && <Spinner /> }
                			</div>

                			<div className="title-chart center">
                				<h3>{ __( 'Chart:', 'learnpress-gradebook' ) }</h3>
                				<p>{ __( 'Total number of true and false questions\'s quiz', 'learnpress-gradebook' ) }</p>
                			</div>
                        </>
                    ) : (
                        <Notice status="warning" isDismissible={ false }>
    					    <p>{ __( 'No data available', 'learnpress-gradebook' ) }</p>
    				    </Notice>
                    ) }
        			
    		    </>
            ) : (
                <Skeleton />
            ) }
        </>
	);
}
