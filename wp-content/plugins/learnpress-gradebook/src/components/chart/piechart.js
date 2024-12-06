import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { Pie } from 'react-chartjs-2';

export default function PieChartStudent( { courseID } ) {
	const [ data, setData ] = useState( '' );
	const [ loading, setLoading ] = useState( false );
	const [ totalEnroll, setTotalEnroll ] = useState( 0 );
	const [ totalFinished, setTotalFinished ] = useState( 0 );

	useEffect( () => {
		setLoading( true );

		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-pie-chart-student', { courseID } ),
					method: 'GET',
				} );

				setTotalEnroll( response.data.datasets.data[ 0 ] ? parseInt( response.data.datasets.data[ 0 ] ) : 0 );
				setTotalFinished( response.data.datasets.data[ 0 ] ? parseInt( response.data.datasets.data[ 1 ] ) : 0 );

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

				setLoading( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
			setLoading( false );
		}
	}, [ courseID ] );

	return (
		<div className="detail-chart">
			<div className="ct-chart">
				<div className={ `${ loading ? 'ct-left detail-chart__loading' : 'ct-left' }` }>
					<Pie data={ data } height={ 160 } />

					{ loading && <Spinner /> }
				</div>
				<div className="ct-right">
					<p>{ __( 'Total students enrolled:', 'learnpress-gradebook' ) } <b>{ totalEnroll }</b></p>
					<p>{ __( 'Total students finished:', 'learnpress-gradebook' ) } <b>{ totalFinished }</b></p>
					<p>{ __( 'Total:', 'learnpress-gradebook' ) } <b>{ totalFinished + totalEnroll }</b></p>
				</div>
			</div>
			<div className="title-chart">
				<h3>{ __( 'Chart 2:', 'learnpress-gradebook' ) }</h3>
				<p>{ __( 'All students registered and finished the course', 'learnpress-gradebook' ) }</p>
			</div>
		</div>
	);
}
