
import React from 'react';

export const VariantNotFound = ({text = 'Variant not found'}) => {
    return <div className="NotFound Content callout warning">{text}</div>
}