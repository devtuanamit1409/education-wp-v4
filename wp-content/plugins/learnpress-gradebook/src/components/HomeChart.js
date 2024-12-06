import { __ } from '@wordpress/i18n';
import { useState, useMemo } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { external, close } from '@wordpress/icons';

import ChartStudent from './chart/student';
import PieChartStudent from './chart/piechart';

export default function HomeChart( { courseID } ) {
	const [ show, setShow ] = useState( true );

	const ChartHTML = useMemo( () => {
		return (
			<>
				<div className="chart_sc">
					<ChartStudent courseID={ courseID } />
					<PieChartStudent courseID={ courseID } />
				</div>
			</>
		);
	}, [ courseID ] );

	return (
		<>
			{ ! show && ChartHTML }
			<div style={ { marginBottom: 20 } }>
				<Button
					isPrimary
					variant="primary"
					onClick={ () => setShow( ! show ) }
					icon={ show ? external : close }
					iconSize={ 20 }
				>
					{ show ? __( 'View Chart', 'learnpress-gradebook' ) : __( 'Close Chart', 'learnpress-gradebook' ) }
				</Button>
			</div>
		</>
	);
}
