import React from 'react';
import PropTypes from 'prop-types';
import "../../fontello/css/fontello.css"
import "./button.css";
import cx from 'classnames';

const Icon = (props) => {
  if (!props.iconName) return null;
  if (props.iconName) {
    return <i className={`icon-${props.iconName}`} />;
  }
}

const Element = ({ element, icon, text, href, classes, target, onClick, toggleStatus, dataSrc, dataLanguage }) => {

  if (element == 'button') {
    return (
      <button
        href={href}
        className={classes}
        onClick={onClick}
        data-src={dataSrc}
        data-language={dataLanguage}
      >
        {text} <Icon iconName={icon} />
      </button>
    );
  }
  return (
    <a href={href} className={classes} target={target}>
      {text} <Icon iconName={icon} />
    </a>
  );
}

const Button = (props) => (
  <Element {...props} />
);

Button.propTypes = {
  element: PropTypes.string,
  icon: PropTypes.string,
  text: PropTypes.string,
  href: PropTypes.string,
  classes: PropTypes.string,
  target: PropTypes.string,
};
Button.defaultProps = {
  element: 'a',
  icon: null,
  text: null,
  href: '#',
  classes: 'Button',
  target: null,
  toggleStatus: null
};

export default Button;
