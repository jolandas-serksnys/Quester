import React from 'react';
import { useQuery } from 'react-query';
import { Game } from '../../models/game.model';
import GamesGrid from '../../components/games-grid/games-grid.component';
import http from '../../common/http-common';
import Container from '../../components/container/container.component';

const HomePage: React.FC = () => {
  
  const { data: games } = useQuery('games-all', async() => {
      const { data } = await http.get(`http://127.0.0.1:8000/api/games`);
      return data as Game[];
  }, {
      staleTime: 5000
  });

  return(
    <Container content={<GamesGrid games={games} />} />
  );
}

export default HomePage;