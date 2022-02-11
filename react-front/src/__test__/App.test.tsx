import { fireEvent, render, screen } from "@testing-library/react";
import { QueryClient, QueryClientProvider } from "react-query";
import { BrowserRouter, MemoryRouter } from "react-router-dom";
import App from "../App";

describe('homepage and gamepage', () => {
    const queryClient = new QueryClient();

    it('should render navbar', () => {
        render(
            <QueryClientProvider client={queryClient}>
                <BrowserRouter>
                    <App/>
                </BrowserRouter>
            </QueryClientProvider>);

        const navBarElement = screen.getByRole('navigation');

        expect(navBarElement).toBeInTheDocument();
    });

    it('should render game cards', async() => {
        render(
            <QueryClientProvider client={queryClient}>
                <BrowserRouter>
                    <App/>
                </BrowserRouter>
            </QueryClientProvider>);

        const gameCardElements = await screen.findAllByTestId('game-card');

        expect(gameCardElements.length).toBe(5);
    });

    it('should render game info', async() => {
        render(
            <QueryClientProvider client={queryClient}>
                <BrowserRouter>
                    <App/>
                </BrowserRouter>
            </QueryClientProvider>);

        const gameCardElements = await screen.findAllByTestId('game-card');
        fireEvent.click(gameCardElements[0], { button: 0 });

        const headerElement = await screen.findByText(/3D Anime style MMORPG/i);

        expect(headerElement).toBeInTheDocument();
    });
})

describe('auth', () => {
    const queryClient = new QueryClient();
    
    it('should render login button', () => {
        render(
            <QueryClientProvider client={queryClient}>
                <BrowserRouter>
                    <App/>
                </BrowserRouter>
            </QueryClientProvider>);

        const loginButtonElement = screen.getByText(/login/i);

        expect(loginButtonElement).toBeInTheDocument();
    })

    it('should route to login page', () => {
        render(
            <QueryClientProvider client={queryClient}>
                <BrowserRouter>
                    <App/>
                </BrowserRouter>
            </QueryClientProvider>);

        const loginButtonElement = screen.getByText(/login/i);
        fireEvent.click(loginButtonElement, { button: 0 });

        const loginHeadingElement = screen.getByRole('heading', { level: 2 });

        expect(loginHeadingElement).toBeInTheDocument();
    })

    it('should login', async() => {
        render(
            <QueryClientProvider client={queryClient}>
                <MemoryRouter initialEntries={['/']}>
                    <App/>
                </MemoryRouter>
            </QueryClientProvider>);

        const loginNavLinkElement = screen.getByRole('link', {name: /login/i});
        fireEvent.click(loginNavLinkElement, { button: 0 });

        const emailInputElement = screen.getByPlaceholderText(/email address/i);
        const passwordInputElement = screen.getByPlaceholderText(/password/i);

        fireEvent.change(emailInputElement, { target: {value: "admin@admin.com"}});
        fireEvent.change(passwordInputElement, { target: {value: "password"}});

        const loginButtonElement = screen.getByRole('button', {name: /login/i});
        fireEvent.click(loginButtonElement, { button: 0 });

        const userWelcomeElement = await screen.findByText(/welcome back, admin@admin.com/i);
        expect(userWelcomeElement).toBeInTheDocument();
    })

    it('should login and check profile page', async() => {
        render(
            <QueryClientProvider client={queryClient}>
                <MemoryRouter initialEntries={['/']}>
                    <App/>
                </MemoryRouter>
            </QueryClientProvider>);

        const loginNavLinkElement = screen.getByRole('link', {name: /login/i});
        fireEvent.click(loginNavLinkElement, { button: 0 });

        const emailInputElement = screen.getByPlaceholderText(/email address/i);
        const passwordInputElement = screen.getByPlaceholderText(/password/i);

        fireEvent.change(emailInputElement, { target: {value: "admin@admin.com"}});
        fireEvent.change(passwordInputElement, { target: {value: "password"}});

        const loginButtonElement = screen.getByRole('button', {name: /login/i});
        fireEvent.click(loginButtonElement, { button: 0 });

        const profileButtonElement = await screen.findByText(/profile/i);
        fireEvent.click(profileButtonElement, { button: 0 });
        
        const userAccountCreationDateElement = screen.getByText(/sun nov 21 2021/i);
        expect(userAccountCreationDateElement).toBeInTheDocument();
    })
})