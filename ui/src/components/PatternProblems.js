
import React from 'react';
import cx from 'classnames';

const PatternProblems = ({problems, className}) => {
    return (
        <div className={cx('callout alert', className)}>
            <h3>There were problems found with this pattern!</h3>
            <ul>
                {problems.map((problem, i) => <li key={i}>{problem}</li>)}
            </ul>
        </div>
    )
}

export default PatternProblems;