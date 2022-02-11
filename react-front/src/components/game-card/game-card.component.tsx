import { css } from "@emotion/css";
import React from "react";
import { Link } from "react-router-dom";
import { Game } from '../../models/game.model';
import Chip from "../chip/chip.component";

const GameCard: React.FC<{game:Game}> = ({game}) => {
    return(
        <Link to={`/g/${game.id}`} 
            className={css`
                position: relative;
                padding: 0.5rem;
                aspect-ratio: 16/9;
                transition: all 0.1s ease-in-out;

                .inner {
                    display: flex;
                    flex-direction: column;
                    justify-content: end;
                    width: 100%;
                    height: 100%;
                    border-radius: 0.5rem;
                    background: #000000;
                    transition: all 0.1s ease-in-out;
                    overflow: hidden;
                    position: relative;
                    padding: 1.5rem;
                    
                    img {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        z-index: ;
                        transition: all 0.1s ease-in-out;

                        height: 100%;
                        min-width: 100%;
                    }
                }

                &:hover {
                    padding: 0;

                    .inner {
                        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1);
                        padding: 2rem;

                        img {
                            opacity: 0.5;
                        }
                    }
                }
            `}
            data-testid="game-card">
            <div className="inner">
                <img src={game.image_url} alt="" />
                <div className={css`
                    position: relative;
                    color: #ffffff;
                    font-weight: bold;
                `}>
                    <h2 className={css`
                        font-size: 1.25rem;
                        margin-bottom: 0.5rem;
                    `}>{game.title}</h2>
                    <div>
                        {game.genre && <Chip text={game.genre}/>}
                    </div>
                </div>
            </div>
        </Link>
    )
}

export default GameCard;