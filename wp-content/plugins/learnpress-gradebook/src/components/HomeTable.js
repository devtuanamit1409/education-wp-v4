/* eslint-disable jsx-a11y/anchor-is-valid */

import { __ } from '@wordpress/i18n';
import { Spinner, Notice } from '@wordpress/components';
import ReactPaginate from 'react-paginate';

import { setLink } from './Breadcrumbs';

export default function HomeTable( { data, loading, setLoading, count, pagination, setPage, setScreen, setStudent } ) {
	return (
		<div>
			{ data?.data?.result?.length > 0 ? (
				<>
					<table className={ `wp-list-table widefat fixed striped table-view-list student ${ loading ? 'lp-gradebook-spinner' : '' }` }>
						<thead>
							<tr>
								<th className="index"></th>
								<th>{ __( 'Student', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Email', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Start time', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Average', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Status', 'learnpress-gradebook' ) }</th>
							</tr>
						</thead>

						<tbody>
							{ data.data.result.map( ( ele, index ) => {
								let result = 0;

								if ( ! ele.result ) {
									result = 0;
								} else if ( Number.isInteger( ele.result ) ) {
									result = parseInt( ele.result );
								} else {
									result = parseFloat( ele.result ).toFixed( 2 );
								}

								return (
									<tr key={ index }>
										<td>{ index + 1 }</td>
										<td><a href="" onClick={ ( e ) => {
											e.preventDefault();
											setScreen( 2 );
											setStudent( ele.user_id );

											setLink( { add: [
												{ param: 'screen', value: '2' },
												{ param: 'student', value: ele.user_id },
											] } );
										} } dangerouslySetInnerHTML={ { __html: ele.user_nicename } } /></td>
										<td dangerouslySetInnerHTML={ { __html: ele.user_email } } />
										<td>{ ele.start_time }</td>
										<td>{ `${ result }%` }</td>
										<td dangerouslySetInnerHTML={ { __html: ele.graduation } } />
									</tr>
								);
							} ) }
						</tbody>

						{ loading && <Spinner /> }

					</table>

					{ count > 10 && (
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
