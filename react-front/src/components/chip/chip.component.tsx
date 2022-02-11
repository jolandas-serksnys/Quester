import { css } from "@emotion/css";
import React from "react";

const Chip: React.FC<{text: string}> = ({text}) => {
    return (
        <div className={css`
            color: #000000;
            background: #ffffff;
            padding: 0.15rem 0.35rem;
            border-radius: 0.25rem;
            font-size: 0.9rem;
            display: inline-block;
            font-weight: bold;
        `}>
            {text}
        </div>
    )
}

export default Chip;