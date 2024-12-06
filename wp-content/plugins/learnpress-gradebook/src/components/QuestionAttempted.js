
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Notice } from '@wordpress/components';
import Skeleton from './skeleton';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export default function QuestionAttachment( { studentID, quizzID } ) {
    const [ loading, setLoading ] = useState( false );
    const [ results, setResults ] = useState( '' );
	const [ resultQuiz, setResultQuiz ] = useState( '' );

    useEffect( () => {
        setLoading( true );
		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/questions-attempted', { studentID, quizzID } ),
					method: 'GET',
				} );
                
				setResults( response.data.result );
                setResultQuiz( Object.keys(response.data.result).length ? JSON.parse( response.data.result.result ) : '' );
				setLoading( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
			setLoading( false );
		}

	}, [ studentID, quizzID ] );
    
	return (
        <div>
        <>
            { ! loading ? (
    		    <>
                  { Object.keys( results ).length > 0 ? (
                        <>
                            <h2>{ __( 'Result Quiz', 'learnpress-gradebook' ) } </h2>
                            <p>{ __( 'Date finish:', 'learnpress-gradebook' ) }<strong> { results[ 'end-time' ] }</strong></p>
                            <p>{ __( 'Questions:', 'learnpress-gradebook' ) } <strong>{ resultQuiz.question_correct } / { resultQuiz.question_count }</strong></p>
                            <p>{ __( 'Time spent:', 'learnpress-gradebook' ) } <strong>{ resultQuiz.time_spend }</strong></p>
                            <p>{ __( 'Marks:', 'learnpress-gradebook' ) } <strong>{ resultQuiz.user_mark } / { resultQuiz.mark }</strong></p>
                            <p>{ __( 'Passing grade:', 'learnpress-gradebook' ) } <strong>{ resultQuiz.passing_grade }</strong></p>
                            <p>{ __( 'Result:', 'learnpress-gradebook' ) } <strong>{ `${ resultQuiz?.result?.toFixed( 2 ) || 0 }%` }</strong></p>
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
        </div>
	);
}
