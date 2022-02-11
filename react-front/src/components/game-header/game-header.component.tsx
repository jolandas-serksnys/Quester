import { css } from "@emotion/css";
import React from "react";

const GameHeader: React.FC<{imageUrl?: string}> = ({imageUrl}) => {
    return(
        <header className={css`
            min-width: 100%;
            height: max(50rem, 50vh);
            background: url(${imageUrl}) top center;
            background-size: cover;
            `}/>
    )
}

export default GameHeader;