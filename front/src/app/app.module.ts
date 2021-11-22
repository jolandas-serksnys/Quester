import { NgModule, APP_INITIALIZER } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

import { JwtInterceptor, ErrorInterceptor, appInitializer } from './_helpers';
import { AuthenticationService } from './_services';

import { GameComponent } from './_views/game/game.component';
import { NavbarComponent } from './_components/navbar/navbar.component';
import { FooterComponent } from './_components/footer/footer.component';
import { GamesComponent } from './_views/games/games.component';
import { HeaderComponent } from './_components/header/header.component';
import { GamesGridComponent } from './_components/games-grid/games-grid.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalGameComponent } from './_components/modal-game/modal-game.component';
import { MapComponent } from './_components/map/map.component';
import { BottomNavComponent } from './_components/bottom-nav/bottom-nav.component';
import { ViewHeaderComponent } from './_components/view-header/view-header.component';
import { GameInfoGridComponent } from './_components/game-info-grid/game-info-grid.component';
import { SignInComponent } from './_views/sign-in/sign-in.component';
import { SignUpComponent } from './_views/sign-up/sign-up.component';
import { GameCreateComponent } from './_components/game-create/game-create.component';
import { GameEditComponent } from './_components/game-edit/game-edit.component';
import { GameManageComponent } from './_components/game-manage/game-manage.component';
import { ToastComponent } from './_components/toast/toast.component';

@NgModule({
  declarations: [
    AppComponent,
    GameComponent,
    NavbarComponent,
    FooterComponent,
    GamesComponent,
    HeaderComponent,
    GamesGridComponent,
    ModalGameComponent,
    MapComponent,
    BottomNavComponent,
    ViewHeaderComponent,
    GameInfoGridComponent,
    SignInComponent,
    SignUpComponent,
    GameCreateComponent,
    GameEditComponent,
    GameManageComponent,
    ToastComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ReactiveFormsModule,
    HttpClientModule,
    NgbModule
  ],
  providers: [
    //{ provide: APP_INITIALIZER, useFactory: appInitializer, multi: true, deps: [AuthenticationService] },
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    { provide: HTTP_INTERCEPTORS, useClass: ErrorInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
