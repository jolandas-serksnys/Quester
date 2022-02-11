import { css } from '@emotion/css';
import React from 'react';

const Footer: React.FC = () => {
    return (
        <div className={css`
            padding: 2rem 0;
            text-align: center;
        `}>
            <footer className='container'>Quester</footer>
        </div>
    );
}

export default Footer;