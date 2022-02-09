import React from 'react';
import { QueryClient, QueryClientProvider } from 'react-query';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import Footer from './components/footer/footer.component';
import Navbar from './components/navbar/navbar.component';
import GamePage from './pages/game/game.page';
import HomePage from './pages/home/home.page';

const queryClient = new QueryClient();

const App: React.FC = () => {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <Navbar></Navbar>
        <Routes>
          <Route path="/" element={<HomePage/>}/>
          <Route path="/g/:gameId" element={<GamePage/>}/>
        </Routes>
        <Footer></Footer>
      </BrowserRouter>
    </QueryClientProvider>
  );
}

export default App;
