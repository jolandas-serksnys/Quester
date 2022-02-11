import http from "../common/http-common";
import { User } from "../models/user.model";

export const login = (email: string, password: string) => {
    return http.post('/auth/login', {
        email,
        password
    })
    .then((response) => {
        if(response.data.access_token) {
            localStorage.setItem('user', JSON.stringify(response.data));
        }

        return response.data;
    });
}

export const logout = () => {
    localStorage.removeItem('user');
}

export const getCurrentUser = (): User => {
    const user:string | null = localStorage.getItem('user');
    if(user !== null) {
        return JSON.parse(user) as User;
    }

    return {} as User;
}

export const isLoggedIn = (): boolean => {
    return getCurrentUser().access_token != null;
}