import React, { useContext, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Container from "../../components/container/container.component";
import { UserContext } from "../../contexts/user.context";

const ProfilePage: React.FC = () => {
    const { isAuthenticated, user } = useContext(UserContext);
    const navigate = useNavigate();
    
    useEffect(() => {
        if(!isAuthenticated) {
            navigate('/');
        }
    })

    return(
        <Container content={
            <ul>
                <li>{user.email}</li>
                <li>{user.name}</li>
                <li>{user.user_group}</li>
                <li>{new Date(user.created_at).toDateString()}</li>
            </ul>
        }/>
    )
}

export default ProfilePage;