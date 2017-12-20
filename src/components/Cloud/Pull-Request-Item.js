import React from 'react';
import PropTypes from 'prop-types';

/**
 * Display status about a single Pull Request.
 *
 * @param {String} date   Date that the Pull Request was opened.
 * @param {Number} id     GitHub Pull Request identifier.
 * @param {String} link   Link to the PUll Request on GitHub.
 * @param {String} status Un-mapped status of the pull request
 * @param {String} title  Title of the Pull Request.
 */
const PullRequestItem = ( { date, id, link, status, statusText, title } ) => {
	return <li className="pull-request-item">
		<div className="pull-request-item__info" >
			<p className="pull-request-item__title">
				<a href={ link }>#{ id }</a> { title }
			</p>
			<p className="pull-request-item__date"><i>{ date }</i></p>
		</div>
		<div className="pull-request-item__status">
			<div className={ `pull-request-item__status-indicator pr-status--${ status }` } />
			{ statusText }
		</div>
	</li>
}

PullRequestItem.defaultProps = {};

PullRequestItem.propTypes = {
	date:   PropTypes.string,
	id:     PropTypes.number,
	link:   PropTypes.string,
	status: PropTypes.string,
	title:  PropTypes.string,
};

export default PullRequestItem;
