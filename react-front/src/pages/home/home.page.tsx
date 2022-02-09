import React from 'react';
import { useQuery } from 'react-query';
import axios from 'axios';
import { Game } from '../../models/game.model';
import { css } from '@emotion/css';
import GameCard from '../../components/game-card/game-card.component';

const HomePage: React.FC = () => {
  
  const { data: games } = useQuery('games-all', async() => {
      const { data } = await axios.get(`http://127.0.0.1:8000/api/games`);
      return data as Game[];
  }, {
      staleTime: 5000
  });

  return(
    <main className='container'>
      <h1>Games</h1>
      <div className={css`
        display: grid;
        grid-template-columns: repeat( auto-fit, minmax(20rem, 1fr))
      `}>
        {games?.map(game => (
          <GameCard game={game} key={game.id} />
        ))}
      </div>
    </main>
  );
}

export default HomePage;