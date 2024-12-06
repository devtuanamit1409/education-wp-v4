/* eslint-disable jsx-a11y/anchor-is-valid */
/* eslint-disable jsx-a11y/click-events-have-key-events */
/* eslint-disable jsx-a11y/no-static-element-interactions */
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import Breadcrumbs from './Breadcrumbs';
import QuesionChart from './QuesionChart';
import QuestionTable from './QuestionTable';
import Skeleton from './skeleton';

export default function Questions( {
	courseID,
	screen,
	setScreen,
	studentID,
	quizzID,
} ) {
	const [ data, setData ] = useState( '' );
	const [ quizTitle, setQuizTitle ] = useState( '' );
	const [ questionTitle, setQuestionTitle ] = useState( '' );
	const [ pagination, setPagination ] = useState( 1 );
	const [ count, setCount ] = useState( '' );
	const [ page, setPage ] = useState( 1 );
	const [ studentName, setStudentName ] = useState( '' );
	const [ courseTitle, setCourseTitle ] = useState( '' );
	const [ loading, setLoading ] = useState( false );
	const [ paginateLoading, setPaginateLoading ] = useState( false );

	useEffect( () => {
		setLoading( true );

		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-questions', {
						courseID,
						quizzID,
						studentID,
						questionTitle,
						limit: 10,
						page,
					} ),
					mothod: 'GET',
				} );
				console.log( response );
				setData( response.data.item );
				setQuizTitle( response.data.quizTitle );
				setCourseTitle( response.data.courseTitle );
				//setResults( response.data.result );
				setStudentName( response.data.studentName );
				//setResultQuiz( Object.keys(response.data.result).length ? JSON.parse( response.data.result.result ) : '' );
				setCount( response.data.count || 1 );
				setPagination( response.data.count / 10 || 1 );
				setLoading( false );
				setPaginateLoading( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
			setLoading( false );
			setPaginateLoading( false );
		}
	}, [ quizzID, studentID, questionTitle, page ] );

	return (
		<div>
			<Breadcrumbs
				titleCourse={ courseTitle }
				studentName={ studentName }
				screen={ screen }
				setScreen={ setScreen }
				quizName={ quizTitle }
			/>

			<QuesionChart
				courseID={ courseID }
				quizzID={ quizzID }
				studentID={ studentID }
			/>
			{ ! data ? (
				<Skeleton />
			) : (
				<QuestionTable
					data={ data }
					pagination={ pagination }
					count={ count }
					setQuestionTitle={ setQuestionTitle }
					questionTitle={ questionTitle }
					courseID={ courseID }
					quizzID={ quizzID }
					studentID={ studentID }
					setPage={ setPage }
					quizTitle={ quizTitle }
					studentName={ studentName }
					loading={ loading }
					paginateLoading={ paginateLoading }
					setPaginateLoading={ setPaginateLoading }
				/>
			) }
		</div>
	);
}
