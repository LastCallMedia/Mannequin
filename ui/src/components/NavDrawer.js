import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Menu from './Menu';
import cx from 'classnames';
import { CloseArrow, Search as SearchIcon } from './Icons';
import './NavDrawer.css';

export class NavDrawer extends Component {
  constructor(props) {
    super(props);
    this.state = { filter: '' };
    this.handleFilterChange = this.handleFilterChange.bind(this);
    this.handleLinkPress = this.handleLinkPress.bind(this);
  }
  handleFilterChange(e) {
    this.setState({ filter: e.target.value });
  }
  handleLinkPress(e) {
    this.props.toggleNav();
  }
  render() {
    const { className, toggleNav, patterns } = this.props;
    const { filter } = this.state;
    const tree = buildTree(filterPatterns(filter, patterns));
    const menuSettings = {
      collapsible: false,
      className: 'l1',
      itemClassName: 'l1',
      onNavigate: this.handleLinkPress,
      children: {
        collapsible: true,
        className: 'l2',
        itemClassName: 'l2',
        onNavigate: this.handleLinkPress,
        children: {
          collapsible: false,
          className: 'l3',
          itemClassName: 'l3',
          onNavigate: this.handleLinkPress
        }
      }
    };

    return (
      <nav className={cx('NavDrawer', className)}>
        <button className="closer drawer-toggle" onClick={toggleNav}>
          <span>Close</span> <CloseArrow />
        </button>
        <form className="SearchForm">
          <input
            type="search"
            placeholder="Search..."
            onKeyUp={this.handleFilterChange}
          />
          <SearchIcon />
        </form>
        <Menu tree={tree} settings={menuSettings} />
      </nav>
    );
  }
}

NavDrawer.propTypes = {
  patterns: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.name
    })
  ),
  toggleNav: PropTypes.func,
  className: PropTypes.string
};
NavDrawer.defaultProps = {
  patterns: []
};

export default NavDrawer;

function buildTree(patterns) {
  // Build a flat object of all groups as arrays of patterns.
  const flat = patterns.reduce((tree, p) => {
    const group = p.metadata.group || 'Unknown';
    const item = {
      name: p.name,
      to: `/pattern/${p.id}`
    };
    if (tree[group]) {
      tree[group].push(item);
    } else {
      tree[group] = [item];
    }
    return tree;
  }, {});

  // Now stack the tree.
  return Object.keys(flat).sort().reduce((arrTree, k) => {
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

function filterPatterns(searchString, patterns) {
  if (searchString.length < 1) return patterns;
  const _searchString = searchString.toLowerCase();
  return patterns.filter(pattern => {
    return (
      pattern.name.toLowerCase().indexOf(_searchString) !== -1 ||
      (pattern.metadata['group'] &&
        pattern.metadata['group'].toLowerCase().indexOf(_searchString) !== -1)
    );
  });
}
