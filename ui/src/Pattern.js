
import React, {Component} from 'react';
import PropTypes from 'prop-types';
import './Pattern.css';

class Pattern extends Component {
  constructor(props) {
    super(props)
    this.state = {set: 'default'}
    this.handleSetChange = this.handleSetChange.bind(this);
  }
  handleSetChange(e) {
    this.setState({set: e.target.value});
  }
  render() {
    const sSet = this.state.set;
    const {name, description, sets} = this.props;
    const set = sets.filter(s => s.id === sSet).pop();
    return (
      <article className="Pattern">
        <header>
          <h4 className="name">{name}</h4>
          <select value={sSet} onChange={this.handleSetChange}>
            {sets.map(s => (
              <option value={s.id}>{s.name}</option>
            ))}
          </select>
          <p className="description">{description}</p>
        </header>
        <iframe title={name} frameBorder="0" src={set.rendered}></iframe>
      </article>
    )
  }
}

const PatternShape = {
  name: PropTypes.string.isRequired,
  description: PropTypes.string,
  rendered: PropTypes.string,
  sets: PropTypes.arrayOf(PropTypes.shape({
    name: PropTypes.string,
    description: PropTypes.string,
    rendered: PropTypes.string.isRequired,
  }))
};

Pattern.propTypes = PatternShape

export default Pattern;

/**
 * Simple wrapper to take a pattern object, as returned by the server, and
 * translate it into a full pattern component.
 */
export const PatternWrapper = ({pattern}) => (
  <Pattern name={pattern.name} rendered={pattern.rendered} sets={pattern.sets}/>
)
PatternWrapper.propTypes = {
  pattern: PropTypes.shape(PatternShape)
}

export const PatternList = ({patterns}) => (
  <ul className="PatternList no-bullet">
    {patterns.map(pattern => (
      <li key={pattern.id}><PatternWrapper pattern={pattern} /></li>
    ))}
  </ul>
);
PatternList.propTypes = {
  patterns: PropTypes.arrayOf(
    PropTypes.shape(PatternShape)
  )
};