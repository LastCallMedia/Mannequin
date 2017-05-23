
import React from 'react';
import {Link} from 'react-router-dom';
import './PatternCard.css';

export function PatternCard(props) {
  let {pattern} = props;
  return (
    <Link className="PatternCard" to={`/pattern/${pattern.id}`}>
      <PatternBadge format={pattern.tags['format']} status={pattern.tags['status']} />
      <h5 className="PatternCard-title">{pattern.name}</h5>
    </Link>
  )
}

export function PatternBadge(props) {
  let {format, status} = props;
  return <span className={`PatternFormatBadge status-${status}`}>{format ? format[0] : '?'}</span>
}