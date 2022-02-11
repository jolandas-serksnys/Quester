import React from 'react';
import { useQuery } from 'react-query';
import { Game } from '../../models/game.model';
import GamesGrid from '../../components/games-grid/games-grid.component';
import http from '../../common/http-common';

const HomePage: React.FC = () => {
  
  const { data: games } = useQuery('games-all', async() => {
      const { data } = await http.get(`http://127.0.0.1:8000/api/games`);
      return data as Game[];
  }, {
      staleTime: 5000
  });

  return(
    <main className='container'>
      <GamesGrid games={games} />
    </main>
  );
}

export default HomePage;