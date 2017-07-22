
import React from 'react';

const PatternProblems = ({problems}) => {
    return (
        <div className="callout alert">
            <h3>There were problems found with this pattern!</h3>
            <ul>
                {problems.map(problem => <li>{problem}</li>)}
            </ul>
        </div>
    )
}

export default PatternProblems;