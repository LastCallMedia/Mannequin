
import React, {Component} from 'react';
import {Link} from 'react-router-dom'
import './Search.css'

class SearchForm extends Component {
  constructor(props) {
    super(props)
    this.state = {search: '', shown: false}
    this.showForm = this.showForm.bind(this);
    this.hideForm = this.hideForm.bind(this);
    this.setSearch = this.setSearch.bind(this);
  }
  showForm() {
    this.setState({shown: true}, () => this.input.focus())
  }
  hideForm() {
    this.setState({shown: false})
  }
  setSearch(e) {
    this.setState({search: e.target.value});
  }
  render() {
    const {shown, search} = this.state;
    const {patterns} = this.props;
    return (
      <form className="SearchForm" onFocus={this.showForm} onBlur={this.hideForm}>
        <a onClick={this.showForm} className="AppearingSearchButton">Search...</a>
        <div className={`appearing ${shown ? 'shown' : 'hiding'}`}>
          <input type="search" value={search} ref={c => this.input = c} onChange={this.setSearch} />
          <SearchResults search={search} patterns={patterns} />
        </div>
      </form>
    )
  }
}

class SearchResults extends Component {
  getResults(search) {
    const {patterns} = this.props;

    var results = [];
    var tree = {};

    if(search.length < 1) {
      return results;
    }

    patterns.forEach(p => {
      const {type: t, group: g} = p.tags;
      const groupMatches = g.toLowerCase().indexOf(search) !== -1;
      const patternMatches = p.name.toLowerCase().indexOf(search) !== -1;

      if(!patternMatches && !groupMatches) {
        return;
      }

      const groupKey = `${t}:${g}`
      if(!tree[groupKey]) {
        tree[groupKey] = {
          name: g,
          to: `/type/${t}/group/${g}`,
          matches: groupMatches,
          patterns: []
        }
      }
      if(patternMatches) {
        tree[groupKey].patterns.push({
          name: p.name,
          to: `/patterns/${p.id}`
        })
      }
    });

    return Object.keys(tree).map(k => {
      return tree[k];
    })
  }
  render() {
    const {search,} = this.props;
    const results = this.getResults(search);

    return (
      <div className="SearchResults">
        <ul className="no-bullet">
          {results.map(leaf => (
            <li className={`group-result ${leaf.matches ? 'match' : 'no-match'}`}>
              <Link to={leaf.to}>{leaf.name}</Link>
              {leaf.patterns.length > 0 &&
              <ul className="no-bullet">
                {leaf.patterns.map(p => (
                  <li className="pattern-result">
                    <Link to={p.to}>{p.name}</Link>
                  </li>
                ))}
              </ul>
              }
            </li>
          ))}
        </ul>
      </div>

    )
  }
}

export default SearchForm;