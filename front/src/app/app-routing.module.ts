import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './login/login.component';
import { AuthGuard } from './_helpers';
import { GameComponent } from './_views/game/game.component';
import { GamesComponent } from './_views/games/games.component';

const routes: Routes = [
  { path: 'games', component: GamesComponent },
  { path: 'login', component: LoginComponent },

  { path: 'g/:gameId', component: GameComponent },
  { path: 'g/:gameId/:gameTitle', component: GameComponent },

  // otherwise redirect to home
  { path: '**', redirectTo: 'games' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
