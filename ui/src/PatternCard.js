
import React from 'react';
import {Link} from 'react-router-dom';
import './PatternCard.css';

export function PatternCard(props) {
  let {pattern} = props;
  return (
    <Link className="PatternCard" to={`/pattern/${pattern.id}`}>
      {/*<PatternBadge format={pattern.tags['format']} status={pattern.tags['status']} />*/}
      <h4 className="PatternCard-title">{pattern.name}</h4>
      {pattern.description.length > 0 && <div className="PatternCard-info">
        <p>{pattern.description}</p>
      </div>}
    </Link>
  )
}

export function PatternBadge(props) {
  let {format, status} = props;
  return <span className={`PatternFormatBadge status-${status}`}>{format ? format[0] : '?'}</span>
}