import { __ } from '@wordpress/i18n';
import { Button, TextControl, Flex, FlexItem, Notice, Dropdown } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { chevronDown } from '@wordpress/icons';
import ReactPaginate from 'react-paginate';
import ExportQuestion from './export/question';

export default function QuestionTable( {
	data,
	pagination,
	count,
	setQuestionTitle,
	questionTitle,
	courseID,
	quizzID,
	studentID,
	setPage,
	quizTitle,
	loading,
	paginateLoading,
	setPaginateLoading,
} ) {
	const [ search, setSearch ] = useState( questionTitle );

	return (
		<div>
			<Flex align="flex-start" justify="flex-start">
				<FlexItem>
					<ExportQuestion courseID={ courseID } quizzID={ quizzID } studentID={ studentID } />
				</FlexItem>

				<FlexItem>
					<form onSubmit={ ( e ) => {
						e.preventDefault();
						setQuestionTitle( search );
					} }>
						<Flex align="flex-start" justify="flex-start">
							<FlexItem>
								<TextControl
									style={ { height: 36 } }
									label={ null }
									placeholder={ __( 'Search quesion…', 'leanpress-gradebook' ) }
									value={ search }
									onChange={ ( value ) => setSearch( value ) }
								/>
							</FlexItem>

							<FlexItem>
								<Button
									isPrimary
									variant="primary"
									onClick={ () => setQuestionTitle( search ) }
								>
									{ __( 'Search', 'leanpress-gradebook' ) }
								</Button>
							</FlexItem>
						</Flex>
					</form>
				</FlexItem>
			</Flex>
			{ Object.keys( data ).length !== 0 ? (
				<>
					<table className={ `wp-list-table widefat fixed striped table-view-list student ${ loading ? 'lp-gradebook-spinner' : '' }` }>
						<thead>
							<tr>
								<th></th>
								<th>{ __( 'Title', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Type', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Correct', 'learnpress-gradebook' ) }</th>
								<th>{ __( 'Retake Detail', 'learnpress-gradebook' ) }</th>
							</tr>
						</thead>
						<tbody>
							{ Object.values( data ).map( ( ele, index ) => {
								return (
									<tr key={ index }>
										<td>{ index + 1 }</td>
										<td dangerouslySetInnerHTML={ { __html: ele.title || '' } } />
										<td dangerouslySetInnerHTML={ { __html: ele.type } } />
										<td>{ ele.correct || '' }</td>
										<td>
											<div className="number_retake">
												<span className="count_retake">
													({ ele.retake_count })
													{ ele.retake_count > 0 ? (
														<Dropdown
															className="lp-gradebook_my_container"
															contentClassName="lp-gradebook_my_popover_content"
															position="bottom right"
															renderToggle={ ( { isOpen, onToggle } ) => (
																<Button
																	variant="link"
																	onClick={ onToggle }
																	aria-expanded={ isOpen }
																	isLink
																	icon={ chevronDown }
																	iconSize={ 20 }
																	iconPosition="right"
																	text={ `${ __( 'View', 'learnpress-gradebook' ) }` }
																>
																</Button>
															) }
															renderContent={ () =>
																<div className="lp-gradebook_retake">
																	{ ele.retake ? (
																		<>
																			{ ele.retake.map( ( val, index ) => {
																				return (
																					<>
																						<div className="lp-gradebook_retake_detail">
																							<span>{ index + 1 }</span>
																							<span>{ val }</span>
																						</div>
																					</>
																				);
																			} ) }
																		</>
																	) : '' }
																</div>
															}
														/>
													) : '' }
												</span>
											</div>
										</td>
									</tr>
								);
							} ) }
						</tbody>
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
								setPaginateLoading( true );
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
