import { fireEvent, render, screen } from "@testing-library/react";
import App from "../App";

describe('homepage and gamepage', () => {
    it('should render navbar', () => {
        render(<App/>);
        const navBarElement = screen.getByRole('navigation');

        expect(navBarElement).toBeInTheDocument();
    });

    it('should render game cards', async() => {
        render(<App/>);
        const gameCardElements = await screen.findAllByTestId('game-card');

        expect(gameCardElements.length).toBe(5);
    });

    it('should render game info', async() => {
        render(<App/>);
        const gameCardElements = await screen.findAllByTestId('game-card');
        fireEvent(gameCardElements[0], new MouseEvent('click'));

        const headerElement = await screen.findByText(/fiesta online/i);

        expect(headerElement).toBeInTheDocument();
    });

    /*
    it('should render game card after leaving game info page', async() => {
        render(<App/>);
        const gameCardElements = await screen.findAllByTestId('game-card');
        fireEvent(gameCardElements[0], new MouseEvent('click'));

        const navBarLinkElement = screen.getByText(/home/i);
        fireEvent(navBarLinkElement, new MouseEvent('click'));

        const newGameCardElements = await screen.findAllByTestId('game-card');

        expect(newGameCardElements).toHaveLength(5);
    });*/
})

describe('auth', () => {
    it('should be able to go to login page', () => {
        render(<App/>)
    })
})