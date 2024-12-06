/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
/* eslint-disable jsx-a11y/anchor-is-valid */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

export function setLink( { add, remove } ) {
	const url = new URL( window.location );

	if ( add ) {
		add.forEach( ( ele ) => {
			url.searchParams.set( ele.param, ele.value );
		} );
	}

	if ( remove ) {
		remove.forEach( ( ele ) => {
			url.searchParams.delete( ele );
		} );
	}

	window.history.pushState( {}, '', url.href );
}

export default function Breadcrumbs( { screen, setScreen, titleCourse, studentName, quizName } ) {
	return (
		<div>
			<div>
				<Button isSecondary variant="secondary" onClick={ () => {
					setScreen( screen - 1 );

					if ( screen === 2 ) {
						setLink( { remove: [ 'screen', 'student' ] } );
					} else if ( screen === 3 ) {
						setLink( { remove: [ 'quiz_id' ] } );
					}
				} }>
					<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg>
					{ __( 'Back', 'learnpress-gradebook' ) }
				</Button>
			</div>

			{ titleCourse ? (
				<nav aria-label="breadcrumb">
					<ol className="breadcrumb">
						<li className="breadcrumb-item">
							<a onClick={ ( e ) => {
								e.preventDefault();
								setLink( { remove: [ 'screen', 'student', 'quiz_id' ] } );

								setScreen( 1 );
							} } dangerouslySetInnerHTML={ { __html: titleCourse } } />
						</li>

						{ screen === 2 ? (
							<li className="breadcrumb-item active" aria-current="page" dangerouslySetInnerHTML={ { __html: studentName } } />
						) : (
							<>
								<li className="breadcrumb-item">
									<a onClick={ ( e ) => {
										e.preventDefault();
										setLink( { remove: [ 'quiz_id' ], add: [ { param: 'screen', value: 2 } ] } );

										setScreen( 2 );
									} } dangerouslySetInnerHTML={ { __html: studentName } } />
								</li>
								<li className="breadcrumb-item active" aria-current="page" dangerouslySetInnerHTML={ { __html: quizName } } />
							</>
						) }
					</ol>
				</nav>
			) : (
				<ul className="lp-skeleton-animation"><li></li></ul>
			) }
		</div>
	);
}
