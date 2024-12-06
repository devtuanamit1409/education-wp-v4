
import { __ } from '@wordpress/i18n';
import { useMemo, useState } from '@wordpress/element';
import ChartQuestion from './chart/questions';
import QuestionAttachment from './QuestionAttempted';
import { Button} from '@wordpress/components';
import { external, close } from '@wordpress/icons';
import Skeleton from './skeleton';

export default function QuesionChart( { courseID, studentID, quizzID } ) {

    const [ show, setShow ] = useState( true );
	const ChartHTML = useMemo( () => {
		return (
			<>
                <div className="chart_sc chart_sc3">
                    <div className="quiz_detail">
                        <QuestionAttachment studentID ={ studentID } quizzID={ quizzID } />
                    </div>  
                    <div className="detail-chart">
                        <ChartQuestion courseID ={ courseID } studentID={ studentID } quizzID={ quizzID } />
                    </div>
    	    	</div>
            </>
		);
	}, [ studentID, quizzID, courseID ] );

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
