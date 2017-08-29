import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import FluidContainer from 'react-fluid-container';
import PropTypes from 'prop-types';
import './Menu.css';

function Menu({ tree, settings }) {
  return (
    <ul className={`MenuList ${settings.className}`}>
      {tree.map((child, i) =>
        <MainMenuItem
          key={i}
          leaf={child}
          className={settings.itemClassName}
          childSettings={settings.children}
          onNavigate={settings.onNavigate}
        />
      )}
    </ul>
  );
}
Menu.propTypes = {
  tree: PropTypes.arrayOf(PropTypes.shape()),
  settings: PropTypes.shape({
    children: PropTypes.shape()
  })
};
Menu.defaultProps = {
  tree: [],
  settings: {}
};

export default Menu;

class MainMenuItem extends Component {
  constructor(props) {
    super(props);
    this.state = { collapsed: true };
    this.toggleCollapse = this.toggleCollapse.bind(this);
    this.handleKeyPress = this.handleKeyPress.bind(this);
  }
  toggleCollapse() {
    this.setState(state => ({ collapsed: !state.collapsed }));
  }
  handleKeyPress(e) {
    switch (e.charCode) {
      case 13:
      case 32:
        this.setState(state => ({ collapsed: !state.collapsed }));
        break;
      default:
        return;
    }
  }
  render() {
    const { leaf, childSettings, className } = this.props;
    const { collapsed } = this.state;
    const isCollapsible = true && leaf.children;
    const isCollapsed = isCollapsible && collapsed;

    if (isCollapsible) {
      return (
        <li
          className={`MenuItem ${className} collapsible ${isCollapsed
            ? 'collapsed'
            : ''}`}
        >
          <a
            onClick={this.toggleCollapse}
            onKeyPress={this.handleKeyPress}
            tabIndex={0}
          >
            {leaf.icon}
            {leaf.name}
          </a>
          {leaf.children &&
            <FluidContainer
              height={isCollapsed ? 0 : 'auto'}
              style={{ overflow: 'hidden' }}
            >
              <Menu tree={leaf.children} settings={childSettings} />
            </FluidContainer>}
        </li>
      );
    }

    return (
      <li className={`MenuItem ${className}`}>
        {leaf.to &&
          <Link to={leaf.to} onClick={this.props.onNavigate}>
            {leaf.icon}
            {leaf.name}
          </Link>}
        {leaf.children &&
          <Menu tree={leaf.children} settings={childSettings} />}
      </li>
    );
  }
}
