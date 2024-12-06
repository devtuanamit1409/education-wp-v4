import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Button, ButtonGroup, Spinner } from '@wordpress/components';
import { Bar } from 'react-chartjs-2';

const BUTTON_LISTS = [
	{ label: __( 'Last 7 days', 'learnpress-gradebook' ), value: 'last-7-days' },
	{ label: __( 'Last 30 days', 'learnpress-gradebook' ), value: 'last-30-days' },
	{ label: __( 'Last 12 months', 'learnpress-gradebook' ), value: 'last-12-month' },
];

const CHART_OPTIONS = {
	scales: {
		yAxes: [
			{
				ticks: {
					beginAtZero: true,
				},
			},
		],
	},
};

export default function ChartStudent( { courseID } ) {
	const [ data, setData ] = useState( {} );
	const [ loading, setLoading ] = useState( false );
	const [ typeChart, setTypeChart ] = useState( BUTTON_LISTS[ 0 ].value );

	const onChangeTypeChart = ( type ) => {
		if ( type === typeChart ) {
			return false;
		}

		setTypeChart( type );
	};

	useEffect( () => {
		if ( data[ typeChart ] ) {
			return;
		}

		setLoading( true );

		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-chart-student', { courseID, typeChart } ),
					method: 'GET',
				} );

				const dataSet = response.data.datasets.map( ( ele ) => ( {
					label: ele.label,
					data: ele.data,
					fill: false,
					backgroundColor: ele.backgroundColor,
					borderColor: ele.borderColor,
				} ) );

				setData( { ...data, [ typeChart ]: {
					labels: response.data.labels,
					datasets: dataSet,
				} } );

				setLoading( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );

			setLoading( false );
		}
	}, [ courseID, typeChart ] );

	return (
		<div className="detail-chart">
			<div style={ { marginBottom: 20 } }>
				<ButtonGroup>
					{ BUTTON_LISTS.map( ( ele ) => {
						return (
							<Button
								key={ ele.value }
								onClick={ () => onChangeTypeChart( ele.value ) }
								isPrimary={ typeChart === ele.value }
							>
								{ ele.label }
							</Button>
						);
					} ) }
				</ButtonGroup>
			</div>

			<div>
				<div className={ `${ loading ? 'detail-chart__loading' : '' }` }>
					<Bar data={ data[ typeChart ] || {} } options={ CHART_OPTIONS } height={ 160 } />
					{ loading && <Spinner /> }
				</div>

				<div className="title-chart">
					<h3>{ __( 'Chart 1:', 'learnpress-gradebook' ) }</h3>
					<p>{ __( 'Number of students registered and finished the course by date', 'learnpress-gradebook' ) }</p>
				</div>
			</div>
		</div>
	);
}
