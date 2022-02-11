import React, { FormEvent, useContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
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
        handleLogin(email, password);
    }

    return(
        <main className="container">
            <form method="post" onSubmit={handleSubmit}>
                <div>
                    <input type="email" name="email" id="email" value={email} onChange={(e) => setEmail(e.target.value)} />
                </div>
                <div>
                    <input type="password" name="password" id="password" value={password} onChange={(e) => setPassword(e.target.value)} />
                </div>
                <div>
                    <button type="submit">Sign In</button>
                </div>
            </form>
        </main>
    )
}

export default LoginPage;