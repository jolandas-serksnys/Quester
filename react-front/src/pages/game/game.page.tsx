import React from "react";
import { useQuery } from "react-query";
import { useParams } from "react-router-dom";
import { Game } from "../../models/game.model";
import http from '../../common/http-common';

const GamePage: React.FC = () => {    
    const { gameId } = useParams();
  
    const { data: game} = useQuery(`game-${gameId}`, async() => {
        const {data} = await http.get(`http://127.0.0.1:8000/api/games/${gameId}`);
        return data as Game;
    }, {
        staleTime: 5000
    });

    return (
        <main className="container">
            <h2>{game?.title}</h2>
            <p>{game?.description}</p>
        </main>
    )
}

export default GamePage;