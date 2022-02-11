import { css } from "@emotion/css";
import React from "react";

const Input: React.FC<{type: 'text' | 'email' | 'password' | 'number', name: string, placeholder: string, value?: string, onChange?: (event: React.ChangeEvent<HTMLInputElement>) => void}> = ({type, name, placeholder, value, onChange}) => {
    return(
        <input 
            type={type} 
            name={name} 
            placeholder={placeholder}
            value={value}
            className={css`
                border: 1px solid #efefef;
                padding: 0.75rem 1.25rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.1rem 0.15rem rgba(0,0,0,.05);
                transition: all .1s ease-in-out;

                &:hover {
                    border-color: #dddddd;
                    box-shadow: 0 0.1rem 0.15rem rgba(0,0,0,.1);
                }
            `}
            onChange={onChange} />
    )
}

export default Input;