
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Link} from 'react-router-dom';
import './Nav.css';

class Nav extends Component {
  render() {
    let {patterns, tags, refresh} = this.props;
    var navType = 'type';
    let theseTags = tags.filter(t => t.type === navType);
    return (
      <div className="Nav">
        <a className="Nav-refresh" onClick={refresh}>Refresh</a>
        <ul className="Nav-tags">
          {theseTags.map(tag => {
            let thesePatterns = patterns.filter(p => p.tags.indexOf(tag.id) !== -1);
            return (<TagNav key={tag.id} tag={tag} patterns={thesePatterns} />)
          })}
        </ul>
      </div>
    )
  }
};

function TagNav(props) {
  let {tag, patterns} = props;
  return (
    <li>
      {tag.name}
      <ul>
        {patterns.map(p => (
          <li key={`tag-${tag.id}-${p.id}`}><Link to={`/pattern/${p.id}`}>{p.name}</Link></li>
        ))}
      </ul>
    </li>
  );
}



const mapStateToProps = (state) => {
  return {
    patterns: state.patterns,
    tags: state.tags
  }
}

export default connect(mapStateToProps)(Nav);