import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './_helpers';
import { GameComponent } from './_views/game/game.component';
import { GamesComponent } from './_views/games/games.component';

const routes: Routes = [
  { path: '', component: HomeComponent, canActivate: [AuthGuard] },
  { path: 'login', component: LoginComponent },

  { path: 'games', component: GamesComponent },
  { path: 'g/:gameId', component: GameComponent },
  { path: 'g/:gameId/:gameTitle', component: GameComponent },

  // otherwise redirect to home
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
