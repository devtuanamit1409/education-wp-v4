/* eslint-disable jsx-a11y/no-onchange */
/* eslint-disable jsx-a11y/anchor-is-valid */
/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Button, Flex, FlexItem, TextControl, SelectControl } from '@wordpress/components';

import ExportItems from './export/items';
import HomeChart from './HomeChart';
import ItemsTable from './ItemsTable';
import Skeleton from './skeleton';
import Breadcrumbs from './Breadcrumbs';

export default function Items( { screen, courseID, setScreen, studentID, setQuizzID } ) {
	const [ data, setData ] = useState( '' );
	const [ submit, setSubmit ] = useState( false );
	const [ itemType, setItemsType ] = useState( '' );
	const [ status, setStatus ] = useState( '' );
	const [ postTitle, setPostsTitle ] = useState();
	const [ page, setPage ] = useState( 1 );
	const [ pagination, setPagination ] = useState( 1 );
	const [ studentName, setStudentName ] = useState( '' );
	const [ titleCourse, settitleCourse ] = useState( '' );
	const [ loading, setLoading ] = useState( false );

	useEffect( () => {
		try {
			async function getResponse() {
				const response = await apiFetch( {
					path: addQueryArgs( 'lp-gradebook/get-items', {
						studentID,
						courseID,
						itemType,
						page,
						limit: 10,
						status,
						postTitle,
					} ),
					method: 'GET',
				} );
                console.log( response );
				setData( response.data );
				setStudentName( response.data.studentName );
				settitleCourse( response.data.titleCourse );
				setSubmit( false );
				setLoading( false );
				setPagination( response?.data?.total_posts / 10 || 1 );
			}

			getResponse();
		} catch ( e ) {
			setData( '' );
			setSubmit( false );
			setLoading( false );
		}
	}, [ studentID, courseID, submit, page ] );

	let itemTypes = data?.types && Object.keys( data.types ).map( ( key ) => {
		return {
			label: data.types[ key ],
			value: key,
		};
	} );

	itemTypes = itemTypes ? [ { label: __( 'Type', 'learnpress-gradebook' ), value: '' }, ...itemTypes ] : [];

	return (
		<div>
			<Breadcrumbs titleCourse={ titleCourse } studentName={ studentName } screen={ screen } setScreen={ setScreen } />

			<HomeChart courseID={ courseID } />

			<Flex>
				<FlexItem>
					<ExportItems setPostsTitle={ setPostsTitle } studentID={ studentID } courseID={ courseID } data={ data } />
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
								value={ postTitle }
								onChange={ ( value ) => setPostsTitle( value ) }
								placeholder={ __( 'Search lesson, quiz…', 'learnpress-gradebook' ) }
							/>
							<SelectControl
								style={ { height: 36 } }
								label={ null }
								value={ itemType }
								onChange={ ( value ) => setItemsType( value ) }
								options={ itemTypes }
							/>
							<SelectControl
								style={ { height: 36 } }
								label={ null }
								value={ status }
								onChange={ ( value ) => setStatus( value ) }
								options={ [
									{ label: __( 'Status', 'learnpress-gradebook' ), value: '' },
									{ label: __( 'Started', 'learnpress-gradebook' ), value: 'started' },
									{ label: __( 'Completed', 'learnpress-gradebook' ), value: 'completed' },
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

			{ ! data || submit ? <Skeleton /> : <ItemsTable data={ data } loading={ loading } setLoading={ setLoading }setSubmit={ setSubmit } pagination={ pagination } setPage={ setPage } setScreen={ setScreen } setQuizzID={ setQuizzID } /> }
		</div>
	);
}
