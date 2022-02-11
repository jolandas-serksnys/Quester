import React from "react";
import { useQuery } from "react-query";
import { useNavigate, useParams } from "react-router-dom";
import { Game } from "../../models/game.model";
import http from '../../common/http-common';
import Container from "../../components/container/container.component";
import GameHeader from "../../components/game-header/game-header.component";

const GamePage: React.FC = () => { 
    const navigate = useNavigate();   
    const { gameId } = useParams();
  
    const { data: game, isLoading } = useQuery(`game-${gameId}`, async() => {
        const {data} = await http.get(`http://127.0.0.1:8000/api/games/${gameId}`);
        return data as Game;
    }, {
        staleTime: 5000,
        onError: (error) => {
            console.log(error);
            navigate('/');
        }
    });

    return (
        <>
            <GameHeader imageUrl={game?.image_url}/>
            <Container content={
                <>
                    {isLoading && <h2>Loading...</h2>}
                    {game && <>
                        <h2>{game?.title}</h2>
                        <p>{game?.description}</p>
                    </>}
                </>
            }/>
        </>
    )
}

export default GamePage;