
import React, {Component} from 'react';
import './MannequinNav.css';
import logo from './logo.svg';

class MannequinNav extends Component {

  render() {
    const ITEMS = [
      {name: 'Atoms', path: '#', children: []},
      {name: 'Molecules', path: '#', children: []},
      {name: 'Organisms', path: '#', children: [
        {name: 'Global', path: '#', children: [
          {name: 'Header', path: '#', children: []},
          {name: 'Footer', path: '#', children: []}
        ]}
      ]},
      {name: 'Templates', path: '#', children: []},
      {name: 'Pages', path: '#', children: []},
    ];
    return (
      <nav className="MannequinNav top-bar">
        <div className="top-bar-title">
          <strong><img src={logo} alt="Mannequin" /></strong>
        </div>
        <div id="responsive-menu">
          <div className="top-bar-left">
            <DropdownMenu items={ITEMS} stack={[VerticalMenu, AccordionMenu]} className="main-menu" />
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
  const MenuElement = stack.pop();
  return (
    <ul className={`menu ${className}`}>
      {items.map(item => (
        <li>
          <a href={item.path}>{item.name}</a>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={stack} />}
        </li>
      ))}
    </ul>
  )
}

function AccordionMenu({items, stack, className}) {
  const MenuElement = stack.pop();
  return (
    <ul className={`accordion menu ${className}`}>
      {items.map(item => (
        <li>
          <a href={item.path}>{item.name}</a>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={stack} />}
        </li>
      ))}
    </ul>
  )
}

function VerticalMenu({items, stack, className}) {
  const MenuElement = stack.pop();
  return (
    <ul className={`vertical menu ${className}`}>
      {items.map(item => (
        <li>
          <a href={item.path}>{item.name}</a>
          {(item.children.length > 0 && MenuElement) && <MenuElement items={item.children} stack={stack} />}
        </li>
      ))}
    </ul>
  )
}

export default MannequinNav;