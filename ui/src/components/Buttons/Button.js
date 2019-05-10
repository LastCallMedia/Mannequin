import React from 'react';
import PropTypes from 'prop-types';
import "../../fontello/css/fontello.css"

const Icon = (props) => {
  if (!props.iconName) return null;
  if (props.iconName) {
    return <i className={`icon-${props.iconName}`} />;
  }
}

const Button = ({ icon }) => (
  <button>
    Button:
    <Icon iconName={icon} />
    Debug:
    <i className={`icon-${icon}`} />
  </button>
);

Button.propTypes = {
  icon: PropTypes.string,
};
Button.defaultProps = {
  icon: null,
};

export default Button;
