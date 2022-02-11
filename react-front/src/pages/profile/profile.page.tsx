import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { User } from "../../models/user.model";
import { getCurrentUser } from "../../services/auth.service"

const ProfilePage: React.FC = () => {
    const user: User = getCurrentUser();
    const navigate = useNavigate();
    
    useEffect(() => {
        if(!user.access_token) {
            navigate('/');
        }
    })

    return(
        <main className="container">
            <h2>{user.email}</h2>
        </main>
    )
}

export default ProfilePage;