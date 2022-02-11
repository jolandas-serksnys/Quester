import { css } from "@emotion/css";
import React, { FormEvent, useContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import Button from "../../components/button/button.component";
import Container from "../../components/container/container.component";
import Input from "../../components/input/input.component";
import { UserContext } from "../../contexts/user.context";

const LoginPage: React.FC = () => {
    const { isAuthenticated, handleLogin } = useContext(UserContext);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const navigate = useNavigate();
    
    useEffect(() => {
        if(isAuthenticated) {
            navigate('/');
        }
    })

    function handleSubmit(event: FormEvent) {
        event.preventDefault();

        if(email.length < 6 || password.length < 6) {
            return;
        }

        handleLogin(email, password);
    }

    return(
        <Container content={
            <div className={css`
                    display: flex;
                    justify-content: center;
                `}>
                <form method="post" onSubmit={handleSubmit} className={css`
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                `}>
                    <h2>Login</h2>
                    <div>
                        <Input type="email" name="email" placeholder="Email address" value={email} onChange={(e) => setEmail(e.target.value)}/>
                    </div>
                    <div>
                        <Input type="password" name="password" placeholder="Password" value={password} onChange={(e) => setPassword(e.target.value)}/>
                    </div>
                    <div>
                        <Button type="submit" text="Login" />
                    </div>
                </form>
            </div>
        }/>
    )
}

export default LoginPage;