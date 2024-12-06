/* eslint-disable @wordpress/i18n-no-variables */
/* eslint-disable jsx-a11y/no-onchange */
import { __ } from '@wordpress/i18n';
import { useState, useEffect, useMemo } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Button, Flex, FlexItem, TextControl, SelectControl } from '@wordpress/components';

import ExportStudent from './export/student';
import HomeChart from './HomeChart';
import HomeTable from './HomeTable';
import Skeleton from './skeleton';

export default function Home( { courseID, setScreen, setStudent } ) {
	const [ data, setData ] = useState( '' );
	const [ page, setPage ] = useState( 1 );
	const [ pagination, setPagination ] = useState( 1 );
	const [ titleCourse, setTitleCourse ] = useState( '' );
	const [ count, setCount ] = useState( 0 );
	const [ submit, setSubmit ] = useState( false );
	const [ userName, setUserName ] = useState();
	const [ average, setAverage ] = useState( '' );
	const [ graduation, setGradution ] = useState( '' );
	const [ loading, setLoading ] = useState( false );

	useEffect( () => {
		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-students', { courseID, limit: 10, page, graduation, average, userName } ),
					method: 'GET',
				} );

				if ( response?.status === 'success' ) {
					setData( response );
					setTitleCourse( response?.data?.title || '' );
					setCount( response?.data?.count || 0 );
					setPagination( response?.data?.count / 10 || 1 );
				}

				setLoading( false );
				setSubmit( false );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
		}
	}, [ courseID, page, submit ] );

	return (
		<div>
			<HomeChart courseID={ courseID } />

			<Flex>
				<FlexItem>
					<Flex>
						<ExportStudent courseID={ courseID } />
						<h2 className="title-head" dangerouslySetInnerHTML={ { __html: titleCourse } } />
					</Flex>
				</FlexItem>
				<FlexItem>
					<form onSubmit={ ( e ) => {
						e.preventDefault();
						setSubmit( true );
					} }>
						<Flex align={ 'flex-start' }>
							<TextControl
								style={ { height: 36 } }
								label={ null }
								value={ userName }
								onChange={ ( value ) => setUserName( value ) }
								placeholder={ __( 'Search student…', 'learnpress-gradebook' ) }
							/>
							{ /* <TextControl
							style={ { height: 36 } }
							label={ null }
							value={ average }
							onChange={ ( value ) => setAverage( value ) }
							placeholder={ __( 'Press % Average…', 'learnpress-gradebook' ) }
						/> */ }
							<SelectControl
								style={ { height: 36 } }
								label={ null }
								value={ graduation }
								onChange={ ( value ) => setGradution( value ) }
								options={ [
									{ label: __( 'Status', 'learnpress-gradebook' ), value: '' },
									{ label: __( 'Passed', 'learnpress-gradebook' ), value: 'passed' },
									{ label: __( 'Failed', 'learnpress-gradebook' ), value: 'failed' },
									{ label: __( 'In Progress', 'learnpress-gradebook' ), value: 'in-progress' },
								] }
							/>
							<Button
								isPrimary
								variant="primary"
								onClick={ () => setSubmit( true ) }
								style={ { height: 36 } }
							>
								{ __( 'Filters', 'learnpress-gradebook' ) }
							</Button>
						</Flex>
					</form>
				</FlexItem>
			</Flex>

			{ ! data || submit ? <Skeleton /> : <HomeTable data={ data } loading={ loading } setLoading={ setLoading }count={ count } pagination={ pagination } setPage={ setPage } setScreen={ setScreen } setStudent={ setStudent } /> }
		</div>
	);
}
