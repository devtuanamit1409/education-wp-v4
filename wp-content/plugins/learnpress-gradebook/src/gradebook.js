
import './index.scss';

import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Spinner } from '@wordpress/components';

import Items from './components/items';
import Home from './components/home';
import Questions from './components/questions';

function Index() {
	const [ student, setStudent ] = useState( '' );
	const [ courseID, setCourseID ] = useState( '' );
	const [ quizzID, setQuizzID ] = useState();
	const [ screen, setScreen ] = useState( 1 );

	useEffect( () => {
		function setStateInLink() {
			const urlString = window.location.href;
			const url = new URL( urlString );
			const courseIdUrl = url.searchParams.get( 'course_id' );
			const screenUrl = url.searchParams.get( 'screen' );
			const studentIdUrl = url.searchParams.get( 'student' );
			const quizzIdUrl = url.searchParams.get( 'quiz_id' );

			setScreen( screenUrl ? parseInt( screenUrl ) : 1 );

			if ( quizzIdUrl ) {
				setQuizzID( quizzIdUrl );
			}

			if ( studentIdUrl ) {
				setStudent( studentIdUrl );
			}

			if ( courseIdUrl ) {
				setCourseID( courseIdUrl );
			}
		}
		setStateInLink();

		document.defaultView.addEventListener( 'popstate', setStateInLink );
		document.defaultView.addEventListener( 'pushstate', setStateInLink );
		document.defaultView.addEventListener( 'replacestate', setStateInLink );

		return () => {
			document.defaultView.removeEventListener( 'popstate', setStateInLink );
			document.defaultView.removeEventListener( 'pushstate', setStateInLink );
			document.defaultView.removeEventListener( 'replacestate', setStateInLink );
		};
	}, [ student ] );

	if ( ! courseID ) {
		return (
			<Spinner />
		);
	}

	return (
		<div className="learnpress-gradebook">
			<>
				{ screen === 1 && (
					<Home
						courseID={ courseID }
						setScreen={ setScreen }
						setStudent={ setStudent }
					/>
				) }

				{ screen === 2 && (
					<Items
						courseID={ courseID }
						screen={ screen }
						setScreen={ setScreen }
						studentID={ student }
						setQuizzID={ setQuizzID }
					/>
				) }

				{ screen === 3 && (
					<Questions
						courseID={ courseID }
						screen={ screen }
						setScreen={ setScreen }
						studentID={ student }
						quizzID={ quizzID }
					/>
				) }
			</>
		</div>
	);
}

wp.element.render( <Index />, document.getElementById( 'learnpress-gradebook-react' ) );
