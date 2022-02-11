import { css } from "@emotion/css";
import React from "react";

const Container: React.FC<{content: React.ReactChild}> = ({content}) => {
    return(
        <div className={css`
            width: 80%;
            margin: auto;
        `}>
            {content}
        </div>
    )
}

export default Container;