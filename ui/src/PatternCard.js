
import React from 'react';
import {Link} from 'react-router-dom';

export function PatternCard(props) {
  let {pattern} = props;
  return (
    <Link className="card" to={`/pattern/${pattern.id}`}>
      <div className="card-divider">
        <PatternBadge format={pattern.tags['format']} status={pattern.tags['status']} />
        <h5>{pattern.name}</h5>
      </div>
      {pattern.description.length > 0 && <div className="card-section">
        <p><small>{pattern.description}</small></p>
      </div>}
    </Link>
  )
}

export function PatternBadge(props) {
  const {format, status} = props;
  let statusClass = 'secondary';
  switch(status) {
    case 'inprogress':
      statusClass = 'warning';
      break;
    case 'complete':
      statusClass = 'success';
      break;
    default:
      break;
  }
  return <span className={`badge float-right ${statusClass}`}>{format ? format[0] : '?'}</span>
}