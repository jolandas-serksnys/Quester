import { css } from "@emotion/css";
import React from "react";

const Button: React.FC<{text: string, type: 'button' | 'submit' | 'reset', fn?: () => void}> = ({text, type, fn}) => {
    return(
        <button 
            type={type} 
            onClick={fn}
            className={css`
                background: #35a2ff;
                color: #ffffff;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                transition: all 0.1s ease-in-out;
                font-weight: bold;

                &:hover {
                    background: #4dacff;
                }

                &:active {
                    transitions: none;
                    background: #1a94ff;
                }
            `}
            >
            {text}
        </button>
    )
}

export default Button;