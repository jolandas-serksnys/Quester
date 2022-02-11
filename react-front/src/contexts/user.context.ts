import { createContext } from "react";
import { User } from "../models/user.model"

export const UserContext = createContext({
    isAuthenticated: false,
    user: {} as User,
    handleLogin: (email: string, password: string) => {},
    handleLogout: () => {}
});