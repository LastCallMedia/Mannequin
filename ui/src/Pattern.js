
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
              <option key={s.id} value={s.id}>{s.name}</option>
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
  used: PropTypes.arrayOf(PropTypes.string),
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

export const PatternInfoWrapper = ({pattern}) => (
  <PatternInfo {...pattern} />
)
PatternInfoWrapper.propTypes = {
  pattern: PropTypes.shape(PatternShape)
}

class PatternInfo extends Component {
  constructor(props) {
    super(props)
    this.state = {mode: 'html'}
    this.switchMode = this.switchMode.bind(this)
  }
  switchMode(e) {
    this.setState({mode: e.target.getAttribute('data-mode')});
  }
  render() {
    const {name, description, used, source} = this.props;
    const {mode} = this.state;
    return (
      <div className="PatternInfo row">
        <div className="column small-6">
          <h3>{name}</h3>
          <PatternInfoUsed used={used} />
          <PatternInfoDescription description={description} />
        </div>
        <div className="column small-6 code">
          <div className="button-group">
            <a className="button" data-mode="html" onClick={this.switchMode}>HTML</a>
            <a className="button" data-mode="raw" onClick={this.switchMode}>Raw</a>
          </div>
          {mode === 'html' && <CodePane src={source} />}
          {mode === 'raw' && <CodePane src={source} />}
        </div>
      </div>
    )
  }
}

const CodePane = ({src}) => (
  <iframe frameBorder="0" src={src}></iframe>
)

const PatternInfoDescription = ({description}) => (
  <div className="PatternInfoDescription">
    <label>Description</label>
    <p>
      {description.length > 0 && description}
      {description.length === 0 && <i>None</i>}
    </p>
  </div>
)

const PatternInfoUsed = ({used}) => {
  return (
    <div className="PatternInfoUsed">
      <label>Used in:</label>
      {used.length > 0 &&
        <ul>
          {used.map(u => (
            <li key={u}>{u}</li>
          ))}
        </ul>
      }
      {used.length === 0 && <p><i>None</i></p>}

    </div>
  )
}
PatternInfoUsed.propTypes = {
  used: PatternShape.used
}
PatternInfoUsed.defaultProps = {
  used: [],
};
