
import PropTypes from 'prop-types';

/**
 * Common Shapes for use on Component.propTypes.
 */
export const VariantShape = {
    id: PropTypes.string,
    name: PropTypes.string,
    description: PropTypes.string,
    rendered: PropTypes.string.isRequired,
    metadata: PropTypes.shape()
}
export const PatternShape = {
    name: PropTypes.string.isRequired,
    rendered: PropTypes.string,
    used: PropTypes.arrayOf(PropTypes.string),
    variants: PropTypes.arrayOf(PropTypes.shape(VariantShape)),
    metadata: PropTypes.shape(),
    problems: PropTypes.arrayOf(PropTypes.string)
};

export const UsedShape = {
    name: PropTypes.string.isRequired,
    id: PropTypes.string.isRequired
}