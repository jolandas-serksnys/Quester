import { css } from "@emotion/css";
import React from "react";
import { Game } from "../../models/game.model";
import GameCard from "../game-card/game-card.component";

const GamesGrid: React.FC<{games?: Game[]}> = ({games}) => {
    return(
        <div className={css`
            display: grid;
            grid-template-columns: repeat( auto-fill, minmax(24rem, 1fr));
            margin: 0 -0.5rem;
        `}>
            {games?.map(game => (
                <GameCard game={game} key={game.id} />
            ))}
        </div>
    )
}

export default GamesGrid;