
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import './NavBar.css';
import logo from './logo.svg';

class MannequinNav extends Component {

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
            <DropdownMenu items={items} stack={[VerticalMenu, AccordionMenu]} className="main-menu" />
          </div>
          <div className="top-bar-right">
            <ul className="menu">
              <li><input type="search" placeholder="Search..." /></li>
            </ul>
          </div>
        </div>
      </nav>
    )
  }
}

function DropdownMenu({items, stack, className}) {
  const submenustack = stack.slice();
  const MenuElement = submenustack.pop();
  return (
    <ul className={`menu ${className}`}>
      {items.map(item => (
        <li key={item.key}>
          <Link to={item.path}>{item.name}</Link>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={submenustack} />}
        </li>
      ))}
    </ul>
  )
}

function AccordionMenu({items, stack, className}) {
  const submenustack = stack.slice();
  const MenuElement = submenustack.pop();
  return (
    <ul className={`accordion menu ${className}`}>
      {items.map(item => (
        <li key={item.key}>
          <Link to={item.path}>{item.name}</Link>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={submenustack} />}
        </li>
      ))}
    </ul>
  )
}

function VerticalMenu({items, stack, className}) {
  const submenustack = stack.slice();
  const MenuElement = submenustack.pop();
  return (
    <ul className={`vertical menu ${className}`}>
      {items.map(item => (
        <li key={item.key}>
          <Link to={item.path}>{item.name}</Link>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={submenustack} />}
        </li>
      ))}
    </ul>
  )
}

export default MannequinNav;