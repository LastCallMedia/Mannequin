import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Menu from './Menu';
import cx from 'classnames';
import { CloseArrow, Search as SearchIcon } from './Icons';
import Button from './Buttons';
import './NavDrawer.css';

export class NavDrawer extends Component {
  constructor(props) {
    super(props);
    this.state = { filter: '' };
    this.handleFilterChange = this.handleFilterChange.bind(this);
  }
  handleFilterChange(e) {
    this.setState({ filter: e.target.value });
  }
  handleLinkPress(e) {
    this.props.toggleNav();
  }
  render() {
    const { className, toggleNav, components } = this.props;
    const { filter } = this.state;
    const tree = buildTree(filterComponents(filter, components));
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
          itemClassName: 'l3'
        }
      }
    };

    return (
      <nav className={cx('NavDrawer', className)}>
        <div className="NavDrawer__top">
          <span>Mannequin</span>
          <Button
            text="Close"
            element="button"
            icon="close"
            classes="Button CloseButton"
            onClick={toggleNav} />
        </div>
        <form className="SearchForm">
          <input
            type="search"
            placeholder="Search..."
            onKeyUp={this.handleFilterChange}
          />
          <i className="icon-search"></i>
        </form>
        <Menu tree={tree} settings={menuSettings} />
      </nav>
    );
  }
}

NavDrawer.propTypes = {
  components: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.name
    })
  ),
  toggleNav: PropTypes.func,
  className: PropTypes.string
};
NavDrawer.defaultProps = {
  components: []
};

export default NavDrawer;

function buildTree(components) {
  // Build a flat object of all groups as arrays of components.
  const flat = components.reduce((tree, c) => {
    const group = c.metadata.group || 'Unknown';
    const item = {
      name: c.name,
      to: `/component/${c.id}`
    };
    if (tree[group]) {
      tree[group].push(item);
    } else {
      tree[group] = [item];
    }
    return tree;
  }, {});

  // Now stack the tree.
  return Object.keys(flat)
    .sort()
    .reduce((arrTree, k) => {
      const parentNode = k.split('>').reduce((t, part) => {
        // Find an existing leaf on the tree.
        let leaf = t.find(item => item.name === part);
        if (leaf) {
          return leaf.children;
        }
        // Create a new leaf on this tree.
        leaf = { name: part, children: [] };
        t.push(leaf);
        return leaf.children;
      }, arrTree);
      flat[k].forEach(i => parentNode.push(i));
      return arrTree;
    }, []);
}

function filterComponents(searchString, components) {
  if (searchString.length < 1) return components;
  const _searchString = searchString.toLowerCase();
  return components.filter(component => {
    return (
      component.name.toLowerCase().indexOf(_searchString) !== -1 ||
      (component.metadata['group'] &&
        component.metadata['group'].toLowerCase().indexOf(_searchString) !== -1)
    );
  });
}
