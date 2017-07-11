
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import FluidContainer from 'react-fluid-container';
import './NavBar.css';
import logo from './svg/logo.svg';

export const TopBar = ({toggleNav}) => {
  return (
    <nav className="MannequinTopBar">
      <div className="inner">
        <Link to="/" className="logo"><strong><img className="main-logo" src={logo} alt="Mannequin" /></strong></Link>
        <div className="title"></div>
        <button className="drawer-toggle opener" onClick={toggleNav}>Navigation <i className="menu-icon"></i></button>
      </div>
    </nav>
  )
}

export class NavDrawer extends Component {
  constructor(props) {
    super(props)
    this.state = {filter: ''}
    this.handleFilterChange = this.handleFilterChange.bind(this)
  }
  handleFilterChange(e) {
    this.setState({filter: e.target.value})
  }
  render() {
    const {open, toggleNav, patterns} = this.props;
    const {filter} = this.state;
    const tree = buildTree(filterPatterns(filter, patterns))
    const menuSettings = {
      collapsible: false,
      className: 'l1',
      itemClassName: 'l1',
      children: {
        collapsible: true,
        className: 'l2',
        itemClassName: 'l2',
        children: {
          collapsible: false,
          className: 'l3',
          itemClassName: 'l3',
        }
      }
    }

    return (
      <nav className={`NavDrawer ${open ? 'open' : 'closed'}`}>
        <button className="closer drawer-toggle" onClick={toggleNav}><span>Close</span> <i className="arrow">&rarr;</i></button>
        <form className="SearchForm">
          <input type="search" placeholder="Search..." onKeyUp={this.handleFilterChange} />
        </form>
        <MainMenu tree={tree.children} settings={menuSettings} />
      </nav>
    )
  }
}


function buildTree(patterns) {
  return patterns.reduce((tree, p) => {
    const group = p.tags['group'] || 'Unknown';
    const parentLeaf = group.split('>').reduce((leaf, g) => {
      return leaf.children[g] || Object.assign(leaf.children, {[g] : {
          name: g,
          children: {},
        }})[g];
    }, tree)
    parentLeaf.children[p.id] = {
      name: p.name,
      to: `/pattern/${p.id}`
    }
    return tree
  }, {children: {}})
}

function filterPatterns(searchString, patterns) {
  if(searchString.length < 1) return patterns;
  const _searchString = searchString.toLowerCase();
  return patterns.filter(pattern => {
    return pattern.name.toLowerCase().indexOf(_searchString) !== -1 ||
      (pattern.tags['group'] && pattern.tags['group'].toLowerCase().indexOf(_searchString) !== -1)
  });
}

function MainMenu({tree, settings = {}}) {
  return (
    <ul className={`MenuList menu ${settings.className}`}>
      {Object.keys(tree).map(k => <MainMenuItem key={k} leaf={tree[k]} className={settings.itemClassName} childSettings={settings.children} />)}
    </ul>
  )
}

class MainMenuItem extends Component {
  constructor(props) {
    super(props)
    this.state = {collapsed: true}
    this.toggleCollapse = this.toggleCollapse.bind(this);
    this.handleKeyPress = this.handleKeyPress.bind(this);
  }
  toggleCollapse() {
    this.setState(state => ({collapsed: !state.collapsed}))
  }
  handleKeyPress(e) {
    switch(e.charCode) {
      case 13:
      case 32:
        this.setState(state => ({collapsed: !state.collapsed}))
        break;
      default:
        return;
    }
  }
  render() {
    const {leaf, childSettings, className} = this.props;
    const {collapsed} = this.state;
    const isCollapsible = true && leaf.children;
    const isCollapsed = isCollapsible && collapsed;

    if(isCollapsible) {
      return (
        <li className={`MenuItem ${className} collapsible ${isCollapsed ?'collapsed':''}`}>
          <a onClick={this.toggleCollapse} onKeyPress={this.handleKeyPress} tabIndex={0}>{leaf.icon}{leaf.name}</a>
          {leaf.children && <FluidContainer height={isCollapsed ? 0 : 'auto'}>
            <MainMenu tree={leaf.children} settings={childSettings} />
          </FluidContainer>}
        </li>
      )
    }

    return (
      <li className={`MenuItem ${className}`}>
        {leaf.to && <Link to={leaf.to}>{leaf.icon}{leaf.name}</Link>}
        {leaf.children && <MainMenu tree={leaf.children} settings={childSettings} />}
      </li>
    )
  }
}
