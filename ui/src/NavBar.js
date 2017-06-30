
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import SearchForm from './Search';
import './NavBar.css';
import logo from './logo.svg';

var menuTreeSample = {
  children: {
    Atom: {
      name: 'Atom',
      children: {
        Global: {
          name: 'Global',
          children: {}
        }
      }
    }
  }
};

class NavBar extends Component {
  buildNewTreeFromPatterns(patterns) {
    let tree = {children: {}};
    patterns.forEach(p => {
      var parentLeaf = p.group.split('>').reduce((leaf, g) => {
        return leaf.children[g] ? leaf.children[g] : leaf.children[g] = {
          name: g,
          children: {}
        };
      }, tree);
      parentLeaf.children[p.id] = {
        name: p.name,
        to: '/foo',
      }
    });
    return tree;
  }

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
    const tree = this.buildNewTreeFromPatterns(patterns);
    const menuSettings = {
      collapsible: false,
      className: 'horizontal MenuList l1 menu main-menu',
      itemClassName: 'l1',
      children: {
        collapsible: true,
        className: 'vertical MenuList l2',
        itemClassName: 'l2',
        children: {
          collapsible: false,
          className: 'vertical l3',
          itemClassName: 'l3',
        }
      }
    }
    return (
      <nav className="MannequinNav top-bar">
        <div className="top-bar-title">
          <Link to="/"><strong><img src={logo} alt="Mannequin" /></strong></Link>
        </div>
        <div id="responsive-menu">
          <div className="top-bar-left">
            <MainMenu tree={tree.children} settings={menuSettings} />
          </div>
          <div className="top-bar-right">
            <ul className="menu">
              <li><SearchForm patterns={patterns} /></li>
            </ul>
          </div>
        </div>
      </nav>
    )
  }
}

function MainMenu({tree, settings = {}}) {
  return (
    <ul className={`MenuList menu ${settings.className}`}>
      {Object.keys(tree).map(k => <MainMenuItem key={k} leaf={tree[k]} className={settings.itemClassName} collapsible={settings.collapsible} childSettings={settings.children} />)}
    </ul>
  )
}

class MainMenuItem extends Component {
  constructor(props) {
    super(props)
    this.state = {collapsed: true}
    this.toggleCollapse = this.toggleCollapse.bind(this);
  }
  toggleCollapse() {
    this.setState(state => ({collapsed: !state.collapsed}))
  }
  render() {
    const {leaf, collapsible, childSettings, className} = this.props;
    const {collapsed} = this.state;
    const isCollapsible = collapsible && leaf.children;
    const isCollapsed = isCollapsible && collapsed;

    return (
      <li className={`MenuItem ${className} ${isCollapsible ?'collapsible':''} ${isCollapsed ?'collapsed':''}`}>
        {leaf.to && <Link to={leaf.to}>{leaf.icon}{leaf.name}</Link>}
        {!leaf.to && <a onClick={this.toggleCollapse}>{leaf.icon}{leaf.name}</a>}
        {leaf.children && <MainMenu tree={leaf.children} settings={childSettings} />}
      </li>
    )
  }
}

export default NavBar;