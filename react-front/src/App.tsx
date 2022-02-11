import React, { useState } from 'react';
import { Route, Routes } from 'react-router-dom';
import Footer from './components/footer/footer.component';
import Navbar from './components/navbar/navbar.component';
import { UserContext } from './contexts/user.context';
import { User } from './models/user.model';
import GamePage from './pages/game/game.page';
import HomePage from './pages/home/home.page';
import LoginPage from './pages/login/login.page';
import ProfilePage from './pages/profile/profile.page';
import { login, logout } from './services/auth.service';

const App: React.FC = () => {
  const [user, setUser] = useState({} as User);

  function handleLogin(email: string, password: string) {
    login(email, password).then(
      (response) => {
        setUser(response);
      },
      (error) => {
        console.log(error);
      }
    )
  }

  function handleLogout() {
    logout();
    setUser({} as User);
  }

  return (
    <UserContext.Provider value={{isAuthenticated: user.access_token != null, user, handleLogin, handleLogout}}>
      <Navbar/>
      <Routes>
        <Route path="/" element={<HomePage/>}/>
        <Route path="/g/:gameId" element={<GamePage/>}/>

        <Route path="/login" element={<LoginPage/>}/>
        <Route path="/profile" element={<ProfilePage/>}/>
      </Routes>
      <Footer></Footer>
    </UserContext.Provider>
  );
}

export default App;
