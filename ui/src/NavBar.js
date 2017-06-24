
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import './NavBar.css';
import logo from './logo.svg';

class NavBar extends Component {

  buildTreeFromPatterns(patterns) {
    let tree = {};
    patterns.forEach(pattern => {
      var type = pattern.tags.type || 'atom';
      var group = pattern.tags.group || 'global';
      if(!tree[type]) {
        tree[type] = {};
      }
      if(!tree[type][group]) {
        tree[type][group] = [];
      }
      tree[type][group].push({
        name: pattern.name,
        key: `${type}:${group}:${pattern.id}`,
        path: `/pattern/${pattern.id}`,
        children: [],
      });
    });

    return Object.keys(tree).map(type => {
      return {
        name: type,
        key: type,
        path: `/type/${type}`,
        children: Object.keys(tree[type]).map(group => {
          return {
            name: group,
            key: `${type}:${group}`,
            path: `/type/${type}/group/${group}`,
            children: tree[type][group]
          }
        })
      }
    });
  }
  render() {
    const {patterns} = this.props;
    const items = this.buildTreeFromPatterns(patterns);
    return (
      <nav className="MannequinNav top-bar">
        <div className="top-bar-title">
          <strong><img src={logo} alt="Mannequin" /></strong>
        </div>
        <div id="responsive-menu">
          <div className="top-bar-left">
            <MannequinMenu items={items} />
          </div>
          <div className="top-bar-right">
            <ul className="menu">
              <li><AppearingSearchForm /></li>
            </ul>
          </div>
        </div>
      </nav>
    )
  }
}

class AppearingSearchForm extends Component {
  constructor(props) {
    super(props);
    this.state = {showing: false};
    this.showForm = this.showForm.bind(this);
  }
  showForm() {
    this.setState({showing: true});
  }
  render() {
    const {showing} = this.state;
    return (
      <div className="AppearingSearchForm">
        <a className="AppearingSearchButton" onClick={this.showForm}>Search...</a>
        {showing && <SearchForm/>}
      </div>
    )
  }
}

class SearchForm extends Component {
  render() {
    return (
      <form className="SearchForm">
        <input type="search" id="search-input" />

        <ul className="results no-bullet">
          <li>
            <a>
              <i className="menu-icon"></i>
              <span className="group">Blocks</span>
              <span className="Pattern">Media Block</span>
            </a>
          </li>
          <li>
            <a>
              <i className="icon-bar"></i>
              <span className="group">Blocks</span>
              <span className="Pattern">Media Block</span>
            </a>
          </li>
          <li>
            <a>
              <i className="icon-bar"></i>
              <span className="group">Blocks</span>
              <span className="Pattern">Media Block</span>
            </a>
          </li>
        </ul>
      </form>
    )
  }
}

function MannequinMenu({items}) {
  return (
    <ul className="horizontal menu main-menu MenuList l1">
      {items.map(i => (
        <MenuItem key={i.key} item={i} className="l1">
          {i.children.length > 0 &&
            <ul className="vertical menu MenuList l2">
              {i.children.map(ii => (
                <CollapsibleSection key={ii.key} item={ii}>
                  {ii.children.length > 0 &&
                    <ul className="vertical menu MenuList l3">
                      <MenuItem key={ii.key} item={ii} className="l3" />
                      {ii.children.map(iii => (
                        <MenuItem key={iii.key} item={iii} className="l3" />
                      ))}
                    </ul>
                  }
                </CollapsibleSection>
              ))}
            </ul>
          }
        </MenuItem>
      ))}
    </ul>
  )
}

class MenuItem extends Component {
  constructor(props) {
    super(props)
    this.state = {focusing: false};
    this.handleFocus = this.handleFocus.bind(this);
    this.handleBlur = this.handleBlur.bind(this);
  }
  handleFocus(e) {
    this.setState({focusing: true})
  }
  handleBlur(e) {
    console.log('blurring');
    this.setState({focusing: false})
  }
  render() {
    const {item, children, className=''} = this.props
    const focusClass = this.state.focusing ? 'focusing' : '';
    return (
      <li className={`MenuItem ${className} ${focusClass}`} onFocus={this.handleFocus} onBlur={this.handleBlur}>
        <Link to={item.path}>{item.name}</Link>
        {children}
      </li>
    )
  }
}
function MenuItem({item, children, className=''}) {
  return (
    <li className={`MenuItem ${className}`}>
      <Link to={item.path}>{item.name}</Link>
      {children}
    </li>
  )
}

class CollapsibleSection extends Component {
  constructor(props) {
    super(props);
    this.state = {open: false};
    this.handleToggle = this.handleToggle.bind(this);
  }
  handleToggle(e) {
    this.setState(s => {
      return {open: !s.open}
    });
    e.preventDefault();
  }
  render() {
    const {item, children} = this.props;
    const statusClass = this.state.open ? 'open' : 'closed';

    return (
      <li className={`CollapsibleSection ${statusClass}`}>
        <a onClick={this.handleToggle}>
          <span className="title">{item.name}</span>
          <span className="indicator" onClick={this.handleToggle}>{this.state.open ? '-' : '+'}</span>
        </a>
        {children}
      </li>
    );
    return <MenuItem item={item} className={statusClass} onClick={this.handleToggle}>{children}</MenuItem>
  }
}

export default NavBar;