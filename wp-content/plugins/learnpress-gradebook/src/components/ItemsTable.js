/* eslint-disable jsx-a11y/anchor-is-valid */

import { __ } from '@wordpress/i18n';
import { Spinner, Notice } from '@wordpress/components';
import ReactPaginate from 'react-paginate';

import { setLink } from './Breadcrumbs';

export default function ItemsTable( { data, loading, setLoading, pagination, setPage, setScreen, setQuizzID } ) {
	return (
		<div>
			{ data.posts && data.posts.length > 0 ? (
				<>
					<table className={ `wp-list-table widefat fixed striped table-view-list student ${ loading ? 'lp-gradebook-spinner' : '' }` }>
						<thead>
							<tr className="table-headers">
								<th></th>
								<th>{ __( 'Title', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Type', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Start time', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'End time', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Graduation', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Status', 'learnpress-gradebook' ) }</th>
							</tr>
						</thead>
						<tbody>
							{ data.posts.map( ( ele, index ) => {
								return (
									<tr key={ index }>
										<td>{ index + 1 }</td>
										<td>
											{ ele?.type?.raw === 'lp_quiz' ? (
												<a href="" onClick={ ( e ) => {
													e.preventDefault();
													setScreen( 3 );
													setQuizzID( ele.id );

													setLink( { add: [
														{ param: 'screen', value: '3' },
														{ param: 'quiz_id', value: ele.id },
													] } );
												} } dangerouslySetInnerHTML={ { __html: ele.title } } />
											) : (
												<span className ="lp-gradebook-item__title" dangerouslySetInnerHTML={ { __html: ele.title } } />
											) }
										</td>
										<td dangerouslySetInnerHTML={ { __html: ele?.type?.render } } />
										<td>{ ele.start_time }</td>
										<td>{ ele.end_time }</td>
										<td dangerouslySetInnerHTML={ { __html: ele.graduation } } />
										<td dangerouslySetInnerHTML={ { __html: ele?.status?.render } } />
									</tr>
								);
							} ) }
						</tbody>

						{ loading && <Spinner /> }

					</table>

					{ data.total_posts > 10 && (
						<ReactPaginate
							previousLabel="&laquo;"
							nextLabel="&raquo;"
							breakLabel={ '...' }
							breakClassName={ 'break-me' }
							pageCount={ pagination }
							marginPagesDisplayed={ 2 }
							pageRangeDisplayed={ 3 }
							onPageChange={ ( pageNumber ) => {
								setPage( ( pageNumber.selected ) + 1 );
								setLoading( true );
							} }
							containerClassName={ 'pagination' }
							activeClassName={ 'active' }
						/>
					) }
				</>
			) : (
				<Notice status="error" isDismissible={ false }>
					<p>{ __( 'No data available', 'learnpress-gradebook' ) }</p>
				</Notice>
			) }
		</div>
	);
}
