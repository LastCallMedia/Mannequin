
import React, {Component} from 'react';
import {Link} from 'react-router-dom'
import './Search.css'

class SearchForm extends Component {
  constructor(props) {
    super(props)
    this.state = {search: ''}
    this.setSearch = this.setSearch.bind(this);
  }
  setSearch(e) {
    this.setState({search: e.target.value});
  }
  render() {
    const {search} = this.state;
    const {patterns} = this.props;
    const hasSearched = search.length > 0;
    const results = groupResults(convertResults(getResults(search, patterns)))
    return (
      <form className="SearchForm" onFocus={this.showForm} onBlur={this.hideForm}>
        <input type="search" value={search} onChange={this.setSearch} placeholder="Search..." />
        {hasSearched && <SearchResults results={results} />}
      </form>
    )
  }
}

function getResults(searchString, patterns) {
  const _searchString = searchString.toLowerCase();
  return patterns.filter(pattern => {
    return pattern.name.toLowerCase().indexOf(_searchString) !== -1 ||
      (pattern.tags['group'] && pattern.tags['group'].toLowerCase().indexOf(_searchString) !== -1)
  });
}

function convertResults(matchingPatterns) {
  return matchingPatterns.map(pattern => {
    return {
      group: pattern.tags['group'] || 'Unknown',
      key: pattern.id,
      name: pattern.name,
      to: `/pattern/${pattern.id}`
    }
  })
}

function groupResults(matches) {
  return matches.reduce((groups, match) => {
    var gi = groups.findIndex(g => g.name === match.group)
    var thisGroup = gi !== -1 ? groups[gi] : groups[groups.length] = {
      name: match.group,
      matches: []
    }
    thisGroup.matches.push(match)
    return groups;
  }, [])
}

const SearchResults = ({results}) => (
  <ul className="SearchResults menu">
    {results.map(group => (
      <li key={group.name}><span className="menu-text">{group.name}</span>
        <ul className="menu">
          {group.matches.map(match => (
            <li key={match.key}><Link to={match.to}>{match.name}</Link></li>
          ))}
        </ul>
      </li>
    ))}
  </ul>
)

export default SearchForm;